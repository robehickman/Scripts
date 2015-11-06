<script type="text/javascript">
$(document).ready(function(){

    function update_display(send)
    { 
        var type = $('#navi_type_select').val() 

        var data = ''
        if(send == 1)
            var data = $('#navi_option_box').html() 

        $.post(
            '<?php echo $ajax_url ?>',
            {
                data: data,
                type: type
            },
            function(data)
            {
                $('#navi_option_box').html(data)
            }
        )
    }

    $('#navi_type_select').change(function(){
        update_display(0)
    })

    update_display(1)
})
</script>

<a class="admin_back_link" href="<?php echo hen($back_url) ?>">&lt;&lt; Back</a>

<h2><?php echo hen($page_title) ?></h2>

<?php display_errors() ?>

<form action="<?php echo hen($form_url) ?>" enctype="multipart/form-data" method="POST">
    <label>Link title</label>

    <input type="text" name="title" value="<?php echo $title ?>" />

    <label>Link type</label>

    <select id="navi_type_select" style="width: 100px" name="type">
        <?php foreach($types as $tyk => $tyv): ?>
            <?php if($type == $tyk): ?>
            <option value="<?php echo hen($tyk) ?>" selected="Selected"><?php echo hen($tyv) ?></option>
            <?php else: ?>
            <option value="<?php echo hen($tyk) ?>"><?php echo hen($tyv) ?></option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>

    <div id="navi_option_box">
        <?php echo hen($data) ?>
    </div>

    <br />

    <input class="submit" type="Submit" name="Submit" value="Save" />

</form>

