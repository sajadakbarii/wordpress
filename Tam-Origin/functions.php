<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.2' );

$secret_key = defined('TAMLAND_PURCHASE_SECTRET_KEY') ? TAMLAND_PURCHASE_SECTRET_KEY : '';
$TAMLAND_PURCHASE_USR = defined('TAMLAND_PURCHASE_USR') ? TAMLAND_PURCHASE_USR : '';
$TAMLAND_MID1_PSW = defined('TAMLAND_MID1_PSW') ? TAMLAND_MID1_PSW : '';
$TAMLAND_MID2_PSW = defined('TAMLAND_MID2_PSW') ? TAMLAND_MID2_PSW : '';
$TAMLAND_KONKOOR_PSW = defined('TAMLAND_KONKOOR_PSW') ? TAMLAND_KONKOOR_PSW : '';
$TAMLAND_TIZHOOSHAN_PSW = defined('TAMLAND_TIZHOOSHAN_PSW') ? TAMLAND_TIZHOOSHAN_PSW : '';

function child_enqueue_styles() {
    wp_enqueue_style( 'bootstrap', get_stylesheet_directory_uri(). '/assets/css/bootstrap.min.css' );
    wp_enqueue_style( 'bootstrap-rtl', get_stylesheet_directory_uri(). '/assets/css/bootstrap.rtl.min.css' );
    wp_enqueue_style( 'bootstrap-utilities', get_stylesheet_directory_uri(). '/assets/css/bootstrap-utilities.min.css' );
    wp_enqueue_style( 'bootstrap-utilities-rtl', get_stylesheet_directory_uri(). '/assets/css/bootstrap-utilities.rtl.min.css' );
    wp_enqueue_style( 'owl-carousel', get_stylesheet_directory_uri(). '/assets/css/owl.carousel.min.css' );
	wp_enqueue_style( 'kc-fab', get_stylesheet_directory_uri(). '/assets/css/kc.fab.css' );
	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), '2.8.35', 'all' );
	
	wp_enqueue_script( 'bootstrap', get_stylesheet_directory_uri(). '/assets/js/bootstrap.min.js' , array(), '5.3.2', true );
	wp_enqueue_script( 'owl-carousel', get_stylesheet_directory_uri(). '/assets/js/owl.carousel.min.js' , array(), '2.3.4', true );
	wp_enqueue_script( 'kc-fab', get_stylesheet_directory_uri(). '/assets/js/kc.fab.min.js' , array(), '', true );
	wp_enqueue_script('java', get_stylesheet_directory_uri() . '/assets/js/java.js', array(), '1.6.29', true);
}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );


function deregister_unnecessary_styles(){
    wp_dequeue_style('rs-roboto');
    wp_deregister_style('rs-roboto');
    
    wp_dequeue_style('tp-material-icons');
    wp_deregister_style('tp-material-icons');
    
    if(is_front_page()){
        //post views
        wp_dequeue_style('post-views-counter-frontend');
        wp_deregister_style('post-views-counter-frontend');
        
        //post category slider
        wp_dequeue_style('wpos-slick-style');
        wp_deregister_style('wpos-slick-style');
        wp_dequeue_style('pciwgas-publlic-style');
        wp_deregister_style('pciwgas-publlic-style');
        
        //Newsletter
        wp_dequeue_style('newsletter');
        wp_deregister_style('newsletter');
        
        //duplicate post
        wp_dequeue_style('duplicate-post');
        wp_deregister_style('duplicate-post');

    }

}
add_action('wp_print_styles','deregister_unnecessary_styles');

/**
 * Filter for Change comment textarea placeholder
*/ 
add_filter( 'comment_form_defaults', 'textarea_placeholder' );
function textarea_placeholder( $fields ) {
    $fields['comment_field'] = str_replace(
        '<textarea',
        '<textarea placeholder="سوال شما"',
        $fields['comment_field']
    );

	return $fields;
}


/**
 * Teacher Courses Schedule
 */
/*add_shortcode('teacher_courses_schedule','teacher_courses_schedule_func');
function teacher_courses_schedule_func() {
    if (!is_singular('teacher')) return;

    $teacher_courses_schedule = get_post_meta(get_the_ID(), 'teacher-courses-schedule', true);

    if (!empty($teacher_courses_schedule)) {
        echo '<table class="teacher-courses-schedule-table">
                <tr>
                    <th>عنوان درس</th>
                    <th>روزهای هفته</th>
                    <th>ساعت کلاسی</th>
                </tr>';

        foreach ($teacher_courses_schedule as $schedule) {
            echo '<tr>
                    <td>' . esc_html($schedule['courses-sch-name']) . '</td>
                    <td>' . esc_html($schedule['courses-sch-day']) . '</td>
                    <td>' . esc_html($schedule['courses-sch-start-time']) . ' تا ' . esc_html($schedule['courses-sch-end-time']) . '</td>
                  </tr>';
        }

        echo '</table>';
    } else {
        echo '<p style="color:#fff">هنوز برنامه کلاسی استاد مشخص نشده است.</p>';
    }
}*/
class TeacherCoursesSchedule {
    // متغیر برای ذخیره آیدی پست استاد
    private $teacher_id;

    // سازنده کلاس که آیدی پست را می‌گیرد
    public function __construct($teacher_id) {
        $this->teacher_id = $teacher_id;
    }

    // تابعی برای دریافت برنامه کلاسی استاد
    public function get_schedule() {
        // دریافت برنامه کلاس از متا داده
        $teacher_courses_schedule = get_post_meta($this->teacher_id, 'teacher-courses-schedule', true);

        // اگر برنامه کلاس موجود بود
        if (!empty($teacher_courses_schedule)) {
            $output = '<table class="teacher-courses-schedule-table">
                        <tr>
                            <th>عنوان درس</th>
                            <th>روزهای هفته</th>
                            <th>ساعت کلاسی</th>
                        </tr>';

            // ایجاد ردیف‌ها برای هر برنامه کلاسی
            foreach ($teacher_courses_schedule as $schedule) {
                $output .= '<tr>
                            <td>' . esc_html($schedule['courses-sch-name']) . '</td>
                            <td>' . esc_html($schedule['courses-sch-day']) . '</td>
                            <td>' . esc_html($schedule['courses-sch-start-time']) . ' تا ' . esc_html($schedule['courses-sch-end-time']) . '</td>
                          </tr>';
            }

            $output .= '</table>';
        } else {
            $output = '<p style="color:#fff">هنوز برنامه کلاسی استاد مشخص نشده است.</p>';
        }

        return $output;
    }

    // تابعی برای نمایش برنامه کلاس‌ها
    public function display_schedule() {
        echo $this->get_schedule();
    }
}

// ثبت شورت کد
add_shortcode('teacher_courses_schedule', 'teacher_courses_schedule_func');

// تابع شورت کد
function teacher_courses_schedule_func() {
    // اگر پست از نوع "teacher" نباشد، تابع اجرا نمی‌شود
    if (!is_singular('teacher')) return;

    // دریافت آیدی پست استاد
    $teacher_id = get_the_ID();

    // ایجاد نمونه از کلاس
    $schedule = new TeacherCoursesSchedule($teacher_id);

    // نمایش برنامه کلاس‌ها
    $schedule->display_schedule();
}

/**
 * Shortcode: [teacher_courses_video_sample] 
*/
add_shortcode('teacher_courses_video_sample','teacher_courses_video_sample_func');
function teacher_courses_video_sample_func(){
    $teacher_sample_courses = get_post_meta( get_the_ID(), 'teacher-sample-courses', true );
    
    if(!empty($teacher_sample_courses)){
        ?>
        <div class="teacher-courses-video-sample-place">
            <div class="teacher-courses-video-sample-wrapper d-flex align-items-center">
                <div class="teacher-courses-video-sample-owl-next">
                    <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.57812 11L6.57812 6L1.57812 1" stroke="#2D3748" stroke-width="1.42857" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="owl-carousel" id="teacher-courses-video-sample">
                    <?php foreach($teacher_sample_courses as $key => $course): ?>
                    <div>
                        <div class="item-box">
                            <div class="h_iframe-aparat_embed_frame">
                                <iframe src="https://www.aparat.com/video/video/embed/videohash/<?php echo esc_html($course['teacher-sample-video-aparat-code']); ?>/vt/frame" allowFullScreen="true" webkitallowfullscreen="true" mozallowfullscreen="true"></iframe>
                            </div>
                            <h4><?php echo esc_html($course['teacher-sample-video-title']); ?></h4>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="teacher-courses-video-sample-owl-prev">
                    <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.4375 1L1.4375 6L6.4375 11" stroke="#2D3748" stroke-width="1.42857" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function(){
                var $carousel = jQuery("#teacher-courses-video-sample");

                if($carousel.find('.owl-nav').hasClass('disabled')){
                    jQuery('.teacher-courses-video-sample-owl-next, .teacher-courses-video-sample-owl-prev').addClass('d-none');
                }

                jQuery('.teacher-courses-video-sample-owl-next').click(function(){
                    $carousel.trigger('next.owl.carousel');
                });

                jQuery('.teacher-courses-video-sample-owl-prev').click(function(){
                    $carousel.trigger('prev.owl.carousel');
                });

                $carousel.owlCarousel({
                    rtl:true,
                    loop:false,
                    margin:30,
                    merge:true,
                    nav:true,
                    autoplay:true,
                    responsiveClass:true,
                    mouseDrag:false,
                    responsive:{
                        0: { items: 1 },
                        600: { items: 2 },
                        1000: { items: 3 }
                    }
                });
            });
        </script>
        <?php
    }
}

/**
 * Shortcode: [teacher_landing_current_courses]
 */
add_shortcode('teacher_landing_current_courses','teacher_landing_current_courses_func');
function teacher_landing_current_courses_func(){
    /*
    global $secret_key;
   $teacher_current_courses = get_post_meta(get_the_ID(), 'teacher-current-courses', true); 
   if (!$teacher_current_courses) return;  // اطمینان از وجود داده‌ها
   ?>
    <div class="teacher-landing-current-courses-place container-fluid">
        <div class="row" id="teacher-landing-current-courses">
            <?php foreach ($teacher_current_courses as $key => $course) : 
                if($course['course-type'] == 'normal-course'){
                    $course_type = 'دوره معمولی';
                }elseif($course['course-type'] == 'multi-teacher'){
                    $course_type = 'چند استاده';
                }elseif($course['course-type'] == 'course-pack'){
                    $course_type = 'بسته';
                }
                $price_token = (int) preg_replace('/[^\d]/', '', $course['price_tax']);
                $token = hash_hmac('sha256', $course['course_id_lms'] . '|' . $price_token, $secret_key);
            ?>
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="teacher-landing-current-courses-card">
                        <?php if (!empty($course['teacher-current-courses-image'])) : ?>
                            <div class="teacher-current-courses-image sa-view-more">
                                <form method="post" action="https://mid1.tamland.ir/course-checkout">
                                    <button type="submit" style="background:transparent;padding:0;width:100%" id="teacher-landing-current-courses-img">
                                        <img src="<?php echo esc_url($course['teacher-current-courses-image']); ?>" alt="Course Image">
                                    </button>
                                    <input type="hidden" name="course_id" value="<?php echo esc_attr($course['course_id']); ?>">
                                    <input type="hidden" name="course_id_lms" value="<?php echo esc_attr($course['course_id_lms']); ?>">
                                    <input type="hidden" name="ref_url_payment" value="<?php echo esc_attr($course['teacher-current-courses-link']); ?>">
                                    <input type="hidden" name="course_type" value="<?php echo esc_attr($course_type); ?>">
                                    <input type="hidden" name="course_name_0" value="<?php echo esc_attr(str_replace("|", "-", $course['teacher-current-courses-title'] . ' ' . $course['secound-title'] . ' ' . get_the_title())); ?>">
                                    <input type="hidden" name="course_price_0" value="<?php echo esc_attr($course['price_tax']); ?>">
                                    <input type="hidden" name="course_numbers" value="1">
                                    <input type="hidden" name="installments" value="<?php echo esc_attr($course['installments']); ?>">
                                    <input type="hidden" name="secure_token" value="<?= $token ?>">
                                </form>
                            </div>
                        <?php endif; ?>

                        <div class="px-2 mb-3">
                            <?php if (!empty($course['teacher-current-courses-title'])) : ?>
                                <form method="post" action="https://mid1.tamland.ir/course-checkout">
                                    <button type="submit" style="background:transparent;padding:0" id="teacher-landing-current-courses-title">
                                        <h2><?php echo esc_html($course['teacher-current-courses-title']); ?></h2>
                                    </button>
                                    <input type="hidden" name="course_id" value="<?php echo esc_attr($course['course_id']); ?>">
                                    <input type="hidden" name="course_id_lms" value="<?php echo esc_attr($course['course_id_lms']); ?>">
                                    <input type="hidden" name="ref_url_payment" value="<?php echo esc_attr($course['teacher-current-courses-link']); ?>">
                                    <input type="hidden" name="course_type" value="<?php echo esc_attr($course_type); ?>">
                                    <input type="hidden" name="course_name_0" value="<?php echo esc_attr(str_replace("|", "-", $course['teacher-current-courses-title'] . ' ' . $course['secound-title'] . ' ' . get_the_title())); ?>">
                                    <input type="hidden" name="course_price_0" value="<?php echo esc_attr($course['price_tax']); ?>">
                                    <input type="hidden" name="course_numbers" value="1">
                                    <input type="hidden" name="installments" value="<?php echo esc_attr($course['installments']); ?>">
                                    <input type="hidden" name="secure_token" value="<?= $token ?>">
                                </form>
                            <?php endif; ?>

                            <?php if (!empty($course['secound-title'])) : ?>
                                <span class="secound-title"><?php echo esc_html($course['secound-title']); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="px-2">
                                <div class="row w-100 mx-0 align-items-center pt-2" style="border-top:1px solid rgba(206, 190, 190, 1)">
                                    <?php if (!empty($course['price_tax'])) : ?>
                                    <div class="col-3 px-1">
                                        <form method="post" action="https://mid1.tamland.ir/course-checkout">
                                            <button type="submit" class="add-to-cart-button-landing cart-icon"  id="teacher-landing-current-courses-buy-button">
                                                <img src="https://tamland.ir/wp-content/uploads/2025/03/Shopping-cart.svg">
                                            </button>
                                            <input type="hidden" name="course_id" value="<?php echo esc_attr($course['course_id']); ?>">
                                            <input type="hidden" name="course_id_lms" value="<?php echo esc_attr($course['course_id_lms']); ?>">
                                            <input type="hidden" name="ref_url_payment" value="<?php echo esc_attr($course['teacher-current-courses-link']); ?>">
                                            <input type="hidden" name="course_type" value="<?php echo esc_attr($course_type); ?>">
                                            <input type="hidden" name="course_name_0" value="<?php echo esc_attr(str_replace("|", "-", $course['teacher-current-courses-title'] . ' ' . $course['secound-title'] . ' ' . get_the_title())); ?>">
                                            <input type="hidden" name="course_price_0" value="<?php echo esc_attr($course['price_tax']); ?>">
                                            <input type="hidden" name="course_numbers" value="1">
                                            <input type="hidden" name="utm_source" value="<?php echo htmlspecialchars($_GET['utm_source'] ?? ''); ?>">
                                            <input type="hidden" name="utm_medium" value="<?php echo htmlspecialchars($_GET['utm_medium'] ?? ''); ?>">
                                            <input type="hidden" name="utm_campaign" value="<?php echo htmlspecialchars($_GET['utm_campaign'] ?? ''); ?>">
                                            <input type="hidden" name="utm_term" value="<?php echo htmlspecialchars($_GET['utm_term'] ?? ''); ?>">
                                            <input type="hidden" name="utm_content" value="<?php echo htmlspecialchars($_GET['utm_content'] ?? ''); ?>">
                                            <input type="hidden" name="installments" value="<?php echo esc_attr($course['installments']); ?>">
                                            <input type="hidden" name="secure_token" value="<?= $token ?>">
                                        </form>
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-9 px-1 text-end">
                                        <div>
                                        <?php if (!empty($course['price_sale'])) : ?>
                                            <span class="old_price"><?php echo esc_html($course['price']); ?> تومان</span>
                                        <?php endif; ?>
                                        <?php if (!empty($course['price_sale_percentage'])) : ?>
                                            <span class="price_sale_percentage_landing"><?php echo esc_html($course['price_sale_percentage']); ?> %</span>
                                        <?php endif; ?>
                                        </div>
                                        <div>
                                        <?php if ($course['price'] == "0") : ?>
                                            <span class="free">رایگان</span>
                                        <?php elseif ($course['price'] > 0) : ?>
                                            <?php if (!empty($course['price_sale'])) : ?>
                                                <span class="price_sale"><?php echo esc_html($course['price_sale']); ?></span>
                                            <?php else: ?>
                                                <span class="price"><?php echo esc_html($course['price']); ?></span>
                                            <?php endif; ?>
                                            <span class="currency">تومان</span>
                                        <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
   <?php
   */
   
    ob_start();
    ?>
    <div class="teacher-current-courses-place container-fluid">
        <div class="row" id="teacher-current-courses">
            <div class="col-12 text-center">
                <span>در حال بارگذاری دوره‌ها...</span>
            </div>
        </div>
    </div>

    <script>
    jQuery(function($){
        $.post(
            "<?php echo esc_url(admin_url('admin-ajax.php')); ?>",
            {
                action: "load_teacher_courses",
                post_id: <?php echo (int) get_the_ID(); ?>
            },
            function(res){
                if(res.success){
                    $('#teacher-current-courses').html(res.data.html);
                    //console.log(typeof res.data);
                    //console.log(res.data);
                } else {
                    $('#teacher-current-courses').html(
                        '<div class="col-12 text-center">دوره‌ای یافت نشد.</div>'
                    );
                }
            },
            'json'
        );
    });
    </script>
    <?php
    return ob_get_clean();

}

/**
 * Shortcode:  teacher_current_courses

add_shortcode('teacher_current_courses','teacher_current_courses_func');
function teacher_current_courses_func() {
    global $secret_key;
    global $TAMLAND_PURCHASE_USR;
    global $TAMLAND_MID1_PSW;
    global $TAMLAND_MID2_PSW;
    global $TAMLAND_KONKOOR_PSW;
    global $TAMLAND_TIZHOOSHAN_PSW;

    $teacher_current_courses = get_post_meta(get_the_ID(), 'teacher-current-courses', true);
    if (!$teacher_current_courses) return;  // اطمینان از وجود داده‌ها
    
    ?>
    <div class="teacher-current-courses-place container-fluid">
        <div class="row" id="teacher-current-courses">
            <?php foreach ($teacher_current_courses as $key => $course) : 
                if($course['course-type'] == 'normal-course'){
                    $course_type = 'دوره معمولی';
                }elseif($course['course-type'] == 'multi-teacher'){
                    $course_type = 'چند استاده';
                }elseif($course['course-type'] == 'course-pack'){
                    $course_type = 'بسته';
                }elseif($course['course-type'] == 'azmoon'){
                    $course_type = 'آزمون';
                }
                $url = $course['teacher-current-courses-link'];
            $parts = parse_url($url);
            
            $scheme = $parts['scheme'];    // https
            $host   = $parts['host'];      // subdomain.tamland.ir
            
            $baseUrl = $scheme . '://' . $host;
            
            $user_url = $baseUrl."/wp-json/jwt-auth/v1/token";
            
            switch ($host) {
                case "mid1.tamland.ir":
                    $user_data = array(
                        "username" => $TAMLAND_PURCHASE_USR,
                        "password" => $TAMLAND_MID1_PSW
                    );
                    break;
            
                case "mid2.tamland.ir":
                    $user_data = array(
                        "username" => $TAMLAND_PURCHASE_USR,
                        "password" => $TAMLAND_MID2_PSW
                    );
                    break;
                    
                case "konkoor.tamland.ir":
                    $user_data = array(
                        "username" => $TAMLAND_PURCHASE_USR,
                        "password" => $TAMLAND_KONKOOR_PSW
                    );
                    break;
                    
                case "tizhooshan.tamland.ir":
                    $user_data = array(
                        "username" => $TAMLAND_PURCHASE_USR,
                        "password" => $TAMLAND_TIZHOOSHAN_PSW
                    );
                    break;
                    
                default:
                    break;
            }
            
            
            $user_ch = curl_init();
            
            curl_setopt($user_ch, CURLOPT_URL, $user_url);
            curl_setopt($user_ch, CURLOPT_POST, true);
            curl_setopt($user_ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($user_ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));
            curl_setopt($user_ch, CURLOPT_POSTFIELDS, json_encode($user_data));
            
            $user_response = curl_exec($user_ch);
            
            if (curl_errno($user_ch)) {
                echo 'Curl error: ' . curl_error($user_ch);
                curl_close($user_ch);
                exit;
            }
            
            curl_close($user_ch);
            
            $user_response_data = json_decode($user_response, true);
            
            // گرفتن توکن
            $user_token = $user_response_data['token'];
            error_log('token: '.$user_token);
            //$course_id = isset($entry['23']) ? intval($entry['23']) : 0;
            $endpoint = $baseUrl . "/wp-json/lms/v1/get-post-by-course-id?course_id_lms=" . $course['course_id_lms'];
            $get_courseid_ch = curl_init();
            curl_setopt_array($get_courseid_ch, array(
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$user_token // اگر نیاز به JWT دارید
                ),
            ));
            
            $get_courseid_response = curl_exec($get_courseid_ch);
            curl_close($get_courseid_ch);
            
            $get_courseid_data = json_decode($get_courseid_response, true);
            error_log('Site:' . $baseUrl);
            error_log(print_r($get_courseid_data, true));
            $course_id = $get_courseid_data['post_id'] ?? null;

                $price_token = (int) preg_replace('/[^\d]/', '', $course['price_tax']);
                $token = hash_hmac('sha256', $course['course_id_lms'] . '|' . $price_token, $secret_key);
            ?>
                <div class="col-6 col-md-4 col-lg-3 mb-3">
                    <div class="teacher-current-courses-card">
                        <?php if (!empty($course['teacher-current-courses-image'])) : ?>
                            <div class="teacher-current-courses-image sa-view-more">
                                <img src="<?php echo esc_url($course['teacher-current-courses-image']); ?>" alt="Course Image">
                                <div class="view-more">
                                    <a href="<?php echo esc_url($course['teacher-current-courses-link']); ?>">مشاهده جزئیات دوره</a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="px-2 mb-3">
                            <?php if (!empty($course['teacher-current-courses-title'])) : ?>
                                <form method="post" action="https://mid1.tamland.ir/course-checkout">
                                    <button type="submit" style="background:transparent;padding:0" id="teacher-current-courses-title">
                                        <h2><?php echo esc_html($course['teacher-current-courses-title']); ?></h2>
                                    </button>
                                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                                    <input type="hidden" name="course_id_lms" value="<?php echo esc_attr($course['course_id_lms']); ?>">
                                    <input type="hidden" name="ref_url_payment" value="<?php echo esc_attr($course['teacher-current-courses-link']); ?>">
                                    <input type="hidden" name="course_type" value="<?php echo esc_attr($course_type); ?>">
                                    <input type="hidden" name="course_name_0" value="<?php echo esc_attr(str_replace("|", "-", $course['teacher-current-courses-title'] . ' ' . $course['secound-title'] . ' ' . get_the_title())); ?>">
                                    <input type="hidden" name="course_price_0" value="<?php echo esc_attr($course['price_tax']); ?>">
                                    <input type="hidden" name="course_numbers" value="1">
                                    <input type="hidden" name="installments" value="<?php echo esc_attr($course['installments']); ?>">
                                    <input type="hidden" name="secure_token" value="<?= $token ?>">
                                </form>
                            <?php endif; ?>

                            <?php if (!empty($course['secound-title'])) : ?>
                                <span class="secound-title"><?php echo esc_html($course['secound-title']); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="px-2">
                                <div class="row w-100 mx-0 align-items-center pt-2" style="border-top:1px solid rgba(206, 190, 190, 1)">
                                    <?php if (!empty($course['price_tax'])) : ?>
                                    <div class="col-3 px-1">
                                        <form method="post" action="https://mid1.tamland.ir/course-checkout">
                                            <button type="submit" class="add-to-cart-button-landing cart-icon"  id="teacher-landing-current-courses-buy-button">
                                                <img src="https://tamland.ir/wp-content/uploads/2025/03/Shopping-cart.svg">
                                            </button>
                                            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                                            <input type="hidden" name="course_id_lms" value="<?php echo esc_attr($course['course_id_lms']); ?>">
                                            <input type="hidden" name="ref_url_payment" value="<?php echo esc_attr($course['teacher-current-courses-link']); ?>">
                                            <input type="hidden" name="course_type" value="<?php echo esc_attr($course_type); ?>">
                                            <input type="hidden" name="course_name_0" value="<?php echo esc_attr(str_replace("|", "-", $course['teacher-current-courses-title'] . ' ' . $course['secound-title'] . ' ' . get_the_title())); ?>">
                                            <input type="hidden" name="course_price_0" value="<?php echo esc_attr($course['price_tax']); ?>">
                                            <input type="hidden" name="course_numbers" value="1">
                                            <input type="hidden" name="utm_source" value="<?php echo htmlspecialchars($_GET['utm_source'] ?? ''); ?>">
                                            <input type="hidden" name="utm_medium" value="<?php echo htmlspecialchars($_GET['utm_medium'] ?? ''); ?>">
                                            <input type="hidden" name="utm_campaign" value="<?php echo htmlspecialchars($_GET['utm_campaign'] ?? ''); ?>">
                                            <input type="hidden" name="utm_term" value="<?php echo htmlspecialchars($_GET['utm_term'] ?? ''); ?>">
                                            <input type="hidden" name="utm_content" value="<?php echo htmlspecialchars($_GET['utm_content'] ?? ''); ?>">
                                            <input type="hidden" name="installments" value="<?php echo esc_attr($course['installments']); ?>">
                                            <input type="hidden" name="secure_token" value="<?= $token ?>">
                                        </form>
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-9 px-1 text-end">
                                        <div>
                                        <?php if (!empty($course['price_sale'])) : ?>
                                            <span class="old_price"><?php echo esc_html($course['price']); ?> تومان</span>
                                        <?php endif; ?>
                                        <?php if (!empty($course['price_sale_percentage'])) : ?>
                                            <span class="price_sale_percentage_landing"><?php echo esc_html($course['price_sale_percentage']); ?> %</span>
                                        <?php endif; ?>
                                        </div>
                                        <div>
                                        <?php if ($course['price'] == "0") : ?>
                                            <span class="free">رایگان</span>
                                        <?php elseif ($course['price'] > 0) : ?>
                                            <?php if (!empty($course['price_sale'])) : ?>
                                                <span class="price_sale"><?php echo esc_html($course['price_sale']); ?></span>
                                            <?php else: ?>
                                                <span class="price"><?php echo esc_html($course['price']); ?></span>
                                            <?php endif; ?>
                                            <span class="currency">تومان</span>
                                        <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}
*/

add_shortcode('teacher_current_courses','teacher_current_courses_func');
function teacher_current_courses_func() {
    ob_start();
    ?>
    <div class="teacher-current-courses-place container-fluid">
        <div class="row" id="teacher-current-courses">
            <div class="col-12 text-center">
                <span>در حال بارگذاری دوره‌ها...</span>
            </div>
        </div>
    </div>

    <script>
    jQuery(function($){
        $.post(
            "<?php echo esc_url(admin_url('admin-ajax.php')); ?>",
            {
                action: "load_teacher_courses",
                post_id: <?php echo (int) get_the_ID(); ?>
            },
            function(res){
                if(res.success){
                    $('#teacher-current-courses').html(res.data.html);
                    //console.log(typeof res.data);
                    //console.log(res.data);
                } else {
                    $('#teacher-current-courses').html(
                        '<div class="col-12 text-center">دوره‌ای یافت نشد.</div>'
                    );
                }
            },
            'json'
        );
    });
    </script>
    <?php
    return ob_get_clean();
}

add_action('wp_ajax_load_teacher_courses', 'load_teacher_courses_func');
add_action('wp_ajax_nopriv_load_teacher_courses', 'load_teacher_courses_func');

function load_teacher_courses_func() {
    global $secret_key;
    global $TAMLAND_PURCHASE_USR;
    global $TAMLAND_MID1_PSW;
    global $TAMLAND_MID2_PSW;
    global $TAMLAND_KONKOOR_PSW;
    global $TAMLAND_TIZHOOSHAN_PSW;
    
    $post_id = intval($_POST['post_id'] ?? 0);
    if(!$post_id){
        wp_send_json_error(['message' => 'Post ID is required']);
    }

    $teacher_current_courses = get_post_meta($post_id, 'teacher-current-courses', true);
    if (!$teacher_current_courses) {
        wp_send_json_error(['message' => 'No courses found']);
    }

    $multi_handle = curl_multi_init();
    $curl_handles = [];
    $results = [];
    //$jwt_tokens = [];
    // ایجاد همه cURL handle ها
    foreach ($teacher_current_courses as $course) {

        $parts = parse_url($course['teacher-current-courses-link']);
        $scheme = $parts['scheme'];
        $host = $parts['host'];
        $baseUrl = $scheme . '://' . $host;

        // user data
        switch ($host) {
            case "mid1.tamland.ir": $user_data = ["username"=>$TAMLAND_PURCHASE_USR,"password"=>$TAMLAND_MID1_PSW]; break;
            case "mid2.tamland.ir": $user_data = ["username"=>$TAMLAND_PURCHASE_USR,"password"=>$TAMLAND_MID2_PSW]; break;
            case "konkoor.tamland.ir": $user_data = ["username"=>$TAMLAND_PURCHASE_USR,"password"=>$TAMLAND_KONKOOR_PSW]; break;
            case "tizhooshan.tamland.ir": $user_data = ["username"=>$TAMLAND_PURCHASE_USR,"password"=>$TAMLAND_TIZHOOSHAN_PSW]; break;
            default: $user_data = []; break;
        }

        // گرفتن JWT token
        $user_url = $baseUrl."/wp-json/jwt-auth/v1/token";
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $user_url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($user_data),
            CURLOPT_TIMEOUT => 10,          // حداکثر 10 ثانیه برای کل request
            CURLOPT_CONNECTTIMEOUT => 5,    // حداکثر 5 ثانیه برای اتصال
        ]);
        $curl_handles[$course['course_id_lms']]['token'] = $ch;
        $curl_handles[$course['course_id_lms']]['baseUrl'] = $baseUrl;
        $curl_handles[$course['course_id_lms']]['course'] = $course;

        curl_multi_add_handle($multi_handle, $ch);
    }

    // اجرا کردن multi-cURL
    $running = null;
    do {
        curl_multi_exec($multi_handle, $running);
        curl_multi_select($multi_handle, 1);
    } while ($running > 0 && $status == CURLM_OK);

    // پردازش پاسخ‌ها
    foreach ($curl_handles as $course_lms => $data) {
        $response = curl_multi_getcontent($data['token']);
        if(curl_errno($data['token'])){
            $results[$course_lms] = ['error' => curl_error($data['token'])];
            continue; // اگر خطا بود ادامه بده و بلوک نشو
        }
        $resp_data = json_decode($response, true);
        /*
        if (!isset($jwt_tokens[$host])) {
            // فقط یک بار JWT بگیر
            $jwt_tokens[$host] = $user_token;
        }
        
        $user_token = $jwt_tokens[$host];*/
        $user_token = $resp_data['token'] ?? '';

        // get course_id via REST API
        $endpoint = $data['baseUrl']."/wp-json/lms/v1/get-post-by-course-id?course_id_lms=".$course_lms;
        $ch2 = curl_init();
        curl_setopt_array($ch2, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer '.$user_token],
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);
        $resp2 = curl_exec($ch2);
        curl_close($ch2);
        $course_id = json_decode($resp2,true)['post_id'] ?? null;

        // ذخیره در cache
        set_transient('teacher_course_'.$course_lms, ['post_id'=>$course_id,'user_token'=>$user_token], HOUR_IN_SECONDS);

        $results[$course_lms] = ['post_id'=>$course_id,'user_token'=>$user_token];
        
        curl_multi_remove_handle($multi_handle, $data['token']);
    }
    
    curl_multi_close($multi_handle);
    
    // ساخت HTML مشابه قبل
    $html = '';
    foreach ($teacher_current_courses as $course) {
    ob_start();
        $course_lms = $course['course_id_lms'];
        $course_id = $results[$course_lms]['post_id'] ?? null;
        $price_token = (int) preg_replace('/[^\d]/','',$course['price_tax'] ?? 0);
        $token = hash_hmac('sha256', $course_lms.'|'.$price_token, $secret_key);

        // course type
        $type = $course['course-type'] ?? 'normal-course';
        switch ($type) {
            case 'normal-course': $course_type='دوره معمولی'; break;
            case 'multi-teacher': $course_type='چند استاده'; break;
            case 'course-pack': $course_type='بسته'; break;
            case 'azmoon': $course_type='آزمون'; break;
            default: $course_type='دوره'; break;
        }

        
        ?>
        <div class="col-6 col-md-4 col-lg-3 mb-3">
                    <div class="teacher-current-courses-card">
                        <?php if (!empty($course['teacher-current-courses-image'])) : ?>
                            <div class="teacher-current-courses-image sa-view-more">
                                <img src="<?php echo esc_url($course['teacher-current-courses-image']); ?>" alt="Course Image">
                                <div class="view-more">
                                    <a href="<?php echo esc_url($course['teacher-current-courses-link']); ?>">مشاهده جزئیات دوره</a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="px-2 mb-3">
                            <?php if (!empty($course['teacher-current-courses-title'])) : ?>
                                <form method="post" action="https://mid1.tamland.ir/course-checkout">
                                    <button type="submit" style="background:transparent;padding:0" id="teacher-current-courses-title">
                                        <h2><?php echo esc_html($course['teacher-current-courses-title']); ?></h2>
                                    </button>
                                    <input type="hidden" name="course_id" value="<?php echo esc_attr($course_id); ?>">
                                    <input type="hidden" name="course_id_lms" value="<?php echo esc_attr($course['course_id_lms']); ?>">
                                    <input type="hidden" name="ref_url_payment" value="<?php echo esc_attr($course['teacher-current-courses-link']); ?>">
                                    <input type="hidden" name="source_link" value="<?php the_permalink($post_id); ?>">
                                    <input type="hidden" name="course_type" value="<?php echo esc_attr($course_type); ?>">
                                    <input type="hidden" name="course_name_0" value="<?php echo esc_attr(str_replace("|", "-", $course['teacher-current-courses-title'] . ' ' . $course['secound-title'] . ' ' . get_the_title())); ?>">
                                    <input type="hidden" name="course_price_0" value="<?php echo esc_attr($course['price_tax']); ?>">
                                    <input type="hidden" name="course_numbers" value="1">
                                    <input type="hidden" name="utm_source" value="<?php echo htmlspecialchars($_GET['utm_source'] ?? ''); ?>">
                                    <input type="hidden" name="utm_medium" value="<?php echo htmlspecialchars($_GET['utm_medium'] ?? ''); ?>">
                                    <input type="hidden" name="utm_campaign" value="<?php echo htmlspecialchars($_GET['utm_campaign'] ?? ''); ?>">
                                    <input type="hidden" name="utm_term" value="<?php echo htmlspecialchars($_GET['utm_term'] ?? ''); ?>">
                                    <input type="hidden" name="utm_content" value="<?php echo htmlspecialchars($_GET['utm_content'] ?? ''); ?>">
                                    <input type="hidden" name="installments" value="<?php echo esc_attr($course['installments']); ?>">
                                    <input type="hidden" name="secure_token" value="<?= $token ?>">
                                </form>
                            <?php endif; ?>

                            <?php if (!empty($course['secound-title'])) : ?>
                                <span class="secound-title"><?php echo esc_html($course['secound-title']); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="px-2">
                                <div class="row w-100 mx-0 align-items-center pt-2" style="border-top:1px solid rgba(206, 190, 190, 1)">
                                    <?php if (!empty($course['price_tax'])) : ?>
                                    <div class="col-3 px-1">
                                        <form method="post" action="https://mid1.tamland.ir/course-checkout">
                                            <button type="submit" class="add-to-cart-button-landing cart-icon"  id="teacher-landing-current-courses-buy-button">
                                                <img src="https://tamland.ir/wp-content/uploads/2025/03/Shopping-cart.svg">
                                            </button>
                                            <input type="hidden" name="course_id" value="<?php echo esc_attr($course_id); ?>">
                                            <input type="hidden" name="course_id_lms" value="<?php echo esc_attr($course['course_id_lms']); ?>">
                                            <input type="hidden" name="ref_url_payment" value="<?php echo esc_attr($course['teacher-current-courses-link']); ?>">
                                            <input type="hidden" name="source_link" value="<?php the_permalink($post_id); ?>">
                                            <input type="hidden" name="course_type" value="<?php echo esc_attr($course_type); ?>">
                                            <input type="hidden" name="course_name_0" value="<?php echo esc_attr(str_replace("|", "-", $course['teacher-current-courses-title'] . ' ' . $course['secound-title'] . ' ' . get_the_title())); ?>">
                                            <input type="hidden" name="course_price_0" value="<?php echo esc_attr($course['price_tax']); ?>">
                                            <input type="hidden" name="course_numbers" value="1">
                                            <input type="hidden" name="utm_source" value="<?php echo htmlspecialchars($_GET['utm_source'] ?? ''); ?>">
                                            <input type="hidden" name="utm_medium" value="<?php echo htmlspecialchars($_GET['utm_medium'] ?? ''); ?>">
                                            <input type="hidden" name="utm_campaign" value="<?php echo htmlspecialchars($_GET['utm_campaign'] ?? ''); ?>">
                                            <input type="hidden" name="utm_term" value="<?php echo htmlspecialchars($_GET['utm_term'] ?? ''); ?>">
                                            <input type="hidden" name="utm_content" value="<?php echo htmlspecialchars($_GET['utm_content'] ?? ''); ?>">
                                            <input type="hidden" name="installments" value="<?php echo esc_attr($course['installments']); ?>">
                                            <input type="hidden" name="secure_token" value="<?= $token ?>">
                                        </form>
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-9 px-1 text-end">
                                        <div>
                                        <?php if (!empty($course['price_sale'])) : ?>
                                            <span class="old_price"><?php echo esc_html($course['price']); ?> تومان</span>
                                        <?php endif; ?>
                                        <?php if (!empty($course['price_sale_percentage'])) : ?>
                                            <span class="price_sale_percentage_landing"><?php echo esc_html($course['price_sale_percentage']); ?> %</span>
                                        <?php endif; ?>
                                        </div>
                                        <div>
                                        <?php if ($course['price'] == "0") : ?>
                                            <span class="free">رایگان</span>
                                        <?php elseif ($course['price'] > 0) : ?>
                                            <?php if (!empty($course['price_sale'])) : ?>
                                                <span class="price_sale"><?php echo esc_html($course['price_sale']); ?></span>
                                            <?php else: ?>
                                                <span class="price"><?php echo esc_html($course['price']); ?></span>
                                            <?php endif; ?>
                                            <span class="currency">تومان</span>
                                        <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
        <?php
        $html .= ob_get_clean();
    }

    wp_send_json_success(['html'=>$html]);
}

/**
 * Shortcode: teacher_future_courses
*/
add_shortcode('teacher_future_courses','teacher_future_courses_func');
function teacher_future_courses_func() {
    $teacher_future_courses = get_post_meta(get_the_ID(), 'teacher-future-courses', true);
    if (!empty($teacher_future_courses)): ?>
        <div class="teacher-future-courses-place container-fluid">
            <div class="row" id="teacher-future-courses">
                <?php foreach ($teacher_future_courses as $index => $course): ?>
                    <div class="col-6 col-md-4 col-lg-3 mb-3">
                        <div class="teacher-future-courses-card">
                            <?php if (!empty($course['teacher-future-courses-image'])): ?>
                                <div class="teacher-future-courses-image mb-3">
                                    <img src="<?php echo esc_url($course['teacher-future-courses-image']); ?>" alt="Course Image">
                                </div>
                            <?php endif; ?>
                            <div class="mb-3 px-2">
                                <?php if (!empty($course['teacher-future-courses-title'])): ?>
                                    <h3 class="mb-3"><?php echo esc_html($course['teacher-future-courses-title']); ?></h3>
                                <?php endif; ?>
                                <?php if (!empty($course['teacher-future-courses-cat-link'])): ?>
                                    <div class="mb-3">
                                        <a href="<?php echo esc_url($course['teacher-future-courses-cat-link']); ?>">
                                            <span class="teacher-future-courses-cat-link"><?php echo esc_html($course['teacher-future-courses-cat']); ?></span>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <svg width="14" height="17" viewBox="0 0 14 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0.960938 2.79028V2.76025V2.00024C0.960938 1.72024 1.071 1.45025 1.271 1.26025C1.461 1.06025 1.73099 0.950195 2.01099 0.950195H12.011C12.291 0.950195 12.551 1.06025 12.751 1.26025C12.951 1.45025 13.061 1.72024 13.061 2.00024V10.0002C13.061 10.2802 12.951 10.5502 12.751 10.7402C12.551 10.9402 12.291 11.0503 12.011 11.0503H7.98096L10.481 15.2202C10.521 15.2802 10.541 15.3502 10.551 15.4202C10.561 15.4902 10.561 15.5601 10.541 15.6301C10.521 15.7001 10.4909 15.7703 10.4509 15.8303C10.4109 15.8803 10.351 15.9302 10.291 15.9702C10.201 16.0202 10.111 16.0503 10.011 16.0503" fill="#2D3748"/>
                                    </svg>
                                    <span class="teacher-name"><?php echo get_the_title(); ?></span>
                                </div>
                            </div>
                            <div>
                                <form method="post" action="/booking-courses">
                                    <button type="submit" class="booking-button"  id="teacher-future-courses-booking-button">
                                        <?php echo esc_html('رزرو دوره'); ?>
                                    </button>
                                    <input type="hidden" name="course_name" value="<?php echo esc_html($course['teacher-future-courses-title'].' - '.get_the_title()); ?>">
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif;
}



/**
 * add iframe html support
 */
add_filter( 'wp_kses_allowed_html', function ( $tags, $context ) {
if ( 'post' === $context ) {
$tags['iframe'] = array(
'src' => true,
'width' => true,
'height' => true,
'width' => true, 
'frameborder' => true,
'allowtransparency' => true,
'allow' => true,
);
}
return $tags;
},10,2);



/*Start Goftino*/
function add_chat_widget() {
    ?>
    <style>
        #Goftino_tamland {
            background: rgb(255, 0, 44);
            border-radius: 50px;
            padding: 0px;
            font-size: 18px;
            position: fixed;
            bottom: 35px;
            left: 30px;
            color: black;
            display: none; 
            cursor: pointer;
            z-index: 9999;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 55px;
            height: 55px;
        }
        #Goftino_tamland svg {
            width: 100%;
            height: 100%;
        }
        #unread_counter {
            background: #000000 !important;
            border-radius: 50%;
            padding: 3px;
            font-size: 12px;
            position: absolute;
            color: white;
            min-width: 18px;
            height: 18px;
            top: -5px;
            right: -5px;
            z-index: 5;
            text-align: center;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @media (min-width: 768px) and (max-width: 1200px) {
        #goftino_w {
            position: fixed !important;
            bottom: 50px !important;
            left: 0px !important;
            z-index: 9999 !important;
        }
    }

    @media only screen and (max-width: 768px) {
        #Goftino_tamland {
            position: fixed ;
            top: 50% ;
            left: -3px;
            transform: translateY(-50%) ;
            padding: 0px;
            transition: none ;
            border-radius: 15% ;
            width: 2.5rem ;
            height: 2.5rem ;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .2), 0 1px 10px rgba(0, 0, 0, .15) ;
            margin: 0 auto ;
        }
        #Goftino_tamland svg {
                width: 80%;
                height: 80%;
            }
    }
    </style>
    <div id="Goftino_tamland">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120">
            <path d="M60.19,53.75a3,3,0,1,0,3.06,3A3,3,0,0,0,60.19,53.75Zm-11.37,0a3,3,0,1,0,3.06,3A3,3,0,0,0,48.81,53.75Zm45.94,4A35,35,0,1,0,52.75,92v12.76s14.55-4.25,30.53-19.28C94.68,74.74,94.75,59.41,94.75,59.41l0,0C94.74,58.87,94.75,58.3,94.75,57.72Zm-10.14.6s0,10.64-8,18.09A57.93,57.93,0,0,1,53,89.8V80.34A24.29,24.29,0,1,1,84.61,57.16c0,.4,0,.8,0,1.19ZM70.69,53.75a3,3,0,1,0,3.06,3A3,3,0,0,0,70.69,53.75Z" transform="translate(0.25 0.25)" style="fill:#ffffff"></path>
        </svg>
        <span id="unread_counter">0</span>
    </div>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            // بررسی و حذف آیکن‌های اضافی
            const widgetButtons = document.querySelectorAll('#Goftino_tamland');
            if (widgetButtons.length > 1) {
                for (let i = 1; i < widgetButtons.length; i++) {
                    widgetButtons[i].remove();
                }
            }

            const widgetObserver = new IntersectionObserver(function (entries, observer) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        loadWidgetScript();
                        observer.disconnect(); 
                    }
                });
            });

            const widgetButton = document.getElementById('Goftino_tamland');
            widgetObserver.observe(widgetButton);

            function loadWidgetScript() {
                var d = document;
                var g = d.createElement("script"),
                    i = "kkkIbA", 
                    s = "https://www.goftino.com/widget/" + i,
                    l = localStorage.getItem("goftino_" + i);

                g.type = "text/javascript";
                g.async = true;
                g.src = l ? s + "?o=" + l : s;

                g.onload = function () {
                    if (typeof Goftino !== "undefined") {
                        Goftino.setWidget({
                            hasIcon: false,
                            counter: "#unread_counter"
                        });

                        widgetButton.style.display = "flex";

                        widgetButton.addEventListener("click", function () {
                            Goftino.toggle();
                        });
                    }
                };
                d.getElementsByTagName("head")[0].appendChild(g);
            }
        });
    </script>
    <?php
}
add_action('wp_footer', 'add_chat_widget');

/*End Goftino*/


/**
 * add Widget support for theme
 */ 
add_theme_support( 'widgets' );

/**
    * Add Iran mobile format 
*/
add_filter( 'gform_phone_formats', 'ir_phone_format', 10, 2 );

function ir_phone_format( $phone_formats ) {
    // فرمت شماره موبایل ایران
    $phone_formats['ir'] = array(
        'label'       => esc_html__( 'شماره موبایل ایران', 'Astra Child' ),
        'mask'        => '',
        'regex'       => '/^09(?:0[0-9]|1[0-9]|9[0-9]|3[0-9]|2[0-9])-?[0-9]{3}-?[0-9]{4}$/',
        'instruction' => esc_html__( 'شماره وارد شده صحیح نمی‌باشد', 'Astra Child' ),
    );
 
    return $phone_formats;
}

/**
 * Add float button that users can link to other Tamland's websites.
 */
add_action('wp_footer','add_float_button');
function add_float_button(){
    ?>
        <div class="kc_fab_wrapper"></div>
        <script>
            jQuery(document).ready(function($){
                var links = [
                    {
                        "bgcolor":"#C4161C",
                        "icon":'<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.9979 7.64865V0.734375L7.97266 7.80482V14.7191H14.8427L21.8679 7.64865H14.9979Z" fill="white"/><path d="M22.4025 15.0901V8.17578L15.3789 15.2447V22.1589H22.2489L29.2726 15.0901H22.4025Z" fill="white"/><path d="M22.7715 22.7091V29.6234L29.7967 22.553V15.6387L22.7715 22.7091Z" fill="white"/><path d="M7.45168 7.27358L14.4769 0.203125H7.57237L0.580078 7.27358H7.45168Z" fill="white"/><path d="M7.31142 22.8689C9.2613 24.8313 10.2394 27.4074 10.2472 29.9851H14.3021C14.2818 26.3773 12.9103 22.7758 10.1814 20.0294C7.43054 17.2608 3.81761 15.8773 0.203125 15.8789V19.9142C2.77841 19.9126 5.35213 20.897 7.31142 22.8689Z" fill="white"/><path d="M0.21096 25.5195C0.207825 25.5195 0.20626 25.5195 0.203125 25.5195V29.9871H4.6797C4.67186 28.7976 4.20947 27.6792 3.37247 26.8368C2.52606 25.9881 1.40377 25.5195 0.21096 25.5195Z" fill="white"/><path d="M6.79103 23.4046C4.97438 21.5762 2.58875 20.6644 0.203125 20.666V22.5512V24.7912C0.20626 24.7912 0.207825 24.7912 0.21096 24.7912C1.60127 24.7912 2.90851 25.3371 3.89129 26.3262C4.86779 27.309 5.40698 28.612 5.41482 30.0002H7.60452H9.51834C9.50894 27.5077 8.54184 25.1667 6.79103 23.4046Z" fill="white"/><path d="M14.6266 22.9227V15.4752H7.22834V8.0293H0.203125V15.1502C4.00414 15.1487 7.80672 16.6031 10.7002 19.5152C13.5717 22.4053 15.0153 26.196 15.0357 29.9931H22.0264V22.9227H14.6266Z" fill="white"/></svg>'
                    },
                    {
                        "url":"https://konkoor.tamland.ir",
                        "bgcolor":"#fff",
                        "color":"#222",
                        "icon":'<svg width="133" height="42" viewBox="0 0 133 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M113.947 13.8352V5.12109L105.016 14.032V22.7461H113.75L122.682 13.8352H113.947Z" fill="#C4161C"></path><path d="M123.36 23.22V14.5059L114.43 23.4148V32.1289H123.164L132.094 23.22H123.36Z" fill="#C4161C"></path><path d="M123.828 32.8006V41.5147L132.76 32.6038V23.8896L123.828 32.8006Z" fill="#C4161C"></path><path d="M104.346 13.3592L113.278 4.44824H104.499L95.6094 13.3592H104.346Z" fill="#C4161C"></path><path d="M104.17 33.0214C106.65 35.4947 107.893 38.7414 107.903 41.99H113.059C113.033 37.4431 111.289 32.9041 107.819 29.4428C104.322 25.9535 99.7284 24.2099 95.1328 24.2119V29.2976C98.4071 29.2956 101.679 30.5362 104.17 33.0214Z" fill="#2D3748"></path><path d="M95.1506 36.3604C95.1466 36.3604 95.1446 36.3604 95.1406 36.3604V41.9908H100.832C100.822 40.4917 100.234 39.0821 99.1701 38.0205C98.094 36.9508 96.6671 36.3604 95.1506 36.3604Z" fill="#C4161C"></path><path d="M103.516 33.6809C101.207 31.3767 98.1737 30.2275 95.1406 30.2295V32.6053V35.4285C95.1446 35.4285 95.1466 35.4285 95.1506 35.4285C96.9182 35.4285 98.5802 36.1164 99.8297 37.363C101.071 38.6016 101.757 40.2438 101.767 41.9934H104.551H106.984C106.972 38.8521 105.742 35.9017 103.516 33.6809Z" fill="#2D3748"></path><path d="M113.479 33.0788V23.6927H104.073V14.3086H95.1406V23.2831C99.9733 23.2812 104.808 25.1142 108.487 28.7844C112.138 32.4267 113.973 37.2042 113.999 41.9897H122.887V33.0788H113.479Z" fill="#2D3748"></path><path d="M82.1803 25.6184L85.5103 28.9406L88.8741 25.5826L85.5103 22.2266L82.1803 25.5488L78.8483 22.2266L75.4844 25.5826L78.8483 28.9406L82.1803 25.6184Z" fill="#C4161C"></path><path d="M27.8806 22.2269L24.5156 25.584L27.8806 28.9411L31.2457 25.584L27.8806 22.2269Z" fill="#C4161C"></path><path d="M46.9141 29.1543V30.2676V35.0134V41.9978H51.671V35.0134H65.6729V23.2812H52.799C49.5546 23.2812 46.9141 25.9156 46.9141 29.1543ZM60.9159 30.2657H51.671V29.1543C51.671 28.534 52.1772 28.029 52.799 28.029H60.9139V30.2657H60.9159Z" fill="#2D3748"></path><path d="M82.3378 37.25H74.2209C73.5991 37.25 73.0929 36.7451 73.0929 36.1248V23.2812H68.3359V36.1248C68.3359 39.3635 70.9765 41.9978 74.2209 41.9978H87.0947V30.2657H82.3378V37.25Z" fill="#2D3748"></path><path d="M39.4981 36.1248C39.4981 36.7451 38.9919 37.25 38.3701 37.25H30.2532V30.2657H25.4963V37.25H17.3793C16.7576 37.25 16.2514 36.7451 16.2514 36.1248V30.9058V26.2337H11.4944V30.9058V36.1248C11.4944 36.7451 10.9882 37.25 10.3665 37.25H1V41.9978H10.3665C11.6797 41.9978 12.8934 41.5663 13.8739 40.8387C14.8544 41.5663 16.068 41.9978 17.3813 41.9978H25.4983H30.2552H38.3721C41.6185 41.9978 44.2571 39.3635 44.2571 36.1248V23.2812H39.5001V36.1248H39.4981Z" fill="#2D3748"></path><path d="M0.792666 18.2807L2.38223 17.2267V9.29619L4.64563 8.72602V18.4362L2.05395 20.164L0.792666 18.2807ZM6.47789 18.298L9.93347 16H8.93135C8.4015 16 7.86588 15.8387 7.32451 15.5162C6.79466 15.1822 6.35119 14.733 5.99411 14.1685C5.64856 13.6041 5.47578 12.9821 5.47578 12.3025C5.47578 11.6345 5.6428 11.0125 5.97684 10.4365C6.31088 9.8606 6.7601 9.40561 7.32451 9.07157C7.90044 8.73754 8.52244 8.57052 9.19052 8.57052H12.9225V13.7366H13.7864L14.1838 14.8769L13.7864 16H12.9225V16.7257L7.7219 20.1812L6.47789 18.298ZM7.73918 12.268C7.73918 12.6711 7.8774 13.0167 8.15385 13.3047C8.44181 13.5926 8.78737 13.7366 9.19052 13.7366H10.6591V10.8166H9.19052C8.79889 10.8166 8.45909 10.9606 8.17113 11.2486C7.88316 11.525 7.73918 11.8648 7.73918 12.268ZM12.8614 14.8769L13.2761 13.7366H16.3861L16.8007 14.8942L16.3861 16H13.2761L12.8614 14.8769ZM15.4477 14.8769L15.8623 13.7366H27.1448C27.5479 13.7366 27.8935 13.5984 28.1815 13.3219C28.4694 13.034 28.6134 12.6884 28.6134 12.2853C28.6134 11.8821 28.4694 11.5423 28.1815 11.2659C27.8935 10.9779 27.5479 10.8339 27.1448 10.8339H16.7262V8.55324L23.7065 4.4411L24.8814 6.16889L20.8729 8.57052H27.1448C27.8244 8.57052 28.4464 8.73754 29.0108 9.07157C29.5867 9.40561 30.0417 9.8606 30.3758 10.4365C30.7098 11.0009 30.8768 11.6172 30.8768 12.2853C30.8768 12.6884 31.015 13.034 31.2915 13.3219C31.5794 13.5984 31.9192 13.7366 32.3109 13.7366L32.7083 14.8769L32.3109 16C31.8271 16 31.3606 15.9021 30.9114 15.7063C30.4737 15.5105 30.0935 15.2455 29.771 14.9115C29.4255 15.2571 29.0281 15.5277 28.5789 15.7236C28.1296 15.9079 27.6516 16 27.1448 16H15.8623L15.4477 14.8769ZM31.7605 16L31.3804 14.8769L31.7778 13.7366H31.7951C32.1983 13.7366 32.5438 13.5984 32.8318 13.3219C33.1197 13.034 33.2637 12.6884 33.2637 12.2853V9.27891L35.5098 8.72602V12.2853C35.5098 12.6884 35.6538 13.034 35.9418 13.3219C36.2298 13.5984 36.5753 13.7366 36.9785 13.7366L37.3759 14.8769L36.9785 16C36.4716 16 35.9936 15.9021 35.5444 15.7063C35.0952 15.4989 34.7093 15.2225 34.3868 14.8769C34.0527 15.2225 33.6611 15.4989 33.2119 15.7063C32.7742 15.9021 32.3019 16 31.7951 16H31.7605ZM33.5056 5.13222H35.458V6.96367H33.5056V5.13222ZM36.0421 14.8769L36.4568 13.7366H39.5668L39.9814 14.8942L39.5668 16H36.4568L36.0421 14.8769ZM38.6284 14.8769L39.043 13.7366H42.1531L42.5677 14.8942L42.1531 16H39.043L38.6284 14.8769ZM41.2146 14.8769L41.6293 13.7366H44.7393L45.154 14.8942L44.7393 16H41.6293L41.2146 14.8769ZM43.8009 14.8769L44.2156 13.7366H47.3256L47.7403 14.8942L47.3256 16H44.2156L43.8009 14.8769ZM46.3872 14.8769L46.8019 13.7366H49.9119L50.3266 14.8942L49.9119 16H46.8019L46.3872 14.8769ZM48.9735 14.8769L49.3882 13.7366H52.4982L52.9128 14.8942L52.4982 16H49.3882L48.9735 14.8769ZM51.5598 14.8769L51.9744 13.7366H55.0845L55.4991 14.8942L55.0845 16H51.9744L51.5598 14.8769ZM54.1461 14.8769L54.5607 13.7366H57.6707L58.0854 14.8942L57.6707 16H54.5607L54.1461 14.8769ZM56.7323 14.8769L57.147 13.7366H60.257L60.6717 14.8942L60.257 16H57.147L56.7323 14.8769ZM59.3186 14.8769L59.7333 13.7366H62.8433L63.258 14.8942L62.8433 16H59.7333L59.3186 14.8769ZM61.9049 14.8769L62.3196 13.7366H65.4296L65.8443 14.8942L65.4296 16H62.3196L61.9049 14.8769ZM64.4912 14.8769L64.9058 13.7366H68.0159L68.4305 14.8942L68.0159 16H64.9058L64.4912 14.8769ZM67.0775 14.8769L67.4921 13.7366H70.6021L71.0168 14.8942L70.6021 16H67.4921L67.0775 14.8769ZM69.6637 14.8769L70.0784 13.7366H73.1884L73.6031 14.8942L73.1884 16H70.0784L69.6637 14.8769ZM72.25 14.8769L72.6647 13.7366H83.9471C84.3503 13.7366 84.6958 13.5984 84.9838 13.3219C85.2718 13.034 85.4158 12.6884 85.4158 12.2853C85.4158 11.8821 85.2718 11.5423 84.9838 11.2659C84.6958 10.9779 84.3503 10.8339 83.9471 10.8339H73.5286V8.55324L80.5088 4.4411L81.6837 6.16889L77.6753 8.57052H83.9471C84.6267 8.57052 85.2487 8.73754 85.8131 9.07157C86.3891 9.40561 86.8441 9.8606 87.1781 10.4365C87.5121 11.0009 87.6792 11.6172 87.6792 12.2853C87.6792 12.9533 87.5121 13.5753 87.1781 14.1513C86.8441 14.7157 86.3891 15.1649 85.8131 15.4989C85.2487 15.833 84.6267 16 83.9471 16H72.6647L72.25 14.8769Z" fill="#C4161C"></path></svg>',
                        "target":"_blank",
                        "title":"کنکور تام‌لند"
                    },
                    {
                        "url":"https://mid1.tamland.ir",
                        "bgcolor":"#fff",
                        "color":"#222",
                        "icon":'<svg width="166" height="49" viewBox="0 0 166 49" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M142.603 15.4751V5.10156L131.961 15.7094V26.083H142.368L153.011 15.4751H142.603Z" fill="#E52041"/><path d="M153.8 26.647V16.2734L143.16 26.8789V37.2525H153.567L164.207 26.647H153.8Z" fill="#E52041"/><path d="M154.375 38.0532V48.4267L165.017 37.8189V27.4453L154.375 38.0532Z" fill="#E52041"/><path d="M131.156 14.9125L141.798 4.30469H131.339L120.746 14.9125H131.156Z" fill="#E52041"/><path d="M130.944 38.3153C133.898 41.2595 135.38 45.1245 135.392 48.9918H141.535C141.504 43.579 139.426 38.1756 135.292 34.0551C131.125 29.9014 125.651 27.8258 120.176 27.8281V33.8823C124.077 33.88 127.976 35.3568 130.944 38.3153Z" fill="#58595B"/><path d="M120.188 42.2891C120.183 42.2891 120.181 42.2891 120.176 42.2891V48.9918H126.957C126.945 47.2072 126.245 45.5292 124.977 44.2653C123.695 42.992 121.995 42.2891 120.188 42.2891Z" fill="#E52041"/><path d="M130.156 39.1009C127.404 36.3578 123.79 34.9898 120.176 34.9922V37.8205V41.1813C120.181 41.1813 120.183 41.1813 120.188 41.1813C122.294 41.1813 124.274 42.0002 125.763 43.4842C127.242 44.9587 128.059 46.9136 128.071 48.9964H131.388H134.287C134.273 45.2569 132.808 41.7446 130.156 39.1009Z" fill="#58595B"/><path d="M142.026 38.3837V27.2102H130.818V16.0391H120.176V26.7227C125.934 26.7203 131.695 28.9024 136.078 33.2715C140.428 37.6074 142.615 43.2948 142.646 48.9916H153.236V38.3837H142.026V38.3837Z" fill="#58595B"/><path d="M104.748 29.5065L108.716 33.4613L112.724 29.4639L108.716 25.4688L104.748 29.4236L100.778 25.4688L96.7695 29.4639L100.778 33.4613L104.748 29.5065Z" fill="#E52041"/><path d="M40.0407 25.4724L36.0312 29.4688L40.0407 33.4651L44.0502 29.4688L40.0407 25.4724Z" fill="#E52041"/><path d="M62.7227 33.718V35.0434V40.6928V49.0073H68.3906V40.6928H85.074V26.7266H69.7346C65.8689 26.7266 62.7227 29.8625 62.7227 33.718ZM79.406 35.041H68.3906V33.718C68.3906 32.9796 68.9938 32.3784 69.7346 32.3784H79.4037V35.041H79.406Z" fill="#636363"/><path d="M104.937 43.3555H95.2659C94.525 43.3555 93.9219 42.7543 93.9219 42.0159V26.7266H88.2539V42.0159C88.2539 45.8713 91.4001 49.0073 95.2659 49.0073H110.605V35.041H104.937V43.3555Z" fill="#636363"/><path d="M53.8944 42.0159C53.8944 42.7543 53.2913 43.3555 52.5504 43.3555H42.879V35.041H37.211V43.3555H27.5396C26.7988 43.3555 26.1956 42.7543 26.1956 42.0159V35.8031V30.2412H20.5277V35.8031V42.0159C20.5277 42.7543 19.9245 43.3555 19.1837 43.3555H8.02344V49.0073H19.1837C20.7485 49.0073 22.1946 48.4937 23.3628 47.6275C24.5311 48.4937 25.9772 49.0073 27.542 49.0073H37.2134H42.8814H52.5528C56.4209 49.0073 59.5648 45.8713 59.5648 42.0159V26.7266H53.8968V42.0159H53.8944Z" fill="#636363"/><path d="M3.666 15.497C3.15467 15.497 2.68233 15.3713 2.249 15.12C1.82433 14.8687 1.48633 14.5263 1.235 14.093C0.983667 13.6683 0.858 13.2003 0.858 12.689V9.543L2.574 9.114V12.689C2.574 12.9923 2.678 13.2523 2.886 13.469C3.10267 13.6857 3.36267 13.794 3.666 13.794H4.966C5.26067 13.794 5.512 13.6857 5.72 13.469C5.93667 13.2523 6.045 12.9923 6.045 12.689V3.654L7.761 3.225V12.689C7.761 13.2003 7.63533 13.6683 7.384 14.093C7.13267 14.5263 6.79467 14.8687 6.37 15.12C5.94533 15.3713 5.47733 15.497 4.966 15.497H3.666ZM9.52626 13.729L12.1263 12H11.3723C10.9736 12 10.5706 11.8787 10.1633 11.636C9.76459 11.3847 9.43092 11.0467 9.16226 10.622C8.90226 10.1973 8.77226 9.72933 8.77226 9.218C8.77226 8.71533 8.89792 8.24733 9.14926 7.814C9.40059 7.38067 9.73859 7.03833 10.1633 6.787C10.5966 6.53567 11.0646 6.41 11.5673 6.41H14.3753V12.546L10.4623 15.146L9.52626 13.729ZM10.4753 9.192C10.4753 9.49533 10.5793 9.75533 10.7873 9.972C11.0039 10.1887 11.2639 10.297 11.5673 10.297H12.6723V8.1H11.5673C11.2726 8.1 11.0169 8.20833 10.8003 8.425C10.5836 8.633 10.4753 8.88867 10.4753 9.192ZM15.6353 3.654L17.3513 3.225V12H15.6353V3.654ZM22.7724 12C22.2611 12 21.7888 11.8743 21.3554 11.623C20.9308 11.3717 20.5928 11.0337 20.3414 10.609C20.0901 10.1757 19.9644 9.70767 19.9644 9.205C19.9644 8.70233 20.0901 8.23867 20.3414 7.814C20.5928 7.38067 20.9351 7.03833 21.3684 6.787C21.8018 6.53567 22.2698 6.41 22.7724 6.41H25.5544V9.192C25.5544 9.504 25.6628 9.76833 25.8794 9.985C26.0961 10.193 26.3561 10.297 26.6594 10.297L26.9584 11.155L26.6594 12C26.2868 12 25.9358 11.9307 25.6064 11.792C25.2858 11.6447 24.9998 11.441 24.7484 11.181C24.4884 11.441 24.1894 11.6447 23.8514 11.792C23.5134 11.9307 23.1538 12 22.7724 12ZM21.6674 9.205C21.6674 9.50833 21.7758 9.76833 21.9924 9.985C22.2091 10.193 22.4691 10.297 22.7724 10.297C23.0758 10.297 23.3314 10.193 23.5394 9.985C23.7474 9.76833 23.8514 9.50833 23.8514 9.205V8.1H22.7724C22.4691 8.1 22.2091 8.20833 21.9924 8.425C21.7758 8.64167 21.6674 8.90167 21.6674 9.205ZM26.3482 11.155L26.6602 10.297H27.4662V3.303L29.1562 2.887V6.41H33.4982C34.0095 6.41 34.4775 6.53567 34.9022 6.787C35.3268 7.03833 35.6648 7.37633 35.9162 7.801C36.1675 8.22567 36.2932 8.69367 36.2932 9.205C36.2932 9.58633 36.2195 9.95033 36.0722 10.297H36.9432L37.2422 11.155L36.9432 12H26.6602L26.3482 11.155ZM33.4982 10.297C33.8015 10.297 34.0615 10.193 34.2782 9.985C34.4948 9.76833 34.6032 9.50833 34.6032 9.205C34.6032 8.90167 34.4948 8.646 34.2782 8.438C34.0615 8.22133 33.8015 8.113 33.4982 8.113H29.1562V10.297H33.4982ZM36.9044 12L36.6314 11.155L36.9174 10.297H36.9434C37.2467 10.297 37.5024 10.193 37.7104 9.985C37.927 9.76833 38.0354 9.50833 38.0354 9.205V7.047H39.7384V9.387C39.7384 9.63833 39.825 9.855 39.9984 10.037C40.1804 10.2103 40.397 10.297 40.6484 10.297C40.8997 10.297 41.112 10.2103 41.2854 10.037C41.4674 9.855 41.5584 9.63833 41.5584 9.387V7.047H43.2614V9.387C43.2614 9.63833 43.348 9.855 43.5214 10.037C43.7034 10.2103 43.92 10.297 44.1714 10.297C44.4227 10.297 44.635 10.2103 44.8084 10.037C44.9904 9.855 45.0814 9.63833 45.0814 9.387V6.982L46.7844 6.553V9.374C46.7844 9.85067 46.6674 10.2927 46.4334 10.7C46.1994 11.0987 45.883 11.415 45.4844 11.649C45.0857 11.883 44.648 12 44.1714 12C43.8594 12 43.5474 11.935 43.2354 11.805C42.932 11.6663 42.659 11.493 42.4164 11.285C42.1824 11.5103 41.9137 11.688 41.6104 11.818C41.307 11.9393 40.9864 12 40.6484 12C40.319 12 39.9897 11.9263 39.6604 11.779C39.3397 11.623 39.0667 11.4367 38.8414 11.22C38.59 11.4627 38.2997 11.6533 37.9704 11.792C37.6497 11.9307 37.3074 12 36.9434 12H36.9044ZM48.4755 13.729L51.0755 12H50.3215C49.9228 12 49.5198 11.8787 49.1125 11.636C48.7138 11.3847 48.3801 11.0467 48.1115 10.622C47.8515 10.1973 47.7215 9.72933 47.7215 9.218C47.7215 8.71533 47.8471 8.24733 48.0985 7.814C48.3498 7.38067 48.6878 7.03833 49.1125 6.787C49.5458 6.53567 50.0138 6.41 50.5165 6.41H53.3245V10.297H53.9745L54.2735 11.155L53.9745 12H53.3245V12.546L49.4115 15.146L48.4755 13.729ZM49.4245 9.192C49.4245 9.49533 49.5285 9.75533 49.7365 9.972C49.9531 10.1887 50.2131 10.297 50.5165 10.297H51.6215V8.1H50.5165C50.2218 8.1 49.9661 8.20833 49.7495 8.425C49.5328 8.633 49.4245 8.88867 49.4245 9.192ZM53.9545 12L53.6685 11.155L53.9675 10.297H53.9805C54.2838 10.297 54.5438 10.193 54.7605 9.985C54.9771 9.76833 55.0855 9.50833 55.0855 9.205V6.943L56.7755 6.527V9.205C56.7755 9.50833 56.8838 9.76833 57.1005 9.985C57.3171 10.193 57.5771 10.297 57.8805 10.297L58.1795 11.155L57.8805 12C57.4991 12 57.1395 11.9263 56.8015 11.779C56.4635 11.623 56.1731 11.415 55.9305 11.155C55.6791 11.415 55.3845 11.623 55.0465 11.779C54.7171 11.9263 54.3618 12 53.9805 12H53.9545ZM53.7335 3.849H56.9055V5.214H53.7335V3.849ZM57.8519 12L57.5659 11.155L57.8519 10.297H57.8779C58.1813 10.297 58.4413 10.1887 58.6579 9.972C58.8746 9.75533 58.9829 9.49967 58.9829 9.205V6.397H61.7779C62.2806 6.397 62.7443 6.52267 63.1689 6.774C63.6023 7.02533 63.9446 7.36767 64.1959 7.801C64.4473 8.22567 64.5729 8.69367 64.5729 9.205C64.5729 9.70767 64.4473 10.1757 64.1959 10.609C63.9446 11.0337 63.6023 11.3717 63.1689 11.623C62.7443 11.8743 62.2806 12 61.7779 12C61.3966 12 61.0369 11.9263 60.6989 11.779C60.3609 11.623 60.0706 11.4107 59.8279 11.142C59.5766 11.4107 59.2819 11.623 58.9439 11.779C58.6146 11.9263 58.2593 12 57.8779 12H57.8519ZM60.6859 9.205C60.6859 9.50833 60.7899 9.76833 60.9979 9.985C61.2146 10.193 61.4746 10.297 61.7779 10.297C62.0813 10.297 62.3369 10.1887 62.5449 9.972C62.7616 9.75533 62.8699 9.49967 62.8699 9.205C62.8699 8.90167 62.7616 8.64167 62.5449 8.425C62.3369 8.20833 62.0813 8.1 61.7779 8.1H60.6859V9.205ZM67.6454 13.729L70.2454 12H69.4914C69.0927 12 68.6897 11.8787 68.2824 11.636C67.8837 11.3847 67.5501 11.0467 67.2814 10.622C67.0214 10.1973 66.8914 9.72933 66.8914 9.218C66.8914 8.71533 67.0171 8.24733 67.2684 7.814C67.5197 7.38067 67.8577 7.03833 68.2824 6.787C68.7157 6.53567 69.1837 6.41 69.6864 6.41H72.4944V12.546L68.5814 15.146L67.6454 13.729ZM68.5944 9.192C68.5944 9.49533 68.6984 9.75533 68.9064 9.972C69.1231 10.1887 69.3831 10.297 69.6864 10.297H70.7914V8.1H69.6864C69.3917 8.1 69.1361 8.20833 68.9194 8.425C68.7027 8.633 68.5944 8.88867 68.5944 9.192ZM77.832 15.497C77.3207 15.497 76.8483 15.3713 76.415 15.12C75.9903 14.8687 75.6523 14.5263 75.401 14.093C75.1497 13.6683 75.024 13.2003 75.024 12.689V9.543L76.74 9.114V12.689C76.74 12.9923 76.844 13.2523 77.052 13.469C77.2687 13.6857 77.5287 13.794 77.832 13.794H79.132C79.4267 13.794 79.678 13.6857 79.886 13.469C80.1027 13.2523 80.211 12.9923 80.211 12.689V6.943L81.927 6.514V12.689C81.927 13.2003 81.8013 13.6683 81.55 14.093C81.2987 14.5263 80.9607 14.8687 80.536 15.12C80.1113 15.3713 79.6433 15.497 79.132 15.497H77.832ZM77.143 6.267H78.612V7.645H77.143V6.267ZM85.9951 12C85.4838 12 85.0114 11.8743 84.5781 11.623C84.1534 11.3717 83.8154 11.0337 83.5641 10.609C83.3128 10.1757 83.1871 9.70767 83.1871 9.205V3.654L84.8901 3.225V9.205C84.8901 9.50833 84.9984 9.76833 85.2151 9.985C85.4318 10.193 85.6918 10.297 85.9951 10.297L86.2941 11.155L85.9951 12ZM85.686 11.155L85.998 10.297H86.869C87.1724 10.297 87.4324 10.193 87.649 9.985C87.8657 9.76833 87.974 9.50833 87.974 9.205V6.943L89.677 6.527V9.205C89.677 9.50833 89.781 9.76833 89.989 9.985C90.2057 10.193 90.4657 10.297 90.769 10.297L91.068 11.155L90.769 12C90.3877 12 90.028 11.9263 89.69 11.779C89.3607 11.623 89.0704 11.415 88.819 11.155C88.5677 11.415 88.273 11.623 87.935 11.779C87.6057 11.9263 87.2504 12 86.869 12H85.998L85.686 11.155ZM86.505 3.849H89.69V5.214H86.505V3.849ZM90.4595 11.155L90.7715 10.297H91.7335C92.0368 10.297 92.2968 10.193 92.5135 9.985C92.7302 9.76833 92.8385 9.50833 92.8385 9.205V7.047H94.5285V9.387C94.5285 9.63833 94.6152 9.855 94.7885 10.037C94.9705 10.2103 95.1872 10.297 95.4385 10.297C95.6898 10.297 95.9022 10.2103 96.0755 10.037C96.2575 9.855 96.3485 9.63833 96.3485 9.387V7.047H98.0515V9.387C98.0515 9.63833 98.1382 9.855 98.3115 10.037C98.4935 10.2103 98.7102 10.297 98.9615 10.297C99.2128 10.297 99.4295 10.2103 99.6115 10.037C99.7935 9.855 99.8845 9.63833 99.8845 9.387V6.982L101.574 6.553V9.205C101.574 9.50833 101.678 9.76833 101.886 9.985C102.103 10.193 102.363 10.297 102.666 10.297L102.978 11.155L102.666 12C102.302 12 101.96 11.935 101.639 11.805C101.319 11.6663 101.037 11.48 100.794 11.246C100.56 11.48 100.283 11.6663 99.9625 11.805C99.6505 11.935 99.3168 12 98.9615 12C98.6495 12 98.3375 11.935 98.0255 11.805C97.7135 11.6663 97.4405 11.493 97.2065 11.285C96.9725 11.5103 96.7038 11.688 96.4005 11.818C96.0972 11.9393 95.7765 12 95.4385 12C95.1092 12 94.7798 11.9263 94.4505 11.779C94.1298 11.623 93.8568 11.4367 93.6315 11.22C93.3802 11.4627 93.0898 11.6533 92.7605 11.792C92.4398 11.9307 92.0975 12 91.7335 12H90.7715L90.4595 11.155ZM102.355 11.155L102.667 10.297H103.616C103.919 10.297 104.179 10.193 104.396 9.985C104.613 9.76833 104.721 9.50833 104.721 9.205V6.943L106.424 6.527V9.205C106.424 9.70767 106.298 10.1757 106.047 10.609C105.796 11.0337 105.453 11.3717 105.02 11.623C104.595 11.8743 104.127 12 103.616 12H102.667L102.355 11.155ZM103.655 13.3H105.124V14.665H103.655V13.3ZM107.481 10.297H110.276V9.205C110.276 8.90167 110.168 8.646 109.951 8.438C109.743 8.22133 109.488 8.113 109.184 8.113H107.676V6.41H109.184C109.696 6.41 110.164 6.53567 110.588 6.787C111.013 7.03833 111.347 7.37633 111.589 7.801C111.841 8.22567 111.966 8.69367 111.966 9.205V12H107.481V10.297Z" fill="#E52041"/></svg>',
                        "target":"_blank",
                        "title":"متوسطه اول تام‌لند"
                    },
                    {
                        "url":"https://mid2.tamland.ir",
                        "bgcolor":"#fff",
                        "color":"#222",
                        "icon":'<svg width="117" height="38" viewBox="0 0 117 38" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100.294 12.2761V4.53906L92.3633 12.4509V20.1879H100.119L108.05 12.2761H100.294Z" fill="#1D4A00"/><path d="M108.644 20.6199V12.8828L100.715 20.7929V28.5299H108.471L116.4 20.6199H108.644Z" fill="#1D4A00"/><path d="M109.066 29.1149V36.852L116.997 28.9402V21.2031L109.066 29.1149Z" fill="#1D4A00"/><path d="M91.7654 11.8571L99.6964 3.94531H91.9017L84.0078 11.8571H91.7654Z" fill="#1D4A00"/><path d="M91.6109 29.314C93.8122 31.5099 94.9164 34.3926 94.9253 37.277H99.5031C99.4801 33.2399 97.9318 29.2098 94.851 26.1365C91.7454 23.0385 87.6665 21.4904 83.5859 21.4922V26.0077C86.4933 26.0059 89.3989 27.1074 91.6109 29.314Z" fill="#959595"/><path d="M83.5948 32.2812C83.5912 32.2812 83.5895 32.2812 83.5859 32.2812V37.2804H88.6397C88.6308 35.9494 88.1088 34.6979 87.1639 33.7552C86.2084 32.8055 84.9414 32.2812 83.5948 32.2812Z" fill="#1D4A00"/><path d="M91.0233 29.9004C88.9724 27.8545 86.2792 26.8342 83.5859 26.8359V28.9454V31.452C83.5895 31.452 83.5912 31.452 83.5948 31.452C85.1644 31.452 86.6401 32.0628 87.7496 33.1696C88.852 34.2694 89.4608 35.7275 89.4696 37.2809H91.9416H94.1022C94.0916 34.4918 92.9998 31.8722 91.0233 29.9004Z" fill="#959595"/><path d="M99.8694 29.3688V21.0351H91.5171V12.7031H83.5859V20.6714C87.8771 20.6697 92.1701 22.2972 95.4367 25.5558C98.6785 28.7898 100.308 33.0316 100.331 37.2806H108.224V29.3688H99.8694Z" fill="#959595"/><path d="M72.0784 22.7381L75.0353 25.6878L78.0222 22.7063L75.0353 19.7266L72.0784 22.6763L69.1198 19.7266L66.1328 22.7063L69.1198 25.6878L72.0784 22.7381Z" fill="#1D4A00"/><path d="M23.863 19.7303L20.875 22.7109L23.863 25.6916L26.8509 22.7109L23.863 19.7303Z" fill="#1D4A00"/><path d="M40.7734 25.8786V26.8671V31.0807V37.282H44.9974V31.0807H57.4303V20.6641H45.9989C43.1181 20.6641 40.7734 23.003 40.7734 25.8786ZM53.2064 26.8654H44.9974V25.8786C44.9974 25.3278 45.4468 24.8795 45.9989 24.8795H53.2046V26.8654H53.2064Z" fill="#959595"/><path d="M72.222 33.0666H65.0146C64.4625 33.0666 64.013 32.6183 64.013 32.0675V20.6641H59.7891V32.0675C59.7891 34.9431 62.1337 37.282 65.0146 37.282H76.4459V26.8654H72.222V33.0666Z" fill="#959595"/><path d="M34.1844 32.0675C34.1844 32.6183 33.7349 33.0666 33.1828 33.0666H25.9754V26.8654H21.7514V33.0666H14.544C13.9919 33.0666 13.5424 32.6183 13.5424 32.0675V27.4338V23.2854H9.3185V27.4338V32.0675C9.3185 32.6183 8.86903 33.0666 8.31693 33.0666H0V37.282H8.31693C9.48307 37.282 10.5607 36.899 11.4314 36.2529C12.302 36.899 13.3796 37.282 14.5458 37.282H21.7532H25.9771H33.1846C36.0672 37.282 38.4101 34.9431 38.4101 32.0675V20.6641H34.1861V32.0675H34.1844Z" fill="#959595"/><path d="M1.01252 10.7016C1.01252 10.0982 1.16082 9.54593 1.45742 9.04478C1.75402 8.5334 2.15289 8.12942 2.65403 7.83282C3.16541 7.53622 3.72281 7.38792 4.32623 7.38792H7.6246C8.22802 7.38792 8.7803 7.53622 9.28145 7.83282C9.79283 8.12942 10.1968 8.5334 10.4934 9.04478C10.79 9.54593 10.9383 10.0982 10.9383 10.7016C10.9383 11.2948 10.79 11.8471 10.4934 12.3585C10.1968 12.8596 9.79283 13.2585 9.28145 13.5551C8.7803 13.8517 8.22802 14 7.6246 14C7.02118 14 6.46889 13.8517 5.96774 13.5551C5.4666 13.2585 5.06772 12.8596 4.77113 12.3585C4.47453 11.8573 4.32623 11.3051 4.32623 10.7016V9.39763C3.96827 9.39763 3.66144 9.52547 3.40576 9.78116C3.1603 10.0368 3.03757 10.3437 3.03757 10.7016V17.1756L1.01252 17.6819V10.7016ZM6.35127 10.7016C6.35127 11.0596 6.474 11.3664 6.71946 11.6221C6.96492 11.8676 7.26664 11.9903 7.6246 11.9903C7.98256 11.9903 8.28427 11.8676 8.52973 11.6221C8.78542 11.3664 8.91326 11.0596 8.91326 10.7016C8.91326 10.3437 8.78542 10.0368 8.52973 9.78116C8.28427 9.52547 7.98256 9.39763 7.6246 9.39763H6.35127V10.7016ZM12.4596 16.0404L15.5279 14H14.6381C14.1676 14 13.692 13.8568 13.2113 13.5704C12.7409 13.2738 12.3471 12.875 12.03 12.3738C11.7232 11.8727 11.5698 11.3204 11.5698 10.717C11.5698 10.1238 11.7181 9.57149 12.0147 9.06012C12.3113 8.54875 12.7102 8.14476 13.2113 7.84816C13.7227 7.55156 14.275 7.40327 14.8682 7.40327H18.1819V14.6443L13.5642 17.7126L12.4596 16.0404ZM13.5795 10.6863C13.5795 11.0443 13.7022 11.3511 13.9477 11.6068C14.2034 11.8625 14.5102 11.9903 14.8682 11.9903H16.1722V9.39763H14.8682C14.5204 9.39763 14.2187 9.52547 13.963 9.78116C13.7074 10.0266 13.5795 10.3283 13.5795 10.6863ZM18.9786 11.9903H22.2769V10.7016C22.2769 10.3437 22.1491 10.042 21.8934 9.7965C21.6479 9.54081 21.3462 9.41297 20.9883 9.41297H19.2087V7.40327H20.9883C21.5917 7.40327 22.144 7.55156 22.6451 7.84816C23.1463 8.14476 23.54 8.54363 23.8264 9.04478C24.123 9.54593 24.2713 10.0982 24.2713 10.7016V14H18.9786V11.9903ZM29.6621 14C29.0586 14 28.5012 13.8517 27.9899 13.5551C27.4887 13.2585 27.0898 12.8596 26.7932 12.3585C26.4966 11.8471 26.3483 11.2948 26.3483 10.7016C26.3483 10.1084 26.4966 9.56127 26.7932 9.06012C27.0898 8.54875 27.4938 8.14476 28.0052 7.84816C28.5166 7.55156 29.0689 7.40327 29.6621 7.40327H32.9451V10.6863C32.9451 11.0545 33.0729 11.3664 33.3286 11.6221C33.5843 11.8676 33.8911 11.9903 34.2491 11.9903L34.6019 13.0028L34.2491 14C33.8093 14 33.3951 13.9182 33.0064 13.7545C32.628 13.5807 32.2905 13.3403 31.9939 13.0335C31.6871 13.3403 31.3342 13.5807 30.9354 13.7545C30.5365 13.9182 30.1121 14 29.6621 14ZM28.358 10.7016C28.358 11.0596 28.4859 11.3664 28.7416 11.6221C28.9973 11.8676 29.3041 11.9903 29.6621 11.9903C30.02 11.9903 30.3217 11.8676 30.5672 11.6221C30.8126 11.3664 30.9354 11.0596 30.9354 10.7016V9.39763H29.6621C29.3041 9.39763 28.9973 9.52547 28.7416 9.78116C28.4859 10.0368 28.358 10.3437 28.358 10.7016ZM33.4215 13.0028L33.7897 11.9903H34.7408V3.73671L36.7352 3.24579V7.40327H41.8592C42.4626 7.40327 43.0149 7.55156 43.516 7.84816C44.0172 8.14476 44.4161 8.54363 44.7126 9.04478C45.0092 9.54593 45.1575 10.0982 45.1575 10.7016C45.1575 11.1516 45.0706 11.5812 44.8967 11.9903H45.9246L46.2775 13.0028L45.9246 14H33.7897L33.4215 13.0028ZM41.8592 11.9903C42.2171 11.9903 42.524 11.8676 42.7797 11.6221C43.0353 11.3664 43.1632 11.0596 43.1632 10.7016C43.1632 10.3437 43.0353 10.042 42.7797 9.7965C42.524 9.54081 42.2171 9.41297 41.8592 9.41297H36.7352V11.9903H41.8592ZM45.4186 14L45.0964 13.0028L45.4339 11.9903H45.4646C45.8226 11.9903 46.1243 11.8676 46.3697 11.6221C46.6254 11.3664 46.7533 11.0596 46.7533 10.7016V8.15499H48.763V10.9164C48.763 11.213 48.8653 11.4687 49.0698 11.6835C49.2846 11.888 49.5403 11.9903 49.8369 11.9903C50.1335 11.9903 50.384 11.888 50.5886 11.6835C50.8034 11.4687 50.9108 11.213 50.9108 10.9164V8.15499H52.9205V10.9164C52.9205 11.213 53.0227 11.4687 53.2273 11.6835C53.4421 11.888 53.6977 11.9903 53.9943 11.9903C54.2909 11.9903 54.5415 11.888 54.7461 11.6835C54.9608 11.4687 55.0682 11.213 55.0682 10.9164V8.07828L57.0779 7.57202V10.9011C57.0779 11.4636 56.9399 11.9852 56.6637 12.4659C56.3876 12.9363 56.0143 13.3096 55.5438 13.5858C55.0733 13.8619 54.5569 14 53.9943 14C53.6262 14 53.258 13.9233 52.8898 13.7699C52.5318 13.6062 52.2096 13.4017 51.9233 13.1562C51.6471 13.4221 51.3301 13.6318 50.9721 13.7852C50.6142 13.9284 50.2357 14 49.8369 14C49.4482 14 49.0596 13.9131 48.6709 13.7392C48.2925 13.5551 47.9703 13.3352 47.7044 13.0795C47.4078 13.3659 47.0652 13.5909 46.6766 13.7545C46.2982 13.9182 45.8942 14 45.4646 14H45.4186ZM58.6134 16.0404L61.6816 14H60.7918C60.3214 14 59.8458 13.8568 59.3651 13.5704C58.8946 13.2738 58.5009 12.875 58.1838 12.3738C57.877 11.8727 57.7236 11.3204 57.7236 10.717C57.7236 10.1238 57.8719 9.57149 58.1685 9.06012C58.4651 8.54875 58.8639 8.14476 59.3651 7.84816C59.8765 7.55156 60.4288 7.40327 61.0219 7.40327H64.3357V11.9903H65.1027L65.4556 13.0028L65.1027 14H64.3357V14.6443L59.7179 17.7126L58.6134 16.0404ZM59.7333 10.6863C59.7333 11.0443 59.856 11.3511 60.1015 11.6068C60.3572 11.8625 60.664 11.9903 61.0219 11.9903H62.326V9.39763H61.0219C60.6742 9.39763 60.3725 9.52547 60.1168 9.78116C59.8611 10.0266 59.7333 10.3283 59.7333 10.6863ZM64.6189 14L64.2814 13.0028L64.6342 11.9903H64.6496C65.0075 11.9903 65.3143 11.8676 65.57 11.6221C65.8257 11.3664 65.9536 11.0596 65.9536 10.7016V8.03226L67.9479 7.54134V10.7016C67.9479 11.0596 68.0758 11.3664 68.3314 11.6221C68.5871 11.8676 68.894 11.9903 69.2519 11.9903L69.6048 13.0028L69.2519 14C68.8019 14 68.3775 13.9131 67.9786 13.7392C67.5797 13.5551 67.2371 13.3096 66.9507 13.0028C66.6541 13.3096 66.3064 13.5551 65.9075 13.7392C65.5189 13.9131 65.0996 14 64.6496 14H64.6189ZM64.3581 4.38104H68.1013V5.99187H64.3581V4.38104ZM68.758 14L68.4205 13.0028L68.758 11.9903H68.7887C69.1467 11.9903 69.4535 11.8625 69.7092 11.6068C69.9649 11.3511 70.0927 11.0494 70.0927 10.7016V7.38792H73.3911C73.9843 7.38792 74.5314 7.53622 75.0326 7.83282C75.5439 8.12942 75.9479 8.5334 76.2445 9.04478C76.5411 9.54593 76.6894 10.0982 76.6894 10.7016C76.6894 11.2948 76.5411 11.8471 76.2445 12.3585C75.9479 12.8596 75.5439 13.2585 75.0326 13.5551C74.5314 13.8517 73.9843 14 73.3911 14C72.9411 14 72.5166 13.9131 72.1177 13.7392C71.7189 13.5551 71.3762 13.3045 71.0899 12.9875C70.7933 13.3045 70.4455 13.5551 70.0467 13.7392C69.658 13.9131 69.2387 14 68.7887 14H68.758ZM72.1024 10.7016C72.1024 11.0596 72.2251 11.3664 72.4706 11.6221C72.7263 11.8676 73.0331 11.9903 73.3911 11.9903C73.749 11.9903 74.0507 11.8625 74.2962 11.6068C74.5519 11.3511 74.6797 11.0494 74.6797 10.7016C74.6797 10.3437 74.5519 10.0368 74.2962 9.78116C74.0507 9.52547 73.749 9.39763 73.3911 9.39763H72.1024V10.7016Z" fill="#1D4A00"/></svg>',
                        "target":"_blank",
                        "title":"متوسطه دوم تام‌لند"
                    },
					                    {
                        "url":"https://tizhooshan.tamland.ir",
                        "bgcolor":"#fff",
                        "color":"#222",
                        "icon":'<svg width="111" height="32" viewBox="0 0 111 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M94.8925 8.53247V1.271L87.5547 8.69649V15.958H94.7305L102.068 8.53247H94.8925Z" fill="#C4161C"/><path d="M102.616 16.3523V9.09082L95.2793 16.5147V23.7762H102.455L109.791 16.3523H102.616Z" fill="#C4161C"/><path d="M103.008 24.3357V31.5971L110.346 24.1716V16.9102L103.008 24.3357Z" fill="#C4161C"/><path d="M86.9997 8.13595L94.3375 0.710449H87.1257L79.8223 8.13595H86.9997Z" fill="#C4161C"/><path d="M86.8545 24.5197C88.8912 26.5807 89.9128 29.2862 89.921 31.9933H94.1564C94.1352 28.2043 92.7026 24.422 89.8522 21.5376C86.9789 18.63 83.2051 17.1771 79.4297 17.1787V21.4167C82.1196 21.415 84.8079 22.4488 86.8545 24.5197Z" fill="#58595B"/><path d="M79.4379 27.3018C79.4346 27.3018 79.433 27.3018 79.4297 27.3018V31.9936H84.1055C84.0973 30.7445 83.6143 29.5698 82.7401 28.6851C81.856 27.7938 80.6838 27.3018 79.4379 27.3018Z" fill="#C4161C"/><path d="M86.3108 25.0695C84.4133 23.1493 81.9215 22.1917 79.4297 22.1934V24.1732V26.5257C79.433 26.5257 79.4346 26.5257 79.4379 26.5257C80.8901 26.5257 82.2555 27.099 83.282 28.1377C84.3019 29.1699 84.8651 30.5383 84.8733 31.9963H87.1605H89.1595C89.1496 29.3786 88.1395 26.92 86.3108 25.0695Z" fill="#58595B"/><path d="M94.4953 24.5685V16.7471H86.7677V8.92725H79.4297V16.4058C83.3999 16.4041 87.3718 17.9316 90.3941 20.99C93.3935 24.0251 94.9014 28.0062 94.9227 31.994H102.225V24.5685H94.4953Z" fill="#58595B"/><path d="M68.788 18.3508L71.5238 21.1192L74.2873 18.321L71.5238 15.5244L68.788 18.2928L66.0507 15.5244L63.2871 18.321L66.0507 21.1192L68.788 18.3508Z" fill="#C4161C"/><path d="M24.1747 15.5248L21.4102 18.3223L24.1747 21.1197L26.9392 18.3223L24.1747 15.5248Z" fill="#C4161C"/><path d="M39.8145 21.2968V22.2246V26.1792V31.9994H43.7225V26.1792H55.2256V16.4028H44.6491C41.9838 16.4028 39.8145 18.598 39.8145 21.2968ZM51.3175 22.223H43.7225V21.2968C43.7225 20.7799 44.1383 20.3591 44.6491 20.3591H51.3159V22.223H51.3175Z" fill="#636363"/><path d="M68.9191 28.0431H62.2507C61.7399 28.0431 61.324 27.6223 61.324 27.1054V16.4028H57.416V27.1054C57.416 29.8042 59.5853 31.9994 62.2507 31.9994H72.8271V22.223H68.9191V28.0431Z" fill="#636363"/><path d="M33.7254 27.1054C33.7254 27.6223 33.3095 28.0431 32.7987 28.0431H26.1303V22.223H22.2223V28.0431H15.5539C15.0431 28.0431 14.6273 27.6223 14.6273 27.1054V22.7564V18.8631H10.7192V22.7564V27.1054C10.7192 27.6223 10.3034 28.0431 9.79257 28.0431H2.09766V31.9994H9.79257C10.8715 31.9994 11.8686 31.6399 12.6741 31.0335C13.4796 31.6399 14.4766 31.9994 15.5556 31.9994H22.2239H26.132H32.8004C35.4674 31.9994 37.6351 29.8042 37.6351 27.1054V16.4028H33.727V27.1054H33.7254Z" fill="#636363"/><path d="M3.102 12.959C2.66933 12.959 2.26967 12.8527 1.903 12.64C1.54367 12.4273 1.25767 12.1377 1.045 11.771C0.832333 11.4117 0.726 11.0157 0.726 10.583V7.921L2.178 7.558V10.583C2.178 10.8397 2.266 11.0597 2.442 11.243C2.62533 11.4263 2.84533 11.518 3.102 11.518H4.202C4.45133 11.518 4.664 11.4263 4.84 11.243C5.02333 11.0597 5.115 10.8397 5.115 10.583V5.721L6.567 5.358V10.583C6.567 11.0157 6.46067 11.4117 6.248 11.771C6.03533 12.1377 5.74933 12.4273 5.39 12.64C5.03067 12.8527 4.63467 12.959 4.202 12.959H3.102ZM2.519 5.149H3.762V6.315H2.519V5.149ZM10.0092 10C9.57656 10 9.17689 9.89367 8.81023 9.681C8.45089 9.46833 8.16489 9.18233 7.95223 8.823C7.73956 8.45633 7.63323 8.06033 7.63323 7.635V2.938L9.07423 2.575V7.635C9.07423 7.89167 9.16589 8.11167 9.34923 8.295C9.53256 8.471 9.75256 8.559 10.0092 8.559L10.2622 9.285L10.0092 10ZM9.74772 9.285L10.0117 8.559H11.9917L12.2557 9.296L11.9917 10H10.0117L9.74772 9.285ZM11.7243 9.285L11.9883 8.559H13.9683L14.2323 9.296L13.9683 10H11.9883L11.7243 9.285ZM13.7008 9.285L13.9648 8.559H15.9448L16.2088 9.296L15.9448 10H13.9648L13.7008 9.285ZM15.6774 9.285L15.9414 8.559H17.9214L18.1854 9.296L17.9214 10H15.9414L15.6774 9.285ZM17.654 9.285L17.918 8.559H19.898L20.162 9.296L19.898 10H17.918L17.654 9.285ZM19.6305 9.285L19.8945 8.559H21.8745L22.1385 9.296L21.8745 10H19.8945L19.6305 9.285ZM21.6071 9.285L21.8711 8.559H23.8511L24.1151 9.296L23.8511 10H21.8711L21.6071 9.285ZM23.5837 9.285L23.8477 8.559H25.8277L26.0917 9.296L25.8277 10H23.8477L23.5837 9.285ZM25.7912 10L25.5602 9.285L25.8022 8.559H25.8242C26.0809 8.559 26.2972 8.471 26.4732 8.295C26.6566 8.11167 26.7482 7.89167 26.7482 7.635V5.809H28.1892V7.789C28.1892 8.00167 28.2626 8.185 28.4092 8.339C28.5632 8.48567 28.7466 8.559 28.9592 8.559C29.1719 8.559 29.3516 8.48567 29.4982 8.339C29.6522 8.185 29.7292 8.00167 29.7292 7.789V5.809H31.1702V7.789C31.1702 8.00167 31.2436 8.185 31.3902 8.339C31.5442 8.48567 31.7276 8.559 31.9402 8.559C32.1529 8.559 32.3326 8.48567 32.4792 8.339C32.6332 8.185 32.7102 8.00167 32.7102 7.789V5.754L34.1512 5.391V7.778C34.1512 8.18133 34.0522 8.55533 33.8542 8.9C33.6562 9.23733 33.3886 9.505 33.0512 9.703C32.7139 9.901 32.3436 10 31.9402 10C31.6762 10 31.4122 9.945 31.1482 9.835C30.8916 9.71767 30.6606 9.571 30.4552 9.395C30.2572 9.58567 30.0299 9.736 29.7732 9.846C29.5166 9.94867 29.2452 10 28.9592 10C28.6806 10 28.4019 9.93767 28.1232 9.813C27.8519 9.681 27.6209 9.52333 27.4302 9.34C27.2176 9.54533 26.9719 9.70667 26.6932 9.824C26.4219 9.94133 26.1322 10 25.8242 10H25.7912ZM29.1242 3.422H30.6422V2.366L31.8192 2.729V4.555H29.1242V3.422ZM35.5822 11.463L37.7822 10H37.1442C36.8068 10 36.4658 9.89733 36.1212 9.692C35.7838 9.47933 35.5015 9.19333 35.2742 8.834C35.0542 8.47467 34.9442 8.07867 34.9442 7.646C34.9442 7.22067 35.0505 6.82467 35.2632 6.458C35.4758 6.09133 35.7618 5.80167 36.1212 5.589C36.4878 5.37633 36.8838 5.27 37.3092 5.27H39.6852V8.559H40.2352L40.4882 9.285L40.2352 10H39.6852V10.462L36.3742 12.662L35.5822 11.463ZM36.3852 7.624C36.3852 7.88067 36.4732 8.10067 36.6492 8.284C36.8325 8.46733 37.0525 8.559 37.3092 8.559H38.2442V6.7H37.3092C37.0598 6.7 36.8435 6.79167 36.6602 6.975C36.4768 7.151 36.3852 7.36733 36.3852 7.624ZM39.9762 9.285L40.2402 8.559H42.2202L42.4842 9.296L42.2202 10H40.2402L39.9762 9.285ZM41.9528 9.285L42.2168 8.559H44.1968L44.4608 9.296L44.1968 10H42.2168L41.9528 9.285ZM43.9294 9.285L44.1934 8.559H46.1734L46.4374 9.296L46.1734 10H44.1934L43.9294 9.285ZM45.9059 9.285L46.1699 8.559H48.1499L48.4139 9.296L48.1499 10H46.1699L45.9059 9.285ZM47.8825 9.285L48.1465 8.559H48.9935C48.7955 8.28767 48.6965 7.97967 48.6965 7.635C48.6965 7.29033 48.7955 6.97867 48.9935 6.7H48.6855V5.27H54.1085C54.5412 5.27 54.9372 5.37633 55.2965 5.589C55.6558 5.80167 55.9418 6.08767 56.1545 6.447C56.3672 6.80633 56.4735 7.20233 56.4735 7.635C56.4735 8.06767 56.3672 8.46367 56.1545 8.823C55.9418 9.18233 55.6558 9.46833 55.2965 9.681C54.9372 9.89367 54.5412 10 54.1085 10H48.1465L47.8825 9.285ZM50.1485 7.635C50.1485 7.89167 50.2365 8.11167 50.4125 8.295C50.5885 8.471 50.8048 8.559 51.0615 8.559C51.3182 8.559 51.5345 8.471 51.7105 8.295C51.8938 8.11167 51.9855 7.89167 51.9855 7.635C51.9855 7.37833 51.8938 7.15833 51.7105 6.975C51.5345 6.79167 51.3182 6.7 51.0615 6.7C50.8122 6.7 50.5958 6.79167 50.4125 6.975C50.2365 7.15833 50.1485 7.37833 50.1485 7.635ZM54.1085 8.559C54.3652 8.559 54.5815 8.471 54.7575 8.295C54.9408 8.11167 55.0325 7.89167 55.0325 7.635C55.0325 7.37833 54.9408 7.15833 54.7575 6.975C54.5815 6.79167 54.3652 6.7 54.1085 6.7H53.1405C53.3385 6.99333 53.4375 7.305 53.4375 7.635C53.4375 7.965 53.3385 8.273 53.1405 8.559H54.1085ZM56.6834 11.452L57.6954 10.781V5.732L59.1364 5.369V7.635C59.1364 7.89167 59.2244 8.11167 59.4004 8.295C59.5838 8.471 59.8038 8.559 60.0604 8.559L60.3134 9.285L60.0604 10C59.7598 10 59.4518 9.868 59.1364 9.604V11.551L57.4864 12.651L56.6834 11.452ZM57.8494 3.103H59.0924V4.258H57.8494V3.103ZM59.7848 9.285L60.0488 8.559H62.0288L62.2928 9.296L62.0288 10H60.0488L59.7848 9.285ZM61.7614 9.285L62.0254 8.559H64.0054L64.2694 9.296L64.0054 10H62.0254L61.7614 9.285ZM63.738 9.285L64.002 8.559H65.982L66.246 9.296L65.982 10H64.002L63.738 9.285ZM65.7145 9.285L65.9785 8.559H67.9585L68.2225 9.296L67.9585 10H65.9785L65.7145 9.285ZM67.9331 10L67.6911 9.285L67.9441 8.559H67.9551C68.2117 8.559 68.4317 8.471 68.6151 8.295C68.7984 8.11167 68.8901 7.89167 68.8901 7.635V5.721L70.3201 5.369V7.635C70.3201 7.89167 70.4117 8.11167 70.5951 8.295C70.7784 8.471 70.9984 8.559 71.2551 8.559L71.5081 9.285L71.2551 10C70.9324 10 70.6281 9.93767 70.3421 9.813C70.0561 9.681 69.8104 9.505 69.6051 9.285C69.3924 9.505 69.1431 9.681 68.8571 9.813C68.5784 9.93767 68.2777 10 67.9551 10H67.9331ZM66.7891 11.089H69.4841V12.233H66.7891V11.089ZM70.9889 9.285L71.2529 8.559H72.0559C72.3126 8.559 72.5326 8.471 72.7159 8.295C72.8993 8.11167 72.9909 7.89167 72.9909 7.635V5.721L74.4319 5.369V7.635C74.4319 8.06033 74.3256 8.45633 74.1129 8.823C73.9003 9.18233 73.6106 9.46833 73.2439 9.681C72.8846 9.89367 72.4886 10 72.0559 10H71.2529L70.9889 9.285ZM72.0119 3.103H74.6959V4.258H72.0119V3.103Z" fill="#C4161C"/></svg>',
                        "target":"_blank",
                        "title":"تیزهوشان تام‌لند"
                    }
                ]
                $('.kc_fab_wrapper').kc_fab(links);
            })
        </script>
    <?php
}

add_shortcode('the_content','the_content_func');
function the_content_func(){
    $post_id = get_the_ID();
    return get_the_content($post_id);
}


add_filter('jpeg_quality', function($arg){return 100;});

add_shortcode('comments_count', 'comments_count_shortcode');
function comments_count_shortcode() {
    $comments_count = get_comments_number();
    if ($comments_count > 0) {
        return $comments_count . ' دیدگاه درباره این مقاله';
    } else {
        return 'بدون دیدگاه';
    }
}


function comments_count_number_only_shortcode() {
    $comments_count = get_comments_number();

    return $comments_count;
}
add_shortcode('comments_count_number_only', 'comments_count_number_only_shortcode');

add_filter( 'wp_lazy_loading_enabled', '__return_false' );

remove_action( 'wp_head','rest_output_link_wp_head');
remove_action( 'wp_head','wp_oembed_add_discovery_links');
remove_action( 'template_redirect', 'rest_output_link_header', 11 );
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10);
remove_action( 'wp_head', 'feed_links_extra', 3 ); 
remove_action( 'wp_head', 'feed_links', 2 ); 
remove_action( 'wp_head', 'rsd_link' ); 
remove_action( 'wp_head', 'wlwmanifest_link' ); 
remove_action( 'wp_head', 'index_rel_link' ); 
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); 
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
remove_action( 'wp_head', 'wp_generator' ); 
remove_action('wp_head', 'wp_resource_hints', 2);


// Disable Gutenberg for editors.
add_filter('use_block_editor_for_post', '__return_false');
// Disable Gutenberg for widgets.
add_filter( 'use_widgets_block_editor', '__return_false' );

add_action( 'wp_enqueue_scripts', function() {
    // Remove CSS on the front end.
    wp_dequeue_style( 'wp-block-library' );

    // Remove Gutenberg theme.
    wp_dequeue_style( 'wp-block-library-theme' );

    // Remove inline global CSS on the front end.
    wp_dequeue_style( 'global-styles' );
}, 20 );

add_action( 'gform_after_submission_12', 'set_cookies_analyis_konkoor', 10, 2 );
function set_cookies_analyis_konkoor( $entry, $form ) {
    $cookie_name = "analyisKonkoorUser";
    $cookie_value = rgar( $entry, '4' );
    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
}

add_action('wp_head','is_analyis_page');
function is_analyis_page(){
if(!is_admin()){
    if(is_page( 11670 )){
        if(!isset($_COOKIE['analyisKonkoorUser'])) {
            header('Location: https://tamland.ir/%D8%AA%D8%AD%D9%84%DB%8C%D9%84-%DA%A9%D9%86%DA%A9%D9%88%D8%B1-%D8%A7%D8%B1%D8%AF%DB%8C%D8%A8%D9%87%D8%B4%D8%AA-1403/');
            exit;
        }  
    }else if(is_page( 11662 )){
        if(isset($_COOKIE['analyisKonkoorUser'])) {
            header('Location: https://tamland.ir/%d8%aa%d8%ad%d9%84%db%8c%d9%84-%d9%88%db%8c%d8%af%db%8c%d9%88%d9%87%d8%a7%db%8c-%da%a9%d9%86%da%a9%d9%88%d8%b1-%d8%a7%d8%b1%d8%af%db%8c%d8%a8%d9%87%d8%b4%d8%aa-1403/');
            exit;
        }
    }
}
}


function enqueue_custom_script() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function fuuu(event) {
                event.preventDefault();
                document.getElementById('topstufilters').style.display = 'flex';
            }

            var link = document.querySelector('a[href="#showfilters"]');
            if (link) {
                link.addEventListener('click', fuuu);
            }
        });
    </script>
    <?php
}
add_action('wp_footer', 'enqueue_custom_script');

// Add TinyMCE button
function my_wp_aparat_add_tinymce() {
    global $typenow;
    if( ! in_array( $typenow, array( 'post', 'page', 'faqs' ) ) )
        return ;
    add_filter( 'mce_external_plugins', 'wp_aparat_add_tinymce_plugin' );
}
add_action( 'admin_head', 'my_wp_aparat_add_tinymce' );

//Filter by authors in admin panel
 function filter_by_the_author()
{
    if (is_admin())
    {
        $params = array(
            'name' => 'author', // this is the "name" attribute for filter <select>
            'show_option_all' => 'همه نویسنده ها' // label for all authors (display posts without filter)
        );
        if ( isset($_GET['user']) )
        {
            $params['selected'] = $_GET['user']; // choose selected user by $_GET variable
        }
        wp_dropdown_users( $params ); // print the ready author list
    }
}
add_action('restrict_manage_posts', 'filter_by_the_author');

//wptelegram_bot
add_filter( 'wptelegram_bot_api_use_proxy', '__return_true' );

//Remove Comments Fields
add_filter('comment_form_fields', 'unset_url_field');
function unset_url_field($fields){
    if(isset($fields['url']))
       unset($fields['url']);

    if(isset($fields['email']))
       unset($fields['email']);
       
    return $fields;
}

function change_comment_form_email_to_mobile($fields) {
    // Remove the default email field
    unset($fields['email']);

    // Add a mobile field instead
    $fields['mobile'] = '<p class="comment-form-mobile ast-grid-common-col ast-width-lg-33 ast-width-md-4 ast-float">
        <label for="mobile" class="screen-reader-text">شماره موبایل <span class="required">*</span></label>
        <input id="mobile" name="mobile" type="text" value="" size="30" maxlength="11" required placeholder="شماره موبایل"/>
    </p>
    <style>
    #mobile{
        border-radius: 8px;
        border: 1px solid #c9d4dd;
        background-color: #fff;
    }
    </style>
    ';

    return $fields;
}
add_filter('comment_form_default_fields', 'change_comment_form_email_to_mobile');

function save_mobile_field_with_comment($comment_id) {
    if (isset($_POST['mobile'])) {
        $mobile = sanitize_text_field($_POST['mobile']);
        add_comment_meta($comment_id, 'mobile', $mobile);
    }
}
add_action('comment_post', 'save_mobile_field_with_comment');

function append_mobile_to_comment_text($comment_text, $comment) {
    //if (is_admin()) return $comment_text; // Don't show in admin list view

    if (is_admin()) {
        $mobile = get_comment_meta($comment->comment_ID, 'mobile', true);
        if ($mobile) {
            $comment_text .= '<p><strong>شماره موبایل:</strong> ' . esc_html($mobile) . '</p>';
        }
    }

    return $comment_text;
}
add_filter('comment_text', 'append_mobile_to_comment_text', 10, 2);


add_filter('mime_types', 'dd_add_jfif_files');
function dd_add_jfif_files($mimes){
    $mimes['jfif'] = "image/jpeg";
    return $mimes;
}

function ss_search_filter( $query ) {
    if ( !$query->is_admin && $query->is_search && $query->is_main_query() ) {
        $query->set( 'post__not_in', array( 62347 ) );
    }
}
add_action( 'pre_get_posts', 'ss_search_filter' );



function restrict_numbers_script() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function() {
        function restrictNumbers(input) {
            const allowedChars = /^[0-9]*$/;
            if (!allowedChars.test(input.value)) {
                input.value = input.value.replace(/[^0-9]/g, '');
            }
            if (input.value.length > 11) {
                input.value = input.value.slice(0, 11);
            }
        }

        jQuery('#input_12_4, #input_12_1, #input_16_9, #input_22_1').on('input', function() {
            restrictNumbers(this);
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'restrict_numbers_script');


add_shortcode('hero', 'her_func');
function her_func(){
    if ( wp_is_mobile()) :
	    echo do_shortcode('[elementor-template id="13945"]');
    else :
	    echo do_shortcode('[elementor-template id="3516"]');
     endif;
}


//add_action('wp_footer','lcpscript');
function lcpscript(){
    ?>
    <script>
        new PerformanceObserver((entryList) => {
  for (const entry of entryList.getEntries()) {
    //console.log('LCP candidate:', entry.startTime, entry);
  }
}).observe({type: 'largest-contentful-paint', buffered: true});
    </script>
    <?php
}

//Analytics Code
function google_analytics_code() {
    ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-4DGPQC36MK"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-4DGPQC36MK');
    </script>
    
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-WLFCW7GM');</script>
    <!-- End Google Tag Manager -->

    <!-- Google Optimize -->
    <!--<script async src="https://www.googleoptimize.com/optimize.js?id=OPT-JCLZC27"></script>-->
    <!-- End Google Optimize -->
    <?php
}
add_action('wp_head', 'google_analytics_code', 10);

function after_body_mine(){
    ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WLFCW7GM"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php
}
add_action('after_body', 'after_body_mine');

function sa_clarity(){
    ?>
    <script type="text/javascript" async>
        window.onload = function(){
            (function(c,l,a,r,i,t,y){
                c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
                t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
                y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
            })(window, document, "clarity", "script", "anhwkzlltf");
        };
    </script>
    <?php
}
add_action('wp_footer', 'sa_clarity', 101);

function sa_add_media(){
    if ( !is_singular( 'luckywheel' ) ) { ?>
        <script type="text/javascript" src="https://s1.mediaad.org/serve/15107/retargeting.js" async></script>
    <?php }
}
add_action('wp_footer', 'sa_add_media', 100);

function add_lazy_load_to_videos($content) {
    $content = str_replace('<iframe', '<iframe loading="lazy"', $content);
    $content = str_replace('<video', '<video loading="lazy"', $content);
    return $content;
}

add_filter('the_content', 'add_lazy_load_to_videos');


/*add_filter( 'rest_authentication_errors', function( $result ) {
    // If a previous authentication check was applied,
    // pass that result along without modification.
    if ( true === $result || is_wp_error( $result ) ) {
        return $result;
    }

    // No authentication has been performed yet.
    // Return an error if user is not logged in.
    if ( ! is_user_logged_in() ) {
        return new WP_Error(
            'rest_not_logged_in',
            __( 'You are not currently logged in.' ),
            array( 'status' => 401 )
        );
    }

    // Our custom authentication check should have no effect
    // on logged-in requests
    return $result;
});
*/
class CourseUpdater {

    public function __construct() {
        // فقط در پنل مدیریت وردپرس متاباکس اضافه می‌شود
        if (is_admin()) {
            add_action('add_meta_boxes', array($this, 'add_custom_button_meta_box'));
        }
    }

    // Add update button for get courses info from LMS
    public function add_custom_button_meta_box() {
        add_meta_box(
            'updatesch-button-meta-box', // Unique ID for the meta box
            'به روز رسانی برنامه کلاسی', // Title of the meta box
            array($this, 'render_updatesch'), // Callback function to render the content
            'teacher', // Replace with your custom post type slug
            'side', // Position (e.g., 'side', 'normal', 'advanced')
            'high' // Priority (e.g., 'high', 'low')
        );
    }

    /**
     * Render the content of the meta box
     */
    public function render_updatesch() {
        ?>
        <input type="button" onClick="CourseUpdater.renderUpdatesch()" class="button button-primary button-large" value="بروزرسانی برنامه کلاسی استاد" id="updatesch-button" />
        <script>
        if (typeof jQuery === 'undefined') {
            var script = document.createElement('script');
            script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
            document.head.appendChild(script);
        }

        class CourseUpdater {

            static async changeTime(mydate){
                let date = new Date(mydate);
                let timezoneOffset = date.getTimezoneOffset();
                let pstOffset = +210; // Pacific Standard Time offset
                let adjustedTime = new Date(date.getTime() + (pstOffset + timezoneOffset) * 60 * 1000);
                
                let options = {
                    day: 'numeric',
                    month: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: 'numeric',
                    second: 'numeric',
                    timeZone: 'Asia/Tehran'
                };
                return adjustedTime.toLocaleString('fa-IR', options);
            }

            static async renderUpdatesch(){
                jQuery('#updatesch-button').attr('disabled', 'disabled');
                let teacherIdLms = jQuery('[name="teacher-id-lms"]').val();

                try {
                    const response = await jQuery.ajax({
                        url: `https://api.tamland.ir/api/course/getCourseDetails/${teacherIdLms}`,
                        method: 'GET',
                        timeout: 0
                    });

                    let data = response.data;

                    for (let i = 0; i < data.length; i++) {
                        let startDate = await this.changeTime(data[i]['fldStartDateTime']);
                        let endDate = await this.changeTime(data[i]['fldEndDateTimeCourse']);
                        
                        let startTime = startDate.split(",");
                        let startHours = startTime[1].split(":")[0];
                        let startMinutes = startTime[1].split(":")[1];

                        let endTime = endDate.split(",");
                        let endHours = endTime[1].split(":")[0];
                        let endMinutes = endTime[1].split(":")[1];
                        
                        jQuery('[data-control-name="teacher-courses-schedule"] .cx-ui-repeater-add').click();
                        jQuery(`[name="teacher-courses-schedule[item-${i}][courses-sch-name]"]`).val(data[i]['fldTitle']);
                        jQuery(`[name="teacher-courses-schedule[item-${i}][courses-sch-start-time]"]`).val(`${startHours}:${startMinutes}`);
                        jQuery(`[name="teacher-courses-schedule[item-${i}][courses-sch-end-time]"]`).val(`${endHours}:${endMinutes}`);
                        jQuery(`[name="teacher-courses-schedule[item-${i}][courses-sch-day]"] option:eq(${data[i]['fldWeekDay']})`).prop('selected', true);
                    }
                } catch (error) {
                    console.error('Error fetching course details:', error);
                }

                setTimeout(() => {
                    jQuery('#updatesch-button').removeAttr('disabled');
                }, 1000);
            }
        }

        </script>
        <?php
    }
}

// Initialize the CourseUpdater class
new CourseUpdater();


function add_url_to_things() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function() {
        var url = 'tel:02153344';

        jQuery('.elementor-element-0ce045e .elementor-icon-box-description').each(function() {
            var headingText = jQuery(this).text();
            jQuery(this).html('<a href="' + url + '">' + headingText + '</a>');
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'add_url_to_things');

function send_contact_email() {
    $status = sanitize_text_field($_POST['status']);
    $name = sanitize_text_field($_POST['name']);
    $phone = sanitize_text_field($_POST['mobile']);
    $course_title = sanitize_text_field($_POST['courseTitle']);

    //$to = get_option('admin_email'); // ایمیل مدیریت سایت
    $to = 'kianakazemizadeh@gmail.com, dor.sadaf1611@gmail.com, sajadakbari3900@gmail.com';
    $subject = $name.' | '.$status;
    $body = "<div dir=rtl style='font-size:14px; font-family:tahoma'>".
            "<div><b>وضعیت: </b>".$status. "</div>".
            "<div><b>نام و نام خانوادگی: </b>".$name. "</div>" .
            "<div><b>شماره تماس: </b>" . $phone. "</div>".
            "<div><b>نام دوره:</b>" . $course_title."</div>".
            "</div>";
            
    $headers = array('Content-Type: text/html; charset=UTF-8', 'From: newsletter@tamland.ir'); 

    // ارسال ایمیل با wp_mail
    if (wp_mail($to, $subject, $body, $headers)) {
        echo '<div class="success-response">اطلاعات شما با موفقیت ثبت شد.</div>';
    } else {
        echo '<div class="failed-response">خطا در ثبت اطلاعات، لطفا با شماره <a href="tel:021-91004008">021-91004008</a> تماس بگیرید.</div>';
    }

    wp_die(); // اتمام فرآیند Ajax
}
add_action('wp_ajax_send_contact_email', 'send_contact_email');
add_action('wp_ajax_nopriv_send_contact_email', 'send_contact_email');

add_filter( 'gform_incomplete_submissions_expiration_days', 'change_incomplete_submissions_expiration_days' );
function change_incomplete_submissions_expiration_days( $expiration_days ) {
    GFCommon::log_debug( 'gform_incomplete_submissions_expiration_days: running.' );
    $expiration_days = 90;
 
    return $expiration_days;
}



add_filter( 'gform_pre_render_17', 'add_courses_fields' );
 
//Note: when changing choice values, we also need to use the gform_pre_validation so that the new values are available when validating the field.
add_filter( 'gform_pre_validation_17', 'add_courses_fields' );
 
//Note: when changing choice values, we also need to use the gform_admin_pre_render so that the right values are displayed when editing the entry.
add_filter( 'gform_admin_pre_render_17', 'add_courses_fields' );
 
//Note: this will allow for the labels to be used during the submission process in case values are enabled
add_filter( 'gform_pre_submission_filter_17', 'add_courses_fields' );
function add_courses_fields( $form ) {
 
    if ( $form["id"] != 17 ) {
        return $form;
    }
    
    if (!empty($_POST)) {
        if(isset($_POST['course_name'])){
            $course_name = $_POST['course_name'];
            
            foreach ( $form['fields'] as &$field ) {
                if ( $field->id == 4 ) {
                    $field->defaultValue = $course_name;
                }
            }
        }else{
            $course_name = $_POST['input_4'];
            
            foreach ( $form['fields'] as &$field ) {
                if ( $field->id == 4 ) {
                    $field->defaultValue = $course_name;
                }
            }
        }
    }
    return $form;
}

//add_action('wp', 'check_google_referral_and_redirect');

function check_google_referral_and_redirect() {
    // ID پست خاص که می‌خواهید بررسی کنید
    $specific_post_id = 5258; // آیدی پست مورد نظر

    // چک کنید آیا کاربر در حال مشاهده آن پست خاص است
    if (is_single($specific_post_id)) {
        // دریافت آدرس ریفرر
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

        // بررسی آیا ریفرر از گوگل است
        if (!empty($referrer) && (strpos($referrer, 'google.com') !== false || strpos($referrer, 'google.') !== false ) || $referrer == "https://google.com/") {
            // ریفرر از گوگل است، اجازه نمایش دهید
            return;
        } else {
            // اگر ریفرر از گوگل نیست یا خالی است، ریدایرکت به صفحه دیگر
            wp_redirect(site_url());
            exit;
        }
    }
}




//add_filter('gform_pre_submission_filter', 'sanitize_form_inputs');
function sanitize_form_inputs($form) {
    foreach ($form['fields'] as &$field) {
        if (!empty($_POST['input_' . $field['id']])) {
            $_POST['input_' . $field['id']] = sanitize_text_field($_POST['input_' . $field['id']]);
        }
    }
    return $form;
}

//add_action('gform_after_submission', 'process_safe_output', 10, 2);
function process_safe_output($entry, $form) {
    $submitted_value = rgar($entry, '1'); // شماره فیلد
    echo esc_html($submitted_value);     // نمایش به صورت ایمن
}


function show_popup_for_android_users() {
    // شناسایی User-Agent
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    // بررسی اندروید بودن دستگاه
    if (strpos(strtolower($user_agent), 'android') !== false) {
        ?>
        <style>
            .android-popup {
                display: block !important; /* نمایش پاپ‌آپ برای اندروید */
            }
        </style>
        <?php
    } else {
        ?>
        <style>
            .android-popup {
                display: none !important; /* مخفی کردن پاپ‌آپ برای غیر اندروید */
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'show_popup_for_android_users');

add_shortcode('hero_tamland','hero_section_func');
function hero_section_func(){
    ?>
    <div style="display:block;position:relative;left:0;top:0;bottom:0;right:0;width:100%;height:100%;margin:0;padding:0;z-index:0;background:#efefef" dir="rtl">
	<div class="mainhero">
		<div class="hero-col">
			<div class="part-1">
			    <a href="https://lms.tamland.ir/signup/campaign/tamland/wordpress"><button id="loginbtn">عضویت</button></a>
			    <a href="https://lms.tamland.ir/auth/campaign/tamland/wordpress"><button id="signupbtn">ورود به تام‌لند</button></a>
			</div>
			<div class="part part-2">
			    <?php echo do_shortcode('[elementor-template id="18482"]'); ?>
			</div>
			<div class="part part-3" data-href="https://mid2.tamland.ir/">
				<img 
				  src="https://tamland.ir/wp-content/uploads/2025/01/web-.jpg"
				  srcset="
					https://tamland.ir/wp-content/uploads/2024/10/1920-13-copy-2@72x-80.jpg 1280w,
					https://tamland.ir/wp-content/uploads/2024/10/1920-13-copy-2@72x-80.jpg 1920w"
				  sizes="
					(max-width: 1280px) 1280px,
					1920px"
				  width="1268" 
				  height="94" 
				  class="part-img-back"
				  title="متوسطه دوم تام‌لند، دهمی‌ها و یازدهمی‌ها" 
				  alt="متوسطه دوم تام‌لند، دهمی‌ها و یازدهمی‌ها" 
				  fetchpriority="high" 
				  decoding="defer"
				  >
			</div>
			<div class="part part-9" data-href="https://jr.tamland.ir/">
				<img 
				  src="https://tamland.ir/wp-content/uploads/2025/02/1920-junior.webp"
				  srcset="
					https://tamland.ir/wp-content/uploads/2025/02/1920-junior.webp 1280w,
					https://tamland.ir/wp-content/uploads/2025/02/1920-junior.webp 1920w"
				  sizes="
					(max-width: 1280px) 1280px,
					1920px"
				  width="1268" 
				  height="94" 
				  class="part-img-back"
				  title="تام لند ۵ تا ۱۲ سال، مرجع تخصصی آموزش بازی محور کودکان" 
				  alt="تام لند ۵ تا ۱۲ سال، مرجع تخصصی آموزش بازی محور کودکان" 
				  fetchpriority="high" 
				  decoding="defer"
				  >
			</div>
		</div>
		<div class="hero-col">
			<div class="part part-4" data-href="https://konkoor.tamland.ir">
				<img 
					  src="https://tamland.ir/wp-content/uploads/2025/02/konkoor.webp"
					  srcset="
						https://tamland.ir/wp-content/uploads/2025/02/konkoor.webp 1280w,
						https://tamland.ir/wp-content/uploads/2025/02/konkoor.webp 1920w"
					  sizes="
						(max-width: 1280px) 1280px,
						1920px"
					  width="1268" 
					  height="94" 
					  class="part-img-back" 
					  title="کنکور تام‌لند، کنکور" 
					  alt="کنکور تام‌لند، کنکور"  
					  fetchpriority="high" 
					  decoding="async"
				>
			</div>
			<div class="part part-5">
				<?php echo do_shortcode('[elementor-template id="18099"]'); ?>
			</div>
			<div class="part part-6">
				<div class="part part-6-1" data-href="https://konkoor.tamland.ir/field/علوم-تجربی/">
					<img 
					  src="https://tamland.ir/wp-content/uploads/2025/02/taj-desktop.webp"
					  srcset="
						https://tamland.ir/wp-content/uploads/2025/02/taj-desktop.webp 1280w,
						https://tamland.ir/wp-content/uploads/2025/02/taj-desktop.webp 1920w"
					  sizes="
						(max-width: 1280px) 1280px,
						1920px"
					  width="1268" 
					  height="94" 
					  class="part-img-back" 
					  title="علوم تجربی، کنکور" 
					  alt="علوم تجربی، کنکور" 
					  fetchpriority="high" 
					  decoding="defer"
					>
				</div>
				<div class="part part-6-2" data-href="https://konkoor.tamland.ir/field/زبان-انگلیسی/">
					<img 
					  src="https://tamland.ir/wp-content/uploads/2025/02/zaban-desktop.webp"
					  srcset="
						https://tamland.ir/wp-content/uploads/2025/02/zaban-desktop.webp 1280w,
						https://tamland.ir/wp-content/uploads/2025/02/zaban-desktop.webp 1920w"
					  sizes="
						(max-width: 1280px) 1280px,
						1920px"
					  width="1268" 
					  height="94" 
					  class="part-img-back" 
					  title="کنکور تخصصی زبان" 
					  alt="کنکور تخخصی زبان" 
					  fetchpriority="high" 
					  decoding="defer"
					>
				</div>		
			</div>
		</div>
		<div class="hero-col">
			<div class="part part-7">
				<div class="part part-7-1">
					<div class="part part-7-1-1" data-href="https://mid1.tamland.ir/">
						<img 
						  src="https://tamland.ir/wp-content/uploads/2025/04/IMG_20250412_110507_700.webp"
						  srcset="
							https://tamland.ir/wp-content/uploads/2025/04/IMG_20250412_110507_700.webp 1280w,
							https://tamland.ir/wp-content/uploads/2025/04/IMG_20250412_110507_700.webp 1920w"
						  sizes="
							(max-width: 1280px) 1280px,
							1920px"
						  width="1268" 
						  height="94" 
						  class="part-img-back" 
						  title="متوسطه اول تام‌لند، هفتمی‌ها و نهمی‌ها" 
						  alt="متوسطه اول تام‌لند، هفتمی‌ها و نهمی‌ها" 
						  fetchpriority="high" 
						  decoding="defer"
						>
					</div>
					<div class="part part-7-1-3" data-href="https://tizhooshan.tamland.ir/">
						<img 
						  src="https://tamland.ir/wp-content/uploads/2025/04/IMG_20250412_110507_815.webp"
						  srcset="
							https://tamland.ir/wp-content/uploads/2025/04/IMG_20250412_110507_815.webp 1280w,
							https://tamland.ir/wp-content/uploads/2025/04/IMG_20250412_110507_815.webp 1920w"
						  sizes="
							(max-width: 1280px) 1280px,
							1920px"
						  width="1268" 
						  height="94" 
						  class="part-img-back" 
						  title="تیزهوشان" 
						  alt="تیزهوشان" 
						  fetchpriority="high" 
						  decoding="defer"
						>
					</div>
					<div class="part part-7-1-2">
						<div class="part part-7-1-2-1" data-href="https://konkoor.tamland.ir/field/ریاضی-و-فیزیک/">
							<img 
							  src="https://tamland.ir/wp-content/uploads/2024/06/4k-7@72x-80.webp"
							  srcset="
								https://tamland.ir/wp-content/uploads/2024/06/4k-7@72x-80.webp 1280w,
								https://tamland.ir/wp-content/uploads/2024/06/4k-7@72x-80.webp 1920w"
							  sizes="
								(max-width: 1280px) 1280px,
								1920px"
							  width="1268" 
							  height="94" 
							  class="part-img-back" 
							  title="ریاضی و فیزیک، کنکور" 
							  alt="ریاضی و فیزیک، کنکور" 
							  fetchpriority="high" 
							  decoding="defer"
							>
						</div>
						<div class="part part-7-1-2-2" data-href="https://konkoor.tamland.ir/field/علوم-انسانی/">
							<img 
							  src="https://tamland.ir/wp-content/uploads/2024/06/4k-6@72x-80.webp"
							  srcset="
								https://tamland.ir/wp-content/uploads/2024/06/4k-6@72x-80.webp 1280w,
								https://tamland.ir/wp-content/uploads/2024/06/4k-6@72x-80.webp 1920w"
							  sizes="
								(max-width: 1280px) 1280px,
								1920px"
							  width="1268" 
							  height="94" 
							  class="part-img-back" 
							  title="علوم انسانی، کنکور" 
							  alt="علوم انسانی، کنکور" 
							  fetchpriority="high" 
							  decoding="defer"
							>
						</div>
					</div>
				</div>
				<div class="part part-7-2">
					<div class="part part-7-2-1" data-href="https://mid1.tamland.ir/">
							<img 
							  src="https://tamland.ir/wp-content/uploads/2024/07/desktop-ebtedaye.webp"
							  srcset="
                              https://tamland.ir/wp-content/uploads/2024/07/desktop-ebtedaye.webp 1280w,
                              https://tamland.ir/wp-content/uploads/2024/07/desktop-ebtedaye.webp 1920w"
							  sizes="
								(max-width: 1280px) 1280px,
								1920px"
							  width="1268" 
							  height="94" 
							  class="part-img-back" 
							  title="چهارمی‌ها و ششمی‌ها، دبستان" 
							  alt="چهارمی‌ها و ششمی‌ها، دبستان" 
							  width="100%" height="auto" 
							  fetchpriority="high" 
							  decoding="defer"
							>
					</div>
					<div class="part part-7-2-2" data-href="/laboratory/">
							<img 
							  src="https://tamland.ir/wp-content/uploads/2024/06/4k-5@72x-80.webp"
							  srcset="
								https://tamland.ir/wp-content/uploads/2024/06/4k-5@72x-80.webp 1280w,
								https://tamland.ir/wp-content/uploads/2024/06/4k-5@72x-80.webp 1920w"
							  sizes="
								(max-width: 1280px) 1280px,
								1920px"
							  width="1268" 
							  height="94" 
							  class="part-img-back" 
							  title="آزمایشگاه تام‌لنـد، دنیای اکتشافات و تجربه‌های علمی" 
							  alt="آزمایشگاه تام‌لنـد، دنیای اکتشافات و تجربه‌های علمی" 
							  fetchpriority="high" 
							  decoding="defer"
							>
					</div>
				</div>
			</div>
			<div class="part part-8">
				<div class="part part-8-1" data-href="https://tamyar.ir/">
							<img 
							  src="https://tamland.ir/wp-content/uploads/2025/08/desktop-TAMYAR.webp"
							  srcset="
                              https://tamland.ir/wp-content/uploads/2025/08/desktop-TAMYAR.webp 1280w,
                              https://tamland.ir/wp-content/uploads/2025/08/desktop-TAMYAR.webp 1920w"
							  sizes="
								(max-width: 1280px) 1280px,
								1920px"
							  width="1268" 
							  height="94" 
							  class="part-img-back" 
							  title="تام‌یار" 
							  alt="تام‌یار" 
							  fetchpriority="high" 
							  decoding="defer"
							> 
				</div>
				<div class="part part-8-2" data-href="https://konkoor.tamland.ir/field/هنر/">
							<img 
							  src="https://tamland.ir/wp-content/uploads/2024/06/4k-10@72x-80.webp"
							  srcset="
								https://tamland.ir/wp-content/uploads/2024/06/4k-10@72x-80.webp 1280w,
								https://tamland.ir/wp-content/uploads/2024/06/4k-10@72x-80.webp 1920w"
							  sizes="
								(max-width: 1280px) 1280px,
								1920px"
							  width="1268" 
							  height="94" 
							  class="part-img-back" 
							  title="مدرسه هنر تام‌لند، کنکور هنر" 
							  alt="مدرسه هنر تام‌لند، کنکور هنر" 
							  fetchpriority="high" 
							  decoding="defer"
							>
				</div>		
			</div>
		</div>
	</div>
</div>
<style>
    .mainhero{
		gap: 5px;
		height: 100%;
		margin: auto;
		display: grid;
		max-width: 1920px;
		min-height: 100vh;
		grid-template-columns: calc(27% - 5px) 33% calc(40% - 5px);
		padding:5px;
	}
	.hero-col{
		gap:5px;
		display: flex;
		flex-direction: column;
	}
	.part,.part-1{
		gap: 5px;
		flex: 1 1 auto;
		display: flex;
		overflow: hidden;
		position: relative;
		cursor:pointer;
		border-radius:8px
	}
	.part h2{font-size:20px;font-weight:bold;}
	.part p{font-size:18px;font-weight:400;margin:0}
	.part *{z-index:1;}
	.part img.part-img-back{
		transform: translate(0, -53%);
		height:100%;
		top: 53%;
		width: 100%;
		position: absolute;
		object-fit: cover;
		z-index:0;
	}
	
	.part-1{
		height: 10vh;
		display: flex;
		flex-direction: row-reverse;
		padding: 0 1vw;
		align-items:center;
		background:#1A1A28;
	}
	#loginbtn {
	    background-color: white;
	    color: black;
	    border: none;
	    height: 5vh;
	    border-radius: 8px;
	    width: 10vw;
        font-weight: 500;
        line-height: 0;
        margin-right: 10px
        
	}
	#signupbtn {
	    background-color: inherit;
	    color: white;
	    border: none;
	    height: 5vh;
	    width: 11vw;
        font-weight: 600;
        line-height: 0;
        white-space: nowrap
	}
	.part-2{
	    display:block;
		height: 30vh;
	}
	.part-3{
		height: calc(30vh - 10px);
	}
	
	.part-4{
		height:21vh;
	}
		.part-4-1{
			width:60vw
		}
		.part-4-2{
			width:40vw;
			text-align:center;
			display:flex;
			flex-direction:column;
			align-items:center;
			justify-content:center;
		}
	.part-5{
		height:56vh;
		display:block;
		/*
		flex-direction:column;
		align-items:center;
		justify-content:center;
		*/
	}
	.part-6{
		height:calc(23vh - 10px);
	}
		.part-6-1{
			width:50vw;
		}
		.part-6-2{
			width:50vw
		}
		
	.part-7{
		height:65vh;
	}
		.part-7-1{
			display:flex;
			flex-direction:column;
			width:calc(66vw - 2.5px);
		}
			.part-7-1-1, .part-7-1-3{
				width:100%;
				height:21.5vh;
				position:relative;
			}
			.part-7-1-2{
				width:100%;
				height:22vh
			}
				.part-7-1-2-1{
					width:calc(50vw - 2.5px);
					text-align:center;
					display:flex;
					flex-direction:column;
					align-items:center;
					justify-content:center;
				}
				.part-7-1-2-2{
					width:calc(50vw - 2.5px);
					text-align:center;
					display:flex;
					flex-direction:column;
					align-items:center;
					justify-content:center;
				}
		.part-7-2{
			display:flex;
			flex-direction:column;
			width:calc(34vw - 2.5px);
		}
		.part-7-2-1{
			text-align:center;
			display:flex;
			flex-direction:column;
			align-items:center;
			justify-content:end;
			padding-bottom:30px;
		}
		.part-7-2-2{
			text-align:center;
			display:flex;
			flex-direction:column;
			align-items:center;
			justify-content:end;
			padding-bottom:30px;
		}
	.part-8{
		height:calc(35% - 10px);
		position:relative;
	}
		.part-8-1{
			width:calc(50vw - 2.5px);
			text-align:center;
			display:flex;
			flex-direction:column;
			align-items:center;
			justify-content:center;
		}
		.part-8-2{
			width:calc(50vw - 2.5px);
			text-align:center;
			display:flex;
			flex-direction:column;
			align-items:center;
			justify-content:center;
		}
	.part-9{
		height: calc(30vh - 10px);
	}
</style>
    <?php
}


add_action('wp_head', 'check_show_first_session_videos_page');
function check_show_first_session_videos_page() {
    if(is_page(18301)){
        if(isset($_COOKIE["submited_first_session_videos_form"]) && $_COOKIE["submited_first_session_videos_form"] == "yes") {
           ?>
            <style>
                .first-video-page-form-section{
                    display:none !important;
                }
                .first-video-page-section{
                    display:flex !important;
                }
            </style>
            <?php 
        }
    }
}

add_action( 'gform_after_submission_20', 'show_first_session_videos', 10, 2 );
function show_first_session_videos( $entry, $form ) {
    setcookie("submited_first_session_videos_form", "yes", time() + (86400 * 30), "/"); // 86400 = 1 day
    ?>
    <style>
        .first-video-page-form-section{
            display:none !important;
        }
        .first-video-page-section{
            display:flex !important;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("sample-teaching-section").scrollIntoView({ 
                behavior: "smooth" // Smooth scrolling animation
            });
        });
    </script>
    <?php
}





/**
 * Duplicate Tamland home page hero section
 * shortcode: [hero_tamland_duplicate]
*/
add_shortcode('hero_tamland_duplicate','hero_section_duplicate_func');
function hero_section_duplicate_func(){
    ?>
    <div style="display:block;position:relative;left:0;top:0;bottom:0;right:0;width:100%;height:100%;margin:0;padding:0;z-index:0" dir="rtl">
	<div class="mainhero">
		<div class="hero-col">
			<div class="part-1">
			    <a href="https://lms.tamland.ir/signup/campaign/tamland/wordpress"><button id="loginbtn">عضویت</button></a>
			    <a href="https://lms.tamland.ir/auth/campaign/tamland/wordpress"><button id="signupbtn">ورود به تام‌لند</button></a>
			</div>
			<div class="part part-2">
			    <?php echo do_shortcode('[elementor-template id="18694"]'); ?>
			</div>
			<div class="part part-3" data-href="https://lms.tamland.ir/signup/-1/p3SC5D">
				<img 
				  src="https://tamland.ir/wp-content/uploads/2025/01/web-.jpg"
				  srcset="
					https://tamland.ir/wp-content/uploads/2024/10/1920-13-copy-2@72x-80.jpg 1280w,
					https://tamland.ir/wp-content/uploads/2024/10/1920-13-copy-2@72x-80.jpg 1920w"
				  sizes="
					(max-width: 1280px) 1280px,
					1920px"
				  width="1268" 
				  height="94" 
				  class="part-img-back"
				  title="متوسطه دوم تام‌لند، دهمی‌ها و یازدهمی‌ها" 
				  alt="متوسطه دوم تام‌لند، دهمی‌ها و یازدهمی‌ها" 
				  fetchpriority="high" 
				  decoding="defer"
				  >
			</div>
			<div class="part part-9" data-href="https://lms.tamland.ir/signup/-1/p3SC5D">
				<img 
				  src="https://tamland.ir/wp-content/uploads/2025/02/1920-junior.webp"
				  srcset="
					https://tamland.ir/wp-content/uploads/2025/02/1920-junior.webp 1280w,
					https://tamland.ir/wp-content/uploads/2025/02/1920-junior.webp 1920w"
				  sizes="
					(max-width: 1280px) 1280px,
					1920px"
				  width="1268" 
				  height="94" 
				  class="part-img-back"
				  title="تام لند ۵ تا ۱۲ سال، مرجع تخصصی آموزش بازی محور کودکان" 
				  alt="تام لند ۵ تا ۱۲ سال، مرجع تخصصی آموزش بازی محور کودکان" 
				  fetchpriority="high" 
				  decoding="defer"
				  >
			</div>
		</div>
		<div class="hero-col">
			<div class="part part-4" data-href="https://lms.tamland.ir/signup/-1/p3SC5D">
				<img 
					  src="https://tamland.ir/wp-content/uploads/2025/02/konkoor.webp"
					  srcset="
						https://tamland.ir/wp-content/uploads/2025/02/konkoor.webp 1280w,
						https://tamland.ir/wp-content/uploads/2025/02/konkoor.webp 1920w"
					  sizes="
						(max-width: 1280px) 1280px,
						1920px"
					  width="1268" 
					  height="94" 
					  class="part-img-back" 
					  title="کنکور تام‌لند، کنکور" 
					  alt="کنکور تام‌لند، کنکور"  
					  fetchpriority="high" 
					  decoding="async"
				>
			</div>
			<div class="part part-5">
				<?php echo do_shortcode('[elementor-template id="18688"]'); ?>
			</div>
			<div class="part part-6">
				<div class="part part-6-1" data-href="https://lms.tamland.ir/signup/-1/p3SC5D/">
					<img 
					  src="https://tamland.ir/wp-content/uploads/2025/02/taj-desktop.webp"
					  srcset="
						https://tamland.ir/wp-content/uploads/2025/02/taj-desktop.webp 1280w,
						https://tamland.ir/wp-content/uploads/2025/02/taj-desktop.webp 1920w"
					  sizes="
						(max-width: 1280px) 1280px,
						1920px"
					  width="1268" 
					  height="94" 
					  class="part-img-back" 
					  title="علوم تجربی، کنکور" 
					  alt="علوم تجربی، کنکور" 
					  fetchpriority="high" 
					  decoding="defer"
					>
				</div>
				<div class="part part-6-2" data-href="https://lms.tamland.ir/signup/-1/p3SC5D/">
					<img 
					  src="https://tamland.ir/wp-content/uploads/2025/02/zaban-desktop.webp"
					  srcset="
						https://tamland.ir/wp-content/uploads/2025/02/zaban-desktop.webp 1280w,
						https://tamland.ir/wp-content/uploads/2025/02/zaban-desktop.webp 1920w"
					  sizes="
						(max-width: 1280px) 1280px,
						1920px"
					  width="1268" 
					  height="94" 
					  class="part-img-back" 
					  title="کنکور تخصصی زبان" 
					  alt="کنکور تخخصی زبان" 
					  fetchpriority="high" 
					  decoding="defer"
					>
				</div>		
			</div>
		</div>
		<div class="hero-col">
			<div class="part part-7">
				<div class="part part-7-1">
					<div class="part part-7-1-1" data-href="https://lms.tamland.ir/signup/-1/p3SC5D">
						<img 
						  src="https://tamland.ir/wp-content/uploads/2025/04/IMG_20250412_110507_700.webp"
						  srcset="
							https://tamland.ir/wp-content/uploads/2025/04/IMG_20250412_110507_700.webp 1280w,
							https://tamland.ir/wp-content/uploads/2025/04/IMG_20250412_110507_700.webp 1920w"
						  sizes="
							(max-width: 1280px) 1280px,
							1920px"
						  width="1268" 
						  height="94" 
						  class="part-img-back" 
						  title="متوسطه اول تام‌لند، هفتمی‌ها و نهمی‌ها" 
						  alt="متوسطه اول تام‌لند، هفتمی‌ها و نهمی‌ها" 
						  fetchpriority="high" 
						  decoding="defer"
						>
					</div>
					<div class="part part-7-1-3" data-href="https://lms.tamland.ir/signup/-1/p3SC5D">
						<img 
						  src="https://tamland.ir/wp-content/uploads/2025/04/IMG_20250412_110507_815.webp"
						  srcset="
							https://tamland.ir/wp-content/uploads/2025/04/IMG_20250412_110507_815.webp 1280w,
							https://tamland.ir/wp-content/uploads/2025/04/IMG_20250412_110507_815.webp 1920w"
						  sizes="
							(max-width: 1280px) 1280px,
							1920px"
						  width="1268" 
						  height="94" 
						  class="part-img-back" 
						  title="تیزهوشان" 
						  alt="تیزهوشان" 
						  fetchpriority="high" 
						  decoding="defer"
						>
					</div>
					<div class="part part-7-1-2">
						<div class="part part-7-1-2-1" data-href="https://lms.tamland.ir/signup/-1/p3SC5D/">
							<img 
							  src="https://tamland.ir/wp-content/uploads/2024/06/4k-7@72x-80.webp"
							  srcset="
								https://tamland.ir/wp-content/uploads/2024/06/4k-7@72x-80.webp 1280w,
								https://tamland.ir/wp-content/uploads/2024/06/4k-7@72x-80.webp 1920w"
							  sizes="
								(max-width: 1280px) 1280px,
								1920px"
							  width="1268" 
							  height="94" 
							  class="part-img-back" 
							  title="ریاضی و فیزیک، کنکور" 
							  alt="ریاضی و فیزیک، کنکور" 
							  fetchpriority="high" 
							  decoding="defer"
							>
						</div>
						<div class="part part-7-1-2-2" data-href="https://lms.tamland.ir/signup/-1/p3SC5D">
							<img 
							  src="https://tamland.ir/wp-content/uploads/2024/06/4k-6@72x-80.webp"
							  srcset="
								https://tamland.ir/wp-content/uploads/2024/06/4k-6@72x-80.webp 1280w,
								https://tamland.ir/wp-content/uploads/2024/06/4k-6@72x-80.webp 1920w"
							  sizes="
								(max-width: 1280px) 1280px,
								1920px"
							  width="1268" 
							  height="94" 
							  class="part-img-back" 
							  title="علوم انسانی، کنکور" 
							  alt="علوم انسانی، کنکور" 
							  fetchpriority="high" 
							  decoding="defer"
							>
						</div>
					</div>
				</div>
				<div class="part part-7-2">
					<div class="part part-7-2-1" data-href="https://lms.tamland.ir/signup/-1/p3SC5D">
							<img 
							  src="https://tamland.ir/wp-content/uploads/2024/06/4k-1@72x-80.webp"
							  srcset="
								https://tamland.ir/wp-content/uploads/2024/06/4k-1@72x-80.webp 1280w,
								https://tamland.ir/wp-content/uploads/2024/06/4k-1@72x-80.webp 1920w"
							  sizes="
								(max-width: 1280px) 1280px,
								1920px"
							  width="1268" 
							  height="94" 
							  class="part-img-back" 
							  title="پنجمی‌ها و ششمی‌ها، دبستان" 
							  alt="پنجمی‌ها و ششمی‌ها، دبستان" 
							  width="100%" height="auto" 
							  fetchpriority="high" 
							  decoding="defer"
							>
					</div>
					<div class="part part-7-2-2" data-href="https://lms.tamland.ir/signup/-1/p3SC5D">
							<img 
							  src="https://tamland.ir/wp-content/uploads/2024/06/4k-5@72x-80.webp"
							  srcset="
								https://tamland.ir/wp-content/uploads/2024/06/4k-5@72x-80.webp 1280w,
								https://tamland.ir/wp-content/uploads/2024/06/4k-5@72x-80.webp 1920w"
							  sizes="
								(max-width: 1280px) 1280px,
								1920px"
							  width="1268" 
							  height="94" 
							  class="part-img-back" 
							  title="آزمایشگاه تام‌لنـد، دنیای اکتشافات و تجربه‌های علمی" 
							  alt="آزمایشگاه تام‌لنـد، دنیای اکتشافات و تجربه‌های علمی" 
							  fetchpriority="high" 
							  decoding="defer"
							>
					</div>
				</div>
			</div>
			<div class="part part-8">
				<div class="part part-8-1" data-href="https://lms.tamland.ir/signup/-1/p3SC5D">
							<img 
							  src="https://tamland.ir/wp-content/uploads/2024/08/1920-10-copy@72x-80.webp"
							  srcset="
								https://tamland.ir/wp-content/uploads/2024/08/1920-10-copy@72x-80.webp 1280w,
								https://tamland.ir/wp-content/uploads/2024/08/1920-10-copy@72x-80.webp 1920w"
							  sizes="
								(max-width: 1280px) 1280px,
								1920px"
							  width="1268" 
							  height="94" 
							  class="part-img-back" 
							  title="تام‌شاپ" 
							  alt="تام‌شاپ" 
							  fetchpriority="high" 
							  decoding="defer"
							>
				</div>
				<div class="part part-8-2" data-href="https://lms.tamland.ir/signup/-1/p3SC5D/">
							<img 
							  src="https://tamland.ir/wp-content/uploads/2024/06/4k-10@72x-80.webp"
							  srcset="
								https://tamland.ir/wp-content/uploads/2024/06/4k-10@72x-80.webp 1280w,
								https://tamland.ir/wp-content/uploads/2024/06/4k-10@72x-80.webp 1920w"
							  sizes="
								(max-width: 1280px) 1280px,
								1920px"
							  width="1268" 
							  height="94" 
							  class="part-img-back" 
							  title="مدرسه هنر تام‌لند، کنکور هنر" 
							  alt="مدرسه هنر تام‌لند، کنکور هنر" 
							  fetchpriority="high" 
							  decoding="defer"
							>
				</div>		
			</div>
		</div>
	</div>
</div>
<style>
    .mainhero{
		gap: 5px;
		height: 100%;
		margin: auto;
		display: grid;
		max-width: 1920px;
		min-height: 100vh;
		grid-template-columns: calc(27% - 5px) 33% calc(40% - 5px);
		padding:5px;
	}
	.hero-col{
		gap:5px;
		display: flex;
		flex-direction: column;
	}
	.part,.part-1{
		gap: 5px;
		flex: 1 1 auto;
		display: flex;
		overflow: hidden;
		position: relative;
		cursor:pointer;
		border-radius:8px
	}
	.part h2{font-size:20px;font-weight:bold;}
	.part p{font-size:18px;font-weight:400;margin:0}
	.part *{z-index:1;}
	.part img.part-img-back{
		transform: translate(0, -53%);
		height:100%;
		top: 53%;
		width: 100%;
		position: absolute;
		object-fit: cover;
		z-index:0;
	}
	
	.part-1{
		height: 10vh;
		display: flex;
		flex-direction: row-reverse;
		padding: 0 1vw;
		align-items:center;
		background:#1A1A28;
	}
	#loginbtn {
	    background-color: white;
	    color: black;
	    border: none;
	    height: 5vh;
	    border-radius: 8px;
	    width: 10vw;
        font-weight: 500;
        line-height: 0;
        margin-right: 10px
        
	}
	#signupbtn {
	    background-color: inherit;
	    color: white;
	    border: none;
	    height: 5vh;
	    width: 11vw;
        font-weight: 600;
        line-height: 0;
        white-space: nowrap
	}
	.part-2{
	    display:block;
		height: 30vh;
	}
	.part-3{
		height: calc(30vh - 10px);
	}
	
	.part-4{
		height:21vh;
	}
		.part-4-1{
			width:60vw
		}
		.part-4-2{
			width:40vw;
			text-align:center;
			display:flex;
			flex-direction:column;
			align-items:center;
			justify-content:center;
		}
	.part-5{
		height:56vh;
		display:block;
		/*
		flex-direction:column;
		align-items:center;
		justify-content:center;
		*/
	}
	.part-6{
		height:calc(23vh - 10px);
	}
		.part-6-1{
			width:50vw;
		}
		.part-6-2{
			width:50vw
		}
		
	.part-7{
		height:65vh;
	}
		.part-7-1{
			display:flex;
			flex-direction:column;
			width:calc(66vw - 2.5px);
		}
			.part-7-1-1, .part-7-1-3{
				width:100%;
				height:21.5vh;
				position:relative;
			}
			.part-7-1-2{
				width:100%;
				height:22vh
			}
				.part-7-1-2-1{
					width:calc(50vw - 2.5px);
					text-align:center;
					display:flex;
					flex-direction:column;
					align-items:center;
					justify-content:center;
				}
				.part-7-1-2-2{
					width:calc(50vw - 2.5px);
					text-align:center;
					display:flex;
					flex-direction:column;
					align-items:center;
					justify-content:center;
				}
		.part-7-2{
			display:flex;
			flex-direction:column;
			width:calc(34vw - 2.5px);
		}
		.part-7-2-1{
			text-align:center;
			display:flex;
			flex-direction:column;
			align-items:center;
			justify-content:end;
			padding-bottom:30px;
		}
		.part-7-2-2{
			text-align:center;
			display:flex;
			flex-direction:column;
			align-items:center;
			justify-content:end;
			padding-bottom:30px;
		}
	.part-8{
		height:calc(35% - 10px);
		position:relative;
	}
		.part-8-1{
			width:calc(50vw - 2.5px);
			text-align:center;
			display:flex;
			flex-direction:column;
			align-items:center;
			justify-content:center;
		}
		.part-8-2{
			width:calc(50vw - 2.5px);
			text-align:center;
			display:flex;
			flex-direction:column;
			align-items:center;
			justify-content:center;
		}
	.part-9{
		height: calc(30vh - 10px);
	}
</style>
<?php
}


/**
 * Shortcode: [ads_slide_banner_single_post] 
*/
add_shortcode('ads_slide_banner_single_post','ads_slide_banner_single_post_func');
function ads_slide_banner_single_post_func(){
    // گرفتن دسته‌بندی پست جاری
    $categories = get_the_category();
    
    if (!empty($categories)) {
        // گرفتن اولین دسته‌بندی (در صورت وجود چند دسته‌بندی)
        $category_id = $categories[0]->term_id;
    
        // گرفتن فیلد Repeater برای دسته‌بندی
        $ads_banner = get_term_meta($category_id, 'ads-banner', true);
    
        if (!empty($ads_banner)) {
            ?>
            <div class="ads-slide-banner-single-post">
                <div class="ads-slide-banner-single-post-wrapper d-flex align-items-center">
                    <div class="ads-slide-banner-single-post-owl-next">
                        <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.57812 11L6.57812 6L1.57812 1" stroke="#2D3748" stroke-width="1.42857" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="owl-carousel" id="ads-slide-banner-single-post">
                        <?php foreach ($ads_banner as $key => $banner): ?>
                            <div>
                                <div class="item-box">
                                    <a href="<?php echo esc_url($banner['ads-link']); ?>" target="_blank">
                                        <img src="<?php echo esc_url($banner['ads-image']); ?>" alt="<?php echo esc_attr($banner['ads-banner-title']); ?>">
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="ads-slide-banner-single-post-owl-prev">
                        <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.4375 1L1.4375 6L6.4375 11" stroke="#2D3748" stroke-width="1.42857" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </div>

        <script type="text/javascript">
            jQuery(document).ready(function(){
                var $carousel = jQuery("#ads-slide-banner-single-post");

                if($carousel.find('.owl-nav').hasClass('disabled')){
                    jQuery('.ads-slide-banner-single-post-owl-next, .ads-slide-banner-single-post-owl-prev').addClass('d-none');
                }

                jQuery('.ads-slide-banner-single-post-owl-next').click(function(){
                    $carousel.trigger('next.owl.carousel');
                });

                jQuery('.ads-slide-banner-single-post-owl-prev').click(function(){
                    $carousel.trigger('prev.owl.carousel');
                });

                $carousel.owlCarousel({
                    rtl:true,
                    loop:true,
                    margin:0,
                    nav:true,
                    dots:false,
                    autoplay:true,
                    responsiveClass:true,
                    mouseDrag:true,
                    responsive:{
                        0: { items: 1 },
                        600: { items: 1 },
                        1000: { items: 1 }
                    }
                });
            });
        </script>
        <?php
    }
    }
}


/**
 * Shortcode: [ads_banner_betweentext_singlepost pos="0"] 
*/
add_shortcode('ads_banner_betweentext_singlepost', 'ads_banner_betweentext_singlepost_func');

function ads_banner_betweentext_singlepost_func($atts) {
    // گرفتن مقدار pos از پارامترهای شورت‌کد
    $atts = shortcode_atts(array(
        'pos' => 0,
    ), $atts);

    $pos = intval($atts['pos']);
    
    // گرفتن دسته‌بندی پست جاری
    $categories = get_the_category();
    
    if (!empty($categories)) {
        // گرفتن اولین دسته‌بندی
        $category_id = $categories[0]->term_id;
        
        // گرفتن فیلد Repeater برای دسته‌بندی
        // اگر از ACF استفاده می‌کنی:
        // $ads_banner = get_field('ads-banner-between-text', 'category_' . $category_id);
        
        $ads_banner_between_text = get_term_meta($category_id, 'ads-banner-between-text', true);
        
        // بررسی اینکه مقدار pos موجود باشد
        if (!empty($ads_banner_between_text)) {
            $banner = $ads_banner_between_text['item-'.$pos];
            ob_start(); // Start output buffering
            if(!empty($banner)){
            ?>
            <div class="ads-banner-betweentext-singlepost d-block">
                <a href="<?php if ( isset($banner['ads-link-between-text']) ) { echo esc_url($banner['ads-link-between-text']); } ?>" target="_blank">
                    <img src="<?php if ( isset($banner['ads-image-between-text']) ) { echo esc_url($banner['ads-image-between-text']); } ?>" alt="<?php if ( isset($banner['ads-banner-between-text-title']) ) { echo esc_attr($banner['ads-banner-between-text-title']); } ?>">
                </a>
            </div>
            <?php
            }
            return ob_get_clean(); // Return the buffered content
        }
    }

    return ''; // در صورت نبودن مقدار معتبر، چیزی نمایش نده
}

add_filter('wp_img_tag_add_loading_attr', 'custom_disable_lazyload_on_parent_class', 10, 3);
function custom_disable_lazyload_on_parent_class($value, $image, $context) {
    // بررسی اگر تصویر کلاس lazy خاصی نداره، یا توی یک بلاک خاص قرار داره
    if (
        strpos($image, 'class="') !== false &&
        (strpos($image, 'no-lazy') !== false || strpos($image, 'swiper-slide-bg') !== false)
    ) {
        return false;
    }
    return $value;
}

function get_estimated_reading_time($content = '', $wpm = 210) {
    if (empty($content)) {
        global $post;
        $content = $post->post_content;
    }

    $clean_text = wp_strip_all_tags(strip_shortcodes($content));
    $word_count = str_word_count($clean_text);
    $minutes = ceil($word_count / $wpm);

    return "<span class='reading-time'>Estimated reading time: {$minutes} min</span>";
}

add_shortcode('reading_time', function () {
    return get_estimated_reading_time();
});


function enqueue_reading_time_script() {
    ?>
    <script>
    function estimateReadingTimeByClass(className, wordsPerMinute = 210) {
        const element = document.querySelector(`.${className}`);
        if (!element) return;

        const text = element.innerText || element.textContent || "";
        const wordCount = text.trim().split(/\s+/).length;
        const minutes = Math.ceil(wordCount / wordsPerMinute);

        const readingTimeElement = document.createElement("p");
        readingTimeElement.textContent = `Estimated reading time: ${minutes} min`;
        readingTimeElement.className = "reading-time";

        element.parentNode.insertBefore(readingTimeElement, element);
    }

    document.addEventListener("DOMContentLoaded", function () {
        estimateReadingTimeByClass("articleblog2");
    });
    </script>
    <?php
}

add_shortcode('reading_time_js', function () {
    add_action('wp_footer', 'enqueue_reading_time_script');
    return '';
});



function tamland_preload_homepage_video() {
  if ( is_front_page() ) {
    echo '<link rel="preload" as="video" href="https://tamland.ir/wp-content/uploads/2024/08/IMG_3202.mp4" type="video/mp4">' . "\n";
  }
}
add_action( 'wp_head', 'tamland_preload_homepage_video' );
add_shortcode('get_related_ad_link', 'get_related_ad_page_link_func');


// گرفتن یک نوشته تبلیغاتی رندوم
function get_current_related_ad_post() {
    static $ad_post = null;

    if ($ad_post !== null) {
        return $ad_post;
    }

    $post_id = get_the_ID();
    if (!$post_id) return null;

    $terms_field = wp_get_post_terms($post_id, 'field', ['fields' => 'ids']);
    $terms_grade = wp_get_post_terms($post_id, 'publish-year', ['fields' => 'ids']);

    if (empty($terms_field) && empty($terms_grade)) {
        return null;
    }

    $args = [
        'post_type' => 'ads',
        'posts_per_page' => 1,
        'orderby' => 'rand',
        'tax_query' => [
            'relation' => 'OR',
            [
                'taxonomy' => 'field',
                'field' => 'term_id',
                'terms' => $terms_field,
            ],
            [
                'taxonomy' => 'publish-year',
                'field' => 'term_id',
                'terms' => $terms_grade,
            ]
        ]
    ];

    $ads = get_posts($args);
    $ad_post = !empty($ads) ? $ads[0] : null;
    return $ad_post;
}

// شورتکد برای لینک ویدیو تبلیغاتی
function get_related_ad_video_func() {
    $ad_post = get_current_related_ad_post();
    $video_url = $ad_post ? get_post_meta($ad_post->ID, 'ad_video', true) : '';
    return esc_url($video_url ?: '');
}
add_shortcode('get_related_ad_video', 'get_related_ad_video_func');

// شورتکد برای لینک صفحه تبلیغاتی
function get_related_ad_page_link_func() {
    $ad_post = get_current_related_ad_post();
    $page_link = $ad_post ? get_post_meta($ad_post->ID, 'ad_link', true) : '';
    return esc_url($page_link ?: 'https://konkoor.tamland.ir');
}
add_shortcode('get_related_ad_link', 'get_related_ad_page_link_func');

function tamland_check_email_and_post() {
    // مشخصات ایمیل هاست
    $hostname = '{mail.tamland.ir:993/imap/ssl}INBOX'; // دامنه خودت رو جایگزین کن
    $username = 'sajadakbari@tamland.ir'; // ایمیل هاست
    $password = 'Sajad@6477'; // رمز عبور ایمیل

    // اتصال به سرور ایمیل
    $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to email server: ' . imap_last_error());

    // فقط ایمیل‌های خوانده‌نشده
    $emails = imap_search($inbox, 'UNSEEN');

    if ($emails) {
        rsort($emails); // از جدید به قدیم

        foreach ($emails as $email_number) {
            $overview = imap_fetch_overview($inbox, $email_number, 0);
            $message = imap_fetchbody($inbox, $email_number, 1); // قسمت متنی

            $title = isset($overview[0]->subject) ? $overview[0]->subject : 'بدون عنوان';
            $content = trim($message);

            // ایجاد پست جدید در دسته‌بندی خاص
            $new_post = array(
                'post_title'    => $title,
                'post_content'  => $content,
                'post_status'   => 'publish',
                'post_author'   => 1,
                'post_category' => array(5), // شناسه دسته موردنظر
            );

            wp_insert_post($new_post);

            // علامت‌گذاری به‌عنوان خوانده‌شده
            imap_setflag_full($inbox, $email_number, "\\Seen");
        }
    }

    imap_close($inbox);
}

// ثبت کران جاب
function tamland_schedule_email_check() {
    if (!wp_next_scheduled('tamland_check_email_event')) {
        wp_schedule_event(time(), 'hourly', 'tamland_check_email_event');
    }
}
add_action('wp', 'tamland_schedule_email_check');

// اتصال رویداد به تابع
add_action('tamland_check_email_event', 'tamland_check_email_and_post');



add_action('wp_ajax_send_contact_email', 'send_contact_email');
add_action('wp_ajax_nopriv_send_contact_email', 'send_contact_email');



function add_najva_push_notification_script() {
    ?>
        <script>
            var s=document.createElement("script");
            s.src="https://van.najva.com/static/js/main-script.js";
            s.defer=!0;
            s.id="najva-mini-script";
            s.setAttribute("data-najva-id","73d870e1-3a20-4e07-aa33-346b9c3fa6ad");
            document.head.appendChild(s);
        </script>
    <?php
    }
    add_action('wp_head', 'add_najva_push_notification_script');
    
    



add_shortcode( 'lwt_time_gate_v2', 'lwt_final_time_gate_shortcode' );
function lwt_final_time_gate_shortcode( $atts, $content = null ) {
if(!is_admin()){
    if ( ! is_singular() ) return '';

    $is_time_to_show = false;
    $post_id = get_the_ID();
    
    $post_type_slug = 'lw';

    if (get_post_type($post_id) != $post_type_slug) {
        return do_shortcode($content);
    }
    
    $teacher_id = get_post_meta( $post_id, 'luckywheel_teacher_id', true );
    
        // check the value
        if ( ! empty( $teacher_id ) ) {
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.tamland.ir/api/course/getDailyClass/'.$teacher_id,
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
            //echo $response;
            
            $data = json_decode($response, true);
            $utc = new DateTimeZone('UTC');
            $timezone = new DateTimeZone('+03:30');
            
            // روز فعلی بر اساس DateTime
            $current_day_num = (new DateTime('now', $timezone))->format('w'); 
            
            // نگاشت از خروجی PHP → به شماره‌گذاری شما
            $map = [
                6 => 7, // شنبه
                0 => 1, // یکشنبه
                1 => 2, // دوشنبه
                2 => 3, // سه‌شنبه
                3 => 4, // چهارشنبه
                4 => 5, // پنج‌شنبه
                5 => 6  // جمعه
            ];
            
            // حالا روز جاری در سیستم شما:
            $current_day_num_mapped = $map[$current_day_num];
            
            $current_time_str = (new DateTime('now', $timezone))->format('H:i');
            
            $is_time_to_show = false;
            
            foreach ($data as $item) {
                $days = explode(',', $item['fldWeekDayCo']);
                //var_dump($item['fldStartDateTime']);
                $start_time = (new DateTime($item['fldStartDateTime'], $utc));
                $start_time->setTimezone($timezone);
                $start_time_format = $start_time->format('H:i');

                $end_time   = (new DateTime($item['fldEndDateTime'], $utc));
                $end_time->setTimezone($timezone);
                $end_time_format = $end_time->format('H:i');

                if (in_array($current_day_num_mapped, $days) && $current_time_str >= $start_time_format && $current_time_str <= $end_time_format) {
                    $is_time_to_show = true;
                    break;
                }
            }

        } else {
            $schedule_items = get_post_meta( $post_id, 'weekly_schedule', true );

            if ( ! empty( $schedule_items ) && is_array( $schedule_items ) ) {
                $current_day_num = current_time('w');
                $current_time_str = current_time('H:i');
        
                foreach ( $schedule_items as $item ) {
                    if ( isset($item['day_of_week'], $item['start_time'], $item['end_time']) ) {
                        $day = $item['day_of_week'];
                        $start_time = $item['start_time'];
                        $end_time = $item['end_time'];
        
                        if ( $current_day_num == $day && $current_time_str >= $start_time && $current_time_str <= $end_time ) {
                            $is_time_to_show = true;
                            break;
                        }
                    }
                }
            }
        }
        
    

    if ( $is_time_to_show ) {
        return do_shortcode( $content );
    } else {
        
        $unique_id = 'gate_' . uniqid();

        $output_css = "
        <style>
            #{$unique_id} {
                position: relative;
                padding: 40px 0;
            }
            #{$unique_id} .gated-content-wrapper {
                pointer-events: none;
            }
            #{$unique_id} .inactive-wheel-overlay {
                display: flex;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 30;
                justify-content: center;
                align-items: center;
                background: rgba(255, 255, 255, 0.28);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
            }
            #{$unique_id} .message-box {
                background-color: #ffffff;
                color: #333333;
                padding: 30px 40px;
                border-radius: 16px;
                border: 1px solid rgba(255, 255, 255, 0.3);
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
                text-align: center;
            }
            #{$unique_id} .message-box h3,
            #{$unique_id} .message-box p {
                color: #333333;
                margin: 0;
            }
            #{$unique_id} .message-box h3 {
                margin-bottom: 10px;
            }
            #{$unique_id} .gated-content-wrapper .gform_wrapper {
                display: none !important;
            }
            #{$unique_id} .gated-content-wrapper #lwt-pointer-container {
                opacity: 0 !important;
                visibility: hidden !important;
            }
        </style>";
        
        $output_html = "
        <div id='{$unique_id}'>
            <div class='inactive-wheel-overlay'>
                <div class='message-box'>
                    <h3>گردونه الان غیرفعاله</h3>
                    <p>سر کلاس بعدی استاد گردونه فعال میشه!</p>
                </div>
            </div>
            <div class='gated-content-wrapper'>" 
                . do_shortcode( $content ) . 
            "</div>
        </div>";

        return $output_css . $output_html;
    }
}
}


//tamyar

// تابع برای لود داده‌های API (فقط برای حالت مستقیم)
function get_teacher_products_data() {
    if ( is_singular( 'teacher' ) && ! function_exists( 'jet_engine' ) ) {
        $teacher_slug = get_post_field( 'post_name', get_the_ID() );
        if ( empty( $teacher_slug ) ) {
            error_log( 'Error: teacher_slug is empty for post ID: ' . get_the_ID() );
            return false;
        }

        $consumer_key = 'ck_1a35a7af1912254ffe912e701e3f390164e78523'; 
        $woo_secret_key = 'cs_dd4ebe8904d400e16484fcb848021ab6a9717a39'; 

        $api_url = 'https://tamyar.ir/wp-json/wc/v3/products?teacher=' . urlencode( $teacher_slug ) . '&per_page=10';

        $response = wp_remote_get( $api_url, array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( $consumer_key . ':' . $woo_secret_key )
            )
        ));

        if ( is_wp_error( $response ) ) {
            error_log( 'Error: API request failed - ' . $response->get_error_message() . ' for URL: ' . $api_url );
            return false;
        }

        $body = wp_remote_retrieve_body( $response );
        $products = json_decode( $body, true );
        if ( ! is_array( $products ) ) {
            error_log( 'Error: Invalid API response - ' . $body );
            return false;
        }

        return $products;
    }
    return false; // در Listing Grid، داده‌ها از endpoint میاد
}

// شورتکد برای نمایش نام محصول
function display_teacher_product_name( $atts ) {
    $output = '';
    // اگر در Listing Grid هستیم، از داده فعلی استفاده می‌کنیم
    if ( function_exists( 'jet_engine' ) && isset( $atts['in_listing'] ) && $atts['in_listing'] ) {
        $product = jet_engine()->listings->data->get_current_object();
        if ( $product instanceof WP_Post ) {
            $name = get_post_meta( $product->ID, 'name', true ); // از متا
            if ( ! $name ) $name = $product->post_title; // پیش‌فرض
            $slug = get_post_meta( $product->ID, 'slug', true );
            if ( ! $slug ) $slug = $product->post_name; // پیش‌فرض
            $product_url = 'https://tamyar.ir/product/' . $slug . '/';
            $output .= '<a href="' . esc_url( $product_url ) . '" target="_blank">' . esc_html( $name ) . '</a>';
        } else {
            $output .= 'داده محصول ناموجود';
        }
    } else {
        // برای حالت مستقیم، از API استفاده می‌کنیم
        $products = get_teacher_products_data();
        if ( ! $products ) return 'خطا در لود محصولات';
        foreach ( $products as $product ) {
            $product_url = 'https://tamyar.ir/product/' . $product['slug'] . '/';
            $output .= '<a href="' . esc_url( $product_url ) . '" target="_blank">' . esc_html( $product['name'] ) . '</a><br>';
        }
    }
    return $output;
}
add_shortcode( 'teacher_product_name', 'display_teacher_product_name' );

// شورتکد برای نمایش قیمت محصول
function display_teacher_product_price( $atts ) {
    $output = '';
    if ( function_exists( 'jet_engine' ) && isset( $atts['in_listing'] ) && $atts['in_listing'] ) {
        $product = jet_engine()->listings->data->get_current_object();
        if ( $product instanceof WP_Post ) {
            $price = get_post_meta( $product->ID, 'price', true ); // از متا
            if ( ! $price ) $price = 'قیمت ناموجود'; // پیش‌فرض
            $slug = get_post_meta( $product->ID, 'slug', true );
            if ( ! $slug ) $slug = $product->post_name; // پیش‌فرض
            $product_url = 'https://tamyar.ir/product/' . $slug . '/';
            $output .= '<a href="' . esc_url( $product_url ) . '" target="_blank">' . esc_html( $price ) . '</a>';
        } else {
            $output .= 'داده محصول ناموجود';
        }
    } else {
        $products = get_teacher_products_data();
        if ( ! $products ) return 'خطا در لود محصولات';
        foreach ( $products as $product ) {
            $product_url = 'https://tamyar.ir/product/' . $product['slug'] . '/';
            $output .= '<a href="' . esc_url( $product_url ) . '" target="_blank">' . esc_html( $product['price'] ) . '</a><br>';
        }
    }
    return $output;
}
add_shortcode( 'teacher_product_price', 'display_teacher_product_price' );

// شورتکد برای نمایش تصویر محصول
function display_teacher_product_image( $atts ) {
    $output = '';
    if ( function_exists( 'jet_engine' ) && isset( $atts['in_listing'] ) && $atts['in_listing'] ) {
        $product = jet_engine()->listings->data->get_current_object();
        if ( $product instanceof WP_Post ) {
            $images = get_post_meta( $product->ID, 'images', true ); // از متا
            $slug = get_post_meta( $product->ID, 'slug', true );
            if ( ! $slug ) $slug = $product->post_name; // پیش‌فرض
            $product_url = 'https://tamyar.ir/product/' . $slug . '/';
            if ( is_array( $images ) && ! empty( $images ) && isset( $images[0]['src'] ) ) {
                $output .= '<a href="' . esc_url( $product_url ) . '" target="_blank"><img src="' . esc_url( $images[0]['src'] ) . '" alt="' . esc_attr( get_post_meta( $product->ID, 'name', true ) ?: $product->post_title ) . '" style="max-width:100px; margin:5px;"></a>';
            } else {
                $output .= 'داده تصویر ناموجود';
            }
        } else {
            $output .= 'داده تصویر ناموجود';
        }
    } else {
        $products = get_teacher_products_data();
        if ( ! $products ) return 'خطا در لود محصولات';
        foreach ( $products as $product ) {
            if ( ! empty( $product['images'] ) ) {
                $product_url = 'https://tamyar.ir/product/' . $product['slug'] . '/';
                $output .= '<a href="' . esc_url( $product_url ) . '" target="_blank"><img src="' . esc_url( $product['images'][0]['src'] ) . '" alt="' . esc_attr( $product['name'] ) . '" style="max-width:100px; margin:5px;"></a>';
            }
        }
    }
    return $output;
}
add_shortcode( 'teacher_product_image', 'display_teacher_product_image' );



// add_filter( 'gform_get_form_filter', 'lwt_add_inline_js_to_form_safe', 10, 2 );
// function lwt_add_inline_js_to_form_safe( $form_string, $form ) {

//     $target_form_id = 25; 
//     $phone_field_id = 3; 


//     if ( $form['id'] != $target_form_id ) {
//         return $form_string;
//     }

//     $script = "
//     <script type='text/javascript'>
//         jQuery(document).ready(function($){
//             // پیدا کردن فیلد ورودی
//             var phoneInput = $('#input_{$target_form_id}_{$phone_field_id}');

//             // اطمینان از وجود فیلد قبل از اجرای کد
//             if (phoneInput.length) {
//                 // محدودیت طول به ۱۱ کاراکتر
//                 phoneInput.attr('maxlength', 11);

//                 // محدود کردن ورودی فقط به اعداد انگلیسی (0-9)
//                 phoneInput.on('input', function() {
//                     this.value = this.value.replace(/[^0-9]/g, '');
//                 });
//             }
//         });
//     </script>
//     ";
//     return $form_string . $script;
// }


add_action( 'gform_after_submission', 'send_lucky_wheel_sms_manually', 50, 2 );

function send_lucky_wheel_sms_manually( $entry, $form ) {

    // ----------- تنظیمات شما -----------
    // شناسه فرم شما از لاگ 25 بود
    $target_form_id = 25; 

    // کلید API کاوه نگار
    $api_key = '6A67394D58385358526B4A2F3672373758307661362B622B5649506831745550'; // <--- کلید API خود را اینجا وارد کنید

    // نام قالب پیامک
    $template_name = 'success-sms-luckywheel';

    // شناسه فیلد تلفن (از لاگ شما 3 بود)
    $phone_field_id = '3'; 

    // شناسه فیلد جایزه (از تصویر شما 5 بود)
    $prize_field_id = '5'; // <--- مطمئن شوید شناسه فیلد جایزه همین است
    // ---------------------------------

    // اگر فرم مورد نظر ما نیست، اجرا نکن
    if ( $form['id'] != $target_form_id ) {
        return;
    }
    error_log('✅ [SMS] مرحله ۱: تابع برای فرم درست اجرا شد. شناسه فرم: ' . $form['id']);

    // تلفن را از ورودی اولیه می‌خوانیم
    $phone = rgar( $entry, $phone_field_id );

    // --- راه‌حل کلیدی: ورودی را دوباره از دیتابیس می‌خوانیم ---
    // چون افزونه شما جایزه را "بعد از" ارسال در دیتابیس ذخیره می‌کند
    // این تابع مطمئن می‌شود که ما آخرین اطلاعات ثبت شده را داریم
    if ( class_exists('GFAPI') ) {
        $fresh_entry = GFAPI::get_entry( $entry['id'] );
        $prize = rgar( $fresh_entry, $prize_field_id );
    } else {
        error_log('❌ [SMS] خطا: GFAPI فعال نیست.');
        return;
    }
    // ----------------------------------------------------

    error_log('اطلاعات [SMS] مرحله ۲: شماره تلفن استخراج شده: [' . $phone . ']');
    error_log('اطلاعات [SMS] مرحله ۲: جایزه استخراج شده (از دیتابیس): [' . $prize . ']');

    // اگر تلفن یا جایزه خالی بود، یا اگر جایزه "پوچ" بود، پیامک ارسال نکن
    if ( empty( $phone ) || empty( $prize ) || $prize == 'پوچ' ) {
        $error_reason = empty($phone) ? 'تلفن خالی' : (empty($prize) ? 'جایزه خالی' : 'جایزه پوچ');
        error_log('❌ [SMS] ارسال متوقف شد. دلیل: ' . $error_reason);
        return;
    }

    // ساخت URL درخواست
    $url = 'https://api.kavenegar.com/v1/' . $api_key . '/verify/lookup.json';
    
    $request_url = add_query_arg( [
        'receptor' => $phone,
        'token'    => $phone,  // توکن اول: تلفن (می‌توانید تغییر دهید)
        'token2'   => $prize,  // توکن دوم: جایزه
        'template' => $template_name,
    ], $url );
    
    error_log('اطلاعات [SMS] مرحله ۳: URL نهایی درخواست: ' . $request_url);

    // ارسال درخواست به کاوه نگار
    $response = wp_remote_get( $request_url );
    
    // ثبت پاسخ کاوه نگار در لاگ
    if ( is_wp_error( $response ) ) {
        error_log('❌ [SMS] خطای وردپرس در ارسال: ' . $response->get_error_message());
    } else {
        $body = wp_remote_retrieve_body( $response );
        error_log('✅ [SMS] پاسخ سرور کاوه نگار: ' . $body);
    }
}


// شورتکد ویدیو بدون مدال
function fsv_player_func($atts) {
    global $post;

    $aparat_code = get_post_meta($post->ID, 'fsv-aparat-code', true);
    $video_name  = get_post_meta($post->ID, 'fsv-video-name', true);
    $video_id    = get_post_meta($post->ID, 'fsv-video-id', true);

    // آی‌دی منحصربه‌فرد برای هر پلیر
    $unique_id = 'player_' . uniqid();

    ob_start();

    if (!empty($aparat_code)) {
        // ویدیو آپارات
        ?>
        <div class="video-container" style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;">
            <iframe src="https://www.aparat.com/video/video/embed/videohash/<?php echo esc_attr($aparat_code); ?>/vt/frame"
                    style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0" allowfullscreen></iframe>
        </div>
        <?php
    } elseif (!empty($video_name) && !empty($video_id)) {
        // ویدیو از stream.tamland.ir
        $token = get_static_token($video_name, $video_id);

        if (empty($token) || strpos($token, 'error') !== false) {
            echo '<p style="color:#fff; text-align:center;">خطا در دریافت ویدیو</p>';
        } else {
            ?>
            <div id="<?php echo esc_attr($unique_id); ?>" class="stream-player" style="width:100%;max-width:800px;margin:auto;"></div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    if (typeof OvenPlayer === "undefined") {
                        console.error("OvenPlayer not loaded. Make sure it's enqueued.");
                        return;
                    }

                    OvenPlayer.create("<?php echo esc_js($unique_id); ?>", {
                        sources: [
                            { label: "1080p", type: "hls", file: "https://stream.tamland.ir/done/<?php echo $video_name; ?>/1080_<?php echo $video_name; ?>_1.m3u8?auth=<?php echo $token; ?>" },
                            { label: "720p", type: "hls", file: "https://stream.tamland.ir/done/<?php echo $video_name; ?>/720_<?php echo $video_name; ?>_1.m3u8?auth=<?php echo $token; ?>" },
                            { label: "480p", type: "hls", file: "https://stream.tamland.ir/done/<?php echo $video_name; ?>/480_<?php echo $video_name; ?>_1.m3u8?auth=<?php echo $token; ?>" },
                            { label: "360p", type: "hls", file: "https://stream.tamland.ir/done/<?php echo $video_name; ?>/360_<?php echo $video_name; ?>_1.m3u8?auth=<?php echo $token; ?>" }
                        ],
                        autoStart: false,
                        mute: false
                    });
                });
            </script>
            <?php
        }
    } else {
        echo '<p style="text-align:center;">ویدیو موجود نیست</p>';
    }

    return ob_get_clean();
}
add_shortcode('fsv_player', 'fsv_player_func');