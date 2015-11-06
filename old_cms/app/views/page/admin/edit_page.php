<a class="admin_back_link" href="<?php echo hen($back_url) ?>">&lt;&lt; Back</a>

<h2>Edit Page</h2>

<?php display_errors(); ?>

<script type="text/javascript" src="/lib/tinymce_jquery/load_tinymce.js"></script>

<form action="<?php echo hen($form_url) ?>" method="post" />
    <label>Title</label>

    <input type="hidden" name="old_title" value="<?php echo hen($title) ?>" />
    <input type="text" name="title" value="<?php echo hen($title) ?>" />

    <label>Content</label>

    <div>
    <textarea name="content" class="tinymce" rows="20" cols="60"><?php echo hen($content) ?></textarea>
    </div>

    <input class="submit" type="Submit" name="Submit" value="Save" />
</form>
