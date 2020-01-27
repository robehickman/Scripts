<?php if($sortable == true): ?>
<script type="text/javascript">
$(document).ready(function(){
    $('table tbody').sortable({
			update: function(event, ui) {
				var sortOrder = $(this).sortable('toArray').toString()
                $.post('<?php echo $sort_url ?>', {SortOrder: sortOrder})
			}
		});

    $('table tbody').disableSelection()
})
</script>
<?php endif; ?>

<?php if(isset($back_url)): ?>
<a href="<?php echo $back_url ?>" class="admin_back_link">&lt;&lt; Back</a>
<?php endif; ?>

<h2 class="admin_h2"><?php echo hen($title) ?></h2>

<div class="gap_s"></div>

<a href="<?php echo $new_url ?>" class="admin_button"><?php echo hen($new_name) ?></a>

<?php if($sortable == true): ?>
<p>Hint: drag and drop rows to change the sort order</p>
<?php endif; ?>


<div class="gap"></div>

    <table class="admin_table">
        <tr>


            <th class="<?php echo $col_classes[0] ?>">
                <?php if(isset($name_col) && $name_col != ''): ?>
                    <?php echo hen($name_col) ?>
                <?php elseif(isset($image_mode) && $image_mode == true): ?>
                Image
                <?php else: ?>
                Name
                <?php endif; ?> 
            </th>

            <?php if(isset($extra_cols)): foreach($extra_cols as $col): ?>
            <th >
                <?php echo hen($col) ?>
            </th>
            <?php endforeach; endif; ?>
            <th class="<?php echo (isset($col_classes)) ? $col_classes[1] : 'narrow'; ?>">Options</th>
        </tr>

        <tbody>
            <?php foreach($list as $item):
            $id = 0; if(isset($item['ID'])) $id = $item['ID']?>
            <tr id="<?php echo hen($id) ?>">
                <td>
                    <?php if((isset($image_mode) && $image_mode == true) ||
                             (isset($item['type']) && $item['type'] == 'image')): ?>
                        <img src="<?php echo hen($item['name']) ?>"
                            alt="<?php echo hen($item['name']) ?>" />
                    <?php else: 
                        echo hen($item['name']);
                        endif;
                    ?>
                </td>

                <?php if(isset($extra_cols)): foreach($extra_cols as $col => $nm): ?>
                <td class="<?php echo $item['extra_cols'][$col][1]; ?>">
                    <?php echo $item['extra_cols'][$col][0]; ?>
                </td>
                <?php endforeach; endif; ?>

                <td>
                    <?php foreach($item['options'] as $lname => $lurl): ?>
                        <a href="<?php echo hen($lurl) ?>" title="test"><?php echo $lname ?></a>
                    <?php endforeach; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
