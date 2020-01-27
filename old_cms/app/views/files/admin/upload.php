<a class="admin_back_link" href="<?php echo hen($back_url) ?>">&lt;&lt; Back</a>

<h2><?php echo hen($title) ?></h2>

<?php display_errors() ?>

<form action="<?php echo hen($form_url) ?>" enctype="multipart/form-data" method="POST">
    <label>File</label>

    <input type="file" name="file" />

    <br />

    <input class="submit" type="Submit" name="Submit" value="Upload" />

</form>
