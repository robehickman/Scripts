<a class="admin_back_link" href="<?php echo hen($back_url) ?>">&lt;&lt; Back</a>

<h2>Edit Category Listing</h2>

<?php display_errors(); ?>

<form action="<?php echo hen($form_url) ?>" enctype="multipart/form-data" method="post" />
    <label>Title</label>

    <input type="hidden" name="old_title" value="<?php echo hen($otitle) ?>" />
    <input type="text" name="title" value="<?php echo hen($title) ?>" />

    <label>In Category</label>

    <select name="category">
        <option value="0">Uncategorised</option>

        <?php foreach($categories as $category): ?>
            <?php if($member_category == $category['ID']): ?> 
            <option value="<?php echo hen($category['ID']) ?>" Selected="Selected"><?php echo hen($category['Name']) ?></option>
            <?php else: ?>
            <option value="<?php echo hen($category['ID']) ?>"><?php echo hen($category['Name']) ?></option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>

    <input class="submit" type="Submit" name="Submit" value="Save" />
</form>
