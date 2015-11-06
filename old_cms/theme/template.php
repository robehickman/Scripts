<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
    if(!isset($description))
        $description = "Page Title";

    $thetitle = "";
    if(isset($title))
        $thetitle = $title . " | " . $thetitle;
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="Description" content="<?php echo hen($description) ?>" />

        <title><?php echo hen($thetitle) ?></title>


        <link type="text/css" href="/theme/style.css" rel="stylesheet" />
		<link type="text/css" href="/lib/jquery_ui/css/ui-lightness/jquery-ui-1.8.23.custom.css" rel="stylesheet" />


		<script type="text/javascript" src="/lib/jquery_ui/js/jquery-1.8.0.min.js"></script>
        <script type="text/javascript" src="/lib/jquery_lightbox/js/jquery.lightbox-0.5.js"></script>
        <link rel="stylesheet" type="text/css" href="/lib/jquery_lightbox/css/jquery.lightbox-0.5.css"
            media="screen" />

        <?php // only load javascript when admin logged in
        if(isset($_SESSION['active_user']) && $_SESSION['active_user']['type'] == 'admin'): ?>
		<script type="text/javascript" src="/lib/jquery_ui/js/jquery-ui-1.8.23.custom.min.js"></script>
        <script type="text/javascript" src="/lib/tinymce_jquery/jscripts/tiny_mce/jquery.tinymce.js"></script>
        <?php endif; ?>

        <script type="text/javascript">
          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', 'UA-34855806-1']);
          _gaq.push(['_trackPageview']);

          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') +
                '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();
        </script>
    </head>

    <body>
        <div id="container">
            <div id="header">
                <div id="titles">
                    <h1><?php echo hen($title) ?></h1>

                    <h2><?php echo hen($description) ?></h2>
                </div>
            </div>

            <div id="inner_container">
                <div id="left">
                    <?php
                        display_navigation();
                    ?>
                </div>

                <div id="right">
                    <div class="page_top"></div>
                    <div class="page_mid">

                        <div class="content_container">
                            <?php echo $content; ?>
                        </div>
                    </div>
                    <div class="page_bot"></div>
                </div>

                <div class="clear"></div>
            </div>
        </div>
    </body>
</html>
