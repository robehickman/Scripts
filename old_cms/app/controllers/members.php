<?php
class ctrl_members extends controller_base
{
    function __CONSTRUCT()
    {
        $this->load_outer_template('template');
        load_helper('errors');
        load_helper('navigation');
    }

    function index()
    {
        $model = instance_model('page');
        $page = $model->get_page_by_clean_title('index');

        if($page == array())
            throw new e_404("Cound not find home page in DB");

        $members = instance_model('member_categories');
        $categories = $members->get_all();

        $view = instance_view('members/index');
        $view = $view->parse_to_variable(array(
            'page'       => $page,
            'categories' => $categories
        ));

        $this->set_template_paramiters(array(
            'content' => $view,
            'description' => strip_content($page[0]['Content'])
        ));
    }

    function category()
    {
        if(!isset($this->params[2]))
            throw new e_404("No page sepecified");

        $m_categories = instance_model('member_categories');
        $category = $m_categories->get_by_clean_name($this->params[2]);

        if($category == array())
            throw new e_404('No such category');

        $m_members = instance_model('members');
        $cat_contents = $m_members->get_in_category($category[0]['ID']);

    // merge in gallery
        $m_gallery = instance_model('gallery');
        $m_set     = instance_model('gallery_set');

        for($i = 0; $i < count($cat_contents); $i ++)
        {
            $tmp = $cat_contents[$i];

            $user_imgs = array();
            $i_set = $m_set->get_by_category_and_user($category[0]['ID'], $tmp['ID']);
            $i1 = 0;
            foreach($i_set as $itm)
                $user_imgs = array_merge($user_imgs, $m_gallery->get_in_set($itm['ID']));

            shuffle($user_imgs);
            
            $user_imgs = array_slice($user_imgs,0,2);

            $cat_contents[$i]['Gallery'] = $user_imgs;
        }

        shuffle($cat_contents);

        $view = instance_view('members/category');
        $view = $view->parse_to_variable(array(
            'category' => $cat_contents,
            'category_title' => $category[0]['Name']
        ));

        $this->set_template_paramiters(array(
            'content' => $view,
            'title' => $category[0]['Name'],
            'description' => $category[0]['Name']
        ));
    }

    function member()
    {
        if(!isset($this->params[2]))
            throw new e_404("No member sepecified");

        $m_members = instance_model('members');
        $member = $m_members->get_by_clean_title($this->params[2]);

        if($member == array())
            throw new e_404('Member does not exist');

    // Get gallery images
        $m_set     = instance_model('gallery_set');
        $m_gallery = instance_model('gallery');

        $set = $m_set->get_by_user($member[0]['ID']);

        $user_gallery = array();
        if($set != array())
            foreach($set as $row)
                $user_gallery = array_merge($user_gallery, $m_gallery->get_in_set($row['ID'])); 

        $view = instance_view('members/member');
        $view = $view->parse_to_variable(array(
            'member' => $member,
            'gallery' => $user_gallery
        ));

        $this->set_template_paramiters(array(
            'content' => $view,
            'title' => $member[0]['Title'],
            'description' => strip_content($member[0]['Content'])
        ));
    }

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Category admin
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

    function admin_categories()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        $m_categories = instance_model('member_categories');
        $categories = $m_categories->get_all();

        $list = array();
        foreach($categories as $category)
        {
            $list []= array(
                'name' => $category['Name'],
                'ID'   => $category['ID'],
                'options' => array(
                    'Edit' => make_url('members', 'admin_categories_edit', $category['ID']),
                    'Delete' => make_url('members', 'admin_categories_delete', $category['ID']))
            );
        }

        $view = instance_view('admin/index_generic');
        $view = $view->parse_to_variable(array(
            'sortable' => true,
            'sort_url' => make_url('members', 'sort_admin_categories'),
            'title' => 'Member categories',
            'new_url'  => make_url('members', 'admin_categories_create'),
            'new_name' => 'New member category',
            'list' => $list,
            'col_classes' => array('tbl_cats_name', 'tbl_cats_opt')
        ));


        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }

// AJAX handler for category sorting
    function sort_admin_categories()
    {
        $this->outer_template = null;

    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            die;

        if(!isset($_POST['SortOrder']))
            die;

        $order = explode(',', $_POST['SortOrder']); 

        $m_categories = instance_model('member_categories');

        $m_categories->update_sort($order);
    }

    function admin_categories_create()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        $title = '';
        if(isset($_POST['Submit']))
        {
            $m_category = instance_model('member_categories');
            $title = $_POST['title'];

            $error = false;
            if($title == '') {
                $error = true;
                new_flash('Please enter a title',1);
            }

            $page = $m_category->get_by_name($title);
            if($page != array()) {
                $error = true;
                new_flash('A category with that name already exsits',1);
            }
            

            if($error == false)
            {
                $clean_title = str_replace(" ","-",$title);
                $clean_title = preg_replace("/[^a-zA-Z0-9\-\_]/", "", $clean_title);

                $m_category->create($title, $clean_title);
                $id = $m_category->last_id();
                
                redirect_to(make_url('members', 'admin_categories_edit', $id));
            }
        }

        $view = instance_view('admin/create_generic');
        $view = $view->parse_to_variable(array(
            'back_url' => make_url('members', 'admin_categories'),
            'title'    => 'New member category',
            'form_url' => make_url('members', 'admin_categories_create'),
            'form_value' => $title
        ));

        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }

    function admin_categories_edit()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        if(!isset($this->params[2]) || (!is_numeric($this->params[2])))
            throw new exception("No page specified");

        $item = $this->params[2];

        $m_category = instance_model('member_categories');
        $category = $m_category->get_by_id($item);

        if($category == array())
            throw new exception("Category does not exist");

        if(isset($_POST['Submit']))
        {
            $title     = $_POST['title'];
            $old_title = $_POST['old_title'];

            $error = false;
            if($title == '') {
                $error = true;
                new_flash('Please enter a title',1);
            }

            $rename = false;
            if($title != $old_title)
            {
                $rename = true;
                $t_category = $m_category->get_by_name($title);
                if($t_category != array()) {
                    $error = true;
                    new_flash('A category with that name already exsits',1);
                }
            }

            $have_file = true;
            if($_FILES['file']['error'] != 4)
            {
                if(!in_array(strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION)),
                     $GLOBALS['allowed_ext']))
                {
                    $error = true;
                    new_flash('File type is not allowed',1);
                }

                $unique_id = sha1(time());
                $tmppath = TMPFILES . '/' . $unique_id . '_' . $_FILES['file']['name'];
                if(isset($_FILES['file']) && move_uploaded_file($_FILES['file']['tmp_name'], $tmppath))
                {
                    try {
                        $im = new Imagick($tmppath);
                     // $im->resizeImage('130','130', null, 0);
                        $im->cropThumbnailImage('130','98');
                        $im->writeImage($tmppath);
                        $im->destroy();
                    }
                    catch(exception $e){
                        $error = true;
                        new_flash('Could not read file',1);
                    }
                }
                else
                {
                    $error = true;
                    new_flash('File failed to upload',1);
                }
            }
            else
                $have_file = false;

            if($error == false)
            {
                $category = $m_category->get_by_id($item);
                $category[0]['Name']       = $title;
                $clean_title                = clean_title($title);
                $category[0]['Clean_name'] = $clean_title;

                if($rename == true)
                {
                    $clean_old = clean_title($old_title);
                    $the_path = 'res/categories/';

                    rename($the_path . $clean_old, $the_path . $clean_title);

                    $category[0]['Image'] = $clean_title;
                }

            // move image file
                if($have_file == true)
                {
                    $newloc = 'res/categories/' . $clean_title;
                    rename($tmppath, $newloc);
                    
                    $category[0]['Image'] = $clean_title;
                }

                $m_category->update_table('member_category', 'ID', $category[0]);

                redirect_to(make_url('members', 'admin_categories'));
            }
            if(file_exists($tmppath))
                unlink($tmppath);
        }
        {
            $title   = $category[0]['Name'];
            $image   = $category[0]['Image'];
        }

        $view = instance_view('members/admin/edit_category');
        $view = $view->parse_to_variable(array(
            'form_url' => make_url('members', 'admin_categories_edit', $item),
            'back_url' => make_url('members', 'admin_categories'),
            'title'    => $title,
            'image'    => $image
        ));

        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }

    function admin_categories_delete()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        $m_categories = instance_model('member_categories');

        if(isset($_POST['Submit']))
        {
            $action = $_POST['Submit'];
            $id     = $_POST['item'];

            if($action == "Delete") {
            // Set any member pages and galleries in category to uncategorised
                $m_members = instance_model('members');
                $m_set     = instance_model('gallery_set');

                $sets    = $m_set->get_by_category($id);
                foreach($sets as $set)
                {
                    $set['Category'] = 0;
                    $m_set->update_table('gallery_set', 'ID', $set);
                }

            // Delete the image
                $the_path = 'res/categories/';

                $category = $m_categories->get_by_id($id);
                if($category == array())
                    throw new exception("Category does not exist");

                $title = $category[0]['Clean_name'];

                unlink($the_path . $title);

            // Delete the category
                $m_categories->delete_by_id($id);
            }

            redirect_to(make_url('members', 'admin_categories'));
        }

        if(!isset($this->params[2]) || (!is_numeric($this->params[2])))
            throw new exception("No category specified");

        $item = $this->params[2];

        $category = $m_categories->get_by_id($item);

        if($category == array())
            throw new exception("Category does not exist");

        $title = hen($category[0]['Name']);

        $view = instance_view('admin/delete_generic');
        $view = $view->parse_to_variable(array(
            'back_url' => make_url('members', 'admin_categories'),
            'title'    => 'Delete member category',
            'msg'      => "Are you sure you wish to <strong>permenantly</strong> delete category $title? <br />
                           All users of this category will be set to 'uncategorised'.",
            'form_url' => make_url('members', 'admin_categories_delete'),
            'item'     => $item
        ));

        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Member page admin
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function admin_pages()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        $m_members = instance_model('members');
        $members = $m_members->get_all();

        $m_set = instance_model('gallery_set');
        $m_categories = instance_model('member_categories');

        $list = array();
        foreach($members as $member)
        {
            $categories = $m_set->get_by_user($member['ID']);

            $in_categories = '';
            if($categories == array())
                $in_categories = 'Uncategoriesd';
            else
            {
                $no_cat = true;
                foreach($categories as $cat)
                {
                    $get_name = $m_categories->get_by_id($cat['Category']);
                    if($get_name != array())
                    {
                        $no_cat = false;
                        $in_categories .= ', ' . hen($get_name[0]['Name']);
                    }
                }

                if($no_cat == false)
                    $in_categories = substr($in_categories, 2);
                else
                    $in_categories = 'Uncategoriesd';
            }

//            print_r2($categories);

 //           print '--<br />';


            $list []= array(
                'name' => $member['Title'],
                'extra_cols' => array(
                    'cats' => array($in_categories, 'tbl_mem_cats')
                ),
                'options' => array(
                    'Categories &amp; Gallery' => make_url('members', 'admin_gallery_set', $member['ID']),
                    'Edit' => make_url('members', 'admin_pages_edit', $member['ID']),
                    'Delete' => make_url('members', 'admin_pages_delete', $member['ID']))
            );
        }


        $view = instance_view('admin/index_generic');
        $view = $view->parse_to_variable(array(
            'sortable' => false,
            'title' => 'Member pages',
            'new_url'  => make_url('members', 'admin_pages_create'),
            'new_name' => 'New member page',
            'extra_cols' => array('cats' => 'Categories'),
            'list' => $list,
            'col_classes' => array('tbl_mem_name', 'tbl_mem_opt')
        ));


        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }

    function admin_pages_create()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        $title = '';
        if(isset($_POST['Submit']))
        {
            $m_members = instance_model('members');
            $title = $_POST['title'];

            $error = false;
            if($title == '') {
                $error = true;
                new_flash('Please enter a title',1);
            }

            $page = $m_members->get_by_title($title);
            if($page != array()) {
                $error = true;
                new_flash('A page with that name already exsits',1);
            }

            if($error == false)
            {
                $clean_title = str_replace(" ","-",$title);
                $clean_title = preg_replace("/[^a-zA-Z0-9\-\_]/", "", $clean_title);

                $m_members->create($title, $clean_title);
                $id = $m_members->last_id();
                redirect_to(make_url('members', 'admin_pages_edit', $id));
            }
        }

        $view = instance_view('admin/create_generic');
        $view = $view->parse_to_variable(array(
            'back_url' => make_url('members', 'admin_pages'),
            'title'    => 'Create member page',
            'form_url' => make_url('members', 'admin_pages_create'),
            'form_value' => $title
        ));

        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }

    function admin_pages_edit()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        if(!isset($this->params[2]) || (!is_numeric($this->params[2])))
            throw new exception("No page specified");

        $item = $this->params[2];

        $m_member     = instance_model('members');
        $m_categories = instance_model('member_categories');

        $member = $m_member->get_by_id($item);

        if($member == array())
            throw new exception("Page does not exist");

        if(isset($_POST['Submit']))
        {
            $title     = $_POST['title'];
            $old_title = $_POST['old_title'];
            $content   = $_POST['content'];

            $error = false;
            if($title == '') {
                $error = true;
                new_flash('Please enter a title',1);
            }

            $renamed = false;
            if($title != $old_title)
            {
                $renamed = true;
                $t_page = $m_member->get_by_title($title);
                if($t_page != array()) {
                    $error = true;
                    new_flash('A page with that name already exsits',1);
                }
            }

            $have_file = true;
            if($_FILES['file']['error'] != 4)
            {
                if(!in_array(strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION)),
                     $GLOBALS['allowed_ext']))
                {
                    $error = true;
                    new_flash('File type is not allowed',1);
                }

                $unique_id = sha1(time());
                $tmppath = TMPFILES . '/' . $unique_id . '_' . $_FILES['file']['name'];
                if(isset($_FILES['file']) && move_uploaded_file($_FILES['file']['tmp_name'], $tmppath))
                {
                    try {
                        $im = new Imagick($tmppath);
                        $im->cropThumbnailImage('220','165');
                        $im->writeImage($tmppath);
                        $im->destroy();
                    }
                    catch(exception $e){
                        $error = true;
                        new_flash('Could not read file',1);
                    }
                }
                else
                {
                    $error = true;
                    new_flash('File failed to upload',1);
                }
            }
            else
                $have_file = false;

            if($error == false)
            {
                $member = $m_member->get_by_id($item);
                $member[0]['Title']       = $title;
                $clean_title = clean_title($title);
                $member[0]['Clean_title'] = $clean_title;
                $member[0]['Content']    = $content;

                if($renamed == true)
                {
                // Rename profile image if exists
                    $clean_old = clean_title($old_title);

                    $the_path = 'res/profiles/';
                    if(file_exists($the_path . $clean_old))
                        rename($the_path . $clean_old, $the_path . $clean_title);

                    $member[0]['Image'] = $clean_title;

                // Rename gallery if exists
                    $the_path = 'res/gallery/';
                    if(file_exists($the_path . $clean_old))
                        rename($the_path . $clean_old, $the_path . $clean_title);
                }

            // move image file
                if($have_file == true)
                {
                    $newloc = 'res/profiles/' . $clean_title;
                    rename($tmppath, $newloc);
                    $member[0]['Image'] = $clean_title;
                }

                $m_member->update_table('members', 'ID', $member[0]);

                redirect_to(make_url('members', 'admin_pages'));
            }
        }
        else
        {
            $title   = $member[0]['Title'];
            $content = $member[0]['Content'];
        }

        $categories = $m_categories->get_all();

        $view = instance_view('members/admin/edit_page');
        $view = $view->parse_to_variable(array(
            'form_url'   => make_url('members', 'admin_pages_edit', $item),
            'back_url'   => make_url('members', 'admin_pages'),
            'title'      => $title,
            'otitle'     => $member[0]['Title'],
            'content'    => $content,
            'image'      => $member[0]['Image']
        ));

        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }

    function admin_pages_delete()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        $m_members = instance_model('members');

        if(isset($_POST['Submit']))
        {
            $action = $_POST['Submit'];
            $id     = $_POST['item'];

            if($action == "Delete") {
            // Clean up gallery sets and galleries
                $m_set     = instance_model('gallery_set');
                $m_members    = instance_model('members');

                $member = $m_members->get_by_id($id);

                if($member == array())
                    Throw new exception('member page does not exist');

                $sets = $m_set->get_by_user($id);

                foreach($sets as $set)
                {
                    $m_set->delete_set($set['ID'], $member[0]['Clean_title']);
                }

                $path = 'res/gallery/';
                rrmdir($path . $member[0]['Clean_title']);

            // Delete profile image

                $path = 'res/profiles/';
                unlink($path . $member[0]['Clean_title']);

                $m_members->delete_by_id($id);
            }

            redirect_to(make_url('members', 'admin_pages'));
        }

        if(!isset($this->params[2]) || (!is_numeric($this->params[2])))
            throw new exception("No member specified");

        $item = $this->params[2];

        $member = $m_members->get_by_id($item);

        if($member == array())
            throw new exception("Member does not exist");

        $title = $member[0]['Title'];

        $view = instance_view('admin/delete_generic');
        $view = $view->parse_to_variable(array(
            'back_url' => make_url('members', 'admin_pages'),
            'title'    => 'Delete member page',
            'msg'      => "Are you sure you wish to <strong>permenantly</strong> delete member $title?",
            'form_url' => make_url('members', 'admin_pages_delete'),
            'item'     => $item
        ));

        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }


/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Member gallery sets admin
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function admin_gallery_set()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        if(!isset($this->params[2]) || (!is_numeric($this->params[2])))
            throw new exception("No user specified");

        $user = $this->params[2];

        $m_set = instance_model('gallery_set');
        $set = $m_set->get_by_user($user);

        $list = array();
        foreach($set as $row)
        {
            $list []= array(
                'name' => $row['Title'],
                'options' => array(
                    'Edit Gallery' => make_url('members', 'admin_gallery', $row['ID']),
                    'Edit'       => make_url('members', 'admin_set_edit', $row['ID']),
                    'Delete'     => make_url('members', 'admin_set_delete', $row['ID']))
            );
        }

        $view = instance_view('admin/index_generic');
        $view = $view->parse_to_variable(array(
            'back_url'  => make_url('members', 'admin_pages'),
            'sortable' => false,
            'title' => 'In Categories',
            'new_url'  => make_url('members', 'admin_set_create', $user),
            'new_name' => 'Add to category',
            'list' => $list,
            'col_classes' => array('tbl_set_name', 'tbl_set_opt')
        ));


        $this->set_template_paramiters(array(
            'content' => $view
        ));

    }

    function admin_set_create()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        if(!isset($this->params[2]) || (!is_numeric($this->params[2])))
            throw new exception("No set specified");

        $user = $this->params[2];

        $title = '';
        if(isset($_POST['Submit']))
        {
            $m_set = instance_model('gallery_set');
            $title = $_POST['title'];

            $error = false;
            if($title == '') {
                $error = true;
                new_flash('Please enter a title',1);
            }

            $set = $m_set->get_by_title($title, $user);
            if($set != array()) {
                $error = true;
                new_flash('A set with that name already exsits',1);
            }

            if($error == false)
            {
                $clean_title = str_replace(" ","-",$title);
                $clean_title = preg_replace("/[^a-zA-Z0-9\-\_]/", "", $clean_title);

                $m_set->create($user, $title, $clean_title);
                $id = $m_set->last_id();

                redirect_to(make_url('members', 'admin_set_edit', $id));
            }
        }

        $view = instance_view('admin/create_generic');
        $view = $view->parse_to_variable(array(
            'back_url' => make_url('members', 'admin_gallery_set', $user),
            'title'    => 'Add To Category',
            'form_label' => 'Referance Name (eg "Charcoal Drawings")',
            'form_url' => make_url('members', 'admin_set_create', $user),
            'form_value' => $title
        ));

        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }

    function admin_set_edit()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        if(!isset($this->params[2]) || (!is_numeric($this->params[2])))
            throw new exception("No set specified");

        $item = $this->params[2];

        $m_categories = instance_model('member_categories');
        $m_set = instance_model('gallery_set');
        $set = $m_set->get_by_id($item);

        $user = $set[0]['Owner'];

        if($set == array())
            throw new exception("Set does not exist");

        if(isset($_POST['Submit']))
        {
            $title     = $_POST['title'];
            $old_title = $_POST['old_title'];
            $category  = $_POST['category'];

            $error = false;
            if($title == '') {
                $error = true;
                new_flash('Please enter a title',1);
            }

            if($title != $old_title)
            {
                $t_set = $m_set->get_by_title($title, $user);
                if($t_set != array()) {
                    $error = true;
                    new_flash('A set with that name already exsits',1);
                }
            }

            $chack_exists = $m_categories->get_by_id($category);
            if($category !=0 && $chack_exists == array()) {
                $error = true;
                new_flash('Category does not exist',1);
            }

            if($error == false)
            {
                $set = $m_set->get_by_id($item);
                $set[0]['Title']       = $title;
                $clean_title           = clean_title($title);
                $set[0]['Clean_title'] = $clean_title;
                $set[0]['Category']    = $category;

                $m_set->update_table('gallery_set', 'ID', $set[0]);

                redirect_to(make_url('members', 'admin_gallery_set', $user));
            }
            if(file_exists($tmppath))
                unlink($tmppath);
        }
        {
            $title    = $set[0]['Title'];
            $category = $set[0]['Category'];
        }

        $categories = $m_categories->get_all();

        $view = instance_view('gallery/admin/edit_set');
        $view = $view->parse_to_variable(array(
            'form_url'  => make_url('members', 'admin_set_edit', $item),
            'back_url'  => make_url('members', 'admin_gallery_set', $user),
            'title'     => $title, 
            'otitle' => $set[0]['Title'],
            'categories' => $categories,
            'member_category' => $category
        ));

        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }

    function admin_set_delete()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        $m_set = instance_model('gallery_set');

        if(!isset($this->params[2]) || (!is_numeric($this->params[2])))
            throw new exception("No set specified");

        $item = $this->params[2];

        $set = $m_set->get_by_id($item);

        if($set == array())
            throw new exception("Set does not exist");
        $user = $set[0]['Owner'];

        $title = $set[0]['Title'];

        if(isset($_POST['Submit']))
        {
            $action = $_POST['Submit'];
            $id     = $_POST['item'];

            if($action == "Delete") {
                $m_member = instance_model('members');
                $member = $m_member->get_by_id($user);

                if($member == array())
                    throw new exception('Member does not exist');


                $m_set->delete_set($id, $member[0]['Clean_title']);
            }

            redirect_to(make_url('members', 'admin_gallery_set', $user));
        }

        $view = instance_view('admin/delete_generic');
        $view = $view->parse_to_variable(array(
            'back_url' => make_url('members', 'admin_pages', $item),
            'title'    => 'Delete member page',
            'msg'      => "Are you sure you wish to <strong>permenantly</strong> delete set $title?",
            'form_url' => make_url('members', 'admin_set_delete', $item),
            'item'     => $item
        ));

        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Member gallery admin
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function admin_gallery()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        if(!isset($this->params[2]) || (!is_numeric($this->params[2])))
            throw new exception("No set specified");

        $item = $this->params[2];

        $m_gallery = instance_model('gallery');
        $m_set     = instance_model('gallery_set');
        $m_member  = instance_model('members');

        $set     = $m_set->get_by_id($item);
        if($set == array())
            throw new exception('Set does not exist');

        $member = $m_member->get_by_id($set[0]['Owner']);

        if($member == array())
            throw new exception('Member does not exist');

        $gallery = $m_gallery->get_in_set($item);

        $list = array();
        foreach($gallery as $image)
        {
            $list []= array(
                'name' => make_url('res', 'gallery', $member[0]['Clean_title'], 'thumbs', $image['File']),
                'options' => array(
                    'Delete' => make_url('members', 'admin_gallery_delete', $item, $image['ID']))
            );
        }

        $view = instance_view('admin/index_generic');
        $view = $view->parse_to_variable(array(
            'image_mode' => true,
            'back_url' => make_url('members', 'admin_gallery_set', $set[0]['Owner']),
            'sortable' => false,
            'title' => 'Group: ' . $set[0]['Title'],
            'new_url'  => make_url('members', 'admin_gallery_create', $item),
            'new_name' => 'Upload image',
            'list' => $list
        ));


        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }

    function admin_gallery_create()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        if(!isset($this->params[2]) || (!is_numeric($this->params[2])))
            throw new exception("No set specified");

        $item = $this->params[2];

        //$m_gallery = instance_model('gallery');
        $m_member  = instance_model('members');
        $m_set     = instance_model('gallery_set');

        //$gallery = $m_gallery->get_in_set($item);
        $set     = $m_set->get_by_id($item);

        if($set == array())
            throw new exception('Set does not exist');

        $member  = $m_member->get_by_id($set[0]['Owner']);

        if($member == array())
            throw new exception('Owning member does not exist');


        $title = '';
        if(isset($_POST['Submit']))
        {
            $file_name = $_FILES['file']['name'];
            $file_mime = $_FILES['file']['type'];

            $error = false;
            if(!in_array(strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION)),
                 $GLOBALS['allowed_ext']))
            {
                $error = true;
                new_flash('File type is not allowed',1);
            }

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
                    $error = true;
                    new_flash('Could not read file',1);
                }
            }
            else
            {
                $error = true;
                new_flash('File failed to upload',1);
            }

            if($error == false)
            {
                if(!file_exists('res/gallery/' . $member[0]['Clean_title']))
                    mkdir('res/gallery/' . $member[0]['Clean_title']);

                if(!file_exists('res/gallery/' . $member[0]['Clean_title'] . '/thumbs'))
                    mkdir('res/gallery/' . $member[0]['Clean_title'] . '/thumbs');


            // move image file
                $newloc = 'res/gallery/' . $member[0]['Clean_title'];

                $hard_file_name = $file_name;
                $ctr = 0;
                for(;;)
                {
                    if($ctr > 200)
                        throw new exception('Could not create unique file name');
 
                    if(file_exists($newloc . '/' . $hard_file_name))
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


            // move file
                rename($tmppath, $newloc . '/' . $file_name);

            // generate thumbnail
                $im = new Imagick($newloc . '/' . $file_name);
                $im->cropThumbnailImage('130','98');
                $im->writeImage($newloc . '/thumbs/' . $file_name);
                $im->destroy();

                $m_gallery  = instance_model('gallery');
                $m_gallery->create($item, $file_name);

                redirect_to(make_url('members', 'admin_gallery', $item));
            }
        }

        $view = instance_view('files/admin/upload');
        $view = $view->parse_to_variable(array(

            'form_url'   => make_url('members', 'admin_gallery_create', $item),
            'back_url'   => make_url('members', 'admin_gallery', $item),
            'title'      => 'Upload image'
        ));

        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }

    function admin_gallery_delete()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        if(!isset($this->params[2]) || (!is_numeric($this->params[2])))
            throw new exception("No set specified");

        $set_id = $this->params[2];

        $m_gallery = instance_model('gallery');

        if(isset($_POST['Submit']))
        {
            $action = $_POST['Submit'];
            $id     = $_POST['item'];

            if($action == "Delete") {
                $image = $m_gallery->get_by_id($id);

                if($image == array())
                    throw new exception("Image does not exist");

                $m_set     = instance_model('gallery_set');
                $m_members = instance_model('members');

                $set = $m_set->get_by_id($set_id);

                if($set == array())
                    throw new exception("Image set does not exist");

                $member = $m_members->get_by_id($set[0]['Owner']);

                if($member == array())
                    throw new exception("Member does not exist");

            // delete file on disk
                $path = 'res/gallery/' . $member[0]['Clean_title'] . '/' . $image[0]['File'];
                if(file_exists($path))
                    unlink($path);

                $path = 'res/gallery/' . $member[0]['Clean_title'] . '/thumbs/' . $image[0]['File'];
                if(file_exists($path))
                    unlink($path);

            // remove from db
                $m_gallery->delete_by_id($id);
            }

            redirect_to(make_url('members', 'admin_gallery', $set_id));
        }


        if(!isset($this->params[3]) || (!is_numeric($this->params[3])))
            throw new exception("No image specified");

        $item   = $this->params[3];

        $image = $m_gallery->get_by_id($item);

        if($image == array())
            throw new exception("Image does not exist");

        $title = $image[0]['File'];

        $view = instance_view('admin/delete_generic');
        $view = $view->parse_to_variable(array(
            'back_url' => make_url('members', 'admin_gallery', $set_id),
            'title'    => 'Delete gallery image',
            'msg'      => "Are you sure you wish to <strong>permenantly</strong> delete image $title?",
            'form_url' => make_url('members', 'admin_gallery_delete', $set_id, $item),
            'item'     => $item
        ));

        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }
}
