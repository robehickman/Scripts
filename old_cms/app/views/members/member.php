<h2><?php print hen($member[0]['Title']) ?></h2>

<?php
    if($member[0]['Image'] != '')
        $image = '/res/profiles/' . hen($member[0]['Image']);
    else 
        $image = '/res/static/no_profile.jpg';
        ?>

<img src="<?php print $image ?>" 
    alt="<?php print hen($member[0]['Title']) ?>'s profile image" 
    class="member_profile_image" />

<?php print $member[0]['Content'] ?>


<?php if($gallery != array()): ?>
<div class="category_grid">
    <div class="gap"></div>

    <h3>Gallery</h3>

<script type="text/javascript">
$(document).ready(function() {
    $('.category_grid a').lightBox();
})
</script>

<?php foreach($gallery as $image): ?>
    <div class="category_box">
        <a href="<?php echo make_url('res', 'gallery', $member[0]['Clean_title'], $image['File']) ?>">
        <img src="<?php echo make_url('res', 'gallery', $member[0]['Clean_title'], 'thumbs', $image['File']) ?>" alt="<?php hen($image['File']) ?>" />
        </a>
    </div>
<?php endforeach; ?>
</div>
<?php endif; ?>

