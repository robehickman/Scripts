<?php

class ctrl_files extends controller_base
{
    function __CONSTRUCT()
    {
        $this->load_outer_template('template');
        load_helper('errors');

    // Require login
        if(!isset($_SESSION['active_user']))
            redirect_to('/');
    }

    function admin_files()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        $m_files = instance_model('files');
        $files = $m_files->get_all('ID', 'desc');

        $list = array();
        foreach($files as $file)
        {
            $name = '';
            $type = '';
            if($file['Is_img'] == true)
            {
                $name = make_url('res', 'files', 'thumbs', $file['Title']);
                $type = 'image';
            }
            else
                $name = $file['Title'];


            $list []= array(
                'name' => $name,
                'type' => $type,
                'extra_cols' => array(
                    'url' => array(make_url('res', 'files', $file['Title']), 'tbl_file_url')
                ),
                'options' => array(
                    'Delete' => make_url('files', 'admin_files_delete', $file['ID']))
            );
        }

        $view = instance_view('admin/index_generic');
        $view = $view->parse_to_variable(array(
            'sortable' => false,
            'title' => 'Files',
            'name_col' => 'Name/Preview',
            'new_url'  => make_url('files', 'admin_files_create'),
            'new_name' => 'Upload file',
            'extra_cols' => array('url' => 'URL'),
            'list' => $list,
            'col_classes' => array('tbl_files_name', 'tbl_files_opt')
        ));


        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }

    function admin_files_create()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        $m_files     = instance_model('files');

        if(isset($_POST['Submit']))
        {
            $file_name = $_FILES['file']['name'];
            $file_mime = $_FILES['file']['type'];

            $error = false;
            if(!in_array(strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION)),
                 $GLOBALS['allowed_ext_files']))
            {
                $error = true;
                new_flash('File type is not allowed',1);
            }
            else
            {
                $is_image = false;

                $unique_id = sha1(time());
                $tmppath = TMPFILES . '/' . $unique_id . '_' . $_FILES['file']['name'];
                if(isset($_FILES['file']) && move_uploaded_file($_FILES['file']['tmp_name'], $tmppath))
                {
                // preprocess images
                    try {
                        $is_image = true;

                        $im = new Imagick($tmppath);
                        $im->adaptiveResizeImage('620','620', true);
                        $im->writeImage($tmppath);
                        $im->destroy();
                    }
                // Handle outher file types
                    catch(exception $e){
                        $is_image = false;
                    }
                }
                else
                {
                    $error = true;
                    new_flash('File failed to upload',1);
                }
            }

            if($error == false)
            {

                $newloc = 'res/files/';

            // Check name is unique
                $hard_file_name = $file_name;
                $ctr = 0;
                for(;;)
                {
                    if($ctr > 200)
                        throw new exception('Could not create unique file name');
 
                    if(file_exists($newloc . $hard_file_name))
                    {
                        $ctr += 1;

                    // add number to file name
                        $split_name = explode('.', $file_name);
                        $base = array_shift($split_name);
                        $ext  = implode('.', $split_name);
                        $hard_file_name = $base . '_' . $ctr . '.' . $ext;
                    }
                    else

                        break;
                }

                $file_name = $hard_file_name;

            // move image file
                rename($tmppath, $newloc . $file_name);

                $size   = filesize($newloc . $file_name);

                if($is_image)
                {
                // generate thumbnail
                    $im = new Imagick($newloc . $file_name);
                    $im->cropThumbnailImage('130','98');
                    $im->writeImage($newloc . 'thumbs/' . $file_name);
                    $im->destroy();
                }

                $m_files->create($file_name, $size, $file_mime, $is_image);

                redirect_to(make_url('files', 'admin_files'));
            }
        }

        $view = instance_view('files/admin/upload');
        $view = $view->parse_to_variable(array(
            'form_url'   => make_url('files', 'admin_files_create'),
            'back_url'   => make_url('files', 'admin_files'),
            'title'      => 'Upload file'
        ));

        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }

    function admin_files_delete()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        $m_files = instance_model('files');

        if(isset($_POST['Submit']))
        {
            $action = $_POST['Submit'];
            $id     = $_POST['item'];

            if($action == "Delete") {
                $file = $m_files->get_by_id($id);

                if($file == array())
                    throw new exception("File does not exist");

            // delete file on disk
                $path = 'res/files/' . $file[0]['Title'];
                if(file_exists($path))
                    unlink($path);

                $path = 'res/files/thumbs/' . $file[0]['Title'];
                if(file_exists($path))
                    unlink($path);

            // remove from db
                $m_files->delete_by_id($id);
            }

            redirect_to(make_url('files', 'admin_files'));
        }

        if(!isset($this->params[2]) || (!is_numeric($this->params[2])))
            throw new exception("No member specified");

        $item = $this->params[2];

        $file = $m_files->get_by_id($item);

        if($file == array())
            throw new exception("File does not exist");

        $title = $file[0]['Title'];

        $view = instance_view('admin/delete_generic');
        $view = $view->parse_to_variable(array(
            'back_url' => make_url('files', 'admin_files'),
            'title'    => 'Delete file page',
            'msg'      => "Are you sure you wish to <strong>permenantly</strong> delete file $title?",
            'form_url' => make_url('files', 'admin_files_delete'),
            'item'     => $item
        ));

        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }
}

