$().ready(function() {
    $('textarea.tinymce').tinymce({
        // Location of TinyMCE script
        script_url : '/lib/tinymce_jquery/jscripts/tiny_mce/tiny_mce.js',

        // General options
        theme : "advanced",
        plugins : "autolink,lists,pagebreak,style,table,advhr,advimage,advlink,inlinepopups,media,contextmenu,paste,directionality,visualchars,xhtmlxtras",

        // Theme options
        theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,tablecontrols,",
        theme_advanced_buttons2 : "bullist,numlist,|blockquote,|,link,unlink,image,media,|,hr,|code,",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "none",
        theme_advanced_resizing : true,

        // Example content CSS (should be your site CSS)
        content_css : "/lib/tinymce_jquery/examples/css/content.css",

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "lists/template_list.js",
        external_link_list_url : "lists/link_list.js",
        external_image_list_url : "lists/image_list.js",
        media_external_list_url : "lists/media_list.js",

        // URLs
        convert_urls : false,
    });
});
