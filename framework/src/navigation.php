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
 * Class for navigation
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
class navigation
{
    var $nav;

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Constructor
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function __CONSTRUCT()
    {
        $this->nav = array(); 
    }

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Add an item to the navigation
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function add_item($title, $url)
    {
        array_push($this->nav, array(
            'title' => $title,
            'url'   => $url));
    }

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Display the navigation
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function display()
    {
        $nav = new view("navigation", "theme/");
        return $nav->parse_to_variable(array(
            "navigation" => $this->nav));
    }
}
