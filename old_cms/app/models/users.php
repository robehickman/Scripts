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

class mdl_users extends database
{
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Create a new user
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function new_user($user, $email, $password, $type)
    {
        validate_username($user);
        validate_password($password);

        $salt = sha1(time());

        $hashed_pass = sha1($salt . $password);

        $query = "INSERT INTO `users`
            (`User_name`, `Password`, `Salt`, `Type`, `Ppal_email`)
            VALUES ('@v','@v','@v', '@v', '@v')";

        $this->query($query, $user, $hashed_pass, $salt, $type, $email);
    }

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Varify user credentials against the users in the database
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function verify_user($user, $password)
    {
        $selected_user = $this->get_user_by_name($user);

        if($selected_user == array())
            return false;

        $salt = $selected_user[0]['Salt'];

        $hashed_pass = sha1($salt . $password);

        if($hashed_pass == $selected_user[0]['Password'])
            return $selected_user;
        else
            return false;
    }

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Varify that a user ID is valid
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function verify_user_id($id)
    {
        $result = $this->get_user_by_id($id);
        if($result == array())
            throw new no_such_user_exception($id);
    }

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Update a users settings
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function update_user($id, $email, $full_name, $location, $web, $bio)
    {
        $this->verify_user_id($id);
        validate_email($email);
        validate_50($full_name);
        validate_50($location);
        validate_url($web);
        validate_bio($bio);

        $query = "UPDATE `users` SET
            `E-mail` = '@v',
            `Full_name` = '@v',
            `Location` = '@v',
            `Web` = '@v',
            `Bio` = '@v'
            WHERE `ID` = '@v' LIMIT 1";

        $this->query($query, $email, $full_name, $location, $web, $bio, $id);
    }

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Update a users password
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function update_password($id, $new_password)
    {
        $this->verify_user_id($id);
        validate_password($new_password);

        $salt = sha1(time());
        $hashed_pass = sha1($salt . $new_password);

        $query = "UPDATE `users` SET
            `Password` = '@v',
            `Salt`     = '@v'
            WHERE `ID` = '@v' LIMIT 1";

        $this->query($query, $hashed_pass, $salt, $id);
    }

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Update a users avatar
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function update_avatar($id, $new_avatar)
    {
        $this->verify_user_id($id);
        validate_avatar($new_avatar);

        $query = "UPDATE `users` SET
            `Avatar` = '@v'
            WHERE `ID` = '@v' LIMIT 1";

        $this->query($query, $new_avatar, $id);
    }

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Get all of the users in the database
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function get_users()
    {
        $query = "SELECT * FROM `users` ORDER BY `ID`";
        return $this->decode_keys(
            $this->sql_to_array($this->query($query)));
    }

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Get a spasific user by username
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function get_user_by_name($user)
    {
        $query = "SELECT * FROM `users` WHERE `User_name` = '@v'";
        return $this->decode_keys(
            $this->sql_to_array($this->query($query, $user)));
    }

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Get user by email
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function get_user_by_email($email)
    {
        $query = "SELECT * FROM `users` WHERE `Ppal_email` = '@v'";
        return $this->decode_keys(
            $this->sql_to_array($this->query($query, $email)));
    }

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Get a spasific user by password
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function get_user_by_id($id)
    {
        $query = "SELECT * FROM `users` WHERE `ID` = '@v'";
        return $this->decode_keys(
            $this->sql_to_array($this->query($query, $id)));
    }

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Update a users email address
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function update_user_email($user_id, $new_email)
    {
        $query = "update `users` set Ppal_email = '@v' where `ID` = '@v'";
        $this->query($query, $new_email, $user_id);
    }

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Decode the public and private keys which are encoded with base64
 * in the db
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    private function decode_keys($result_array)
    {
        return $result_array;
    }
}

// Exceptions
class no_such_user_exception extends exception { }
