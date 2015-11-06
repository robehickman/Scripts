<label>Page</label>

<select id="navi_type_select" name="data">
    <?php foreach($pages as $page):
        if($page['Internal'] != 1): ?>
            <?php if($page['ID'] == $data): ?>
            <option value="<?php echo hen($page['ID']) ?>" selected="Selected">
                <?php echo hen($page['Title']) ?>
            </option>
            <?php else: ?>
            <option value="<?php echo hen($page['ID']) ?>">
                <?php echo hen($page['Title']) ?>
            </option>
            <?php endif; ?>
    <?php endif; endforeach; ?>
</select>
