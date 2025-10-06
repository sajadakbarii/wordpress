<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '3.4.4' );
define( 'EHP_THEME_SLUG', 'hello-elementor' );

define( 'HELLO_THEME_PATH', get_template_directory() );
define( 'HELLO_THEME_URL', get_template_directory_uri() );
define( 'HELLO_THEME_ASSETS_PATH', HELLO_THEME_PATH . '/assets/' );
define( 'HELLO_THEME_ASSETS_URL', HELLO_THEME_URL . '/assets/' );
define( 'HELLO_THEME_SCRIPTS_PATH', HELLO_THEME_ASSETS_PATH . 'js/' );
define( 'HELLO_THEME_SCRIPTS_URL', HELLO_THEME_ASSETS_URL . 'js/' );
define( 'HELLO_THEME_STYLE_PATH', HELLO_THEME_ASSETS_PATH . 'css/' );
define( 'HELLO_THEME_STYLE_URL', HELLO_THEME_ASSETS_URL . 'css/' );
define( 'HELLO_THEME_IMAGES_PATH', HELLO_THEME_ASSETS_PATH . 'images/' );
define( 'HELLO_THEME_IMAGES_URL', HELLO_THEME_ASSETS_URL . 'images/' );

if ( ! isset( $content_width ) ) {
    $content_width = 800; // Pixels.
}

function loadAssets() {
    if(is_front_page()){
        wp_enqueue_script('multiStepCoursesGrid', get_template_directory_uri() . '/assets/js/multiStepCoursesGrid.js', array('jquery'), null, true);
    }
    wp_enqueue_script('popup-player', get_template_directory_uri() . '/assets/js/popupPlayer.js', array('jquery'), null, true);
    wp_enqueue_style('assets-style', get_template_directory_uri() . '/assets/css/style.css', array(), null);
}
add_action('wp_enqueue_scripts', 'loadAssets');

add_shortcode('add_to_cart_button_course', 'add_to_cart_button_course_func');
function add_to_cart_button_course_func($atts){
    $button_atts = shortcode_atts( array(
        'id' => '',
        'title' => 'ثبت نام در این دوره',
        'is_archive' => false,
        'teachers_course_id' => ''
    ), $atts );

    $checkout_page_url = 'https://mid1.tamland.ir/course-checkout';
    if($button_atts['id'] == ''){
        $post = get_post();
    }else{
        $post = get_post($button_atts['id']);
    }
    $item_name = str_replace("|","-",$post->post_title);
    $secound_title = str_replace("|","-", get_post_meta($post->ID, 'secound-title', true));
    $post_type = get_post_type($post->ID);
    $course_type = get_post_meta( $post->ID, 'course-type', true );

    //Creating item array.
    $items = array();

    if($course_type == 'normal-course' || $course_type == 'course-pack' || ($course_type == 'multi-teacher' && $button_atts['is_archive'] == true) || $post_type == 'exams'){
        // Get Prices
        $price = get_post_meta( $post->ID, 'price_tax', true );
        $price_sale = get_post_meta( $post->ID, 'price_sale_tax', true );
        $region_price = get_post_meta( $post->ID, 'region-price', true );
        if($post_type == 'exams'){
            $course_id_lms = get_post_meta( $post->ID, 'exam-id-lms', true );
        }else{
            $course_id_lms = get_post_meta( $post->ID, 'course-id-lms', true );
        }

        if($region_price !=""){
            for( $i = 0; $i < 1; $i++ ){
                if($region_price['item-'.$i]['region-price-sale-tax'] != ""){
                    $items[] = array( 'price' => $region_price['item-'.$i]['region-price-sale-tax'], 'text' => $item_name.' (با تخفیف)');
                }else{
                    $items[] = array( 'price' => $region_price['item-'.$i]['region-price-tax'], 'text' => $item_name);
                }
            }
        }else{
            if($price_sale == ""){
                $items[] = array( 'price' => $price, 'text' => $item_name.' '.$secound_title);
            }else{
                $items[] = array( 'price' => $price_sale, 'text' => $item_name.' (با تخفیف)');
            }
        }

    }elseif($course_type == 'multi-teacher'){
        $teachers_course = get_post_meta( $post->ID, 'teachers-course', true );
        $teacher_course_id = $button_atts['teachers_course_id'];
        foreach( $teachers_course as $teachers_course_item ){
            if($teachers_course_item['teacher-course-name'] === $button_atts['teachers_course_id']){
                $course_id_lms = $teachers_course_item['teacher-course-id-lms'];
                if($teachers_course_item['teacher-course-price-sale-area-1-tax'] != ""){
                    for( $i = 1; $i < 2; $i++ ){
                        if($teachers_course_item['teacher-course-price-sale-area-'.$i.'-tax'] != ""){
                            $items[] = array( 'price' => $teachers_course_item['teacher-course-price-sale-area-'.$i.'-tax'], 'text' => $item_name.' '.get_the_title($teachers_course_item['teacher-course-name']).' (با تخفیف)', 'isSelected' => true);
                        }else{
                            $items[] = array( 'price' => $teachers_course_item['teacher-course-price-area-'.$i.'-tax'], 'text' => $item_name.' '.get_the_title($teachers_course_item['teacher-course-name']), 'isSelected' => true);
                        }
                    }
                }else{
                    if($teachers_course_item['teacher-course-price-sale-tax'] == ""){
                        $items[] = array( 'price' => $teachers_course_item['teacher-course-price-tax'], 'text' => $item_name.' '.$secound_title.' '.get_the_title($teachers_course_item['teacher-course-name']), 'isSelected' => true);
                    }else{
                        $items[] = array( 'price' => $teachers_course_item['teacher-course-price-sale-tax'], 'text' => $item_name.' '.get_the_title($teachers_course_item['teacher-course-name']).' (با تخفیف)', 'isSelected' => true);
                    }
                }
            }
        }
    }
    $token = hash_hmac('sha256', $course_id_lms . '|' . $items[0]["price"], $secret_key);
    ?>
    <form method="post" action="<?php echo $checkout_page_url; ?>">
        <?php
        if($button_atts['is_archive'] == true){
            ?>
            <button type="submit" class="add-to-cart-button cart-icon"><img src="https://konkoor.tamland.ir/wp-content/uploads/2024/09/Buy.svg"></button>
            <?php
        }else{
            ?>
            <button type="submit" class="add-to-cart-button w-100"><?php  echo $button_atts['title']; ?></button>
            <?php
        }
        ?>
        <input type="hidden" name="post_id" value="<?php echo $post->ID; ?>">
        <input type="hidden" name="teacher_course_id" value="<?php echo $teacher_course_id; ?>">
        <input type="hidden" name="course_id_lms" value="<?php echo $course_id_lms; ?>">
        <input type="hidden" name="ref_url_payment" value="<?php the_permalink(); ?>">
        <?php
        if($course_type == 'normal-course'){
            ?>
            <input type="hidden" name="course_type" value="دوره معمولی">
            <?php
        }elseif($course_type == 'multi-teacher'){
            ?>
            <input type="hidden" name="course_type" value="چند استاده">
            <?php
        }elseif($course_type == 'course-pack'){
            ?>
            <input type="hidden" name="course_type" value="بسته">
            <?php
        }
        if($post_type == 'exams'){
            ?>
            <input type="hidden" name="course_type" value="آزمون">
            <?php
        }
        for($i = 0; $i < count($items); $i++){
            echo '
            <input type="hidden" name="course_name_'.$i.'" value="'.$items[$i]["text"].'">
            <input type="hidden" name="course_price_'.$i.'" value="'.$items[$i]["price"].'">
            ';
        }
        ?>
        <input type="hidden" name="course_numbers" value="<?php echo count($items); ?>">
        <input type="hidden" name="utm_source" value="<?php echo htmlspecialchars($_GET['utm_source'] ?? ''); ?>">
        <input type="hidden" name="utm_medium" value="<?php echo htmlspecialchars($_GET['utm_medium'] ?? ''); ?>">
        <input type="hidden" name="utm_campaign" value="<?php echo htmlspecialchars($_GET['utm_campaign'] ?? ''); ?>">
        <input type="hidden" name="utm_term" value="<?php echo htmlspecialchars($_GET['utm_term'] ?? ''); ?>">
        <input type="hidden" name="utm_content" value="<?php echo htmlspecialchars($_GET['utm_content'] ?? ''); ?>">
        <input type="hidden" name="secure_token" value="<?= $token ?>">
    </form>
    <?php
}

function az3_tag_descriptions_shortcode( $atts ) {
    global $post;

    if ( ! $post ) {
        return '';
    }

    // گرفتن برچسب‌های پست
    $tags = get_the_tags( $post->ID );
    $tag_desc = [];

    if ( $tags && ! is_wp_error( $tags ) ) {
        foreach ( $tags as $tag ) {
            if ( ! empty( $tag->description ) ) {
                $tag_desc[] = $tag->description;
            }
        }
    }

    // خروجی: توضیحات تگ‌ها با کاما
    return esc_attr( implode( ',', $tag_desc ) );
}
add_shortcode( 'az3_tags_desc', 'az3_tag_descriptions_shortcode' );





function my_exam_lessons_sources_accordion_qfpe() {
    ?>
    <style>
        .my-accordion {
            font-family: 'ایران‌سنسX', 'Arial', sans-serif;
            direction: rtl;
            overflow: hidden;
            margin: 20px 0;
            background-color: transparent;
        }
        .my-accordion .accordion-item {
            background-color: transparent;
        }
        .my-accordion .accordion-item:last-child {
            border-bottom: none;
        }
        .my-accordion .accordion-header {
            background-color: transparent;
            color: #c0392b;
            font-size: 1.15rem;
            font-weight: bold;
            padding: 18px 25px;
            border-right :none;
            border-left :none;
            border-top :none;
            border-bottom: 1px solid #DBDFED;
            cursor: pointer;
            width: 100%;
            text-align: right;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.2s ease;
        }
        .my-accordion .accordion-header:hover {
            background-color: transparent;
        }
        .my-accordion .accordion-header .icon {
            width: 20px;
            height: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
            color: #c0392b;
            transition: transform 0.2s ease;
        }
        .my-accordion .accordion-header .icon::before {
            content: '+';
        }
        .my-accordion .accordion-header.active .icon::before {
            content: '-';
        }
        .my-accordion .accordion-content {
            display: none;
            background-color: transparent !important;
            padding: 0 20px 10px 20px;
            background-color: #fcfcfc;
            border-top: 1px solid #eee;
        }
        .lesson-list {
            list-style: none;
            padding: 0;
            margin: 10px 0 0 0;
        }
        .lesson-item {
            border-radius: 10px;
            margin-bottom: 10px;
            overflow: hidden;
            border:none;
        }
        .lesson-item:last-child {
            margin-bottom: 0;
        }
        .lesson-item .lesson-header {
            background-color: transparent;
            color: #333;
            font-size: 1rem;
            font-weight: bold;
            padding: 12px 20px;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: right;
            display: flex;
            justify-content: start;
            align-items: center;
            transition: background-color 0.2s ease;
        }
        .lesson-item .lesson-header:hover {
        }
        .lesson-item .lesson-header .icon {
            width: 16px;
            height: 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 0.75rem;
            color: #777;
            line-height: 1;
            transform: rotate(0deg);
            transition: transform 0.2s ease;
        }
        .lesson-item .lesson-header .icon::before {
            content: '▼';
            color:black;
        }
        .lesson-item .lesson-header.active .icon::before {
            content: '▲';
            color:black;
        }
        .lesson-item .lesson-content {
            display: none;
            padding: 10px 20px;
        }
        .source-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .source-item {
            margin-bottom: 10px;
            background-color: #FCFCFC;
            border-radius:12px;
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
            padding: 12px 12px;
        }
        .source-item:last-child {
            border-bottom: none;
        }
        .source-item .teacher-info {
            font-size: 0.95rem;
            color: #555;
            font-weight: 500;
            white-space: nowrap;
            width: 20%;
        }
        .source-item .source-links {
            width:80%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            flex-flow:row-reverse;
        }
        .source-item .source-link {
            width:31%;
            text-decoration: none;
            color: #444;
            border-radius: 8px;
            padding: 8px 12px;
            display: flex;
            direction:ltr;
            align-items: center;
            gap:2px;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }
        .source-item .source-link:hover {
            background-color: #ebebeb;
            border-color: #ccc;
        }
        .source-item .source-icon {
            font-family: 'Font Awesome 5 Free', 'Arial', sans-serif;
            font-weight: 900;
            font-size: 1rem;
            color: #666;
        }
        .source-item .source-link .source-icon-img {
            width: 20px;
            height: 20px;
        }

        @media (max-width: 768px) {
            .my-accordion {
                margin: 10px 0;
                border-radius: 8px;
            }
            .my-accordion .accordion-header {
                padding: 12px 15px;
                font-size: 1rem;
            }
            .lesson-item .lesson-header {
                padding: 10px 15px;
                font-size: 0.95rem;
            }
            .lesson-item .lesson-header .icon {
                width: 14px;
                height: 14px;
            }
            .source-item {
                flex-direction: column;
                align-items: flex-start;
                padding: 10px;
            }
            .source-item .teacher-info {
                width: 100%;
                text-align: right;
                margin-bottom: 8px;
            }
            .source-item .source-links {
                direction:ltr;
                width: 100%;
                justify-content: space-around;
                flex-flow: row;
                gap: 8px;
            }
            .source-item .source-link {
                width: auto;
                padding: 6px 10px;
                justify-content: center;
            }
            .source-item .source-link .source-text {
                display: none;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // اکاردئون اصلی
            document.querySelectorAll('.accordion-header').forEach(header => {
                header.addEventListener('click', function() {
                    const targetId = this.dataset.target;
                    const content = document.getElementById(targetId);
                    const isActive = this.classList.contains('active');

                    // بستن همه
                    document.querySelectorAll('.accordion-header').forEach(h => h.classList.remove('active'));
                    document.querySelectorAll('.accordion-content').forEach(c => c.style.display = 'none');

                    // باز کردن فقط اگر قبلا باز نبود
                    if (!isActive) {
                        this.classList.add('active');
                        content.style.display = 'block';
                    }
                });
            });

            // اکاردئون درس‌ها
            document.querySelectorAll('.lesson-header').forEach(header => {
                header.addEventListener('click', function() {
                    const targetId = this.dataset.target;
                    const content = document.getElementById(targetId);
                    const isActive = this.classList.contains('active');

                    // بستن همه درس‌ها در همان آزمون
                    const parentAccordion = this.closest('.accordion-content');
                    parentAccordion.querySelectorAll('.lesson-header').forEach(h => h.classList.remove('active'));
                    parentAccordion.querySelectorAll('.lesson-content').forEach(c => c.style.display = 'none');

                    if (!isActive) {
                        this.classList.add('active');
                        content.style.display = 'block';
                    }
                });
            });
        });

    </script>
    <?php
    ob_start();

    // Fetch data with original slugs
    $exams = get_post_meta(get_the_ID(), 'exams_qfpe', true);
    $lessons = get_post_meta(get_the_ID(), 'lessons_qfpe', true);
    $sources = get_post_meta(get_the_ID(), 'sources_qfpe', true);

    if ($exams) :
        ?>
        <div class="my-accordion my-accordion-qfpe">
            <?php foreach ($exams as $exam_index => $exam) :
                $exam_id = isset($exam['id_exam_qfpe']) ? $exam['id_exam_qfpe'] : '';
                $exam_date = isset($exam['date_exam_qfpe']) ? $exam['date_exam_qfpe'] : '';
                $accordion_exam_id = 'exam-qfpe-' . $exam_index;
                ?>

                <div class="accordion-item">
                    <button class="accordion-header" type="button" data-target="<?php echo esc_attr($accordion_exam_id); ?>">
                        <span class="exam-title">
                         <?php if (!empty($exam_date)) : ?>
                             <?php echo esc_html($exam_date); ?>
                         <?php endif; ?>
                        </span>
                        <span class="icon"></span>
                    </button>

                    <div id="<?php echo esc_attr($accordion_exam_id); ?>" class="accordion-content">
                        <?php
                        $has_lessons = false;
                        if ($lessons) {
                            echo '<ul class="lesson-list">';
                            foreach ($lessons as $lesson_index => $lesson) {
                                if (isset($lesson['exam_id_lesson_qfpe'], $lesson['id_lesson_qfpe'], $lesson['name_lesson_qfpe'])
                                    && $lesson['exam_id_lesson_qfpe'] == $exam_id) {
                                    $has_lessons = true;
                                    $lesson_id = $lesson['id_lesson_qfpe'];
                                    $lesson_name = $lesson['name_lesson_qfpe'];
                                    $accordion_lesson_id = $accordion_exam_id . '-lesson-' . $lesson_index;
                                    ?>
                                    <li class="lesson-item">
                                        <button class="lesson-header" type="button" data-target="<?php echo esc_attr($accordion_lesson_id); ?>">
                                            <span class="lesson-title"><?php echo esc_html($lesson_name); ?></span>
                                            <span class="icon"></span>
                                        </button>

                                        <div id="<?php echo esc_attr($accordion_lesson_id); ?>" class="lesson-content">
                                            <?php
                                            $has_sources = false;
                                            if ($sources) {
                                                echo '<ul class="source-list">';
                                                foreach ($sources as $source) {
                                                    if (isset($source['exam_id_sources_qfpe'], $source['lesson_id_sources_qfpe'])
                                                        && $source['exam_id_sources_qfpe'] == $exam_id
                                                        && $source['lesson_id_sources_qfpe'] == $lesson_id) {
                                                        $has_sources = true;
                                                        ?>
                                                        <li class="source-item">
                                                            <div class="teacher-info">
                                                                استاد <?php echo esc_html($source['teacher_sources_qfpe']); ?>
                                                            </div>
                                                            <div class="source-links">
                                                                <?php if (!empty($source['voice_sources_qfpe'])) : ?>
                                                                    <a href="<?php echo esc_url($source['voice_sources_qfpe']); ?>" target="_blank" class="source-link voice-link">
                                                                        <img src="https://azmoon.tamland.ir/wp-content/uploads/2025/09/import.svg" alt="Video Icon" class="source-icon-img">
                                                                        <span class="source-text">voice</span>
                                                                    </a>
                                                                <?php endif; ?>
                                                                <?php if (!empty($source['video_sources_aparat_qfpe']) || !empty($source['video_sources_name_qfpe'])) : ?>
                                                                    <a href="javascript:void(0)"
                                                                       class="source-link video-link send-video-data"
                                                                       data-aparat="<?php echo esc_attr($source['video_sources_aparat_qfpe']); ?>"
                                                                       data-name="<?php echo esc_attr($source['video_sources_name_qfpe']); ?>"
                                                                       data-id="<?php echo esc_attr($source['video_sources_id_qfpe']); ?>">
                                                                        <img src="https://azmoon.tamland.ir/wp-content/uploads/2025/09/import-1.svg" alt="Video Icon" class="source-icon-img">
                                                                        <span class="source-text">video</span>
                                                                    </a>
                                                                <?php endif; ?>
                                                                <?php if (!empty($source['doc_sources_qfpe'])) : ?>
                                                                    <a href="<?php echo esc_url($source['doc_sources_qfpe']); ?>" target="_blank" class="source-link pdf-link">
                                                                        <img src="https://azmoon.tamland.ir/wp-content/uploads/2025/09/import-2.svg" alt="Video Icon" class="source-icon-img">
                                                                        <span class="source-text">PDF</span>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>
                                                        </li>
                                                        <?php
                                                    }
                                                }
                                                echo '</ul>';
                                            }
                                            if (!$has_sources) {
                                                echo '<p>هیچ منبعی برای این درس ثبت نشده.</p>';
                                            }
                                            ?>
                                        </div>
                                    </li>
                                    <?php
                                }
                            }
                            echo '</ul>';
                        }
                        if (!$has_lessons) {
                            echo '<p>هیچ درسی برای این آزمون ثبت نشده.</p>';
                        }
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php
    endif;

    return ob_get_clean();
}
add_shortcode('exam_lessons_sources', 'my_exam_lessons_sources_accordion_qfpe');

/**
 * Shortcode function for the new 'llatt' slugs.
 */
function lessons_learnt_after_test_func_llatt() {
    ?>
    <?php
    ob_start();

    // Fetch data with new slugs
    $exams = get_post_meta(get_the_ID(), 'exams_llatt', true);
    $lessons = get_post_meta(get_the_ID(), 'lessons_llatt', true);
    $sources = get_post_meta(get_the_ID(), 'sources_llatt', true);

    if ($exams) :
        ?>
        <div class="my-accordion my-accordion-llatt">
            <?php foreach ($exams as $exam_index => $exam) :
                $exam_id = isset($exam['id_exams_llatt']) ? $exam['id_exams_llatt'] : '';
                $exam_date = isset($exam['date_exams_llatt']) ? $exam['date_exams_llatt'] : '';
                $accordion_exam_id = 'exam-llatt-' . $exam_index;
                ?>

                <div class="accordion-item">
                    <button class="accordion-header" type="button" data-target="<?php echo esc_attr($accordion_exam_id); ?>">
                        <span class="exam-title">
                         <?php if (!empty($exam_date)) : ?>
                             <?php echo esc_html($exam_date); ?>
                         <?php endif; ?>
                        </span>
                        <span class="icon"></span>
                    </button>

                    <div id="<?php echo esc_attr($accordion_exam_id); ?>" class="accordion-content">
                        <?php
                        $has_lessons = false;
                        if ($lessons) {
                            echo '<ul class="lesson-list">';
                            foreach ($lessons as $lesson_index => $lesson) {
                                if (isset($lesson['exam_id_lesson_llatt'], $lesson['id_lesson_llatt'], $lesson['name_lesson_llatt'])
                                    && $lesson['exam_id_lesson_llatt'] == $exam_id) {
                                    $has_lessons = true;
                                    $lesson_id = $lesson['id_lesson_llatt'];
                                    $lesson_name = $lesson['name_lesson_llatt'];
                                    $accordion_lesson_id = $accordion_exam_id . '-lesson-' . $lesson_index;
                                    ?>
                                    <li class="lesson-item">
                                        <button class="lesson-header" type="button" data-target="<?php echo esc_attr($accordion_lesson_id); ?>">
                                            <span class="lesson-title"><?php echo esc_html($lesson_name); ?></span>
                                            <span class="icon"></span>
                                        </button>

                                        <div id="<?php echo esc_attr($accordion_lesson_id); ?>" class="lesson-content">
                                            <?php
                                            $has_sources = false;
                                            if ($sources) {
                                                echo '<ul class="source-list">';
                                                foreach ($sources as $source) {
                                                    if (isset($source['exam_id_sources_llatt'], $source['lesson_id_sources_llatt'])
                                                        && $source['exam_id_sources_llatt'] == $exam_id
                                                        && $source['lesson_id_sources_llatt'] == $lesson_id) {
                                                        $has_sources = true;
                                                        ?>
                                                        <li class="source-item">
                                                            <div class="teacher-info">
                                                                استاد <?php echo esc_html($source['teacher_sources_llatt']); ?>
                                                            </div>
                                                            <div class="source-links">
                                                                <?php if (!empty($source['voice_sources_llatt'])) : ?>
                                                                    <a href="<?php echo esc_url($source['voice_sources_llatt']); ?>" target="_blank" class="source-link voice-link">
                                                                        <img src="https://azmoon.tamland.ir/wp-content/uploads/2025/09/import.svg" alt="Video Icon" class="source-icon-img">
                                                                        <span class="source-text">voice</span>
                                                                    </a>
                                                                <?php endif; ?>
                                                                <?php if (!empty($source['video_sources_aparat_llatt']) || !empty($source['video_sources_name_llatt'])) : ?>
                                                                    <a href="javascript:void(0)"
                                                                       class="source-link video-link send-video-data"
                                                                       data-aparat="<?php echo esc_attr($source['video_sources_aparat_llatt']); ?>"
                                                                       data-name="<?php echo esc_attr($source['video_sources_name_llatt']); ?>"
                                                                       data-id="<?php echo esc_attr($source['video_sources_id_llatt']); ?>">
                                                                        <img src="https://azmoon.tamland.ir/wp-content/uploads/2025/09/import-1.svg" alt="Video Icon" class="source-icon-img">
                                                                        <span class="source-text">video</span>
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if (!empty($source['doc_sources_llatt'])) : ?>
                                                                    <a href="<?php echo esc_url($source['doc_sources_llatt']); ?>" target="_blank" class="source-link pdf-link">
                                                                        <img src="https://azmoon.tamland.ir/wp-content/uploads/2025/09/import-2.svg" alt="Video Icon" class="source-icon-img">
                                                                        <span class="source-text">PDF</span>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>
                                                        </li>
                                                        <?php
                                                    }
                                                }
                                                echo '</ul>';
                                            }
                                            if (!$has_sources) {
                                                echo '<p>هیچ منبعی برای این درس ثبت نشده.</p>';
                                            }
                                            ?>
                                        </div>
                                    </li>
                                    <?php
                                }
                            }
                            echo '</ul>';
                        }
                        if (!$has_lessons) {
                            echo '<p>هیچ درسی برای این آزمون ثبت نشده.</p>';
                        }
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php
    endif;
    return ob_get_clean();
}
add_shortcode('lessons_learnt_after_test', 'lessons_learnt_after_test_func_llatt');

// Add this code to your theme's functions.php or a custom plugin
function get_dynamic_classes() {
    global $post;

    if ($post->post_type !== 'exams') {
        return '';
    }

    $classes = array();

    // Map for azmoon-tags taxonomy
    $azmoon_map = array(
        67 => 'az-tag-konkoor',
        68 => 'az-tag-mid2',
        71 => 'az-tag-tiz6',
        72 => 'az-tag-tiz9',
    );

    $azmoon_terms = wp_get_post_terms($post->ID, 'azmoon-tags', array('fields' => 'ids'));

    foreach ($azmoon_terms as $term_id) {
        if (isset($azmoon_map[$term_id])) {
            $classes[] = $azmoon_map[$term_id];
        }
    }

    // Map for field taxonomy
    $field_map = array(
        60 => 'az-tag-tajrobi',
        61 => 'az-tag-riazi',
        62 => 'az-tag-ensani',
    );

    $field_terms = wp_get_post_terms($post->ID, 'field', array('fields' => 'ids'));

    foreach ($field_terms as $term_id) {
        if (isset($field_map[$term_id])) {
            $classes[] = $field_map[$term_id];
        }
    }

    return implode(' ', $classes);
}

add_shortcode('dynamic_classes', 'get_dynamic_classes');
// گرفتن آی‌پی کاربر
function get_user_ip_static() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// گرفتن توکن ویدیو
function get_static_token($video_name, $video_id) {
    $ip = get_user_ip_static();

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.tamland.ir/api/course/getWpStreamToken/' . esc_html($video_name) . '/' . esc_html($video_id) . '/' . $ip,
        CURLOPT_RETURNTRANSFER => true,
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}

// شورتکد برای مدال ویدیو (مدال فقط یک بار ساخته می‌شود)
function videoPlayerPopUp_func() {
    ob_start();
    ?>
    <div id="videoModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; justify-content:center; align-items:center;">
        <div class="modal-content" style="position:relative; width:90%; max-width:800px; background:#000;">
            <span id="closeModal" style="position:absolute; top:-30px; right:-30px; color:#fff; cursor:pointer; font-size:24px;">&times;</span>
            <div id="modalPlayer"></div>
        </div>
    </div>
    <style>
        #closeModal{
            @media(max-height:768px){
                right:0;
                top: 0;
            }
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const modal = document.getElementById('videoModal');
            const modalPlayer = document.getElementById('modalPlayer');
            const closeBtn = document.getElementById('closeModal');

            // بستن مدال با کلیک روی دکمه
            closeBtn.addEventListener('click', () => {
                modal.style.display = 'none';
                modalPlayer.innerHTML = ''; // پاک کردن محتوا
            });

            // بستن مدال با کلیک روی پس‌زمینه
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    modalPlayer.innerHTML = '';
                }
            });

            // Event delegation برای همه المان های send-video-data
            document.body.addEventListener('click', async (e) => {
                const el = e.target.closest('.send-video-data');
                if (!el) return;

                e.preventDefault();
                e.stopPropagation();

                const videoName = el.getAttribute('data-name');
                const videoId = el.getAttribute('data-id');
                const aparatCode = el.getAttribute('data-aparat');

                modal.style.display = 'flex';
                modalPlayer.innerHTML = ''; // پاک کردن محتوای قبلی

                if (videoName && videoId) {
                    // اگر نام و شناسه HLS موجود بود → OvenPlayer
                    const token = await fetch('<?php echo admin_url("admin-ajax.php"); ?>?action=get_video_token&video_name=' + videoName + '&video_id=' + videoId)
                        .then(res => res.text());

                    modalPlayer.innerHTML = '<div id="player_dynamic"></div>';
                    OvenPlayer.create('player_dynamic', {
                        sources: [
                            { label: '1080', type: 'hls', file: 'https://stream.tamland.ir/done/' + videoName + '/1080_' + videoName + '_1.m3u8?auth=' + token },
                            { label: '720', type: 'hls', file: 'https://stream.tamland.ir/done/' + videoName + '/720_' + videoName + '_1.m3u8?auth=' + token },
                            { label: '480', type: 'hls', file: 'https://stream.tamland.ir/done/' + videoName + '/480_' + videoName + '_1.m3u8?auth=' + token },
                            { label: '360', type: 'hls', file: 'https://stream.tamland.ir/done/' + videoName + '/360_' + videoName + '_1.m3u8?auth=' + token }
                        ]
                    });
                } else if (aparatCode) {
                    // اگر فقط کد آپارات موجود بود → iframe
                    modalPlayer.innerHTML = `
                <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;">
                    <iframe src="https://www.aparat.com/video/video/embed/videohash/${aparatCode}/vt/frame"
                        style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0" allowfullscreen></iframe>
                </div>
            `;
                } else {
                    modalPlayer.innerHTML = '<p style="color:#fff; text-align:center;">ویدیو موجود نیست!</p>';
                }
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('videoPlayerPopUp', 'videoPlayerPopUp_func');

// تابع AJAX برای گرفتن توکن
add_action('wp_ajax_get_video_token', 'ajax_get_video_token');
add_action('wp_ajax_nopriv_get_video_token', 'ajax_get_video_token');
function ajax_get_video_token() {
    $video_name = sanitize_text_field($_GET['video_name']);
    $video_id = sanitize_text_field($_GET['video_id']);
    echo get_static_token($video_name, $video_id);
    wp_die();
}
