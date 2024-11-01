<?php if (is_user_logged_in()) { ?>
    <script>
        window.location.replace('my-account');
    </script>
<?php      
}else{ 
    global $wpdb;
    $table_otp_login_register_settings = $wpdb->prefix . "sslcare_otp_login_register_settings";
    $checkData = $wpdb->get_row( "SELECT * FROM $table_otp_login_register_settings WHERE id =1");    
    if($checkData->enable_otp == 'Yes'){
?>    

<link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ ) ?>asset/css/custom.css?v=1.9">
		
<script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__ ) ?>asset/jquery-validator/1.13.1/js/jquery.validate.js"></script>

<script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__ ) ?>asset/jquery-validator/1.13.1/js/additional-methods.js"></script>

<div class="wrapper-custom-rz">
    <div class="overlay-custom"></div>
    <!---Popup-->
    <div class="modal-custom success-otp">
        <div class="modal-wrapper">
            <div class="modal-header">
                <button class="modal-close success-ok">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 close-custom-otp"> <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /> </svg>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="modal-heading"></h5>
                <div class="modal-content">
                    <button type="submit" class="success-ok">OK</button>
                </div>
            </div>
        </div>	
    </div>
    <script>
        jQuery(document).on('click', '.success-ok', function(){
            location.reload();
        });
    </script>
    <!---Popup Ends-->
    <div class="each-column">
        <h2 class="title">Login</h2>
        <div class="login_type_section">
            <label>
                <input type="radio" name="sslcare_login_type" value="login_with_otp" checked>
                Login with OTP
            </label>

            <label>
                <input type="radio" name="sslcare_login_type" value="login_with_username">
                Login with Username/Email
            </label>
        </div>
        <form class="sslcare-otp-login-form-custom-rz">
            <div class="form-item">
                <label for="sslcare_phone_login"><?php _e( 'Phone', 'woocommerce' ); ?><span class="mendatory">*</span></label>
                <input type="text" class="input-text" name="sslcare_phone_login" id="sslcare_phone_login" value="" />
            </div>
            <div class="form-item">
                <label>Captcha<span class="mendatory">*</span></label>
                <fieldset>
                    <span id="SuccessMessage2" class="success">Success! you have entered the correct code.</span>
                    <input class="captcha_custom" type="text" id="UserCaptchaCode2" class="CaptchaTxtField" name="captcha" placeholder='Please insert correct captcha shown below'>
                    <span id="WrongCaptchaError2" class="error"></span>
                    <div class='CaptchaWrap' style="margin-top:15px;">
                    <div id="CaptchaImageCode2" class="CaptchaTxtField">
                        <canvas id="CapCode2" class="capcode" width="300" height="80"></canvas>
                    </div> 
                    <input type="button" class="ReloadBtn" onclick='CreateCaptcha2();'>
                    </div>
                </fieldset>
            </div>
            <button type="submit" class="woocommerce-button button sslcare-otp-login-form-custom-rz__submit" name="login" value="Log in">Log in</button>
        </form>
        <div id="sslcare-custom-login-container">
            <form id="custom-login-form">
                <div class="form-item">
                    <label for="username_sslcare">Username/Email:</label>
                    <input type="text" name="username_sslcare" id="username_sslcare" />
                </div>
                <div class="form-item">
                    <label for="password_sslcare">Password:</label>
                    <input type="password" name="password_sslcare" id="password_sslcare" />
                </div>
                <div id="custom-login-message"></div>
                <button type="submit" id="custom-login-btn">Log In</button>
                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="forgot-password sslcare-forgot-password">Forgot Password?</a>
            </form>
        </div>
        <div class="clear"></div>
    </div>
    <div class="each-column">
        <h2 class="title">Register</h2>

        <form class="sslcare-otp-register-form-custom-rz">
            <div class="form-item">
                <label for="sslcare_phone"><?php _e( 'Phone', 'woocommerce' ); ?><span class="mendatory">*</span></label>
                <input type="text" class="input-text" name="sslcare_phone" id="sslcare_phone" value="" />
        
            </div>

            <div class="form-item">
                <label>Captcha<span class="mendatory">*</span></label>
                <fieldset>
                    <span id="SuccessMessage" class="success">Success! you have entered the correct code.</span>
                    <input class="captcha_custom" type="text" id="UserCaptchaCode" class="CaptchaTxtField" name="captcha" placeholder='Please insert correct captcha shown below'>
                    <span id="WrongCaptchaError" class="error"></span>
                    <div class='CaptchaWrap'>
                    <div id="CaptchaImageCode" class="CaptchaTxtField">
                        <canvas id="CapCode" class="capcode" width="300" height="80"></canvas>
                    </div> 
                    <input type="button" class="ReloadBtn" onclick='CreateCaptcha();'>
                    </div>
                </fieldset>
            </div>

            <div class="form-item">
                <label for="reg_email">Email address&nbsp;<span class="required" aria-required="true">*</span></label>
                <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="">			
            </div>
            <div class="form-item">
                <label for="reg_password_sslcare">Password&nbsp;<span class="required" aria-required="true">*</span></label>
                <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="reg_password_sslcare" id="reg_password_sslcare" value="">			
            </div>
            <button type="submit" class="woocommerce-Button woocommerce-button button sslcare-otp-register-form-custom-rz__submit" name="register" value="Register">Register</button>
        </form>
    </div>
</div>
<?php }else{ ?>
    <script>
        window.location.replace('my-account');
    </script>
<?php } } ?>