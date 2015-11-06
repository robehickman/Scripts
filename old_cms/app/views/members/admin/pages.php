<script type="text/javascript">
$(document).ready(function(){
    $('table tbody').sortable()
    $('table tbody').disableSelection()
})
</script>

<a href="<?php echo make_url('admin') ?>" class="admin_back_link">&lt;&lt; Back</a>

<h2 class="admin_h2">Categories</h2>

<a href="<?php echo make_url('members', 'admin_pages_create'); ?>" class="admin_button">New Member</a>

<div class="gap"></div>

<table class="admin_table">
    <tr>
        <th>Name</th>
        <th>Options</th>
    </tr>

    <tbody>
        <?php foreach($categories as $category): ?>
        <tr>
            <td><?php echo $category['Name'] ?></td>
            <td class="narrow">
                <a href="<?php echo make_url('members', 'admin_categories_edit', $category['ID']); ?>">Edit</a>
                <a href="<?php echo make_url('members', 'admin_categories_delete', $category['ID']); ?>">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
