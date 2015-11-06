<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="/theme/admin_style.css" />

    <?php // only load javascript when admin logged in
        if(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'): ?>
		<script type="text/javascript" src="/lib/jquery_ui/js/jquery-1.8.0.min.js"></script>
		<script type="text/javascript" src="/lib/jquery_ui/js/jquery-ui-1.8.23.custom.min.js"></script>

        
        <script type="text/javascript" src="/lib/tinymce_jquery/jscripts/tiny_mce/jquery.tinymce.js"></script>
   <?php endif; ?>
    </head>

    <body>
        <div id="container">
            <div id="header">
                <span>
                    <a href="/" target="_blank">View Site</a>
                    <a href="<?php echo make_url('user', 'logout') ?>">Log Out</a>
                </span>
            </div>

            <div id="main_content">
                <div id="center">
                    <?php echo $content; ?>
                </div>

                <div id="left">
                    <div class="navsect">
                        <p><a href="<?php echo make_url('admin') ?>">Dashboard</a></p>

                        <h3>Members</h3>
                        <p><a href="<?php echo make_url('members', 'admin_categories') ?>">Member Categories</a></p>
                        <p><a href="<?php echo make_url('members', 'admin_pages') ?>">Member Pages</a></p>

                        <h3>Content</h3>
                        <p><a href="<?php echo make_url('page', 'admin_pages') ?>">Static Pages</a></p>

                        <p><a href="<?php echo make_url('files', 'admin_files') ?>">Files</a></p>


                        <h3>Internal Settings</h3>
                        <p><a href="<?php echo make_url('navi', 'admin_navi') ?>">Navigation</a></p>
                        <p><a href="<?php echo make_url('user', 'edit') ?>">User Settings</a></p>
                    </div>
                </div>

                <div class="clear"></div>

                <div class="gap"></div>
            </div>
       </div>
   </body>
</html>
