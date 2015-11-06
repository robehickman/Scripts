<a class="admin_back_link" href="<?php echo hen($back_url) ?>">&lt;&lt; Back</a>

<h2><?php echo hen($title) ?></h2>

<?php display_errors(); ?>

<form action="<?php echo hen($form_url) ?>" method="POST" >
    <?php
        if(isset($form_label) && $form_label != '')
            $disp = $form_label;
        else
            $disp = 'Title';
    ?>

    <label><?php echo hen($disp); ?></label>
    <input type="text" name="title" value="<?php echo hen($form_value) ?>" />

    <input type="submit" name="Submit" value="Create" class="submit" />
</form>
