<?php
// Script to auto-generate WordPress Next Gen Gallery tabels from the filesystem.

define('RPATH', 'path to wp root');

// Maje sure not in mantinance mode
if(file_exists(RPATH . '/.maintenance'))
    unlink(RPATH . '/.maintenance');

include RPATH . "/wp-config.php";

//===============================================================================
// Adds ability to create gallery setting gid
//===============================================================================
class nggdb_extended extends nggdb
{
    function __construct(){parent::__construct();}

    function add_gallery_gid($gid = '', $title = '', $path = '', $description = '',
        $pageid = 0, $previewpic = 0, $author = 0  )
    {
        global $wpdb;
       
        $slug = nggdb::get_unique_slug( sanitize_title( $title ), 'gallery' );
		
		if ( false === $wpdb->query( $wpdb->prepare(
            "INSERT INTO $wpdb->nggallery (
                gid, name, slug, path, title, galdesc, pageid, previewpic, author
            ) VALUES (
                %d, %s, %s, %s, %s, %s, %d, %d, %d)",
            $gid, $slug, $slug, $path, $title, $description, $pageid, $previewpic, $author )))
        {
			return false;
		}
		
		$galleryID = (int) $wpdb->insert_id;
         
		//and give me the new id		
		return $galleryID;
    }
}

$fh = fopen(RPATH . '/.maintenance', 'w');
fwrite ($fh, '<?php $upgrading = time(); ?>');
fclose($fh);

// Clear the tabels
mysql_query("delete from wp_ngg_pictures");
mysql_query("delete from wp_ngg_gallery");
mysql_query("delete from wp_ngg_album");
mysql_query("delete from wp_posts where comment_status = 'gallery_page'");

// Run
$gallery_id = 1;

recur_dir(RPATH . "path to gallery", "web path to gallery", "dir name");

unlink(RPATH . '/.maintenance');

//===============================================================================
// Main function, calls recursivly to walk file tree and convert images
//===============================================================================
function recur_dir($path, $wpath, $dir)
{
    global $gallery_id;
    $db = new nggdb_extended();

    $images = array();
    $children = array();
    $gal_created = 0;

// Read directory config file
    $config = null;
    if(file_exists($path . '/galleryconf'))
        $config  = config_parser($path . '/galleryconf');

// Loop over files in directory
    $dhandle = opendir($path);
    while($file = readdir($dhandle))
    {
        if(!($file === '.svn' || $file === '.' || $file === '..'))
        {
        // decend into directory
            if(is_dir($path .'/'. $file) && $file != 'thumbs')
            {
                print "\nEntering: $file\n";

                $child = recur_dir($path .'/'. $file, $wpath .'/'. $file, $file);
                array_push($children, $child);

                print "\nLeaving: $file\n";
            }
        // prosess images
            else if(preg_match("/(.JPG|.PNG|.GIF)/i", $file))
            {
                if($gal_created == 0)
                {
                // Create gallery
                    $db->add_gallery_gid($gallery_id, $dir, "wp-content/$wpath", '', 0, $previewpic = 0);

                    if($config != null)
                    {
                        $body = $config['body'];                  
                        if(strtolower($config['head']['position']) == 'after')
                            $post = "[nggallery id=$gallery_id]\n<div style='clear: both;'></div>$body";
                        else
                            $post = "$body\n[nggallery id=$gallery_id]";
                    }
                    else
                        $post = "[nggallery id=$gallery_id]";

                // Create gallary post
                    wp_insert_post(array(
                        'post_title'     => $dir,
                        'post_name'      => $slug,
                        'comment_status' => 'gallery_page',
                        'post_type'      => 'page',
                        'post_content'   => $post,
                        'post_status'    => 'publish',
                        'post_author'    => 1));

                    $gal_created = 1;
                }

            // create thumbs
                if(!file_exists("$path/thumbs"))
                    mkdir("$path/thumbs");

               exec("convert -resize 180x180 \"$path/$file\" \"{$path}/thumbs/thumbs_$file\"");

            // insert into db
                print "Added file: $file\n";
                $id = $db->insert_image($gallery_id, $file, $file, "", 0);

                array_push($images, $id);
            }
        }
    }

    $return = array(
        'Name'     => $dir,
        'Path'     => $path,
        'ID'       => $gallery_id,
        'Img'      => $images,
        'Children' => $children);

    if($gal_created == 1)
        $gallery_id ++;

// Generate index gallary
    if($config != null && strtolower($config['head']['mode']) == 'index')
    {
        print "\n\n-- making index --\n";

        $galleries = array();
        get_galleries($return, $galleries);

    // make album
        $album_galleries = array();

        foreach($galleries as $gallery)
        {
            array_push($album_galleries, $gallery['ID']);
        }

        $id = $db->add_album($config['head']['title'], $previewpic = 0, '',serialize($album_galleries) , $pageid = 0);

        $body = $config['body'];                  
        if(strtolower($config['head']['position']) == 'after')
            $post = "[album id=$id template=compact]\n<div style='clear: both;'></div>$body";
        else
            $post = "$body\n[album id=$id template=compact]";

        wp_insert_post(array(
            'post_title'     => $config['head']['title'],
            'post_name'      => $config['head']['title'],
            'comment_status' => 'gallery_page',
            'post_type'      => 'page',
            'post_content'   => $post,
            'post_status'    => 'publish',
            'post_author'    => 1));
    }

    return $return;
}

//===============================================================================
// Find valid galleries form tree
//===============================================================================
function get_galleries($gal_tree, &$galleries)
{
    foreach($gal_tree['Children'] as $child)
    {
        get_galleries($child, $galleries);
    }

    if(count($gal_tree['Img']) > 0)
        array_push($galleries, $gal_tree);
}

//===============================================================================
// Parses per directory config files
//===============================================================================
function config_parser($path)
{
    $conffile = fopen($path, 'r');
    $config   = fread($conffile, filesize($path));

    $parsed_config = array();
    $parsed_config['head'] = array();
    $parsed_config['body'] = "";

// split into head and body sections
    $lines = explode("\n", $config);

    $head = $body = array();
    $mode = 0;

    foreach($lines as $line)
    {
        if(preg_match("/--body--/i", $line) and $mode == 0)
            $mode = 1;
        else {
            if($mode == 0) array_push($head, $line);
            else array_push($body, $line);
        }
    }

// Parse key-value pares
    foreach($head as $line)
    {
        $result = explode(':', $line);

        $key   = array_shift($result);
        $value = "";

        $id = 0;
        foreach($result as $item) {
            if($id > 0) $value .= ":$item";
            else $value .= trim($item);

            $id ++;
        }

        if($key != "") {
            $parsed_config['head'] = array_merge(
                $parsed_config['head'], array(strtolower($key) => $value));
        }
    }

// Re join body
    $body_txt = "";

    foreach($body as $line)
        $body_txt .= "$line\n";

    $parsed_config['body'] = trim($body_txt);

    return $parsed_config;
}

