<div id="full_width_box">
    <h2>Affiliate Registration</h2>

    <div class="gap"></div>

    <?php display_errors(); ?>

    <table class="register">
        <form action="<?php echo esc(make_url("user", "register")); ?>" method="post">
            <tr>
            <td><input name="name"  type="text" value="<?php echo hen($form_vals['name']); ?>" /></td>
                <td>User name</td>
            </tr>

            <tr>
            <td><input name="ppal_email"  type="text" value="<?php echo hen($form_vals['ppal_email']); ?>" /></td>
                <td>PayPal Email</td>
            </tr>

            <tr>
                <td><input name="pass"   type="password" /></td>
                <td>Password</td>
            </tr>

            <tr>
                <td><input name="pass_v"   type="password" /></td>
                <td>Retype password</td>
            </tr>

            <tr>
                <td class="submit" colspan="2"><input type="Submit" name="Submit" value="Register" /></td>
            </tr>
        </form>
    </table>
</div>
