<a class="admin_back_link" href="<?php echo hen($back_url) ?>">&lt;&lt; Back</a>

<h2><?php echo hen($title) ?></h2>

<div class="gap_s"></div>

<p><?php echo $msg ?></p>

<form action="<?php echo hen($form_url) ?>" method="POST" >
    <input type="hidden" name="item" value="<?php echo hen($item) ?>" />
    <input class="submit_inl" type="submit" name="Submit" value="Delete" />
    <input class="submit_inl" type="submit" name="Submit" value="Cancel" />
</form>
