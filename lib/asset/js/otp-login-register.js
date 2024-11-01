var phoneNumber;
var cd;
var emailStored;
var passStored;
var captchaData;
var captchaData2;
// Access the template directory URL
var pluginDirectoryURL = ajax_otp_login_object.plugin_directory;
var elapsedTimeTotal;

jQuery(function(){
    CreateCaptcha2();
});

// Create Captcha
function CreateCaptcha2() {
    //$('#InvalidCapthcaError2').hide();
    var alpha = new Array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
                        
    var i;
    for (i = 0; i < 6; i++) {
        var a = alpha[Math.floor(Math.random() * alpha.length)];
        var b = alpha[Math.floor(Math.random() * alpha.length)];
        var c = alpha[Math.floor(Math.random() * alpha.length)];
        var d = alpha[Math.floor(Math.random() * alpha.length)];
        var e = alpha[Math.floor(Math.random() * alpha.length)];
        var f = alpha[Math.floor(Math.random() * alpha.length)];
    }
    cd = a + ' ' + b + ' ' + c + ' ' + d + ' ' + e + ' ' + f;
    jQuery('#CaptchaImageCode2').empty().append('<canvas id="CapCode2" class="capcode" width="300" height="80"></canvas>')

    var c = document.getElementById("CapCode2"),
        ctx=c.getContext("2d"),
        x = c.width / 2,
        img = new Image();

    img.src = pluginDirectoryURL+"lib/asset/images/salvage-tileable-and-seamless-pattern.jpg";
    img.onload = function () {
        var pattern = ctx.createPattern(img, "repeat");
        ctx.fillStyle = pattern;
        ctx.fillRect(0, 0, c.width, c.height);
        ctx.font="46px Roboto Slab";
        ctx.fillStyle = '#ccc';
        ctx.textAlign = 'center';
        ctx.setTransform (1, -0.12, 0, 1, 0, 15);
        ctx.fillText(cd,x,55);
    };
}

jQuery(function(){
    CreateCaptcha();
});

// Create Captcha
function CreateCaptcha() {
    //$('#InvalidCapthcaError').hide();
    var alpha = new Array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
                        
    var i;
    for (i = 0; i < 6; i++) {
        var a = alpha[Math.floor(Math.random() * alpha.length)];
        var b = alpha[Math.floor(Math.random() * alpha.length)];
        var c = alpha[Math.floor(Math.random() * alpha.length)];
        var d = alpha[Math.floor(Math.random() * alpha.length)];
        var e = alpha[Math.floor(Math.random() * alpha.length)];
        var f = alpha[Math.floor(Math.random() * alpha.length)];
    }
    cd = a + ' ' + b + ' ' + c + ' ' + d + ' ' + e + ' ' + f;
    jQuery('#CaptchaImageCode').empty().append('<canvas id="CapCode" class="capcode" width="300" height="80"></canvas>')

    var c = document.getElementById("CapCode"),
        ctx=c.getContext("2d"),
        x = c.width / 2,
        img = new Image();

    img.src = pluginDirectoryURL+"lib/asset/images/salvage-tileable-and-seamless-pattern.jpg";
    img.onload = function () {
        var pattern = ctx.createPattern(img, "repeat");
        ctx.fillStyle = pattern;
        ctx.fillRect(0, 0, c.width, c.height);
        ctx.font="46px Roboto Slab";
        ctx.fillStyle = '#ccc';
        ctx.textAlign = 'center';
        ctx.setTransform (1, -0.12, 0, 1, 0, 15);
        ctx.fillText(cd,x,55);
    };
}

function elapsedTimeTotalFunction(elapsedTimeTotal, id_timer){
    var timeLimitInMinutes = elapsedTimeTotal;
    var timeLimitInSeconds = timeLimitInMinutes * 60;
    var timerElement = jQuery('#'+id_timer).text();

    function startTimer() {
    timeLimitInSeconds--;
    var minutes = Math.floor(timeLimitInSeconds / 60);
    var seconds = timeLimitInSeconds % 60;

    if (timeLimitInSeconds < 0) {
        jQuery('#'+id_timer).text = '00:00';
        // alert("Times up");
        clearInterval(timerInterval);
        return;
    }

    var html = minutes + ' minute:' + seconds + ' seconds';

    jQuery('#'+id_timer).html(html);

    }

    var timerInterval = setInterval(startTimer, 1000);
}

jQuery(document).ready(function($) {
    $('#sslcare-custom-login-container').hide();
    // Validate Captcha
    function ValidateCaptcha2() {
    var string1 = removeSpaces(cd);
    var string2 = removeSpaces($('#UserCaptchaCode2').val());
        // console.log('Captha ki',string1);
        // console.log('input ki',string2);
        if (string1 == string2) {
            if($(".sslcare-otp-login-form-custom-rz").valid()){
                $(".sslcare-otp-login-form-custom-rz").on('submit', function(e){
                    e.preventDefault();
                });
                $('.overlay-custom').css("display", "flex");
                // alert("All Data is OK");
                var msisdn = $('input[name=sslcare_phone_login]').val();
                captchaData2 = string2;

                var data = {
                    action: 'otp_login_ajax_action',
                    nonce: ajax_otp_login_object.nonce,
                    phone : msisdn, 
                    capData : captchaData2,
                    login : 'login',
                };
            
                $.post(ajax_otp_login_object.ajax_url, data, function(response) {
                    // Handle the AJAX response
                    // console.log(response);
                    // alert("hello world");
                    $('.overlay-custom').hide();
                    $('#UserCaptchaCode2-error').hide();
                    if(response == 'user-has-no-role'){
                        $('.modal-custom .modal-heading').text('User is not active. Please contact with the Service owner.');
                        $('.success-otp').toggleClass('is-visible'); 
                    }else if(response == 'user-not-found'){
                        // alert("User not found Popup");
                        $('.modal-custom .modal-heading').text('User not found');
                        $('.success-otp').toggleClass('is-visible'); 
                    }else if(response == 'sms-limit-exceeded'){
                        // alert("SMS Limit Exceeded Popup");
                        $('.modal-custom .modal-heading').text('SMS Limit Exceeded Popup');
                        $('.success-otp').toggleClass('is-visible'); 
                    }else{
                        phoneNumber = response.phone;
                        captchaData2 = response.cap_data;
                        var futureTime = response.future_time;
                        var otpSentTime = response.otp_sent_time;
                        // Calculate the time difference in milliseconds
                        var startDate = new Date(otpSentTime);
                        var endDate = new Date(futureTime);
                        var timeDiff = endDate - startDate;
                        // Convert the time difference to hours, minutes, and seconds
                        var hours = Math.floor(timeDiff / (1000 * 60 * 60));
                        timeDiff -= hours * 1000 * 60 * 60;
                        var minutes = Math.floor(timeDiff / (1000 * 60));
                        elapsedTimeTotal = minutes;
                        timeDiff -= minutes * 1000 * 60;
                        var seconds = Math.floor(timeDiff / 1000);
                        if(response.status_code == 200){
                            var html = '<style>.help-block{color:#da0000;font-size:12px;}</style><p class="form-row form-row-wide otp-custom"><form id="otp_submission_login"><label style="display:block" for="otp">OTP</label><input style="width:90%; margin-bottom:10px;" type="number" class="input-text" name="otp_login" id="otp_login" value="" /><button class="woocommerce-button button sslcare-otp-login-form-custom-rz__submit wp-element-button" id="otp_button" name="otp_button" value="Submit" style="margin-top:25px;">Submit</button></form></p>'
                            $('.sslcare-otp-login-form-custom-rz').replaceWith(html);
                            var html3 = '<div id="otp_login-error-expiration" class="help-block-expiration">Elapsed Time: <span id="timer">'+minutes+':00</span></div>';
					        $(html3).insertAfter("input[name = 'otp_login']");
                            var id_timer = 'timer';
                        }
                        elapsedTimeTotalFunction(elapsedTimeTotal, id_timer);
                    }
                })
                .fail(function(xhr, status, error) {
                    // Error handler
                    console.error('Error:', status, error);
                });
            }
        }else {
            console.log("Data is not correct");
        }
    }

    // Remove Spaces
    function removeSpaces(string) {
    return string.split(' ').join('');
    }


    // Check Captcha
    function CheckCaptcha2() {
        var result = ValidateCaptcha2();
        if( $("#UserCaptchaCode2").val() == "" || $("#UserCaptchaCode2").val() == null || $("#UserCaptchaCode2").val() == "undefined") {
            // $('#WrongCaptchaError2').text('Please enter code given below in a picture.').show();
            $('#UserCaptchaCode2').focus();
        } else {
            if(result == false) { 
            $('#WrongCaptchaError2').text('Invalid Captcha! Please try again.').show();
            CreateCaptcha2();
            $('#UserCaptchaCode2').focus().select();
            }
            else { 
                $('#UserCaptchaCode2').val('').attr('place-holder','Enter Captcha - Case Sensitive');
                CreateCaptcha2();
                $('#WrongCaptchaError2').fadeOut(100);
                // $('#SuccessMessage2').fadeIn(500).css('display','block').delay(5000).fadeOut(250);
                return result;
            }
        }  
    }

    // Check for text changes every second
    var interval = setInterval(function () {
        var currentText = $('#timer').text();

        if (currentText == '0 minute:0 seconds') {
            var html3 = '<div id="otp_login-error" class="help-block">OTP has been expired</div>';
            $('#otp_login-error').remove();
			$(html3).insertAfter("input[name = 'otp_login']");
            currentText = '';
            clearInterval(interval); // stop the interval
        }
    }, 1000);

    // Check for text changes every second
    var interval2 = setInterval(function () {
        var currentText2 = $('#timer2').text();

        if (currentText2 == '0 minute:0 seconds') {
            var html3 = '<div id="otp-error" class="help-block">OTP has been expired</div>';
            $('#otp-error').remove();
			$(html3).insertAfter("input[name = 'otp']");
            currentText2 = '';
            clearInterval(interval2); // stop the interval
        }
    }, 1000);

    $(".sslcare-otp-login-form-custom-rz").on("submit" ,function(e){
        e.preventDefault();
        if($(this).valid()){
            var checkedResulet = CheckCaptcha2();
            // $('.overlay-custom').css("display", "flex");
            // $(this).submit();
        }
    });

    /*----Phone Number Validation--------*/
    $.validator.addMethod("phoneBD", function(registration_mobile_number, element) {
        registration_mobile_number = registration_mobile_number.replace(/\s+/g, "");
        return this.optional(element) || registration_mobile_number.length > 9 && 
        registration_mobile_number.match(/(^(\+88|0088)?(01){1}[3456789]{1}(\d){8})$/);
    }, "Please enter a valid mobile number eg: 01XXXXXXXXX -Total 11 Digit");

    var form = $(".sslcare-otp-login-form-custom-rz");
        // console.log("ki pelam", form);
        form.validate({
        errorElement: 'span',
        errorClass: 'help-block',
        highlight: function(element, errorClass, validClass) {
            $(element).closest().addClass("has-error");
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).closest().removeClass("has-error");
        },
        rules: {
            sslcare_phone_login: {
                required: true,
                phoneBD: true,
                minlength: 11,
                maxlength: 11,
            },
            // email: {
            // 	required: true,
            // 	email: true,
            // },
            captcha: {
                required: true,
            },
        },
        messages: {
            sslcare_phone_login: {
                required: "Please enter a valid mobile number",
            },
            // email: {
            // 	required: "Please enter a valid email address",
            // 	email: "eg. name@gmail.com"
            // },
            captcha: {
                required: "Please insert correct captcha text here",
            },
        }
    });
    $(document).on('submit','#otp_submission_login',function(e){
        e.preventDefault();
        $('#otp_submission_login').validate({
            errorElement: 'div',
            errorClass: 'help-block',
            highlight: function(element, errorClass, validClass) {
                $(element).closest().addClass("has-error");
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).closest().removeClass("has-error");
            },
            rules: {
                otp_login: {
                    required: true,
                    digits: true
                },
            },
            messages: {
                otp_login: {
                    required: "Please enter the OTP",
                },
            }
        });
        if($('#otp_submission_login').valid()){
            // console.log("phone number ki ashole ekhane paisi kina", phoneNumber);
            var otp = $('input[name=otp_login]').val();

            var data = {
                action: 'otp_send_ajax_action',
                nonce: ajax_otp_send_object.nonce,
                phone : phoneNumber,
                otp : otp,
                capData : captchaData2,
                login : 'login',
            };

            $('.overlay-custom').show();
    
            $.post(ajax_otp_send_object.ajax_url, data, function(response) {
                // Handle the AJAX response
                // console.log("bai kisu ayy", response);
                $('#UserCaptchaCode2-error').hide();
                if(response == 'otp-incorrect'){
                    $('.overlay-custom').hide();
                    // alert('Incorrect OTP');
                    var html = '<div id="otp_login-error_incorrect" class="help-block">OTP is not correct.</div>';
                    $('#otp_login-error').remove();
                    $('#otp_login-error_incorrect').remove();
					$(html).insertAfter("input[name = 'otp_login']");
                }else if(response == 'otp-expired'){
                    $('.overlay-custom').hide();
                    // alert("Otp has been expired");
                    var html = '<div id="otp_login-error" class="help-block">OTP has been expired.</div>';
                    $('#otp_login-error').remove();
					$(html).insertAfter("input[name = 'otp_login']");
                }else{
                    var data = {
                        action: 'final_login_ajax_action',
                        nonce: ajax_final_login_object.nonce,
                        email: response.email,
                        phone : phoneNumber, 
                        login : 'login',
                    };

                    $('.overlay-custom').show();

                    $.post(ajax_final_login_object.ajax_url, data, function(response) {
                        // Handle the AJAX response
                        // console.log(response);
                        $('.overlay-custom').show();
                        $('#UserCaptchaCode2-error').hide();
                        location.reload();
                    });
                }
            })
            .fail(function(xhr, status, error) {
                // Error handler
                console.error('Error:', status, error);
            });
        }
    });
    // Used for Login Ends here


    // Used for Register
    // Validate Captcha
    function ValidateCaptcha() {
        var string1 = removeSpaces(cd);
        var string2 = removeSpaces($('#UserCaptchaCode').val());
            // console.log('Captha ki',string1);
            // console.log('input ki',string2);
            if (string1 == string2) {
                if($(".sslcare-otp-register-form-custom-rz").valid()){
                    $(".sslcare-otp-register-form-custom-rz").on('submit', function(e){
                        e.preventDefault();
                    });
                    $('.overlay-custom').css("display", "flex");
                    // alert("All Data is OK");
                    var email = $('input[name=email]').val();
                    var reg_password_sslcare = $('input[name=reg_password_sslcare]').val();
                    var msisdn = $('input[name=sslcare_phone]').val();
                    captchaData = string2;

                    var data = {
                        action: 'otp_register_ajax_action',
                        nonce: ajax_otp_register_object.nonce,
                        phone : msisdn, 
                        email : email,
                        password : reg_password_sslcare,
                        capData : captchaData,
                        register : 'register',
                    };
                
                    $.post(ajax_otp_register_object.ajax_url, data, function(response) {
                        // Handle the AJAX response
                        // console.log('ami eta dekhte chai',response);
                        $('.overlay-custom').hide();
                        $('#UserCaptchaCode-error').hide();
                        // console.log('mamu mamu 2', response);
                        if(response == 'weak-password'){
                            $('.modal-custom .modal-heading').text('Password must be at least 8 characters long and include at least one letter, one number, and one special character (@$!%*?&)');
                            $('.success-otp').toggleClass('is-visible'); 
                        }
                        if(response == 'user-already-exists'){
                            // alert("Bai user eta ase.. onno kisu try koren Popup");
                            $('.modal-custom .modal-heading').text('User already exists with this Phone number or Email address. Please try a different Phone number or Email address');
                            $('.success-otp').toggleClass('is-visible'); 
                        }else if(response == 'otp-not-sent'){
                            // alert("OTP is not sent");
                            $('.modal-custom .modal-heading').text('OTP is not sent');
                            $('.success-otp').toggleClass('is-visible'); 
                        }else if(response == 'sms-limit-exceeded'){
                            // alert("SMS Limit Exceeded Popup");
                            $('.modal-custom .modal-heading').text('SMS Limit Exceeded Popup');
                            $('.success-otp').toggleClass('is-visible'); 
                        }else{
                            phoneNumber = response.phone;
                            emailStored = response.email;
                            passStored = response.reg_password_sslcare;
                            captchaData = response.cap_data;
                            var futureTime = response.future_time;
                            var otpSentTime = response.otp_sent_time;
                            // Calculate the time difference in milliseconds
                            var startDate = new Date(otpSentTime);
                            var endDate = new Date(futureTime);
                            var timeDiff = endDate - startDate;
                            // Convert the time difference to hours, minutes, and seconds
                            var hours = Math.floor(timeDiff / (1000 * 60 * 60));
                            timeDiff -= hours * 1000 * 60 * 60;
                            var minutes = Math.floor(timeDiff / (1000 * 60));
                            elapsedTimeTotal = minutes;
                            timeDiff -= minutes * 1000 * 60;
                            var seconds = Math.floor(timeDiff / 1000);
                            if(response.status_code == 200){
                                var html = '<style>.help-block{color:#da0000;font-size:12px;}</style><p class="form-row form-row-wide otp-custom"><form id="otp_submission"><label style="display:block" for="otp">OTP</label><input style="width:90%; margin-bottom:10px;" type="number" class="input-text" name="otp" id="otp" value="" /><button class="woocommerce-button button sslcare-otp-login-form-custom-rz__submit wp-element-button" id="otp_button" name="otp_button" value="Submit" style="margin-top:25px;">Submit</button></form></p>'
                                $('.sslcare-otp-register-form-custom-rz').replaceWith(html);
                                var html3 = '<div id="otp-error-expiration" class="help-block-expiration">Elapsed Time: <span id="timer2">'+minutes+':00</span></div>';
					            $(html3).insertAfter("input[name = 'otp']");
                                var id_timer = 'timer2';
                            }
                            elapsedTimeTotalFunction(elapsedTimeTotal, id_timer);
                        }
                    })
                    .fail(function(xhr, status, error) {
                        // Error handler
                        console.error('Error:', status, error);
                    });
                }
            }else {
                console.log("Data is not correct");
            }
        }

        // Remove Spaces
        function removeSpaces(string) {
        return string.split(' ').join('');
        }


        // Check Captcha
        function CheckCaptcha() {
            var result = ValidateCaptcha();
            if( $("#UserCaptchaCode").val() == "" || $("#UserCaptchaCode").val() == null || $("#UserCaptchaCode").val() == "undefined") {
                // $('#WrongCaptchaError').text('Please enter code given below in a picture.').show();
                $('#UserCaptchaCode').focus();
            } else {
                if(result == false) { 
                $('#WrongCaptchaError').text('Invalid Captcha! Please try again.').show();
                CreateCaptcha();
                $('#UserCaptchaCode').focus().select();
                }
                else { 
                    $('#UserCaptchaCode').val('').attr('place-holder','Enter Captcha - Case Sensitive');
                    CreateCaptcha();
                    $('#WrongCaptchaError').fadeOut(100);
                    // $('#SuccessMessage').fadeIn(500).css('display','block').delay(5000).fadeOut(250);
                    return result;
                }
            }  
        }

        $(document).on('submit','#otp_submission',function(e){
            e.preventDefault();
            $('#otp_submission').validate({
                errorElement: 'div',
                errorClass: 'help-block',
                highlight: function(element, errorClass, validClass) {
                    $(element).closest().addClass("has-error");
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).closest().removeClass("has-error");
                },
                rules: {
                    otp: {
                        required: true,
                        digits: true
                    },
                },
                messages: {
                    otp: {
                        required: "Please enter the OTP",
                    },
                }
            });
            if($('#otp_submission').valid()){
                // console.log("phone number ki ashole ekhane paisi kina", phoneNumber);
                var otp = $('input[name=otp]').val();

                var data = {
                    action: 'otp_send_ajax_action',
                    nonce: ajax_otp_send_object.nonce,
                    phone : phoneNumber, 
                    email: emailStored,
                    password: passStored,
                    otp : otp,
                    capData : captchaData,
                    register : 'register',
                };

                $('.overlay-custom').show();
            
                $.post(ajax_otp_register_object.ajax_url, data, function(response) {
                    // Handle the AJAX response
                    // console.log("halar.......",response);
                    $('#UserCaptchaCode-error').hide();
                    if(response == 'otp-incorrect'){
                        $('.overlay-custom').hide();
                        // alert('Incorrect OTP');
                        var html = '<div id="otp-error_incorrect" class="help-block">OTP is not correct.</div>';
                        $('#otp-error').remove();
                        $('#otp-error_incorrect').remove();
                        $(html).insertAfter("input[name = 'otp']");
                    }else if(response == 'otp-expired'){
                        $('.overlay-custom').hide();
                        var html = '<div id="otp-error" class="help-block">OTP has been expired.</div>';
                        $('#otp-error').remove();
                        $(html).insertAfter("input[name = 'otp']");
                    }else{
                        var data = {
                            action: 'final_login_ajax_action',
                            nonce: ajax_final_login_object.nonce,
                            email: response.email,
                            phone : response.phone,
                            password : response.reg_password_sslcare,
                            register : 'register',
                        };

                        $('.overlay-custom').show();
                    
                        $.post(ajax_otp_register_object.ajax_url, data, function(response) {
                            // Handle the AJAX response
                            // console.log(response);
                            // $('.overlay-custom').hide();
                                $('#UserCaptchaCode-error').hide();
                                // console.log('bhai dekh dekh', response);
                                if(response == 'user-already-exists'){
                                    $('.overlay-custom').hide();
                                    // alert("Bai user eta ase.. onno kisu try koren Popup");
                                    $('.modal-custom .modal-heading').text('User already exists with this Phone number or Email address. Please try a different Phone number or Email address');
                                    $('.success-otp').toggleClass('is-visible'); 
                                }else{
                                    $('.overlay-custom').show();
                                    location.reload();
                                }
                        });
                    }
                })
                .fail(function(xhr, status, error) {
                    // Error handler
                    console.error('Error:', status, error);
                });
            }
        });

        $(".sslcare-otp-register-form-custom-rz").on("submit" ,function(e){
            e.preventDefault();
            if($(this).valid()){
                var checkedResulet = CheckCaptcha();
                // $('.overlay-custom').css("display", "flex");
                // $(this).submit();
            }
        });

        /*----Phone Number Validation--------*/
        $.validator.addMethod("phoneBD", function(registration_mobile_number, element) {
            registration_mobile_number = registration_mobile_number.replace(/\s+/g, "");
            return this.optional(element) || registration_mobile_number.length > 9 && 
            registration_mobile_number.match(/(^(\+88|0088)?(01){1}[3456789]{1}(\d){8})$/);
        }, "Please enter a valid mobile number eg: 01XXXXXXXXX -Total 11 Digit");

        /*----Add custom validation method for a strong password--------*/
        $.validator.addMethod("strongPassword", function(value, element) {
            // Use a regular expression to enforce a strong password
            return this.optional(element) || /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(value);
        }, "Password must be at least 8 characters long and include at least one letter, one number, and one special character (@$!%*?&).");

        var form = $(".sslcare-otp-register-form-custom-rz");
            // console.log("ki pelam", form);
            form.validate({
            errorElement: 'span',
            errorClass: 'help-block',
            highlight: function(element, errorClass, validClass) {
                $(element).closest().addClass("has-error");
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).closest().removeClass("has-error");
            },
            rules: {
                sslcare_phone: {
                    required: true,
                    phoneBD: true,
                    minlength: 11,
                    maxlength: 11,
                },
                email: {
                    required: true,
                    email: true,
                },
                reg_password_sslcare: {
                    required: true,
                    strongPassword: true, // Apply custom validation rule
                },
                captcha: {
                    required: true,
                },
            },
            messages: {
                sslcare_phone: {
                    required: "Please enter a valid mobile number",
                },
                email: {
                    required: "Please enter a valid email address",
                    email: "eg. name@gmail.com"
                },
                reg_password_sslcare: {
                    required: "Please enter a secure password",
                },
                captcha: {
                    required: "Please insert correct captcha text here",
                },
            }
        });

    //Used for Register Ends here  

    $(document).on('submit','#custom-login-form',function(e){
        e.preventDefault();
        $('#custom-login-form').validate({
            errorElement: 'div',
            errorClass: 'help-block',
            highlight: function(element, errorClass, validClass) {
                $(element).closest().addClass("has-error");
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).closest().removeClass("has-error");
            },
            rules: {
                username_sslcare: {
                    required: true,
                },
                password_sslcare: {
                    required: true,
                },
            },
            messages: {
                username_sslcare: {
                    required: "Please enter username",
                },
                password_sslcare: {
                    required: "Please enter the password",
                },
            }
        });
        if($('#custom-login-form').valid()){

            var username_sslcare = $('input[name=username_sslcare]').val();
            var password_sslcare = $('input[name=password_sslcare]').val();

            // console.log(username_sslcare);
            // console.log(password_sslcare);

            $('.overlay-custom').show();

            var data = {
                action: 'otp_login_ajax_action',
                nonce: ajax_otp_login_object.nonce,
                username: username_sslcare,
                password: password_sslcare,
                login_with_username : 'login_with_username',
            };
        
            $.post(ajax_otp_login_object.ajax_url, data, function(response) {
                // Handle the AJAX response
                // console.log("response ashole ki ashtese", response);
                // alert("hello world");
                if(response == 'login-with-username-failed'){
                    $('.overlay-custom').hide();
                    $('#custom-login-message').text('');
                    $('#custom-login-message').text('Username or Password is incorrect');
                }
                if(response == 'login-with-username-success'){
                    location.reload();
                    // $('#custom-login-message').text('');
                    // $('#custom-login-message').text('Username or Password is matched');
                }
            })
            .fail(function(xhr, status, error) {
                // Error handler
                console.error('Error:', status, error);
            });
        }
    });
    
    $(document).on('keyup', 'input[name="otp_login"]', function(){
        var input_value = $(this).val();
        if(input_value === ''){
            $('#otp_login-error_incorrect').remove();
        }
    });

    $(document).on('keyup', 'input[name="otp"]', function(){
        var input_value = $(this).val();
        if(input_value === ''){
            $('#otp-error_incorrect').remove();
        }
    });

    $(document).on('change', 'input[name="sslcare_login_type"]', function(){
        var sslcare_login_type = $(this).val();
        if(sslcare_login_type === 'login_with_otp'){
            $('.sslcare-otp-login-form-custom-rz').show();
            $('#sslcare-custom-login-container').hide();
        }
        if(sslcare_login_type === 'login_with_username'){
            $('#sslcare-custom-login-container').show();
            $('.sslcare-otp-login-form-custom-rz').hide();
        }
    });

});