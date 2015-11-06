<?php

class ctrl_navi extends controller_base
{
    function __CONSTRUCT()
    {
        $this->load_outer_template('admin');
        load_helper('errors');

    // Require login
        if(!isset($_SESSION['active_user']))
            redirect_to('/');
    }

    function admin_navi()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        $m_navi = instance_model('navigation');
        $navi = $m_navi->get_all('Order');

        $list = array();
        foreach($navi as $item)
        {
            $list []= array(
                'name' => $item['Title'],
                'ID'   => $item['ID'],
                'options' => array(
                    'Edit' => make_url('navi', 'admin_navi_edit', $item['ID']),
                    'Delete' => make_url('navi', 'admin_navi_delete', $item['ID']))
            );
        }

        $view = instance_view('admin/index_generic');
        $view = $view->parse_to_variable(array(
            'sortable' => true,
            'sort_url' => make_url('navi', 'sort_admin_navi'),
            'title' => 'Navigation',
            'new_url'  => make_url('navi', 'admin_navi_create'),
            'new_name' => 'New link',
            'list' => $list,
            'col_classes' => array('tbl_navi_name', 'tbl_navi_opt')
        ));

        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }

// AJAX handler for category sorting
    function sort_admin_navi()
    {
        $this->outer_template = null;

    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            die;

        if(!isset($_POST['SortOrder']))
            die;

        $order = explode(',', $_POST['SortOrder']); 

        print_r2($order);

        $m_navi = instance_model('navigation');
        $m_navi->update_sort($order);
    }

    function admin_navi_create()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        $m_navi     = instance_model('navigation');

        if(isset($_POST['Submit']))
        {
            $title = $_POST['title'];

            $error = false;

            if($title == '')
            {
                $error = true;
                new_flash('Please enter a title', 1);
            }

            if($error == false)
            {
                $m_navi->create($title);

                redirect_to(make_url('navi', 'admin_navi'));
            }
        }
        else
        {
            $title = '';
        }

        $view = instance_view('admin/create_generic');
        $view = $view->parse_to_variable(array(
            'form_url'   => make_url('navi', 'admin_navi_create'),
            'form_value' => $title,
            'back_url'   => make_url('navi', 'admin_navi'),
            'title'      => 'New navigation item'
        ));

        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }

    function admin_navi_edit()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        if(!isset($this->params[2]) || (!is_numeric($this->params[2])))
            throw new exception("No link specified");

        $item = $this->params[2];

        $m_navi   = instance_model('navigation');

        $navi = $m_navi->get_by_id($item);
        if($navi == array())
            throw new exception("Link does not exist");

        if(isset($_POST['Submit']))
        {
            $title = $_POST['title'];
            $type = $_POST['type'];
            $data = $_POST['data'];

            $error = false;
        
            if($title == '')
            {
                $error = true;
                new_flash('Please enter a title', 1);
            }

            if($type == 'page')
            {
                $m_page = instance_model('page');
                $page = $m_page->get_by_id($data);

                if($page == array())
                {
                    $error = true;
                    new_flash('Page does not exist', 1);
                }
            }
            else if($type == 'url')
            {
                if($data == '')
                {
                    $error = true;
                    new_flash('Please enter a URL', 1);
                }
            }
            else
            {
                $error = true;
                new_flash('Unknown type', 1);
            }

            if($error == false)
            {
                 $navi[0]['Title'] = $title;    
                 $navi[0]['Type']  = $type;    
                 $navi[0]['Data']  = $data;    

                 $m_navi->update_table('navigation', 'ID', $navi[0]);

                redirect_to(make_url('navi', 'admin_navi'));
            }
        }
        else
        {
            $title   = $navi[0]['Title'];
            $type    = $navi[0]['Type'];
            $data    = $navi[0]['Data'];
        }

        $view = instance_view('navi/admin/edit_main');
        $view = $view->parse_to_variable(array(
            'ajax_url'   => make_url('navi', 'ajax_admin_navi_edit'),
            'form_url'   => make_url('navi', 'admin_navi_edit', $item),
            'back_url'   => make_url('navi', 'admin_navi'),
            'page_title' => 'Edit navi link',
            'title'      => $title,
            'type'       => $type,
            'data'       => $data,
            'types'      => array('url' => 'URL', 'page' => 'Static Page')
        ));

        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }

// AJAX handler for above
    function ajax_admin_navi_edit()
    {
        $this->outer_template = null;

    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $type = $_POST['type'];
        $data = trim($_POST['data']);

        if($type == 'page')
        {
            $m_page = instance_model('page');
            $pages = $m_page->get_all();

            $view = instance_view('navi/admin/edit_page');
            $view->parse(array(
                'pages' => $pages,
                'data' => $data,
            ));
        }
        else if($type == 'url')
        {
            $view = instance_view('navi/admin/edit_url');
            $view->parse(array(
                'data' => $data,
            ));
        }
        else
            print 'Unknown type';
    }


    function admin_navi_delete()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        $m_navi = instance_model('navigation');

        if(isset($_POST['Submit']))
        {
            $action = $_POST['Submit'];
            $id     = $_POST['item'];

            if($action == "Delete") {
                $link = $m_navi->get_by_id($id);

                if($link == array())
                    throw new exception("Navi link does not exist");

            // remove from db
                $m_navi->delete_by_id($id);
            }

            redirect_to(make_url('navi', 'admin_navi'));
        }

        if(!isset($this->params[2]) || (!is_numeric($this->params[2])))
            throw new exception("No link specified");

        $item = $this->params[2];

        $link = $m_navi->get_by_id($item);

        if($link == array())
            throw new exception("Navi link does not exist");

        $title = $link[0]['Title'];

        $view = instance_view('admin/delete_generic');
        $view = $view->parse_to_variable(array(
            'back_url' => make_url('navi', 'admin_navi'),
            'title'    => 'Delete navigation link',
            'msg'      => "Are you sure you wish to <strong>permenantly</strong> delete link $title?",
            'form_url' => make_url('navi', 'admin_navi_delete'),
            'item'     => $item
        ));

        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }
}

