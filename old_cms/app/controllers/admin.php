<?php

class ctrl_admin extends controller_base
{
    var $table_editor = null;

    function __CONSTRUCT()
    {
        $this->load_outer_template('admin');

    // Require login
        if(!isset($_SESSION['active_user']))
            redirect_to('/');
    }

    function index()
    {
        $root = get_app_root();

        $view    = instance_view('admin/index');
        $content = $view -> parse_to_variable(array(
        ));

        $this->set_template_paramiters(array(
            'path'    => $root,
            'content' => $content
        ));
    }
}

