<<<<<<< HEAD
<?php
/*
Plugin Name: لاگین LMS تاملند
Description: با این افزونه از طریق سرویس LMS می‌توانید لاگین و ثبت نام کنید.
Version: 1.0
Author: Sajad Akbari
Author URL: sajadakbari.ir
*/

function custom_login_form() {
    ob_start();
    ?>
    <form id="mobile-login-form" method="post">
        <h2 class="login-title">ثبت نام و ورود به تام‌شاپ</h2>
        <div id="mobile-section">
            <div id="mobileInput">
                <input type="text" id="mobile" name="mobile" placeholder="شماره موبایل" required>
            </div>
            <button type="submit" id="send-otp">ورود</button>
        </div>
    </form>
    <div id="response"></div>
        <style>
        .login-title{
            margin-bottom:30px;
            font-size:20px;
            text-align:Center;
        }
        input[type="text"]{
            display:block;
            width:100%;
            padding:15px;
            border-radius:8px;
            border:1px solid #eee;
            background:#fff;
            text-align:center;
        }
        button{
            margin-top:15px;
            width:100%;
            border-radius:8px;
            display:block;
        }
        .lms-alert{
            width:100%;
            display:block;
            padding:7px 15px;
            border-radius:8px;
            text-align:center;
            font-size:16px;
        }
        .info-alert{
            background:#e3eeff;
            color:#001d4a;
            border:1px solid #cfe2ff;
        }
        .danger-alert{
            background:#ffe3e3;
            color:#4a0000;
            border:1px solid #ffcfcf;
        }
        .success-alert{
            background:#e3fff7;
            color:#004a36;
            border:1px solid #cffff4;
        }
        #response{
            margin-top:15px;
        }
        #mobileInput{
            margin-bottom:15px;
        }
        #timer{
            padding: 0 7px;
        }
        #sendOtpAgain{
            text-align:center;
            font-size:14px;
            cursor:pointer;
        }
    </style>
    <script>
        var totalTime = 2 * 60; // مدت زمان تایمر بر حسب ثانیه (2 دقیقه)
        jQuery(document).ready(function(){
            jQuery('#mobile-login-form').on('submit', function(event){
                jQuery('#send-otp').html('<img src="<?php echo plugins_url('assets/img/motion-blur-2.svg', __FILE__); ?>" style="height:20px">').attr('disabled','disabled').css({'background-color':'#888', 'cursor':'default'});
            });
        });
        function updateTimerDisplay() {
            let minutes = Math.floor(totalTime / 60);
            let seconds = totalTime % 60;
            // نمایش زمان در قالب دقیقه:ثانیه
            jQuery('#timer').text(
                (minutes < 10 ? '0' + minutes : minutes) + ':' +
                (seconds < 10 ? '0' + seconds : seconds)
            );
        }
    </script>
    <?php
    return ob_get_clean();
}

add_shortcode('mobile_login_form', 'custom_login_form');


function handle_custom_login_form_submission() {
    
    if ( isset($_POST['mobile']) && !isset($_POST['otp']) ) {
        $mobile = sanitize_text_field($_POST['mobile']);
        

        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.tamland.ir/api/user/checkPhoneNumber/'.$mobile,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        $response_json = json_decode($response);
        
        $response_json_objvars = get_object_vars($response_json[0]);
        
        $having_mobile = $response_json_objvars['fldHavingMobile'];
        
        if($having_mobile == 1){
            
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.tamland.ir/api/user/Login',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{
                "Username":"'.$mobile.'",
                "Password":"",
                "SchoolId":-1,
                "Os":"", 
                "Browser":"",
                "Device":"",
                "otpCall":"0"
            }
            ',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            ));
            
            $response_login = curl_exec($curl);
            
            curl_close($curl);

            $response_login_json = json_decode($response_login);
            
            switch ($response_login_json->status) {
              case 1:
                  ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">نام کاربری شما معتبر نیست.</div>');
                      });
                  </script>
                  <?php
                break;
              case 2:
                 ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">خطا! مجدد تلاش کنید.</div>');
                      });
                  </script>
                  <?php
                break;
              case 3:
                ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">خطا! مجدد تلاش کنید.</div>');
                      });
                  </script>
                <?php
                break;
              case 4:
                ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">کاربر غیرفعال می‌باشد</div>');
                      });
                  </script>
                <?php
                break;
              case 5:
                  ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">رمز عبور ضروری است</div>');
                      });
                  </script>
                <?php
                break;
              case 6:
                  ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">خطا! مجدد تلاش کنید.</div>');
                      });
                  </script>
                 <?php
                break; 
              case 7:
                  ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('input#mobile').val("<?php echo $mobile; ?>").css('background-color','#fafafa');
                          jQuery('#mobileInput').after('<div id="verifyCodeInput" style="display:none">'+
                                    '<input type="text" id="otp" name="otp" placeholder="کد تایید">'+
                                    '</div>');
                            jQuery('#verifyCodeInput').fadeIn(500);
                            jQuery('input#otp').focus();
                          jQuery('#response').html('<div class="lms-alert info-alert">کد ارسال شد! <span id="timer">02:00</span></div>');
                          jQuery('#send-otp').text('ارسال کد تایید');
                          
                          
                        let countdown = setInterval(function () {
                            if (totalTime <= 0) {
                                clearInterval(countdown); // توقف تایمر
                                jQuery('#timer').text('00:00'); // نمایش صفر
                                jQuery('#response').html('<div id="sendOtpAgain"><img width="16px" height="16px" src="<?php echo plugins_url("assets/img/arrow-circle-with-half-broken-line-svgrepo-com.svg", __FILE__); ?>"> ارسال مجدد کد تایید</div>');
                            } else {
                                totalTime--;
                                updateTimerDisplay();
                            }
                        }, 1000); // اجرای تابع هر 1 ثانیه
            
                        updateTimerDisplay(); // به‌روزرسانی اولیه نمایش تایمر
                        
                        jQuery('#response').on('click', '#sendOtpAgain', function() {
                            let mobile = jQuery('input#mobile').val();
                            var settings = {
                              "url": "https://api.tamland.ir/api/user/Login",
                              "method": "POST",
                              "timeout": 0,
                              "headers": {
                                "Content-Type": "application/json"
                              },
                              "data": JSON.stringify({
                                "Username": mobile,
                                "Password": "",
                                "SchoolId": -1,
                                "Os": "",
                                "Browser": "",
                                "Device": "",
                                "otpCall": "0"
                              }),
                            };
                            
                            jQuery.ajax(settings).done(function (response) {
                              //console.log(response);
                              totalTime = 2 * 60;
                              jQuery('input#otp').focus();
                              jQuery('#response').html('<div class="lms-alert info-alert">کد ارسال شد! <span id="timer">02:00</span></div>');
                              let countdown = setInterval(function () {
                                if (totalTime <= 0) {
                                    clearInterval(countdown); // توقف تایمر
                                    jQuery('#timer').text('00:00'); // نمایش صفر
                                    jQuery('#response').html('<div id="sendOtpAgain"><img width="16px" height="16px" src="<?php echo plugins_url("assets/img/arrow-circle-with-half-broken-line-svgrepo-com.svg", __FILE__); ?>"> ارسال مجدد کد تایید</div>');
                                } else {
                                    totalTime--;
                                    updateTimerDisplay();
                                }
                            }, 1000); // اجرای تابع هر 1 ثانیه
                
                            updateTimerDisplay(); // به‌روزرسانی اولیه نمایش تایمر
                            });
                            
                        });
                      });
                  </script>
                 <?php
                break;
              case 8:
                  ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">IP کاربر نامعتبر است.</div>');
                      });
                  </script>
                 <?php
                break; 
              case 9:
                  ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">کاربر بلاک شده است.</div>');
                      });
                  </script>
                 <?php
                break; 
              case 10:
                  ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">کاربر بلاک شده است.</div>');
                      });
                  </script>
                 <?php
                break; 
              case 11:
                  ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">رمز عبور شما باید تغییر کند</div>');
                      });
                  </script>
                 <?php
                break; 
              case 12:
                  ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">کد تایید غیرفعال است</div>');
                      });
                  </script>
                 <?php
                break; 
              default:
                //code block
            }

        }elseif($having_mobile == 0){
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.tamland.ir/api/user/signup',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{
                "Mobile":"'.$mobile.'",
                 "SchoolId":-1,
                 "Affiliate":"", 
                 "ConsultantId":"",
                 "Campaign":""
            }',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
            
            ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('input#mobile').remove();
                          jQuery('#mobileInput').prepend('<input type="text" id="signupmobile" name="signupmobile" placeholder="شماره موبایل" required>'); 
                          jQuery('input#signupmobile').val("<?php echo $mobile; ?>").css('background-color','#fafafa');
                          jQuery('#mobileInput').after('<div id="verifyCodeInput" style="display:none">'+
                                    '<input type="text" id="otp" name="otp" placeholder="کد تایید">'+
                                    '</div>');
                            jQuery('#verifyCodeInput').fadeIn(500);
                            jQuery('input#otp').focus();
                          jQuery('#response').html('<div class="lms-alert info-alert">کد ارسال شد! <span id="timer">02:00</span></div>');
                          jQuery('#send-otp').text('ثبت نام');
                          
                          let countdown = setInterval(function () {
                            if (totalTime <= 0) {
                                clearInterval(countdown); // توقف تایمر
                                jQuery('#timer').text('00:00'); // نمایش صفر
                                jQuery('#response').html('<div id="sendOtpAgain"><img width="16px" height="16px" src="<?php echo plugins_url("assets/img/arrow-circle-with-half-broken-line-svgrepo-com.svg", __FILE__); ?>"> ارسال مجدد کد تایید</div>');
                            } else {
                                totalTime--;
                                updateTimerDisplay();
                            }
                        }, 1000); // اجرای تابع هر 1 ثانیه
            
                        updateTimerDisplay(); // به‌روزرسانی اولیه نمایش تایمر
                        
                        jQuery('#response').on('click', '#sendOtpAgain', function() {
                            let mobile = jQuery('input#signupmobile').val();
                            var settings = {
                              "url": "https://api.tamland.ir/api/user/signup",
                              "method": "POST",
                              "timeout": 0,
                              "headers": {
                                "Content-Type": "application/json"
                              },
                              "data": JSON.stringify({
                                "Mobile": mobile,
                                "SchoolId": -1,
                                "Affiliate": "",
                                "ConsultantId": "",
                                "Campaign": ""
                              }),
                            };
                            
                            jQuery.ajax(settings).done(function (response) {
                              //console.log(response);
                              totalTime = 2 * 60;
                              jQuery('input#otp').focus();
                          jQuery('#response').html('<div class="lms-alert info-alert">کد ارسال شد! <span id="timer">02:00</span></div>');
                          let countdown = setInterval(function () {
                            if (totalTime <= 0) {
                                clearInterval(countdown); // توقف تایمر
                                jQuery('#timer').text('00:00'); // نمایش صفر
                                jQuery('#response').html('<div id="sendOtpAgain"><img width="16px" height="16px" src="<?php echo plugins_url("assets/img/arrow-circle-with-half-broken-line-svgrepo-com.svg", __FILE__); ?>"> ارسال مجدد کد تایید</div>');
                            } else {
                                totalTime--;
                                updateTimerDisplay();
                            }
                        }, 1000); // اجرای تابع هر 1 ثانیه
            
                        updateTimerDisplay(); // به‌روزرسانی اولیه نمایش تایمر
                        
                            });
                        });
                      });
                  </script>
                 <?php
        }

    }elseif( isset($_POST['mobile']) && isset($_POST['otp']) ) {
        $mobile = sanitize_text_field($_POST['mobile']);
        $otp = sanitize_text_field($_POST['otp']);
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.tamland.ir/api/user/verifyLoginCode',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "Mobile":"'.$mobile.'",
            "VerificationCode":"'.$otp.'"
        }
        ',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        $response_json = json_decode($response);
        $token_login = $response_json->data;
        if($token_login != ""){
        $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.tamland.ir/api/user/Login',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{
                "Username":"'.$mobile.'",
                "Password": " ",
                "SchoolId":  -1,
                "os": "",
                "ip": "",
                "browser": "",
                "device":  "",
                "firebase": "",
                "appFirebase": "",
                "token": "'.$token_login.'"
            }',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            ));
            
            $response_login2 = curl_exec($curl);
            
            curl_close($curl);

            $response_login_json2 = json_decode($response_login2);
            
            $token = $response_login_json2->token;
            if($token != ""){
                $curl_loadinfo = curl_init();
                
                curl_setopt_array($curl_loadinfo, array(
                  CURLOPT_URL => 'https://api.tamland.ir/api/user/loadInfo',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'GET',
                  CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: bearer '.$token,
                  ),
                ));
                
                $response_loadinfo = curl_exec($curl_loadinfo);
                
                curl_close($curl_loadinfo);
                
                $response_loadinfo_json = json_decode($response_loadinfo, true); // تبدیل به آرایه
                $response_loadinfo_json_objvars = $response_loadinfo_json['data'][0]; // دسترسی به اولین عنصر داده
                $fldUserGUID = $response_loadinfo_json_objvars['fldUserGUID'];
                $fldUserCo = $response_loadinfo_json_objvars['fldPkUserCo'];
                $user = get_user_by('login', $fldUserGUID );
                if (!$user) {
                    $user_id = wp_create_user($fldUserGUID, wp_generate_password(), $fldUserGUID . '@example.com');
                    $user = get_user_by('id', $user_id);
                }
                // ذخیره $fldUserCo در usermeta
                update_user_meta($user->ID, 'fldUserCo', $fldUserCo);
                
                wp_set_auth_cookie($user->ID);
                $expiration = time() + (3600 * 24 * 7); // 7 days from now
                setcookie('tamshToken', $token, $expiration, '/', 'tamyar.ir', true, true);

                wp_redirect(site_url().'/my-account/');
            }
        }

    }elseif( isset($_POST['signupmobile']) && isset($_POST['otp']) ) {
        $signupmobile = sanitize_text_field($_POST['signupmobile']);
        $otp = sanitize_text_field($_POST['otp']);

        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.tamland.ir/api/user/verifyCode',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "Mobile":"'.$signupmobile.'",
            "VerificationCode":"'.$otp.'"
        }
        ',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        
         $response_json = json_decode($response);
        $token_signup = $response_json->token;
        if($token_signup != ""){
            ?>
                  <script>
                      jQuery(document).ready(function(){
                          localStorage.setItem("tamshToken", "<?php echo $token_signup; ?>");
                          jQuery('input#mobile').remove();
                          jQuery('#response').html('<div class="lms-alert success-alert">ثبت نام شما با موفقیت انجام شد</div>');
                          jQuery('#send-otp').remove();
                          jQuery('#response').after('<button type="button" id="goToLogin">ورود به تام‌شاپ</button>');
                          
                          jQuery('#goToLogin').click(function(){
                              jQuery('#mobileInput').html('<input type="text" id="mobile" name="mobile" placeholder="شماره موبایل" required>');
                              jQuery('#mobileInput').after('<button type="submit" id="send-otp">ورود</button>');
                              jQuery('#goToLogin').remove();
                              jQuery('#response').empty();
                              
                          });
                      });
                  </script>
                 <?php
        }

    }
}
=======
<?php
/*
Plugin Name: لاگین LMS تاملند
Description: با این افزونه از طریق سرویس LMS می‌توانید لاگین و ثبت نام کنید.
Version: 1.0
Author: Sajad Akbari
Author URL: sajadakbari.ir
*/

function custom_login_form() {
    ob_start();
    ?>
    <form id="mobile-login-form" method="post">
        <h2 class="login-title">ثبت نام و ورود به تام‌شاپ</h2>
        <div id="mobile-section">
            <div id="mobileInput">
                <input type="text" id="mobile" name="mobile" placeholder="شماره موبایل" required>
            </div>
            <button type="submit" id="send-otp">ورود</button>
        </div>
    </form>
    <div id="response"></div>
        <style>
        .login-title{
            margin-bottom:30px;
            font-size:20px;
            text-align:Center;
        }
        input[type="text"]{
            display:block;
            width:100%;
            padding:15px;
            border-radius:8px;
            border:1px solid #eee;
            background:#fff;
            text-align:center;
        }
        button{
            margin-top:15px;
            width:100%;
            border-radius:8px;
            display:block;
        }
        .lms-alert{
            width:100%;
            display:block;
            padding:7px 15px;
            border-radius:8px;
            text-align:center;
            font-size:16px;
        }
        .info-alert{
            background:#e3eeff;
            color:#001d4a;
            border:1px solid #cfe2ff;
        }
        .danger-alert{
            background:#ffe3e3;
            color:#4a0000;
            border:1px solid #ffcfcf;
        }
        .success-alert{
            background:#e3fff7;
            color:#004a36;
            border:1px solid #cffff4;
        }
        #response{
            margin-top:15px;
        }
        #mobileInput{
            margin-bottom:15px;
        }
        #timer{
            padding: 0 7px;
        }
        #sendOtpAgain{
            text-align:center;
            font-size:14px;
            cursor:pointer;
        }
    </style>
    <script>
        var totalTime = 2 * 60; // مدت زمان تایمر بر حسب ثانیه (2 دقیقه)
        jQuery(document).ready(function(){
            jQuery('#mobile-login-form').on('submit', function(event){
                jQuery('#send-otp').html('<img src="<?php echo plugins_url('assets/img/motion-blur-2.svg', __FILE__); ?>" style="height:20px">').attr('disabled','disabled').css({'background-color':'#888', 'cursor':'default'});
            });
        });
        function updateTimerDisplay() {
            let minutes = Math.floor(totalTime / 60);
            let seconds = totalTime % 60;
            // نمایش زمان در قالب دقیقه:ثانیه
            jQuery('#timer').text(
                (minutes < 10 ? '0' + minutes : minutes) + ':' +
                (seconds < 10 ? '0' + seconds : seconds)
            );
        }
    </script>
    <?php
    return ob_get_clean();
}

add_shortcode('mobile_login_form', 'custom_login_form');


function handle_custom_login_form_submission() {
    
    if ( isset($_POST['mobile']) && !isset($_POST['otp']) ) {
        $mobile = sanitize_text_field($_POST['mobile']);
        

        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.tamland.ir/api/user/checkPhoneNumber/'.$mobile,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        $response_json = json_decode($response);
        
        $response_json_objvars = get_object_vars($response_json[0]);
        
        $having_mobile = $response_json_objvars['fldHavingMobile'];
        
        if($having_mobile == 1){
            
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.tamland.ir/api/user/Login',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{
                "Username":"'.$mobile.'",
                "Password":"",
                "SchoolId":-1,
                "Os":"", 
                "Browser":"",
                "Device":"",
                "otpCall":"0"
            }
            ',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            ));
            
            $response_login = curl_exec($curl);
            
            curl_close($curl);

            $response_login_json = json_decode($response_login);
            
            switch ($response_login_json->status) {
              case 1:
                  ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">نام کاربری شما معتبر نیست.</div>');
                      });
                  </script>
                  <?php
                break;
              case 2:
                 ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">خطا! مجدد تلاش کنید.</div>');
                      });
                  </script>
                  <?php
                break;
              case 3:
                ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">خطا! مجدد تلاش کنید.</div>');
                      });
                  </script>
                <?php
                break;
              case 4:
                ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">کاربر غیرفعال می‌باشد</div>');
                      });
                  </script>
                <?php
                break;
              case 5:
                  ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">رمز عبور ضروری است</div>');
                      });
                  </script>
                <?php
                break;
              case 6:
                  ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">خطا! مجدد تلاش کنید.</div>');
                      });
                  </script>
                 <?php
                break; 
              case 7:
                  ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('input#mobile').val("<?php echo $mobile; ?>").css('background-color','#fafafa');
                          jQuery('#mobileInput').after('<div id="verifyCodeInput" style="display:none">'+
                                    '<input type="text" id="otp" name="otp" placeholder="کد تایید">'+
                                    '</div>');
                            jQuery('#verifyCodeInput').fadeIn(500);
                            jQuery('input#otp').focus();
                          jQuery('#response').html('<div class="lms-alert info-alert">کد ارسال شد! <span id="timer">02:00</span></div>');
                          jQuery('#send-otp').text('ارسال کد تایید');
                          
                          
                        let countdown = setInterval(function () {
                            if (totalTime <= 0) {
                                clearInterval(countdown); // توقف تایمر
                                jQuery('#timer').text('00:00'); // نمایش صفر
                                jQuery('#response').html('<div id="sendOtpAgain"><img width="16px" height="16px" src="<?php echo plugins_url("assets/img/arrow-circle-with-half-broken-line-svgrepo-com.svg", __FILE__); ?>"> ارسال مجدد کد تایید</div>');
                            } else {
                                totalTime--;
                                updateTimerDisplay();
                            }
                        }, 1000); // اجرای تابع هر 1 ثانیه
            
                        updateTimerDisplay(); // به‌روزرسانی اولیه نمایش تایمر
                        
                        jQuery('#response').on('click', '#sendOtpAgain', function() {
                            let mobile = jQuery('input#mobile').val();
                            var settings = {
                              "url": "https://api.tamland.ir/api/user/Login",
                              "method": "POST",
                              "timeout": 0,
                              "headers": {
                                "Content-Type": "application/json"
                              },
                              "data": JSON.stringify({
                                "Username": mobile,
                                "Password": "",
                                "SchoolId": -1,
                                "Os": "",
                                "Browser": "",
                                "Device": "",
                                "otpCall": "0"
                              }),
                            };
                            
                            jQuery.ajax(settings).done(function (response) {
                              //console.log(response);
                              totalTime = 2 * 60;
                              jQuery('input#otp').focus();
                              jQuery('#response').html('<div class="lms-alert info-alert">کد ارسال شد! <span id="timer">02:00</span></div>');
                              let countdown = setInterval(function () {
                                if (totalTime <= 0) {
                                    clearInterval(countdown); // توقف تایمر
                                    jQuery('#timer').text('00:00'); // نمایش صفر
                                    jQuery('#response').html('<div id="sendOtpAgain"><img width="16px" height="16px" src="<?php echo plugins_url("assets/img/arrow-circle-with-half-broken-line-svgrepo-com.svg", __FILE__); ?>"> ارسال مجدد کد تایید</div>');
                                } else {
                                    totalTime--;
                                    updateTimerDisplay();
                                }
                            }, 1000); // اجرای تابع هر 1 ثانیه
                
                            updateTimerDisplay(); // به‌روزرسانی اولیه نمایش تایمر
                            });
                            
                        });
                      });
                  </script>
                 <?php
                break;
              case 8:
                  ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">IP کاربر نامعتبر است.</div>');
                      });
                  </script>
                 <?php
                break; 
              case 9:
                  ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">کاربر بلاک شده است.</div>');
                      });
                  </script>
                 <?php
                break; 
              case 10:
                  ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">کاربر بلاک شده است.</div>');
                      });
                  </script>
                 <?php
                break; 
              case 11:
                  ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">رمز عبور شما باید تغییر کند</div>');
                      });
                  </script>
                 <?php
                break; 
              case 12:
                  ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('#response').html('<div class="lms-alert danger-alert">کد تایید غیرفعال است</div>');
                      });
                  </script>
                 <?php
                break; 
              default:
                //code block
            }

        }elseif($having_mobile == 0){
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.tamland.ir/api/user/signup',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{
                "Mobile":"'.$mobile.'",
                 "SchoolId":-1,
                 "Affiliate":"", 
                 "ConsultantId":"",
                 "Campaign":""
            }',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
            
            ?>
                  <script>
                      jQuery(document).ready(function(){
                          jQuery('input#mobile').remove();
                          jQuery('#mobileInput').prepend('<input type="text" id="signupmobile" name="signupmobile" placeholder="شماره موبایل" required>'); 
                          jQuery('input#signupmobile').val("<?php echo $mobile; ?>").css('background-color','#fafafa');
                          jQuery('#mobileInput').after('<div id="verifyCodeInput" style="display:none">'+
                                    '<input type="text" id="otp" name="otp" placeholder="کد تایید">'+
                                    '</div>');
                            jQuery('#verifyCodeInput').fadeIn(500);
                            jQuery('input#otp').focus();
                          jQuery('#response').html('<div class="lms-alert info-alert">کد ارسال شد! <span id="timer">02:00</span></div>');
                          jQuery('#send-otp').text('ثبت نام');
                          
                          let countdown = setInterval(function () {
                            if (totalTime <= 0) {
                                clearInterval(countdown); // توقف تایمر
                                jQuery('#timer').text('00:00'); // نمایش صفر
                                jQuery('#response').html('<div id="sendOtpAgain"><img width="16px" height="16px" src="<?php echo plugins_url("assets/img/arrow-circle-with-half-broken-line-svgrepo-com.svg", __FILE__); ?>"> ارسال مجدد کد تایید</div>');
                            } else {
                                totalTime--;
                                updateTimerDisplay();
                            }
                        }, 1000); // اجرای تابع هر 1 ثانیه
            
                        updateTimerDisplay(); // به‌روزرسانی اولیه نمایش تایمر
                        
                        jQuery('#response').on('click', '#sendOtpAgain', function() {
                            let mobile = jQuery('input#signupmobile').val();
                            var settings = {
                              "url": "https://api.tamland.ir/api/user/signup",
                              "method": "POST",
                              "timeout": 0,
                              "headers": {
                                "Content-Type": "application/json"
                              },
                              "data": JSON.stringify({
                                "Mobile": mobile,
                                "SchoolId": -1,
                                "Affiliate": "",
                                "ConsultantId": "",
                                "Campaign": ""
                              }),
                            };
                            
                            jQuery.ajax(settings).done(function (response) {
                              //console.log(response);
                              totalTime = 2 * 60;
                              jQuery('input#otp').focus();
                          jQuery('#response').html('<div class="lms-alert info-alert">کد ارسال شد! <span id="timer">02:00</span></div>');
                          let countdown = setInterval(function () {
                            if (totalTime <= 0) {
                                clearInterval(countdown); // توقف تایمر
                                jQuery('#timer').text('00:00'); // نمایش صفر
                                jQuery('#response').html('<div id="sendOtpAgain"><img width="16px" height="16px" src="<?php echo plugins_url("assets/img/arrow-circle-with-half-broken-line-svgrepo-com.svg", __FILE__); ?>"> ارسال مجدد کد تایید</div>');
                            } else {
                                totalTime--;
                                updateTimerDisplay();
                            }
                        }, 1000); // اجرای تابع هر 1 ثانیه
            
                        updateTimerDisplay(); // به‌روزرسانی اولیه نمایش تایمر
                        
                            });
                        });
                      });
                  </script>
                 <?php
        }

    }elseif( isset($_POST['mobile']) && isset($_POST['otp']) ) {
        $mobile = sanitize_text_field($_POST['mobile']);
        $otp = sanitize_text_field($_POST['otp']);
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.tamland.ir/api/user/verifyLoginCode',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "Mobile":"'.$mobile.'",
            "VerificationCode":"'.$otp.'"
        }
        ',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        $response_json = json_decode($response);
        $token_login = $response_json->data;
        if($token_login != ""){
        $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.tamland.ir/api/user/Login',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{
                "Username":"'.$mobile.'",
                "Password": " ",
                "SchoolId":  -1,
                "os": "",
                "ip": "",
                "browser": "",
                "device":  "",
                "firebase": "",
                "appFirebase": "",
                "token": "'.$token_login.'"
            }',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            ));
            
            $response_login2 = curl_exec($curl);
            
            curl_close($curl);

            $response_login_json2 = json_decode($response_login2);
            
            $token = $response_login_json2->token;
            if($token != ""){
                $curl_loadinfo = curl_init();
                
                curl_setopt_array($curl_loadinfo, array(
                  CURLOPT_URL => 'https://api.tamland.ir/api/user/loadInfo',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'GET',
                  CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: bearer '.$token,
                  ),
                ));
                
                $response_loadinfo = curl_exec($curl_loadinfo);
                
                curl_close($curl_loadinfo);
                
                $response_loadinfo_json = json_decode($response_loadinfo, true); // تبدیل به آرایه
                $response_loadinfo_json_objvars = $response_loadinfo_json['data'][0]; // دسترسی به اولین عنصر داده
                $fldUserGUID = $response_loadinfo_json_objvars['fldUserGUID'];
                $fldUserCo = $response_loadinfo_json_objvars['fldPkUserCo'];
                $user = get_user_by('login', $fldUserGUID );
                if (!$user) {
                    $user_id = wp_create_user($fldUserGUID, wp_generate_password(), $fldUserGUID . '@example.com');
                    $user = get_user_by('id', $user_id);
                }
                // ذخیره $fldUserCo در usermeta
                update_user_meta($user->ID, 'fldUserCo', $fldUserCo);
                
                wp_set_auth_cookie($user->ID);
                $expiration = time() + (3600 * 24 * 7); // 7 days from now
                setcookie('tamshToken', $token, $expiration, '/', 'tamyar.ir', true, true);

                wp_redirect(site_url().'/my-account/');
            }
        }

    }elseif( isset($_POST['signupmobile']) && isset($_POST['otp']) ) {
        $signupmobile = sanitize_text_field($_POST['signupmobile']);
        $otp = sanitize_text_field($_POST['otp']);

        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.tamland.ir/api/user/verifyCode',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "Mobile":"'.$signupmobile.'",
            "VerificationCode":"'.$otp.'"
        }
        ',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        
         $response_json = json_decode($response);
        $token_signup = $response_json->token;
        if($token_signup != ""){
            ?>
                  <script>
                      jQuery(document).ready(function(){
                          localStorage.setItem("tamshToken", "<?php echo $token_signup; ?>");
                          jQuery('input#mobile').remove();
                          jQuery('#response').html('<div class="lms-alert success-alert">ثبت نام شما با موفقیت انجام شد</div>');
                          jQuery('#send-otp').remove();
                          jQuery('#response').after('<button type="button" id="goToLogin">ورود به تام‌شاپ</button>');
                          
                          jQuery('#goToLogin').click(function(){
                              jQuery('#mobileInput').html('<input type="text" id="mobile" name="mobile" placeholder="شماره موبایل" required>');
                              jQuery('#mobileInput').after('<button type="submit" id="send-otp">ورود</button>');
                              jQuery('#goToLogin').remove();
                              jQuery('#response').empty();
                              
                          });
                      });
                  </script>
                 <?php
        }

    }
}
>>>>>>> cb98cdc711d2ea53ba0421fa77901203fc804fe4
add_action('wp_head', 'handle_custom_login_form_submission');