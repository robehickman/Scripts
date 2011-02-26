<?php foreach($navigation as $item): ?>

<div class="navsect">
<h4><a href="<?php echo $item['url']; ?>"><?php echo $item['title'] ?></a></h4>
</div>
<?php endforeach; ?>
