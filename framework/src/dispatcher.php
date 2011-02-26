<?php
/*
 Copyright 2009 Robert Hickman

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class dispatcher
{
    var $handlers;
    var $navigation;

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Constructor
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function __CONSTRUCT()
    {
        $this->handlers          = array();
        $this->navigation        = new navigation();

    // Connect to the DB
        mysql_connect(HOST, USERNAME, PASSWORD)or
            die("cannot connect");

        mysql_select_db(DB_NAME)or die("cannot select DB");
    }

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Register a function as a handler for a URL
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function register_handler($name, $func)
    {
        if(!function_exists($func))
        {
            die("Function '$func' does not exist\n");
        }

        if(!isset($this->handlers[$name]))
        {
            $this->handlers = array_merge(
                $this->handlers, array("$name" => $func));
        }
        else
        {
            die("A handler for $name is already registered\n");
        }
    }

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Register a module
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function register_module($reg_func)
    {
        if(!function_exists($reg_func))
        {
            die("Function '$reg_func' does not exist\n");
        }

        $reg_func($this, $this->navigation);
    }

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Run the controller
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function run()
    {
        $site_index = get_site_index();
        $page_title = strip_uscores($site_index['sect']);

        try
        {   
            $result = $this->run_handler($page_title);
        }
        catch(Exception $e)
        {
            if(function_exists("framework_error_callback"))
                framework_error_callback($e->getMessage());

            $result = make_return("Error",
                $e->getMessage());
        }

        $this->display($result);
    }

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Display the main template
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function display($result)
    {
        $page_title  = $result['page_title'];
        $content     = $result['content'];

        if($result['ajax'] == false)
        {
            $navigation = "";
            $navigation = $this->navigation->display();

       // Display the main template
            $view = new view("main", "theme/");
            $view->parse(array(
                'path'        => get_current_path(),
                'page_title'  => $page_title,
                'navigation'  => $navigation,
                'content'     => $content));
        }
        else
        {
            print $content;
        }
    }

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Run the relevent handler function
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    private function run_handler($page_title)
    {
        if(isset($this->handlers[$page_title]))
        {
            return $this->handlers[$page_title]();
        }
        else
        {
            return make_return("Error","No such page.\n");
        }
    }
}
