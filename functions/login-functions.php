<?php
/**
 * Generate the HTML markup of the form
 *
 * @since 1.0.0
 *
 * @param string $custom_redirect The URL where the user should be redirected after submitting the form.
 *
 * @return void Return early if the user is logged in.
 * @author WebsiteGuider
 **/
function custom_html_login_form( $custom_redirect ) {
    if( ! is_user_logged_in() ) {
        ?>
        <form method="post" class="custom-login-form">
        <style type="text/css">
            .custom-error {
                margin-bottom: 10px;
                padding: 10px;
                background-color: #d64646;
                color: white;
                border-radius: 5px;
            }
            .custom-error p {
                margin: 0;
            }
        </style>
            <p>
                <label for="custom-user"><?php _e('Username', 'custom-login-register'); ?></label>
                <input type="text" name="custom_user" id="custom-user" placeholder="Type Username or Email" />
            </p>
            <p>
                <label for="custom-pass"><?php _e('Password', 'custom-login-register'); ?></label>
                <input type="password" name="custom_pass" id="custom-pass" placeholder="Type the password" />
            </p>
            <p>
            <label for="remember-me">
                <input type="checkbox" name="remember_me" id="remember-me"/>
                <?php _e('Remember Me', 'custom-login-register'); ?>
            </label>
            </p>
            <p>
                <a href="<?php echo wp_lostpassword_url(); ?>"><?php _e('Lost Password', 'custom-login-register'); ?></a>
            </p>
            <p>
                <input type="hidden" name="custom_login_nonce" id="custom_login_nonce" value="<?php echo wp_create_nonce('custom-login-nonce'); ?>" />
                <input type="hidden" name="custom_redirection" value="<?php echo esc_url( $custom_redirect ); ?>"/>
                <input type="submit" name="submit_custom_login" value="<?php _e('Login', 'custom-login-register'); ?>" />    
            </p>
        </form>
        <?php
    } else {
        _e("You are logged in", 'custom-login-register');
    }
}
