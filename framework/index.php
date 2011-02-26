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

/*++++++++++++++++++++ Common setings +++++++++++++++++++++*/
// Default (index) controller
    define("DEFAULT_PAGE",  "Hello World");

// use mod_rewrite for pritty(search engine frendly) url's
    define("USE_REWRITE",   true);


/*++++++++++++++++ Database configuration +++++++++++++++++*/
// Host name
    define("HOST",         "localhost");       

// Database username
    define("USERNAME",     "");             

// Database password
    define("PASSWORD",     ""); 

// Database name
    define("DB_NAME",      "");        

/*++++++++++++++++++ End of configuration +++++++++++++++++*/
    include "src/framework.php";
    run_framework();
