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

class view
{
    var $str;

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
* Load in template file and expand macros into PHP
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function __CONSTRUCT($tplname, $tplpath = "app/views/")
    {
        $filename =  $tplpath . $tplname . ".php"; 

        if (!file_exists($filename))
            die("View $tplname.php does not exist.");

        $fh = fopen($filename, 'r');
        $this->str = fread($fh, filesize($filename));
        fclose($fh);

    // Add PHP close tag to exit PHP mode 
        $this->str = "?>" . $this->str;
    }

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
* Display the main template
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function parse($array = array())
    {
        extract($array);
        eval($this->str);
    }

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Generate HTML for post editing form
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function parse_to_variable($array = array())
    {
        extract($array);

        ob_start();
        eval($this->str);
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }
}
