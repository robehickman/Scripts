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
 * Database base class
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
class database 
{
/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Convert a mysql resource into an array
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function sql_to_array($sql_result)
    {
        $array = array();

        if($sql_result != NULL)
        {
            while($row = mysql_fetch_assoc($sql_result))
            {
                array_push($array, $row);
            }
        }

        return $array;
    }

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Variable argument function, takes an SQL query string
 * containing the `@v' character pair and substitutes
 * eatch occourance with the next avalable argument.
 * Arguments are automaticaly escaped with
 * mysql_real_escape_string.
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function query($query)
    {
    // split query
        $str_array = str_split($query);

    // Get arguments and create an arg counter
        $args = func_get_args();
        $arg = 1;

        $query = "";

        for($i = 0; $i < count($str_array); $i ++)
        {
            if($str_array[$i] == '@' &&
                $str_array[$i + 1] == 'v')
            {
                if($arg <= count($args) - 1)
                {
                    $query .= mysql_real_escape_string($args[$arg]);
                    $arg ++;
                    $i ++; // skip `v' charicter
                }
                else
                {
                    die("To few arguments for query\n");
                }
            }
            else
                $query .= $str_array[$i];
        }

        return mysql_query($query);
    }
}
