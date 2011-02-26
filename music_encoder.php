<?php
#recursivly reencode music files into OGG
recur_dir("source", "dest");

function recur_dir($path, $alt_path)
{
    print "===\n $path" . "\n\n";
    $dhandle = opendir($path);

    while($file = readdir($dhandle))
        if(!($file === '.svn' || $file === '.' || $file === '..'))
        {
        // if the current file is not a directory
            if(!is_dir($path . $file))
            {
            // find file name without extension and display
                $split = preg_split("/\.[A-Za-z0-9]+$/", $file);

            // encode
                $of_name = $path . $file;
                $nf_name = $alt_path . $split[0] . ".ogg";


                $of_name = preg_replace("/\"/", "\\\"", $of_name);

                $nf_name = preg_replace("/\:/", "", $nf_name);
                $nf_name = preg_replace("/\"/", "", $nf_name);

                if(!file_exists($nf_name))
                {
                    // if already in ogg format, move
                    if(preg_match("/.+\.ogg/", $file))
                    {
                        print "Moving " . $split[0] . ".ogg\n";

                        shell_exec("cp \"$of_name\" \"$nf_name\"  2> /dev/null");
                       print ("cp \"$of_name\" \"$nf_name\"  2> /dev/null");
                    }
                    // else reencode
                    else
                    {
                        print "Encoding " . $split[0] . ".ogg\n";
                        shell_exec("ffmpeg -i \"$of_name\" -acodec vorbis -aq 50 \"$nf_name\" 2> /dev/null");
                    }
                }
                else
                {
                    print "Skipping " . $split[0] . ".ogg\n";
                }
            }

        // else recur into the directory
            else
            {
                $current = $alt_path . $file . '/';
            // create the directory if it does not exist
                if(!file_exists($current)) 
                    mkdir($current);
                recur_dir($path . $file . "/", $current);
            }
        }
}
