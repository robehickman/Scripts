<a class="admin_back_link" href="<?php echo $back_url ?>">&lt;&lt; Back</a>

<h2>Edit Member Page</h2>

<?php display_errors(); ?>

<script type="text/javascript" src="/lib/tinymce_jquery/load_tinymce.js"></script>

<form action="<?php echo $form_url ?>" enctype="multipart/form-data" method="post" />
    <label>Title</label>

    <input type="hidden" name="old_title" value="<?php echo hen($otitle) ?>" />
    <input type="text" name="title" value="<?php echo hen($title) ?>" />

    <label>Content</label>

    <div>
    <textarea name="content" class="tinymce" rows="20" cols="60"><?php echo hen($content) ?></textarea>
    </div>

    <label>Profile Image</label>

    <?php
        if($image != '')
            $image = '/res/profiles/' . hen($image);
        else 
            $image = '/res/static/no_profile.jpg';
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


    <input class="submit" type="Submit" name="Submit" value="Save" />
</form>

