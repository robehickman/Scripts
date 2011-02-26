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

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Constructs a URL with section/subsection information
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
function make_url($sect, $sub_sect = NULL, $id = NULL)
{
    $sect = add_uscores($sect);
    $path = get_current_path();

    if(USE_REWRITE == true)
    {
        if($sub_sect == NULL && $id == NULL)
        {
            return $path . "$sect.html";
        }

        else if($id == NULL)
        {
            return $path . "$sect/$sub_sect.html";
        }

        else
        {
            return $path . "$sect/$sub_sect/$id.html";
        }
    }
    else
    {
        if($sub_sect == NULL && $id == NULL)
        {
            return $path . "index.php?section=$sect";
        }

        else if($id == NULL)
        {
            return $path . "index.php?section=$sect&page=$sub_sect";
        }

        else
        {
            return $path . "index.php?section=$sect&page=$sub_sect&id=$id";
        }
        return $path;
    }
}

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Get the section and page name form the URL and set
 * them to point to the home page if the URL variables
 * dont exsit
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
function get_site_index()
{
// get sect
    if(isset($_GET['section']))
    {
        $section = $_GET['section'];
    }
    else
    {
        $section = DEFAULT_PAGE;
    }

// get page
    if(isset($_GET['page']))
    {
        $page = $_GET['page'];
    }
    else
    {
        $page = NULL;
    }

// get id
    if(isset($_GET['id']))
    {
        $id = $_GET['id'];
    }
    else
    {
        $id = NULL;
    }

    return array(
        "sect" => $section,
        "page" => $page,
        "id"   => $id);
}

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Get the path of the CMS root
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
function get_current_path()
{
    $array = explode("/", $_SERVER['PHP_SELF']);
    array_shift($array);
    array_pop($array);

    $path = "/";

    foreach($array as $item)
    {
        $path .= $item . "/";
    }

    return $path;
}

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Replace spaces with underscores
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
function add_uscores($str)
{
    return str_replace(" ", "_", $str);
}

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Replace undersores with spaces
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
function strip_uscores($str)
{
    return str_replace("_", " ", $str);
}

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Create handler return array
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
function make_return($page_title, $content, $ajax = false)
{
    return array(
        'page_title' => $page_title,
        'content'    => $content,
        'ajax'       => $ajax);
}

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Abstraction around model instance creation.
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
function instance_model($model_name)
{
    return new $model_name();
}

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Abstraction around view instance creation.
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
function instance_view($view_name)
{
    return new view($view_name);
}

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Raise an error, instantly stops execution of the
 * application and displays an error message.
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
function raise_error($error)
{
    throw new exception($error);
}
