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

    include "src/common.php";
    include "src/database.php";
    include "src/view.php";
    include "src/navigation.php";
    include "src/dispatcher.php";

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Main framework function.
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
function run_framework()
{
    session_start();

// Strip majic quotes
    if(get_magic_quotes_gpc()) {
        $_GET = array_map('stripslashes', $_GET);
        $_POST = array_map('stripslashes', $_POST);
    }

// Create main dispatcher instance
    $dispatcher = new dispatcher();

// set up autoloader for models
    spl_autoload_register('autoload_models');

// Load controlers
    load_controllers($dispatcher);

// Run the dispatcher to call the relevent handaler function
    $dispatcher->run();
}

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Model autoloader
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
function autoload_models($class_name)
{
    include "app/models/$class_name.php";
}

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Load controlers
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
function load_controllers(&$dispatcher)
{
    $controlers = array();
    $sorted_controllers = array();
    $dir_handle = opendir("app/controlers/");

// Read all controler file names
    while ($file = readdir($dir_handle)) 
    {
        if(preg_match("/[0-9]+.+/", $file))
        {
            $name_split = preg_split("/[_\.]/", $file);
            //print_r($name_split);

            $order = array_shift($name_split);
            array_pop($name_split);

            $mod_name = "";
            foreach($name_split as $seg)
                $mod_name .= $seg . "_";

            $mod_name = substr($mod_name, 0, -1);

            array_push($controlers,
                array($file, $mod_name, $order));
        }
    }

// Sort and import controlers
    for($i = 0; $i < count($controlers); $i ++)
    {
        foreach($controlers as $controler)
        {
            if ($controler[2] == ($i + 1))
            {
                require "app/controlers/" . $controler[0];

                array_push($sorted_controllers, $controler[1]);

                break;
            }
        }
    }

// Call controller register functions now they have all
// bean included
    foreach($sorted_controllers as $controler)
    {
        $dispatcher->register_module("register_" . $controler);
    }
}
