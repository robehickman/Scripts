<div id="full_width_box">
    <h2>Sign in</h2>

    <div class="gap"></div>

    <?php display_errors(); ?>

    <table class="register">
        <form action="<?php echo make_url("user", "login"); ?>" method="post" id="login">
            <tr>
                <td><input name="user" type="text" /></td>
                <td>Username</td>
            </tr>

            <tr>
                <td><input name="pass" type="password" /></td>
                <td>Password</td>
            </tr>

            <tr>
                <td class="submit" colspan="2"><input type="Submit" name="Submit" value="Sign in" /></td>
            </tr>
        </form>
    </table>

</div>
