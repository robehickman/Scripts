<?php
/*
 * Copyright 2010 Robert Hickman
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
 * implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

// Includes
    include "src/common.php";
    include "src/dispatcher.php";
    include "src/database.php";
    include "src/controller.php";
    include "src/view.php";

// Load config file
    include "config.php";

// Setup
    session_start();
    header('Content-Type: text/html; charset=UTF-8');
    mb_internal_encoding("UTF-8");

    mysql_connect(HOST, USERNAME, PASSWORD)or
        die("cannot connect");
    mysql_select_db(DB_NAME)or die("cannot select DB");


// Run the dispatcher
    try
    {
        dispatcher();
    }
    catch(exception $e)
    {
        if(APP_MODE == 'test')
            throw $e;
        else
            die('Something went wrong');
    }
