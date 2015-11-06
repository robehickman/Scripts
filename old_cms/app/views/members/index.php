<h2><?php echo hen($page[0]['Title']) ?></h2>

<?php echo $page[0]['Content'] ?>

<div class="category_grid">
<?php foreach($categories as $category): ?>
    <div class="category_box">
        <?php
            $image = '/res/static/no_category.jpg';
            if($category['Image'])
                $image = make_url('res', 'categories', $category['Image']);
        ?>

        <p><a href="<?php echo make_url('members', 'category', $category['Clean_name']); ?>" >
            <?php echo hen($category['Name']) ?>
        </a></p>
        <a href="<?php echo make_url('members', 'category', $category['Clean_name']); ?>" >
            <img src="<?php echo $image ?>" alt="" />
        </a>
    </div>
<?php endforeach; ?>
</div>
