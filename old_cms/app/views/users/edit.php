<div id="full_width_box">
    <h2>User Settings</h2>

    <div class="gap"></div>

    <?php display_errors(); ?>

    <table class="register">
        <form action="<?php echo esc(make_url("user", "edit")); ?>" method="post">
            <tr>
            <td><input name="ppal_email"  type="text" value="<?php echo hen($form_vals['ppal_email']); ?>" /></td>
                <td>Email</td>
            </tr>

            <tr>
                <td><input name="oldpass"   type="password" /></td>
                <td>Old Password (leave black to keep)</td>
            </tr>

            <tr>
                <td><input name="pass"   type="password" /></td>
                <td>New Password</td>
            </tr>

            <tr>
                <td><input name="pass_v"   type="password" /></td>
                <td>Retype New Password</td>
            </tr>

            <tr>
                <td  colspan="2"><input class="submit" type="Submit" name="Submit" value="Save" /></td>
            </tr>
        </form>
    </table>

</div>
