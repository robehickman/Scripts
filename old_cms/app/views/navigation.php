<ul>
    <?php foreach($navi as $row): ?>
    <li><a href="<?php echo hen($row['url']) ?>"><?php echo hen($row['title']) ?></a></li>
    <?php endforeach ?>
</ul>
