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
    catch(redirecting_to $e){die;}
    catch(e_404 $e) {
        handle_error($e);
    }
    catch(exception $e)
    {
        handle_error($e);
    }


// Error handler
function handle_error($e)
{
    if(APP_MODE == 'test')
        throw $e;
    else
    {
    // Log the error with transaction id if avalable
        $type  = get_class($e);
        $trace = print_r($e->getTrace(), true);
        $msg = $e->getMessage();

        $pay_id = 'n/a';
        if(isset($_SESSION['payment_id']))
            $pay_id = $_SESSION['payment_id'];

        if($type == 'e_404')
        {
            $error = instance_view('404', 'theme/');
            $error = $error->parse_to_variable(array()); 
        }
        else
        {
            try {
                $model = instance_model('error_log');
                $code = $model->create($type, $msg, $trace, $pay_id);
            } catch(exception $e) {
                die();
            }

            $error = instance_view('server_error', 'theme/');
            $error = $error->parse_to_variable(array(
                'code' => $code
            )); 
        }

        $outer_template = instance_view('template', 'theme/');
        $outer_template->parse(array(
            'content' => $error
        ));
    }
}
