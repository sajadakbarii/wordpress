<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
date_default_timezone_set('Asia/Tehran');
error_log('Page Loaded (Line 8): '. print_r( $_POST, true ));
$entry_id = $_GET['entry_id'];
if(!empty($entry_id)){
$result = GFAPI::get_entry( $entry_id );
if(isset($_COOKIE['discountIsSet'])) {
    $discount_is_set = $_COOKIE['discountIsSet'];
}
if(!empty($_POST)){
$cookie_name = "sentapi";
if(!isset($_COOKIE[$cookie_name])) {
setcookie($cookie_name, 0, time() + (60 * 3), "/"); // 3 minutes
}
error_log('Cookie Set (Line 16): '. print_r($_COOKIE, true)); // Added error_log for cookie status
?>
<div class="container my-5">
    <div class="bill">
<?php
//print_r($_POST);
$gwtransaction_code = $_POST['RRN'];
gform_update_meta( $entry_id, 'gateway_transition_id', $gwtransaction_code );
error_log('Transaction Code Set (Line 24): '. print_r($gwtransaction_code, true)); // Log transaction code

$Amount = $result[6]."0";
$course_type = $result[9];
if($course_type == "دوره معمولی" || $course_type == "چند استاده" || $course_type == "آزمون"){
    $course_type_lms = 1;
}elseif($course_type == "بسته"){
    $course_type_lms = 2;
}
$date = new DateTime();
$payment_date_o = $date->format( 'd F Y H:i' );

if (isset($_POST['OrderId'])) 
				$order_id = $_POST['OrderId'];
				error_log('Order ID (Line 38): '. print_r($order_id, true)); // Log Order ID
				
				if ($order_id) {
					if(isset($_POST['status']) && $_POST['status'] == "0"){
					    error_log('Before Verify (Line 42): '. print_r( $_POST, true ));
						$corporationPin = "3F20B9936DD04AE6A9A7AFB98887A42D";
						$token=$_POST['Token'];
						$Amount = (int) str_replace(',', '', $_POST['Amount']);
                        error_log('Token (Line 45): '. print_r($token, true)); // Log Token before sending request
                        
                        error_log('Sending confirm payment request (Line 47): ' . print_r($confirmData, true));
						$confirmData = array(
							"CorporationPin" => $corporationPin,
							"Token" => $token,
						);

						// Encode the data as a JSON string
						$confirmPostFields = json_encode($confirmData);
                        error_log('Confirm Post Fields (Line 55): ' . print_r($confirmPostFields, true)); // Log the JSON data to be sent
                        
						$curl = curl_init();

						curl_setopt_array($curl, array(
							CURLOPT_URL => 'https://pna.shaparak.ir/mhipg/api/Payment/confirm',
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => '',
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 0,
							CURLOPT_FOLLOWLOCATION => true,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => 'POST',
							CURLOPT_POSTFIELDS =>$confirmPostFields,
							CURLOPT_HTTPHEADER => array(
								'Content-Type: application/json',
								'Cookie: cookiesession1=678B28A8A9990742D7412CE00BD0687F'
							),
						));

						$response = curl_exec($curl);
						error_log('CURL Response (Line 76): '. print_r($response, true)); // Log CURL response
						
						curl_close($curl);

						$confirmPaymentResponse = json_decode($response);
						
						$paymentInfo = get_object_vars($confirmPaymentResponse);
						error_log('After Verify (Line 83): '.print_r( $paymentInfo, true ));
						
                        if($paymentInfo['status'] == "0"){
                            
                            ?>
                            <script>
                            // گرفتن URL فعلی
                            const currentUrl = new URL(window.location.href);
                            
                            // اضافه کردن پارامتر جدید
                            currentUrl.searchParams.append('payment_status', '1'); // 'newParam' نام پارامتر و 'value' مقدار آن
                            
                            // اعمال URL جدید بدون reload
                            window.history.pushState({ path: currentUrl.href }, '', currentUrl.href);
                            </script>
                            <?php
			              $card_number = $paymentInfo['cardNumberMasked'];  
			              $payment_date = GF_jdate( 'd F Y ساعت H:i', strtotime( $payment_date_o ), '', date_default_timezone_get());
						
                        gform_update_meta( $entry_id, 'payment_status', 'Paid');
                        gform_update_meta( $entry_id, 'transaction_type', 1 );
                        gform_update_meta( $entry_id, 'payment_date', $payment_date );
                        gform_update_meta( $entry_id, 'card_number', $card_number );
                        $result = GFAPI::get_entry( $entry_id );
                        error_log("Result line 107: " . print_r($result, true));
                            //call api LMS
						$lmsdata = array(
						    "Name" => $result[1],
							"Mobile" => $result[2],
							"CourseId" => (int)$result[8],
							"Price" => $Amount,
							"Status" => 0,
							"TrackingCode" => $result['gateway_transition_id'],
							"Type" => (int)$course_type_lms,
							"DiscountCode" => isset($result[20]) ? $result[20] : "",
							"PaymentDate" => $payment_date_o,
							"WPCode" => (int)$order_id,
							"MaskedCardNumber" => $card_number,
							"UtmSource" => isset($result[11]) ? $result[11] : "",
							"UtmMedium" => isset($result[12]) ? $result[12] : "",
							"UtmChannel" => isset($result[13]) ? $result[13] : ""
						);
                    
						// Encode the data as a JSON string
						$lmsdatas = json_encode($lmsdata);
                        error_log('LMS Data Encoded (LINE 127): ' . print_r($lmsdatas, true));
                        
						$curl2 = curl_init();

						curl_setopt_array($curl2, array(
							CURLOPT_URL => 'https://api.tamland.ir/api/payment/savePayment',
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => '',
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 0,
							CURLOPT_FOLLOWLOCATION => true,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => 'POST',
							CURLOPT_POSTFIELDS =>$lmsdatas,
							CURLOPT_HTTPHEADER => array(
								'Content-Type: application/json',
								'Cookie: cookiesession1=678B28A8A9990742D7412CE00BD0687F'
							),
						));

						$lmsresponses = curl_exec($curl2);
                        
                        if ($lmsresponses === false) {
                            error_log("cURL error (Line 150): " . curl_error($curl2));
                        }
                        
						curl_close($curl2);
                        error_log('LMS Response (Line 154): ' . print_r($lmsresponses, true));
						$lmsresponsesjson = json_decode($lmsresponses);
						if (isset($lmsresponsesjson->data[0])) {
                            $lmspaymentInfo = get_object_vars($lmsresponsesjson->data[0]);
                            error_log('LMS Payment Info (Line 158): ' . print_r($lmspaymentInfo, true));
                        } else {
                            error_log("Invalid LMS API Response (Line 160): " . print_r($lmsresponsesjson, true));
                            $lmspaymentInfo = [];
                        }
						//$lmspaymentInfo = get_object_vars($lmsresponsesjson);
						//$lmspaymentInfo = get_object_vars($lmspaymentInfo['data'][0]);
						//error_log('After Send to LMS (Line 146): '.print_r( $lmsresponses, true ));
                            ?>
                            <div class="card">
                              <div class="card-header" style="text-align: center;padding-top: 15px;padding-bottom: 15px;background-color: #b1ffd3;">
                                <h5 class="card-title" style="color: #007533;margin:0">سفارش شما با موفقیت ثبت شد.</h5>
                              </div>
                              <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span>
                                                نام خریدار
                                            </span>
                                            <span>
                                                <?php
                                                    echo $result[1];
                                                ?>
                                            </span>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span>
                                                موبایل
                                            </span>
                                            <span>
                                                <?php
                                                    echo $result[2];
                                                ?>
                                            </span>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span>
                                                شماره سفارش
                                            </span>
                                            <span>
                                                <?php
                                                    echo $order_id;
                                                ?>
                                            </span>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span>
                                                دوره خریداری شده
                                            </span>
                                            <span>
                                                <?php
                                                    $course = explode('|',$result[7],-1);
                                                    echo $course[0];
                                                ?>
                                            </span>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span>
                                               مبلغ پرداخت شده
                                            </span>
                                            <span>
                                                <?php
                                                    echo substr($Amount, 0, -1).' تومان';
                                                ?>
                                            </span>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span>
                                               شماره پیگیری
                                            </span>
                                            <span>
                                                <?php
                                                    echo $gwtransaction_code;
                                                ?>
                                            </span>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span>
                                                زمان پرداخت
                                            </span>
                                            <span>
                                                <?php
                                                    echo $payment_date;
                                                ?>
                                            </span>
                                        </div>
                                    </li>
                                  </ul>
                              </div>
                            </div>
                        <?php
                        if($lmspaymentInfo['status'] == "1"){ ?>
                            <div class="alert alert-info my-3 text-center" role="alert">
                                <?php echo '<strong>'.$result[1].'</strong> عزیز، درس <strong>'.$course[0].'</strong> در حساب کاربری شما در تام‌لند ثبت گردید.<br> برای مشاهده وارد پنل کاربری تام‌لند شوید. <a href="https://lms.tamland.ir" class="alert-link btn btn-primary text-white">ورود به تام‌لند</a>' ?>
                            </div>
                        <?php
                        error_log('After submit in LMS (Line 267): '.print_r( $lmspaymentInfo, true ));
                            }else{

                            $curl3 = curl_init();
                            
                            curl_setopt_array($curl3, array(
                              CURLOPT_URL => 'https://api.tamland.ir/api/payment/savePaymentFailure/'.$order_id,
                              CURLOPT_RETURNTRANSFER => true,
                              CURLOPT_ENCODING => '',
                              CURLOPT_MAXREDIRS => 10,
                              CURLOPT_TIMEOUT => 0,
                              CURLOPT_FOLLOWLOCATION => true,
                              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                              CURLOPT_CUSTOMREQUEST => 'GET',
                            ));
                            
                            $response3 = curl_exec($curl3);
                            
                            if ($response3 === false) {
                                error_log('Curl error (Line 286): '.curl_error($curl3));
                            }
                            curl_close($curl3);

                                ?>
                                    <div class="alert alert-danger my-3 text-center" role="alert">
                                        <?php echo '<strong>'.$result[1].'</strong> عزیز، ثبت دوره در حساب کاربری شما با خطا مواجه شده است.<br>لطفا با به همراه داشتن شماره سفارش و شماره پیگیری به بخش پشتیبانی تام‌لند به شماره تماس <strong>02153344</strong> تماس بگیرید.<br>شماره پیگیری: '.$gwtransaction_code.'<br>شماره سفارش: '.$order_id; ?>
                                    </div>
                                <?php
						    $email_course = explode('|',$result[7],-1);
    						?>
    						<script>
    						    let mobile = "<?php echo $result[2]; ?>";
    						    let name = "<?php echo $result[1]; ?>";
    						    let courseTitle = "<?php echo $email_course[0]; ?>";
                                let formData = new FormData();
                                formData.append('status', 'ثبت دوره کاربر در پنل LMS با خطا مواجه شده است');
                                formData.append('name', name);
                                formData.append('mobile', mobile);
                                formData.append('courseTitle', courseTitle);
                                formData.append('action', 'send_contact_email'); // اضافه کردن اکشن به formData
                            
                                jQuery.ajax({
                                    type: 'POST',
                                    url: 'https://tamland.ir/wp-admin/admin-ajax.php', // فایل PHP که اطلاعات را پردازش می‌کند
                                    data: formData,
                                    processData: false, // غیرفعال کردن پردازش خودکار
                                    contentType: false, // غیرفعال کردن تنظیم خودکار نوع محتوا
                                    success: function(response) {
                                        console.log(response);
                                    },
                                    error: function(xhr, status, error) {
                                            error_log('AJAX Error (Line 318): ' + error);
                                    }
                                });
                            </script>
                        <?php
                        error_log('Error submit in LMS (Line 323): '.print_r( $lmspaymentInfo, true ));
                            }
                        }elseif($paymentInfo['status'] == "-2508" && $paymentInfo['appToken'] == "0"){
                            ?>
        					<div class="text-center">
    							<a href="<?php echo $result[10]; ?>" class="btn btn-danger text-white">بازگشت به صفحه دوره</a>
    						</div>
							<?php
							error_log('Error (Line 331): '.print_r( $paymentInfo, true ));
                        }else{
                            ?>
                            <script>
                            // گرفتن URL فعلی
                            const currentUrl = new URL(window.location.href);
                            
                            // اضافه کردن پارامتر جدید
                            currentUrl.searchParams.append('payment_status', '0'); // 'newParam' نام پارامتر و 'value' مقدار آن
                            
                            // اعمال URL جدید بدون reload
                            window.history.pushState({ path: currentUrl.href }, '', currentUrl.href);
                            </script>
                            <?php
                        if($_COOKIE[$cookie_name] == 0) {
                            gform_update_meta( $entry_id, 'payment_status', 'Failed');
                            $result = GFAPI::get_entry( $entry_id );
                            setcookie($cookie_name, 1, time() + (60 * 3), "/"); // 3 minutes
                            $lmsdata = array(
						    "Name" => $result[1],
							"Mobile" => $result[2],
							"CourseId" => (int)$result[8],
							"Price" => $Amount,
							"Status" => 17,
							"TrackingCode" => '',
							"Type" => $course_type_lms,
							"DiscountCode" => isset($result[20]) ? $result[20] : "",
							"PaymentDate" => $payment_date_o,
							"WPCode" => (int)$order_id,
							"MaskedCardNumber" => "",
							"UtmSource" => isset($result[11]) ? $result[11] : "",
							"UtmMedium" => isset($result[12]) ? $result[12] : "",
							"UtmChannel" => isset($result[13]) ? $result[13] : ""
						);
                        error_log('LMS data (Line 364): '.print_r($lmsdata, true));
						// Encode the data as a JSON string
						$lmsdatas = json_encode($lmsdata);
                        error_log('Encoded LMS data (Line 367): ' . print_r($lmsdatas, true));
						$curl2 = curl_init();

						curl_setopt_array($curl2, array(
							CURLOPT_URL => 'https://api.tamland.ir/api/payment/savePayment',
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => '',
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 0,
							CURLOPT_FOLLOWLOCATION => true,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => 'POST',
							CURLOPT_POSTFIELDS =>$lmsdatas,
							CURLOPT_HTTPHEADER => array(
								'Content-Type: application/json',
								'Cookie: cookiesession1=678B28A8A9990742D7412CE00BD0687F'
							),
						));

						$lmsresponses = curl_exec($curl2);
                        error_log('LMS API response (Line 387): ' . print_r($lmsresponses, true));
                        
                        if (curl_errno($curl2)) {
                            error_log('cURL Error (Line 390): ' . curl_error($curl2));
                        }

						curl_close($curl2);

						$lmsresponsesjson = json_decode($lmsresponses);
						error_log('Decoded LMS response (Line 396): ' . print_r($lmsresponsesjson, true));
						if (!$lmsresponsesjson) {
                            error_log('Error: Invalid LMS response. (Line 398)');
                        } else {
                            error_log('LMS Response is valid. (Line 400)');
                        }
							?>
							<div class="alert alert-danger my-3 text-center" role="alert">
        					    <p class="mb-0">
    						        	پرداخت ناموفق !
    						    </p>
        					</div>
        					<div class="text-center">
    							<a href="<?php echo $result[10]; ?>" class="btn btn-danger text-white">بازگشت به صفحه دوره</a>
    						</div>
							<?php
							error_log('Error (Line 412): '.print_r( $lmsresponses, true ));
                        }
                    }
                        
                    } elseif(isset($_POST['status']) && $_POST['status'] == "-138"){
                        ?>
                        <script>
                        // گرفتن URL فعلی
                        const currentUrl = new URL(window.location.href);
                        
                        // اضافه کردن پارامتر جدید
                        currentUrl.searchParams.append('payment_status', '0'); // 'newParam' نام پارامتر و 'value' مقدار آن
                        
                        // اعمال URL جدید بدون reload
                        window.history.pushState({ path: currentUrl.href }, '', currentUrl.href);
                        </script>
                        <?php
                        if($_COOKIE[$cookie_name] == 0) {
                            gform_update_meta( $entry_id, 'payment_status', 'Cancelled');
                            $result = GFAPI::get_entry( $entry_id );
                            setcookie($cookie_name, 1, time() + (60 * 3), "/"); // 3 minutes
                         //call api LMS
						$lmsdata = array(
						    "Name" => $result[1],
							"Mobile" => $result[2],
							"CourseId" => (int)$result[8],
							"Price" => $Amount,
							"Status" => 17,
							"TrackingCode" => '',
							"Type" => $course_type_lms,
							"DiscountCode" => isset($result[20]) ? $result[20] : "",
							"PaymentDate" => $payment_date_o,
							"WPCode" => (int)$order_id,
							"MaskedCardNumber" => "",
							"UtmSource" => isset($result[11]) ? $result[11] : "",
							"UtmMedium" => isset($result[12]) ? $result[12] : "",
							"UtmChannel" => isset($result[13]) ? $result[13] : ""
						);

						// Encode the data as a JSON string
						$lmsdatas = json_encode($lmsdata);
                        error_log('Encoded LMS data for cancelled payment (Line 452): ' . print_r($lmsdatas, true));
                        
						$curl2 = curl_init();

						curl_setopt_array($curl2, array(
							CURLOPT_URL => 'https://api.tamland.ir/api/payment/savePayment',
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => '',
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 0,
							CURLOPT_FOLLOWLOCATION => true,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => 'POST',
							CURLOPT_POSTFIELDS =>$lmsdatas,
							CURLOPT_HTTPHEADER => array(
								'Content-Type: application/json',
								'Cookie: cookiesession1=678B28A8A9990742D7412CE00BD0687F'
							),
						));

						$lmsresponses = curl_exec($curl2);
                        error_log('LMS API response for cancelled payment (Line 473): ' . print_r($lmsresponses, true));
                        if (curl_errno($curl2)) {
                            error_log('cURL Error (Line 475): ' . curl_error($curl2));
                        }
						curl_close($curl2);

						$lmsresponsesjson = json_decode($lmsresponses);
                        error_log('Decoded LMS response for cancelled payment (Line 480): ' . print_r($lmsresponsesjson, true));
                        if (!$lmsresponsesjson) {
                            error_log('Error: Invalid LMS response for cancelled payment. (Line 482)');
                        } else {
                            error_log('LMS Response is valid for cancelled payment. (Line 484)');
                        }

						?>
						<div class="alert alert-danger my-3 text-center" role="alert">
						    <p class="mb-0">
						        	انصراف از پرداخت !
						    </p>
						</div>
						<div class="alert alert-warning my-3 text-center" role="alert">
						    <p>
						        	در صورتی که نیاز به راهنمایی دارید، کارشناس‌های تام‌لند به شما کمک خواهند کرد.
						    </p>
						    <p class="mb-0">
						        شماره تماس: <strong style="display:inline-block;direction:ltr">021 - 53344</strong>
						    </p>
						</div>
						<div class="text-center">
						    <a href="tel:02153344" class="btn btn-primary text-white">نیاز به راهنمایی دارم</a>
							<a href="<?php echo $result[10]; ?>" class="btn btn-danger text-white">بازگشت به صفحه دوره</a>
						</div>
						<?php
						error_log('Cancelled (Line 506): '.print_r( $lmsresponses, true ));
                    }
					} else {
					    
                        if($_COOKIE[$cookie_name] == 0) {
                            gform_update_meta( $entry_id, 'payment_status', 'Failed');
                            $result = GFAPI::get_entry( $entry_id );
                            setcookie($cookie_name, 1, time() + (60 * 3), "/"); // 3 minutes
					    $lmsdata = array(
						    "Name" => $result[1],
							"Mobile" => $result[2],
							"CourseId" => (int)$result[8],
							"Price" => $Amount,
							"Status" => 17,
							"TrackingCode" => '',
							"Type" => $course_type_lms,
							"DiscountCode" => isset($result[20]) ? $result[20] : "",
							"PaymentDate" => $payment_date_o,
							"WPCode" => (int)$order_id,
							"MaskedCardNumber" => "",
							"UtmSource" => isset($result[11]) ? $result[11] : "",
							"UtmMedium" => isset($result[12]) ? $result[12] : "",
							"UtmChannel" => isset($result[13]) ? $result[13] : ""
						);

						// Encode the data as a JSON string
						$lmsdatas = json_encode($lmsdata);
                        error_log('CURL Error (Line 532): ' . $lmsdatas);
                        
						$curl2 = curl_init();

						curl_setopt_array($curl2, array(
							CURLOPT_URL => 'https://api.tamland.ir/api/payment/savePayment',
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => '',
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 0,
							CURLOPT_FOLLOWLOCATION => true,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => 'POST',
							CURLOPT_POSTFIELDS =>$lmsdatas,
							CURLOPT_HTTPHEADER => array(
								'Content-Type: application/json',
								'Cookie: cookiesession1=678B28A8A9990742D7412CE00BD0687F'
							),
						));

						$lmsresponses = curl_exec($curl2);
                        if ($lmsresponses === false) {
                            error_log('CURL Error (Line 554): ' . curl_error($curl2));
                        }
						curl_close($curl2);

						$lmsresponsesjson = json_decode($lmsresponses);
                        if ($lmsresponsesjson === null) {
                            error_log('JSON Decode Error (Line 560): ' . json_last_error_msg());
                        }
                    ?>
    					<div class="alert alert-danger my-3 text-center" role="alert">
    					    <p class="mb-0">
						        	پرداخت ناموفق !
						    </p>
    					</div>
    					<div class="text-center">
							<a href="<?php echo $result[10]; ?>" class="btn btn-danger text-white">بازگشت به صفحه دوره</a>
						</div>
					<?php
					error_log('Failed (Line 572): '.print_r( $lmsresponses, true ));
					} 
				}
				} else {
                    if($_COOKIE[$cookie_name] == 0) {
                    setcookie($cookie_name, 1, time() + (60 * 3), "/"); // 3 minutes
                    gform_update_meta( $entry_id, 'payment_status', 'Failed');
                    $result = GFAPI::get_entry( $entry_id );
				    $lmsdata = array(
						    "Name" => $result[1],
							"Mobile" => $result[2],
							"CourseId" => (int)$result[8],
							"Price" => $Amount,
							"Status" => 17,
							"TrackingCode" => '',
							"Type" => $course_type_lms,
							"DiscountCode" => isset($result[20]) ? $result[20] : "",
							"PaymentDate" => $payment_date_o,
							"WPCode" => (int)$order_id,
							"MaskedCardNumber" => "",
							"UtmSource" => isset($result[11]) ? $result[11] : "",
							"UtmMedium" => isset($result[12]) ? $result[12] : "",
							"UtmChannel" => isset($result[13]) ? $result[13] : ""
						);

						// Encode the data as a JSON string
						$lmsdatas = json_encode($lmsdata);

						$curl2 = curl_init();

						curl_setopt_array($curl2, array(
							CURLOPT_URL => 'https://api.tamland.ir/api/payment/savePayment',
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => '',
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 0,
							CURLOPT_FOLLOWLOCATION => true,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => 'POST',
							CURLOPT_POSTFIELDS =>$lmsdatas,
							CURLOPT_HTTPHEADER => array(
								'Content-Type: application/json',
								'Cookie: cookiesession1=678B28A8A9990742D7412CE00BD0687F'
							),
						));

						$lmsresponses = curl_exec($curl2);
                        if ($lmsresponses === false) {
                            error_log('CURL Error (Line 619): ' . curl_error($curl2));
                        }
						curl_close($curl2);

						$lmsresponsesjson = json_decode($lmsresponses);
                        if ($lmsresponsesjson === null) {
                            error_log('JSON Decode Error (Line 625): ' . json_last_error_msg());
                        }
                ?>
    				<div class="alert alert-danger my-3 text-center" role="alert">
    					    <p class="mb-0">
						        	پرداخت ناموفق !
						    </p>
    					</div>
    					<div class="text-center">
							<a href="<?php echo $result[10]; ?>" class="btn btn-danger text-white">بازگشت به صفحه دوره</a>
						</div>
				<?php
				error_log('Failed (Line 637): '.print_r( $lmsresponses, true ));
				}
				}
			}elseif(!empty($result[20]) && $result[18] == '0' && $discount_is_set == true){
			    $cookie_name = "sentapi";
                if(!isset($_COOKIE[$cookie_name])) {
                    setcookie($cookie_name, 0, time() + (60 * 3), "/"); // 3 minutes
                }
                error_log('Cookie Set (Line 16): '. print_r($_COOKIE, true)); // Added error_log for cookie status
                ?>
                <div class="container my-5">
                    <div class="bill">
                <?php
                $Amount = '0';
                $card_number = '';
                $course_type = $result[9];
                if($course_type == "دوره معمولی" || $course_type == "چند استاده" || $course_type == "آزمون"){
                    $course_type_lms = 1;
                }elseif($course_type == "بسته"){
                    $course_type_lms = 2;
                }
                $date = new DateTime();
                $payment_date_o = $date->format( 'd F Y H:i' );
                
                $order_id = $result['id'];
                error_log('Order ID (Line 38): '. print_r($order_id, true)); // Log Order ID
                
                $payment_date = GF_jdate( 'd F Y ساعت H:i', strtotime( $payment_date_o ), '', date_default_timezone_get());
                						
                                        gform_update_meta( $entry_id, 'payment_status', 'Paid');
                                        gform_update_meta( $entry_id, 'transaction_type', 1 );
                                        gform_update_meta( $entry_id, 'payment_date', $payment_date );
                                            //call api LMS
                						$lmsdata = array(
                						    "Name" => $result[1],
                							"Mobile" => $result[2],
                							"CourseId" => (int)$result[8],
                							"Price" => $Amount,
                							"Status" => 0,
                							"TrackingCode" => $result['gateway_transition_id'],
                							"Type" => (int)$course_type_lms,
                							"DiscountCode" => isset($result[20]) ? $result[20] : "",
                							"PaymentDate" => $payment_date_o,
                							"WPCode" => (int)$order_id,
                							"MaskedCardNumber" => $card_number,
                							"UtmSource" => isset($result[11]) ? $result[11] : "",
                							"UtmMedium" => isset($result[12]) ? $result[12] : "",
                							"UtmChannel" => isset($result[13]) ? $result[13] : ""
                						);
                                    
                						// Encode the data as a JSON string
                						$lmsdatas = json_encode($lmsdata);
                                        error_log('LMS Data Encoded (LINE 127): ' . print_r($lmsdatas, true));
                                        
                						$curl2 = curl_init();
                
                						curl_setopt_array($curl2, array(
                							CURLOPT_URL => 'https://api.tamland.ir/api/payment/savePayment',
                							CURLOPT_RETURNTRANSFER => true,
                							CURLOPT_ENCODING => '',
                							CURLOPT_MAXREDIRS => 10,
                							CURLOPT_TIMEOUT => 0,
                							CURLOPT_FOLLOWLOCATION => true,
                							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                							CURLOPT_CUSTOMREQUEST => 'POST',
                							CURLOPT_POSTFIELDS =>$lmsdatas,
                							CURLOPT_HTTPHEADER => array(
                								'Content-Type: application/json',
                								'Cookie: cookiesession1=678B28A8A9990742D7412CE00BD0687F'
                							),
                						));
                
                						$lmsresponses = curl_exec($curl2);
                                        
                                        if ($lmsresponses === false) {
                                            error_log("cURL error (Line 150): " . curl_error($curl2));
                                        }
                                        
                						curl_close($curl2);
                                        error_log('LMS Response (Line 154): ' . print_r($lmsresponses, true));
                						$lmsresponsesjson = json_decode($lmsresponses);
                						if (isset($lmsresponsesjson->data[0])) {
                                            $lmspaymentInfo = get_object_vars($lmsresponsesjson->data[0]);
                                            error_log('LMS Payment Info (Line 158): ' . print_r($lmspaymentInfo, true));
                                        } else {
                                            error_log("Invalid LMS API Response (Line 160): " . print_r($lmsresponsesjson, true));
                                            $lmspaymentInfo = [];
                                        }
                						//$lmspaymentInfo = get_object_vars($lmsresponsesjson);
                						//$lmspaymentInfo = get_object_vars($lmspaymentInfo['data'][0]);
                						//error_log('After Send to LMS (Line 146): '.print_r( $lmsresponses, true ));
                                            ?>
                                            <div class="card">
                                              <div class="card-header" style="text-align: center;padding-top: 15px;padding-bottom: 15px;background-color: #b1ffd3;">
                                                <h5 class="card-title" style="color: #007533;margin:0">سفارش شما با موفقیت ثبت شد.</h5>
                                              </div>
                                              <div class="card-body">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item">
                                                        <div class="d-flex justify-content-between">
                                                            <span>
                                                                نام خریدار
                                                            </span>
                                                            <span>
                                                                <?php
                                                                    echo $result[1];
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="d-flex justify-content-between">
                                                            <span>
                                                                موبایل
                                                            </span>
                                                            <span>
                                                                <?php
                                                                    echo $result[2];
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="d-flex justify-content-between">
                                                            <span>
                                                                شماره سفارش
                                                            </span>
                                                            <span>
                                                                <?php
                                                                    echo $order_id;
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="d-flex justify-content-between">
                                                            <span>
                                                                دوره خریداری شده
                                                            </span>
                                                            <span>
                                                                <?php
                                                                    $course = explode('|',$result[7],-1);
                                                                    echo $course[0];
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="d-flex justify-content-between">
                                                            <span>
                                                               مبلغ پرداخت شده
                                                            </span>
                                                            <span>
                                                                رایگان
                                                            </span>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="d-flex justify-content-between">
                                                            <span>
                                                                زمان خرید دوره
                                                            </span>
                                                            <span>
                                                                <?php
                                                                    echo $payment_date;
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="d-flex justify-content-between">
                                                            <span>
                                                                کد تخفیف ثبت شده
                                                            </span>
                                                            <span>
                                                                <?php
                                                                    echo $result['20'];
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </li>
                                                  </ul>
                                              </div>
                                            </div>
                                        <?php
                                        if($lmspaymentInfo['status'] == "1"){ ?>
                                            <div class="alert alert-info my-3 text-center" role="alert">
                                                <?php echo '<strong>'.$result[1].'</strong> عزیز، درس <strong>'.$course[0].'</strong> در حساب کاربری شما در تام‌لند ثبت گردید.<br> برای مشاهده وارد پنل کاربری تام‌لند شوید. <a href="https://lms.tamland.ir" class="alert-link btn btn-primary text-white">ورود به تام‌لند</a>' ?>
                                            </div>
                                        <?php
                                        error_log('After submit in LMS (Line 267): '.print_r( $lmspaymentInfo, true ));
                                            }else{
                
                                            $curl3 = curl_init();
                                            
                                            curl_setopt_array($curl3, array(
                                              CURLOPT_URL => 'https://api.tamland.ir/api/payment/savePaymentFailure/'.$order_id,
                                              CURLOPT_RETURNTRANSFER => true,
                                              CURLOPT_ENCODING => '',
                                              CURLOPT_MAXREDIRS => 10,
                                              CURLOPT_TIMEOUT => 0,
                                              CURLOPT_FOLLOWLOCATION => true,
                                              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                              CURLOPT_CUSTOMREQUEST => 'GET',
                                            ));
                                            
                                            $response3 = curl_exec($curl3);
                                            
                                            if ($response3 === false) {
                                                error_log('Curl error (Line 286): '.curl_error($curl3));
                                            }
                                            curl_close($curl3);
                
                                                ?>
                                                    <div class="alert alert-danger my-3 text-center" role="alert">
                                                        <?php echo '<strong>'.$result[1].'</strong> عزیز، ثبت دوره در حساب کاربری شما با خطا مواجه شده است.<br>لطفا با به همراه داشتن شماره سفارش و شماره پیگیری به بخش پشتیبانی تام‌لند به شماره تماس <strong>02153344</strong> تماس بگیرید.<br>شماره پیگیری: '.$gwtransaction_code.'<br>شماره سفارش: '.$order_id; ?>
                                                    </div>
                                                <?php
                						    $email_course = explode('|',$result[7],-1);
                    						?>
                    						<script>
                    						    let mobile = "<?php echo $result[2]; ?>";
                    						    let name = "<?php echo $result[1]; ?>";
                    						    let courseTitle = "<?php echo $email_course[0]; ?>";
                                                let formData = new FormData();
                                                formData.append('status', 'ثبت دوره کاربر در پنل LMS با خطا مواجه شده است');
                                                formData.append('name', name);
                                                formData.append('mobile', mobile);
                                                formData.append('courseTitle', courseTitle);
                                                formData.append('action', 'send_contact_email'); // اضافه کردن اکشن به formData
                                            
                                                jQuery.ajax({
                                                    type: 'POST',
                                                    url: 'https://tamland.ir/wp-admin/admin-ajax.php', // فایل PHP که اطلاعات را پردازش می‌کند
                                                    data: formData,
                                                    processData: false, // غیرفعال کردن پردازش خودکار
                                                    contentType: false, // غیرفعال کردن تنظیم خودکار نوع محتوا
                                                    success: function(response) {
                                                        console.log(response);
                                                    },
                                                    error: function(xhr, status, error) {
                                                            error_log('AJAX Error (Line 318): ' + error);
                                                    }
                                                });
                                            </script>
                                        <?php
                                        error_log('Error submit in LMS (Line 323): '.print_r( $lmspaymentInfo, true ));
                                            }
                                        
			}else{
			    header("Location: $result[10]");
                exit;
			}

?>
</div>
</div>
<?php
}
else{
    ?>
    <div class="container my-5">
        <div class="bill">
            <div class="alert alert-danger" role="alert">
              درخواست شما نامعتبر است.
            </div>
        </div>
    </div>
    <?php
}
get_footer();

?>
