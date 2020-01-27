<a class="admin_back_link" href="<?php echo $back_url ?>">&lt;&lt; Back</a>

<h2><?php echo hen($title) ?></h2>

<?php display_errors() ?>

<form action="<?php echo $form_url ?>" enctype="multipart/form-data" method="POST">
    <label>Name</label>
    <input type="text" name="title" value="<?php echo hen($title) ?>" />
    <input type="hidden" name="old_title" value="<?php echo hen($title) ?>" />

    <label>Image</label>

<?php
    if($image != '')
        $image = '/res/categories/' . hen($image);
    else 
        $image = '/res/static/no_category.jpg';
?>


    <table>
        <tr>
            <td>
                <img src="<?php echo hen($image) ?>" alt="Current Image" />
            </td>

            <td>
                <p>Upload new image:</p>
                <input type="file" name="file" />
            </td>
        </tr>
    </table>
    
    <br />

    <input class="submit" type="Submit" name="Submit" value="Save" />

</form>

