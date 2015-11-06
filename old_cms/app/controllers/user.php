<?php
class ctrl_user extends controller_base 
{
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Setup
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function __CONSTRUCT ()
    {
        $this->load_outer_template('template');
        load_helper('errors');
        load_helper('navigation');
    }

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Allow a user to log in
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function login()
    {
        if(isset($_SESSION['active_user']))
            redirect_to(make_url('admin'));

        if(!isset($_POST['Submit']))
        {
        // display login form
            $view = instance_view('users/login');
            $view = $view->parse_to_variable(array());

            $this->set_template_paramiters(array(
                'content' => $view
            ));
        }
        else
        {
            try
            {
            // handle log in
                $user     = $_POST['user'];
                $password = $_POST['pass'];

                $usr = instance_model('users');
                $selected_user = $usr->verify_user($user, $password);

                if($selected_user == false)
                {
                    throw new exception();
                }
                else
                    log_in_user($selected_user[0]['User_name'],
                        $selected_user[0]['ID'], $selected_user[0]['Type']);
            }
            catch(redirecting_to $e)
            {
                throw $e;
            }
            catch(exception $e)
            {
                new_flash('Username or password is incorrect', 1);

            // display login form
                $view = instance_view('users/login');
                $view = $view->parse_to_variable(array());

                $this->set_template_paramiters(array(
                    'content' => $view
                ));
            }
        }
    }

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Allow a user to log out
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function logout()
    {
        $this->outer_template = '';
        $_SESSION = array();

        redirect_to('/');
    }

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Allow a user to register
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function register()
    {
        if(isset($_SESSION['active_user']))
            redirect_to(make_url('admin'));

        if(ALLOW_REGISTRATION == false)
            die('Registration is disabled.');

        if(!isset($_POST['Submit']))
        {
            $form_vals = make_reg_vals_array('', '', '', '');

        // display register form
            $view = instance_view('users/register');
            $view = $view->parse_to_variable(array(
                'form_vals' => $form_vals));

            $this->set_template_paramiters(array(
                'content' => $view
            ));
        }
        else
        {
        // reed the form
            $form_vals = array(
                'errs'       => array(),
                'name'       => $_POST['name'],
                'ppal_email' => $_POST['ppal_email'],
                'pass'       => $_POST['pass'],
                'pass_v'     => $_POST['pass_v']);

        // Instance users model
            $usr = instance_model('users');
            $test_exists = array();

        // Validate user name
            try
            {
                validate_username($form_vals['name']);
                $test_exists = $usr->get_user_by_name($form_vals['name']);

                if($test_exists != array())
                {
                    new_flash('User name is already tacken on this node', 1);
                    $form_vals['name'] = '';
                }
            }
            catch(exception $e)
            {
                if(strlen($form_vals['name']) < 3)
                {
                    new_flash('User name too short, min 3 charicters', 1);
                    $form_vals['name'] = '';
                }

                else if(strlen($form_vals['name']) > 30)
                {
                    new_flash('User name too long, max 30 charicters', 1);
                    $form_vals['name'] = '';
                }

                else if(!preg_match('/^[a-zA-Z0-9_]+$/', $form_vals['name']))
                {
                    new_flash('User names must contain only alphanumeric charicters and the underscore', 1);
                    $form_vals['name'] = '';
                }
            }

        // Validate email
            try
            {
                validate_email($form_vals['ppal_email']);
                $test_exists = $usr->get_user_by_email($form_vals['ppal_email']);

                if($test_exists != array())
                {
                    new_flash('Email address is already in use', 1);
                    $form_vals['ppal_email'] = '';
                }
            }
            catch(exception $e)
            {
                new_flash('Email address is invalid', 1);
            }

        // Validate passwords
            if(mb_strlen($form_vals['pass'], 'utf8') < 6)
                new_flash('Password too short, min 6 charicters', 1);

            else if(sha1($form_vals['pass']) != sha1($form_vals['pass_v']))
                new_flash('Passwords do not match', 1);

            if(count(get_errors()) == 0)
            {
            // Everything was valid, save, login and redirect
                $usr->new_user($form_vals['name'], $form_vals['ppal_email'], $form_vals['pass'], 'affiliate');

                $new_id = $usr->get_user_by_name($form_vals['name']);

                log_in_user($new_id[0]['User_name'], $new_id[0]['ID'], 'affiliate');
            }

        // else re-display the register form and show errors
            else
            {
                $view = instance_view("users/register");
                $view = $view->parse_to_variable(array(
                    'form_vals' => $form_vals));

                $this->set_template_paramiters(array(
                    'content' => $view
                ));
            }
        }
    }


/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Allow a user to change there settings
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function edit()
    {
        if(!(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'))
            redirect_to('/');

        $this->load_outer_template('admin');

        $usr = instance_model('users');

        if(!isset($_POST['Submit']))
        {
            $user = $usr->get_user_by_id($_SESSION['active_user']['id']);

            if($user == array())
                throw new exception("User does not exist");

            $form_vals = make_reg_vals_array('', $user[0]['Ppal_email'], '', '');

        // display user edit form
            $view = instance_view('users/edit');
            $view = $view->parse_to_variable(array(
                'form_vals' => $form_vals));

            $this->set_template_paramiters(array(
                'content' => $view
            ));
        }
        else
        {
        // reed the form
            $form_vals = array(
                'errs'       => array(),
                'ppal_email' => $_POST['ppal_email'],
                'oldpass'    => $_POST['oldpass'],
                'pass'       => $_POST['pass'],
                'pass_v'     => $_POST['pass_v']);

        // Instance users model
            $test_exists = array();

        // Validate email
            try
            {
                validate_email($form_vals['ppal_email']);
                $test_exists = $usr->get_user_by_email($form_vals['ppal_email']);

                if($test_exists != array() && $test_exists[0]['ID'] != $_SESSION['active_user']['id'])
                {
                    new_flash('Email address is already in use', 1);
                    $form_vals['ppal_email'] = '';
                }
            }
            catch(exception $e)
            {
                    new_flash('Email address is invalid', 1);
            }

        // Validate passwords
            if($form_vals['oldpass'] != '')
            {
                try {
                    $selected_user = $usr->verify_user($_SESSION['active_user']['name'], $form_vals['oldpass']);

                    if($selected_user == false)
                        throw new exception();

                    if(mb_strlen($form_vals['pass'], 'utf8') < 6)
                        new_flash('Password too short, min 6 charicters', 1);

                    else if(sha1($form_vals['pass']) != sha1($form_vals['pass_v']))
                        new_flash('Passwords do not match', 1);
                }
                catch(redirecting_to $e)
                {
                    throw $e;
                }
                catch(exception $e)
                {
                    new_flash('Username or password is incorrect', 1);
                }
            }

            if(count(get_errors()) == 0)
            {
            // Everything was valid, save, login and redirect
                $usr->update_user_email($_SESSION['active_user']['id'], $form_vals['ppal_email']);

                if($form_vals['oldpass'])
                {
                    $usr->update_password($_SESSION['active_user']['id'], $form_vals['pass']);
                }

                new_flash("Settings updated", 1);
            }

        // else re-display the register form and show errors
            //else
            //{
                $view = instance_view("users/edit");
                $view = $view->parse_to_variable(array(
                    'form_vals' => $form_vals));

                $this->set_template_paramiters(array(
                    'content' => $view
                ));
            //}
        }
    }
}
