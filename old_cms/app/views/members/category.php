<h2><?php print hen($category_title); ?></h2>

<div class="category_page">
<?php foreach($category as $item): ?>
    <div class="member_block">
        <?php $url = make_url('members', 'member', $item['Clean_title']) ?>
        <h3><a href="<?php echo  $url ?>" class="member_title">
            <?php echo hen($item['Title']) ?>
        </a></h3>

        <?php
            if($item['Image'] != '')
                $image = '/res/profiles/' . hen($item['Image']);
            else 
                $image = '/res/static/no_profile.jpg';
        ?>

        <a href='<?php echo  $url ?>'>
            <img src="<?php print $image ?>" 
                alt="<?php print hen($item['Title']) ?>'s profile image" 
                class="cat_profile_image" />
        </a>

        <p class="block_content">
            <?php print strip_content($item['Content']);  ?>
            <a href='<?php echo  $url ?>'> Read More &gt;&gt;</a>
        </p>

        <?php if(count($item['Gallery']) > 0): ?>
        <div class="category_grid">
        <?php foreach($item['Gallery'] as $image): ?>
            <div class="category_box">
                <a href='<?php echo  $url ?>'>
                <img src="<?php echo make_url('res', 'gallery', $item['Clean_title'], 'thumbs', $image['File']) ?>" alt="<?php hen($image['File']) ?>" />
                </a>
            </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>
