<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <title>MVC Framework</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="<?php echo $path; ?>theme/style.css" />
    </head>

    <body>
        <div id="container">
            <div id="header">
                MVC Framework
            </div>

            <div id="main_content">
                <div id="center">
                    <span class="pages_head">
                        <?php echo $page_title ?>
                    </span>

                    <?php echo $content; ?>
                </div>

                <div id="left">
                    <?php echo $navigation; ?>
                </div>

                <div class="clear"></div>
            </div>
       </div>
   </body>
</html>
