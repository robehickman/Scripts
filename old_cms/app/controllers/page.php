<?php

class ctrl_page extends controller_base
{
    function __CONSTRUCT()
    {
        $this->load_outer_template('template');
        load_helper('errors');
        load_helper('navigation');
        load_helper('members');
    }

    function catch_all()
    {
        if(!isset($this->params[1]))
            throw new e_404("No page sepecified");

        $m_page = instance_model('page');
        $page = $m_page->get_page_by_clean_title($this->params[1]);

        if($page == array())
            throw new e_404("Page not found");

        $view = instance_view('page/template');
        $view = $view->parse_to_variable(array(
            'page' => $page
        ));

        $this->set_template_paramiters(array(
            'content' => $view,
            'title' => $page[0]['Title'],
            'description' => strip_content($page[0]['Content'])
        ));
    }

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Static page admin
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function admin_pages()
    {
    // Require admin login
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        $m_pages = instance_model('page');
        $pages = $m_pages->get_all();

        $list = array();
        foreach($pages as $page)
        {
            $opts = array('Edit' => make_url('page', 'admin_pages_edit', $page['ID']));

            if($page['Internal'] != 1)
                $opts['Delete'] = make_url('page', 'admin_pages_delete', $page['ID']);

            $list []= array(
                'name' => $page['Title'],
                'options' => $opts
            );
        }

        $view = instance_view('admin/index_generic');
        $view = $view->parse_to_variable(array(
            'sortable' => false,
                'title' => 'Static pages',
            'new_url'  => make_url('page', 'admin_pages_create'),
            'new_name' => 'New static page',
            'list' => $list,
            'col_classes' => array('tbl_stat_name', 'tbl_stat_opt')
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
            $m_page = instance_model('page');
            $title = $_POST['title'];

            $error = false;
            if($title == '') {
                $error = true;
                new_flash('Please enter a title',1);
            }

            $page = $m_page->get_page_by_title($title);
            if($page != array()) {
                $error = true;
                new_flash('A page with that name already exsits',1);
            }
            

            if($error == false)
            {
                $clean_title = clean_title($title);

                $m_page->create($title, $clean_title);
                $id = $m_page->last_id();
                redirect_to(make_url('page', 'admin_pages_edit', $id));
            }
        }

        $view = instance_view('admin/create_generic');
        $view = $view->parse_to_variable(array(
            'back_url' => make_url('page', 'admin_pages'),
            'title'    => 'New static page',
            'form_url' => make_url('page', 'admin_pages_create'),
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

        $m_page = instance_model('page');
        $page = $m_page->get_by_id($item);

        if($page == array())
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

            if($title != $old_title)
            {
                $t_page = $m_page->get_page_by_title($title);
                if($t_page != array()) {
                    $error = true;
                    new_flash('A page with that name already exsits',1);
                }
            }

            /*
            if($content == '') {
                $error = true;
                new_flash('Please enter some content',1);
            }
            */

            if($error == false)
            {

                $page = $m_page->get_by_id($item);
                $page[0]['Title']       = $title;

            // Maintain naming on internal pages to prevent unfound page errors.
                if($page[0]['Internal'] == 0) { 
                    $clean_title = clean_title($title);
                    $page[0]['Clean_title'] = $clean_title;
                }
                $page[0]['Content']     = $content;

                $m_page->update_table('pages', 'ID', $page[0]);

                redirect_to(make_url('page', 'admin_pages'));
            }
        }
        {
            $title   = $page[0]['Title'];
            $content = $page[0]['Content'];
        }

        $view = instance_view('page/admin/edit_page');
        $view = $view->parse_to_variable(array(
            'form_url' => make_url('page', 'admin_pages_edit', $item),
            'back_url' => make_url('page', 'admin_pages'),
            'title'   => $title,
            'content' => $content
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

        $m_page = instance_model('page');

        if(isset($_POST['Submit']))
        {
            $action = $_POST['Submit'];
            $id     = $_POST['item'];

            if($action == "Delete") {
                $m_page->delete_by_id($id);
            }

            redirect_to(make_url('page', 'admin_pages'));
        }

        if(!isset($this->params[2]) || (!is_numeric($this->params[2])))
            throw new exception("No page specified");

        $item = $this->params[2];

        $page = $m_page->get_by_id($item);

        if($page == array())
            throw new exception("Page does not exist");

        $title = $page[0]['Title'];

        $view = instance_view('admin/delete_generic');
        $view = $view->parse_to_variable(array(
            'back_url' => make_url('page', 'admin_pages'),
            'title'    => 'Delete static page',
            'msg'      => "Are you sure you wish to <strong>permenantly</strong> delete page $title?",
            'form_url' => make_url('page', 'admin_pages_delete'),
            'item'     => $item
        ));

        $this->set_template_paramiters(array(
            'content' => $view
        ));
    }
}

