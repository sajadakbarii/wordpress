<?php

$TU = defined('TU') ? TU : '';
$TP = defined('TP') ? TP : '';

// Enqueue styles from parent and child theme
function shopex_child_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'), wp_get_theme()->get('Version'));
    
    wp_enqueue_script('java', get_stylesheet_directory_uri() . '/assets/js/java.js', array(), '1.0.1', true);
}
add_action('wp_enqueue_scripts', 'shopex_child_enqueue_styles');


function tamyar_add_orders_pagination_endpoint() {
    add_rewrite_endpoint( 'orders/page', EP_PAGES );
}
add_action( 'init', 'tamyar_add_orders_pagination_endpoint' );


add_filter('woocommerce_ship_to_different_address_checked', '__return_false');
// remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
// remove_action( 'woocommerce_before_cart', 'woocommerce_cart_coupon', 10 );
// remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals_coupon_form', 10 );
/*
function add_google_analytics() {
  ?>
  <!-- Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-S0TXBW0FLK"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-S0TXBW0FLK');
  </script>
  <!-- End Google Analytics -->
  <?php
}
add_action('wp_head', 'add_google_analytics');*/



add_action('wp_head','add_extra_script', 9999);
function add_extra_script(){
    ?>
<script type="text/javascript" defer>
  window.addEventListener("load", function() {
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);
        t.async = true;
        t.src = "https://www.clarity.ms/tag/" + i;
        y=l.getElementsByTagName(r)[0];
        y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "ss1v26uf9k");
    
    // لود کردن فایل اصلی gtag.js
    var gaScript = document.createElement("script");
    gaScript.src = "https://www.googletagmanager.com/gtag/js?id=G-S0TXBW0FLK"; // جایگزین با GA-ID خودت
    gaScript.async = true;
    document.body.appendChild(gaScript);

    // بعد از لود شدن اسکریپت، gtag مقداردهی شود
    gaScript.onload = function() {
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-S0TXBW0FLK'); // جایگزین با GA-ID خودت
    };
    
    ["keydown","touchmove","touchstart","mouseover"].forEach(function(v){window.addEventListener(v,function(){if(!window.isGoftinoAdded){window.isGoftinoAdded=1;var i="qHxEq5",d=document,g=d.createElement("script"),s="https://www.goftino.com/widget/"+i,l=localStorage.getItem("goftino_"+i);g.type="text/javascript",g.async=!0,g.src=l?s+"?o="+l:s;d.getElementsByTagName("head")[0].appendChild(g);}})});
  });
</script>
    <?php
}
function teacher_list_from_products_shortcode($atts){
    $atts = shortcode_atts([
        'count'   => 0,  
        'columns' => 3,  
    ], $atts, 'teacher_list');


    $product_q = new WP_Query([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'posts_per_page' => -1,
    ]);
    $product_ids = $product_q->posts;

    if (empty($product_ids)) {
        return '<p>محصولی یافت نشد.</p>';
    }

    // 2) از روی آیدیِ محصولات، ترم‌های teacher را بگیر
    $terms = get_terms([
        'taxonomy'   => 'teacher',
        'hide_empty' => true,          // فقط اساتیدی که واقعاً به محصول وصل‌اند
        'object_ids' => $product_ids,  // مهم: از خودِ محصولات انتخاب کن
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>استادی یافت نشد.</p>';
    }

    // 3) با هر رفرش، ترتیب اساتید عوض شود
    shuffle($terms);

    // 4) محدودیت تعداد (در صورت نیاز)
    if (intval($atts['count']) > 0) {
        $terms = array_slice($terms, 0, intval($atts['count']));
    }

    // 5) خروجی HTML
    ob_start();
    echo '<div class="teacher-list grid cols-'.intval($atts['columns']).'">';
    foreach ($terms as $t) {
        $link = get_term_link($t);

        // اگر برای taxonomy تصویر ذخیره می‌کنی (مثلاً با ACF یا افزونهٔ ترم‌امیج)، این خط می‌تونه تصویر رو نشون بده
        $thumb_id = get_term_meta($t->term_id, 'thumbnail_id', true);
        $img_html = $thumb_id ? wp_get_attachment_image($thumb_id, 'thumbnail') : '';

        echo '<div class="teacher-item">';
        echo   '<a href="'.esc_url($link).'">';
        echo     $img_html;
        echo     '<h3>'.esc_html($t->name).'</h3>';
        echo   '</a>';
        echo '</div>';
    }
    echo '</div>';

    return ob_get_clean();
}
add_shortcode('teacher_list', 'teacher_list_from_products_shortcode');




add_action('wp_enqueue_scripts', function () {

    $should_load_blockui = false;


    if (wp_script_is('awf', 'enqueued') || wp_script_is('yith-ywar-frontend', 'enqueued')) {
        $should_load_blockui = true;
    }

    if ($should_load_blockui) {
        if (!wp_script_is('jquery-blockui', 'registered')) {
            wp_register_script(
                'jquery-blockui',
                plugins_url('woocommerce/assets/js/jquery-blockui/jquery.blockUI.min.js'),
                array('jquery'),
                '2.70',
                true
            );
        }

        if (!wp_script_is('jquery-blockui', 'enqueued')) {
            wp_enqueue_script('jquery-blockui');
        }
    }
}, 5);


add_filter( 'woocommerce_account_menu_items', 'add_cart_tab_to_my_account', 50 );
function add_cart_tab_to_my_account( $items ) {
    $items['my-cart'] = 'سبد خرید';
    return $items;
}


add_action( 'woocommerce_account_my-cart_endpoint', 'my_account_cart_content' );
function my_account_cart_content() {
    echo '<h2>سبد خرید</h2>';
    echo do_shortcode('[woocommerce_cart]');
}

// اضافه کردن فیلد teacher به REST API محصولات
add_filter( 'woocommerce_rest_product_schema', function( $schema ) {
    $schema['properties']['teacher'] = array(
        'description' => __( 'Teachers terms.', 'textdomain' ),
        'type'        => 'array',
        'context'     => array( 'view', 'edit' ),
        'items'       => array(
            'type' => 'object',
            'properties' => array(
                'id'   => array( 'type' => 'integer' ),
                'name' => array( 'type' => 'string' ),
                'slug' => array( 'type' => 'string' ),
            ),
        ),
    );
    return $schema;
});

// نمایش teacher داخل response
add_filter( 'woocommerce_rest_prepare_product_object', function( $response, $object, $request ) {
    $terms = wp_get_post_terms( $object->get_id(), 'teacher' );
    $data = array();
    foreach ( $terms as $term ) {
        $data[] = array(
            'id'   => $term->term_id,
            'name' => $term->name,
            'slug' => $term->slug,
        );
    }
    $response->data['teacher'] = $data;
    return $response;
}, 10, 3 );

// ذخیره teacher از طریق API
add_action( 'woocommerce_rest_insert_product_object', function( $object, $request, $creating ) {
    if ( isset( $request['teacher'] ) && is_array( $request['teacher'] ) ) {
        $ids = array_map( function( $term ) {
            return is_array( $term ) && isset( $term['id'] ) ? intval( $term['id'] ) : intval( $term );
        }, $request['teacher'] );

        wp_set_post_terms( $object->get_id(), $ids, 'teacher' );
    }
}, 10, 3 );



// اجازه فیلتر محصولات بر اساس taxonomy=teacher در wc/v3
add_filter('woocommerce_rest_product_object_query', function($args, $request){
  $teacher      = $request->get_param('teacher');       // slug, comma-separated
  $teacher_term = $request->get_param('teacher_term');  // term_id, comma-separated

  $tax_query = isset($args['tax_query']) ? $args['tax_query'] : [];

  if (!empty($teacher)) {
    $tax_query[] = [
      'taxonomy' => 'teacher',
      'field'    => 'slug',
      'terms'    => array_map('trim', explode(',', $teacher)),
      'operator' => 'IN',
    ];
  }

  if (!empty($teacher_term)) {
    $tax_query[] = [
      'taxonomy' => 'teacher',
      'field'    => 'term_id',
      'terms'    => array_map('intval', array_map('trim', explode(',', $teacher_term))),
      'operator' => 'IN',
    ];
  }

  if (!empty($tax_query)) {
    $args['tax_query'] = $tax_query;
  }
  return $args;
}, 10, 2);


add_action('admin_footer', 'add_custom_button_to_products_page');
function add_custom_button_to_products_page() {
    // Check if we are on the All Products page
    $screen = get_current_screen();
    if ($screen->id !== 'edit-product') {
        return;
    }

    ?>
    <script type="text/javascript">
        var tamshopIds = []; // آرایه برای ذخیره tamshop-product-id
        var shopProductIds = [];
        var shopProductsDetails = {};
        var tamshopCatIds = [];
        var tamshopTeacherIds = [];
        var hasNewProduct = false;
        var addedProduct = 0;
        var tokenImg = ""; 
        const token = btoa("ck_9f4beb4f30c7ff4d7fe458035da45274cf1272bf:cs_2dabbcd8423ea8d632269cde1b2118c251b8dacc");
        // Function to get product properties
        const options = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ "username": "sajad", "password": "Hoe3yglky)Xt)eWnG5^Wchtm" })
        };
        fetch('https://tamyar.ir/wp-json/jwt-auth/v1/token', options)
        .then(response => response.json())
        .then(response => tokenImg = response.token)
         .catch(err => console.error(err));
        jQuery(document).ready(function ($) {
            // Function to call any API and return a promise
            function callApi(url, method, headers, data) {
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: url,
                        method: method,
                        timeout: 0,
                        headers: headers,
                        data: data
                    })
                    .done(function(response) {
                        resolve(response); // Resolve with the response data
                    })
                    .fail(function(error) {
                        reject(error); // Reject on error
                    });
                });
            }
            // Add the custom button at the end of the buttons section
            const customButton = $('<a href="<?php echo admin_url("admin-post.php?action=add_new_woocommerce_product"); ?>" id="tamshop-update-product-button" class="page-title-action custom-action-button">افزودن و بروزرسانی محصولات تام‌یار</a>');
            $('.wrap .page-title-action:last').after(customButton);
            
            // Handle button click
            $('.custom-action-button').on('click', function (e) {
                e.preventDefault();
                
                // Add Loading
                $('body').append('<div id="isUpdating" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.8);display:flex;justify-content:center;align-items:center;color:#fff;z-index:999999;flex-direction:column">'+
                '<img src="https://tamyar.ir/wp-content/themes/shopex-child/assets/img/wait3.gif" style="margin-bottom:7px;">'+
                '<p style="font-size:32px;">لطفا، صفحه رو مجدد بارگذاری نکنید ...</p>'+
                '</div>');
                                            
                $('#tamshop-update-product-button').text('صبور باش، داره محصولات رو بروز میکنه ...');
                
                //Get All Of Cats  in Woocommerce
                var wcGettamshopCatsIds_settings = {
                  "url": "https://tamyar.ir/wp-json/wc/v3/products/categories",
                  "method": "GET",
                  "timeout": 0,
                  "headers": {
                    "Authorization": "Basic " + token
                  },
                };
                
                $.ajax(wcGettamshopCatsIds_settings).done(function (wcGettamshopCatsIds_response) {
                    wcGettamshopCatsIds_response.forEach(function (product_cat) {
                        var key = product_cat.tamshop_product_cat_id;
                        var value = product_cat.id;
                       tamshopCatIds.push({ [key]: value });
                    });
                });
                
                //Get All Of Teachers Taxonomies  in Woocommerce
                var wcGettamshopTeachersIds_settings = {
                  "url": "https://tamyar.ir/wp-json/wp/v2/teacher",
                  "method": "GET",
                  "timeout": 0,
                  "headers": {
                    "Authorization": "Basic " + token
                  },
                  data: {
                    per_page: 50, // Set to the maximum value for the number of products per page (default is usually 10)
                    page: 1 // Start from page 1
                  }
                };
                $.ajax(wcGettamshopTeachersIds_settings).done(function (wcGettamshopTeachersIds_response) {
                    wcGettamshopTeachersIds_response.forEach(function (product_teacher) {
                        var key = product_teacher.tamshop_product_teacher_id;
                        var value = product_teacher.id;
                       tamshopTeacherIds.push({ [key]: value });
                    });
                });
                //console.log(tamshopTeacherIds);
                var wcGettamshopIds_settings = {
                    url: "https://tamyar.ir/wp-json/wc/v3/products",
                    method: "GET",
                    timeout: 0,
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "Basic " + token
                    },
                    data: {
                        per_page: 50, // Set to the maximum value for the number of products per page (default is usually 10)
                        page: 1 // Start from page 1
                    }
                };
                async function getAllProducts(page) {
                    try {
                        const wcGettamshopIds_response = await callApi(wcGettamshopIds_settings.url, wcGettamshopIds_settings.method, wcGettamshopIds_settings.headers, wcGettamshopIds_settings.data);
                         // Process the response data (for example, print it)
                        //console.log("Products from page " + page + ":", wcGettamshopIds_response);
                        
                        wcGettamshopIds_settings.data.page = page;
                        
                        wcGettamshopIds_response.forEach(function (product) {
                            var tamshopMeta = product.meta_data.find(function (meta) {
                                return meta.key === "tamshop-product-id";
                            });
                            if (tamshopMeta) {
                                tamshopIds.push(tamshopMeta.value);
                            }
                        });
                        // Check if there are more products to fetch (if the response contains 100 products, you can assume there is a next page)
                        if (wcGettamshopIds_response.length >= 50) {
                            // Recursively fetch next page
                            getAllProducts(page + 1);
                        } else {
                            var getShopProducts_settings = {
                                "url": "https://api.tamland.ir/api/shop/getShopProducts",
                                "method": "POST",
                                "timeout": 0,
                                "headers": {
                                "Content-Type": "application/json"
                                },
                                "data": JSON.stringify({
                                "Category": "-1",
                                "HomeCategory": "-1"
                                }),
                            };
                            const getShopProducts_response = await callApi(getShopProducts_settings.url, getShopProducts_settings.method, getShopProducts_settings.headers, getShopProducts_settings.data);
                            //console.log("Next API getShopProducts_response:", getShopProducts_response);
                                    // ساختن آرایه shopProductIds از پاسخ API
                                shopProductIds = getShopProducts_response.data.map(function (product) {
                                    return product['fldPkProduct'];
                                });
                                
                                shopProductsDetails = getShopProducts_response.data.map(function(product) {
                                    let D_regularPrice = product['fldAmountTamPriceAdmin']?.toString() ?? '';
                                    let D_tamcoinPercentage = parseFloat(product['fldCoinPercentage']);
                                    let D_tamcoinToPrice = Math.floor(((D_tamcoinPercentage * D_regularPrice) / 100) / 10000) * 10000;
                                    
                                    return {
                                        'fldPkProduct': product['fldPkProduct'],
                                        'fldProductTitle': product['fldProductTitle'],
                                        'fldAmountTamPriceAdmin': product['fldAmountTamPriceAdmin'],
                                        'fldProductImageUrl': product['fldProductImageUrl'],
                                        'fldFKFirstCategory': product['fldFKFirstCategory']
                                                                .split(',')
                                                                .map(cat => cat.trim())
                                                                .filter(cat => cat.length > 0),
                                        'fldPkDetailProduct': product['fldPkDetailProduct'],
                                        'fldCoinPercentage': product['fldCoinPercentage'],
                                        'tamcoinToPrice': D_tamcoinToPrice
                                    };
                                });
                                //console.log(shopProductsDetails);
                            
                            for(i = 0;i <= getShopProducts_response.data.length;i++){
                                if(i < getShopProducts_response.data.length){
                                    var product = getShopProducts_response.data[i];  // آیتم جاری از آرایه getShopProducts_response.data
                                    let tamshopId = product['fldPkProduct'];
                                    if ($.inArray(Number(getShopProducts_response.data[i]['fldPkProduct']), tamshopIds.map(Number)) == -1) {
                                        hasNewProduct = true;
                                        $('#tamshop-update-product-button').text('در حال بروزرسانی ...').addClass('disabled');
                                        addedProduct++;
                                        //console.log(addedProduct);
                                        let title = product['fldProductTitle'];
                                        let regularPrice = product['fldAmountTamPriceAdmin']?.toString() ?? '';
                                        let featureImg = product['fldProductImageUrl'];
                                        let category = product['fldFKFirstCategory']
                                                                .split(',')
                                                                .map(cat => cat.trim())
                                                                .filter(cat => cat.length > 0); // حذف مقادیر خالی
                                        let wc_categories = [];
                                        
                                        let teacher = product['fldFkSellerCo'];
                                        //console.log(teacher);
                                        let wc_teachers = [];
                                        let productDetailsId = product['fldPkDetailProduct'];
                                        let tamcoinPercentage = parseFloat(product['fldCoinPercentage']);
                                        let tamcoinToPrice = Math.floor(((tamcoinPercentage * regularPrice) / 100) / 10000) * 10000;
                                        
                                        var productDetails_settings = {
                                            "url": "https://api.tamland.ir/api/shop/GetProdDetail/"+productDetailsId,
                                            "method": "GET",
                                            "timeout": 0,
                                        };
                                        
                                        const productDetails_response = await callApi(productDetails_settings.url, productDetails_settings.method);
                                        //console.log("Next API productDetails_response:", productDetails_response);
                                        let productDescription = productDetails_response[0]?.fldProductDescription;
                                        
                                        category.forEach(cat => {
                                                let foundObject = tamshopCatIds.find(obj => obj.hasOwnProperty(cat));
                                                if (foundObject) {
                                                    let catId = parseInt(foundObject[cat]); // تبدیل قطعی به عدد
                                                    //console.log("cat:", cat, "mapped ID:", catId, "type:", typeof catId);
                                                    if (!isNaN(catId)) {
                                                        wc_categories.push({ id: catId });
                                                    }else{
                                                        wc_categories.push({ id: 0 });
                                                    }
                                                }
                                            });
                                        
                                        if (wc_categories.length === 0) {
                                            wc_categories.push({ id: 0 });
                                        } 
                                        //console.log(wc_categories);
                                        if (Array.isArray(teacher)) {
                                            teacher.forEach(teach => {
                                                let foundObject = tamshopTeacherIds.find(obj => obj.hasOwnProperty(teach));
                                                if (foundObject) {
                                                    let teachId = parseInt(foundObject[teach]);
                                                    wc_teachers.push({ id: isNaN(teachId) ? 0 : teachId });
                                                }
                                            });
                                        } else {
                                            let foundObject = tamshopTeacherIds.find(obj => obj.hasOwnProperty(teacher));
                                            if (foundObject) {
                                                let teachId = parseInt(foundObject[teacher]);
                                                wc_teachers.push({ id: isNaN(teachId) ? 0 : teachId });
                                            }
                                        }


                                        if (wc_teachers.length === 0) {
                                            wc_teachers.push({ id: 0 });
                                        } 

                                        var ProductPropertySettings = {
                                            url: "https://api.tamland.ir/api/shop/getProductProperty/" + tamshopId + "/-1",
                                            method: "GET",
                                            timeout: 0,
                                        };
                                        $("#isUpdating p").text("در حال افزودن محصول " + title);
                                        const ProductPropertyResponse = await callApi(ProductPropertySettings.url, ProductPropertySettings.method);
                                        //console.log("Next API ProductPropertyResponse:", ProductPropertyResponse);

                                        let tamshopAttr = {
                                            size: [],
                                            color: [],
                                            other: {}
                                        };
                                        
                                        // مقداردهی اولیه به صورت Set برای حذف خودکار مقادیر تکراری
                                        tamshopAttr.size  = new Set(tamshopAttr.size || []);
                                        tamshopAttr.color = new Set(tamshopAttr.color || []);
                                        tamshopAttr.other = tamshopAttr.other || {};
                                        ProductPropertyResponse.forEach(function(property) {
                                            let rawValue = property.fldPropertyValueTitle ? String(property.fldPropertyValueTitle).trim() : "";
                                            
                                            // جدا کردن مقادیر با ویرگول
                                            let values = rawValue ? rawValue.split(",").map(v => v.trim()).filter(v => v) : [];
                                        
                                            values.forEach(value => {
                                                if (property.fldFkProperty == 1) {
                                                    tamshopAttr.size.add(value);
                                                } 
                                                else if (property.fldFkProperty == 2) {
                                                    tamshopAttr.color.add(value);
                                                } 
                                                else {
                                                    if (!tamshopAttr.other[property.fldFkProperty]) {
                                                        tamshopAttr.other[property.fldFkProperty] = {
                                                            name: property.fldTitleProperty,
                                                            options: new Set()
                                                        };
                                                    }
                                                    tamshopAttr.other[property.fldFkProperty].options.add(value);
                                                }
                                            });
                                        });
                                        
                                        // در نهایت اگر نیاز داری دوباره به آرایه تبدیل کنی:
                                        tamshopAttr.size  = Array.from(tamshopAttr.size);
                                        tamshopAttr.color = Array.from(tamshopAttr.color);
                                        
                                        Object.keys(tamshopAttr.other).forEach(key => {
                                            tamshopAttr.other[key].options = Array.from(tamshopAttr.other[key].options);
                                        });
                                                                                
                                        // حالا attributes رو بسازیم
                                        let attributes = [
                                            {
                                                id: 1,
                                                name: "رنگ",
                                                position: 0,
                                                visible: false,
                                                variation: true,
                                                options: Array.isArray(tamshopAttr.color) ? tamshopAttr.color.filter(Boolean) : [],
                                            },
                                            {
                                                id: 5,
                                                name: "سایز",
                                                position: 1,
                                                visible: false,
                                                variation: true,
                                                options: Array.isArray(tamshopAttr.size) ? tamshopAttr.size.filter(Boolean) : [],
                                            }
                                        ];
                                        // بقیه ویژگی‌ها رو اضافه کن
                                        let positionCounter = 2;
                                        
                                        for (let key in tamshopAttr.other) {
                                            attributes.push({
                                                name: tamshopAttr.other[key].name,
                                                position: positionCounter++,
                                                visible: true,
                                                variation: false,
                                                options: Array.isArray(tamshopAttr.other[key].options) ? tamshopAttr.other[key].options.filter(Boolean) : []
                                            });
                                        }
                                        //console.log(tamshopAttr);
                                        if (!tamshopAttr.color.length && !tamshopAttr.size.length) {
                                            
                                                        let imageId = null;
                                                        try {
                                                            let fullImageUrl = "https://stream.tamland.ir/tamland/1402/shop/" + featureImg;
                                                            let fileName = featureImg.split('/').pop();
                                                        
                                                            const imageResponse = await fetch(fullImageUrl);
                                                            if (!imageResponse.ok) throw new Error("خطا در دریافت تصویر از تملند");
                                                        
                                                            const imageBlob = await imageResponse.blob();
                                                            const formData = new FormData();
                                                            formData.append("file", imageBlob, fileName);
                                                        
                                                            const uploadResponse = await fetch("https://tamyar.ir/wp-json/wp/v2/media", {
                                                                method: "POST",
                                                                headers: {
                                                                    "Content-Disposition": "attachment; filename=" + featureImg,
                                                                    "Authorization": "Bearer " + tokenImg
                                                                },
                                                                body: formData
                                                            });
                                                        
                                                            if (uploadResponse.ok) {
                                                                const uploadedImage = await uploadResponse.json();
                                                                imageId = uploadedImage.id;
                                                                console.log("✅ تصویر با موفقیت آپلود شد:", imageId);
                                                            } else {
                                                                console.warn("⚠️ خطا در آپلود تصویر:", await uploadResponse.text());
                                                            }
                                                        } catch (err) {
                                                            console.error("❌ خطا در فرآیند دانلود یا آپلود تصویر:", err);
                                                        }

                                                        var wc_settings = {
                                                          "url": "https://tamyar.ir/wp-json/wc/v3/products",
                                                          "method": "POST",
                                                          "timeout": 0,
                                                          "headers": {
                                                            "Content-Type": "application/json",
                                                            "Authorization": "Basic " + token
                                                          },
                                                          "data": JSON.stringify({
                                                            "name": title,
                                                            "type": "simple",
                                                            "regular_price": regularPrice,
                                                            "description": productDescription,
                                                            "categories": wc_categories,
                                                            "teacher": wc_teachers,
                                                            "images": [{ id: imageId }],
                                                            "attributes": attributes,
                                                            "meta_data": [
                                                              {
                                                                "key": "tamshop-product-id",
                                                                "value": tamshopId
                                                              },
                                                              /*{
                                                                "key": "_harikrutfiwu_url",
                                                                "value": {
                                                                    "img_url": "https://stream.tamland.ir/tamland/1402/shop/"+featureImg,
                                                                    "width": "",
                                                                    "height": ""
                                                                }
                                                              },*/
                                                              {
                                                                "key": "max_prctamcoins_required",
                                                                "value": tamcoinToPrice
                                                              }
                                                            ]
                                                          }),
                                                        };
                                                        const wc_response = await callApi(wc_settings.url, wc_settings.method, wc_settings.headers, wc_settings.data);
                                                        //console.log("Next API wc_response:", wc_response);
                                                    
                                                    }else{
                                                        let imageId = null;
                                                        try {
                                                            let fullImageUrl = "https://stream.tamland.ir/tamland/1402/shop/" + featureImg;
                                                            let fileName = featureImg.split('/').pop();
                                                        
                                                            const imageResponse = await fetch(fullImageUrl);
                                                            if (!imageResponse.ok) throw new Error("خطا در دریافت تصویر از تملند");
                                                        
                                                            const imageBlob = await imageResponse.blob();
                                                            const formData = new FormData();
                                                            formData.append("file", imageBlob, fileName);
                                                        
                                                            const uploadResponse = await fetch("https://tamyar.ir/wp-json/wp/v2/media", {
                                                                method: "POST",
                                                                headers: {
                                                                    "Content-Disposition": "attachment; filename=" + featureImg,
                                                                    "Authorization": "Bearer " + tokenImg
                                                                },
                                                                body: formData
                                                            });
                                                        
                                                            if (uploadResponse.ok) {
                                                                const uploadedImage = await uploadResponse.json();
                                                                imageId = uploadedImage.id;
                                                                console.log("✅ تصویر با موفقیت آپلود شد:", imageId);
                                                            } else {
                                                                console.warn("⚠️ خطا در آپلود تصویر:", await uploadResponse.text());
                                                            }
                                                        } catch (err) {
                                                            console.error("❌ خطا در فرآیند دانلود یا آپلود تصویر:", err);
                                                        }
                                                        // Proceed with using tamshopAttr.color
                                                        var wc_settings = {
                                                          "url": "https://tamyar.ir/wp-json/wc/v3/products",
                                                          "method": "POST",
                                                          "timeout": 0,
                                                          "headers": {
                                                            "Content-Type": "application/json",
                                                            "Authorization": "Basic " + token
                                                          },
                                                          "data": JSON.stringify({
                                                            "name": title,
                                                            "type": "variable",
                                                            "description": productDescription,
                                                            "categories": wc_categories,
                                                            "teacher": wc_teachers,
                                                            "images": [{ id: imageId }],
                                                            "attributes": attributes,
                                                            "meta_data": [
                                                              {
                                                                "key": "tamshop-product-id",
                                                                "value": tamshopId
                                                              },
                                                              /*{
                                                                "key": "_harikrutfiwu_url",
                                                                "value": {
                                                                    "img_url": "https://stream.tamland.ir/tamland/1402/shop/"+featureImg,
                                                                    "width": "",
                                                                    "height": ""
                                                                }
                                                              },*/
                                                              {
                                                                "key": "max_prctamcoins_required",
                                                                "value": tamcoinToPrice
                                                              }
                                                            ]
                                                          }),
                                                        };
                                                        const wc_response = await callApi(wc_settings.url, wc_settings.method, wc_settings.headers, wc_settings.data);
                                                        //console.log("Next API wc_response:", wc_response);
                                                        
                                                        var wc_attrSettings = {
                                                          "url": "https://tamyar.ir/wp-json/wc/v3/products/"+wc_response.id+"/variations",
                                                          "method": "POST",
                                                          "timeout": 0,
                                                          "headers": {
                                                            "Content-Type": "application/json",
                                                            "Authorization": "Basic " + token
                                                          },
                                                          "data": JSON.stringify({
                                                            "regular_price": regularPrice,
                                                            "attributes": [
                                                              {
                                                                "id": 1,
                                                                "option": ""
                                                              },
                                                              {
                                                                "id": 5,
                                                                "option": ""
                                                              }
                                                            ]
                                                          }),
                                                        };
                                                        const wc_attrResponse = await callApi(wc_attrSettings.url, wc_attrSettings.method, wc_attrSettings.headers, wc_attrSettings.data);
                                                        //console.log("Next API wc_response:", wc_attrResponse);
                                                    }
                                        
                                    }
                                }else if(i == getShopProducts_response.data.length){
                                    $('#tamshop-update-product-button').text(addedProduct + ' محصول با موفقیت ثبت شد.').removeClass('disabled').addClass('success');
                                    console.log("تمام محصولات دریافت شد.");
                                    //return;
                                    /*setTimeout(function () {
                                        window.location.reload();
                                    }, 5000);*/
                                }
                            }
                            if(hasNewProduct == false){
                                $('#tamshop-update-product-button').text('بروزرسانی لیست محصولات تامشاپ');
                                var message = '<div class="notice notice-warning is-dismissible">' +
                                              '<p>هیچی محصولی برای اضافه شدن نیست :(</p>' +
                                              '<button type="button" class="notice-dismiss"><span class="screen-reader-text">رد کردن این اخطار</span></button>'+
                                              '</div>';
                                            
                                // افزودن پیام به بالای صفحه
                                $('div.wrap').prepend(message);
                                // فعال کردن قابلیت بستن پیام
                                $('.notice.is-dismissible').on('click', '.notice-dismiss', function() {
                                    $(this).closest('.notice').fadeOut();
                                });
                            }
                            
                            async function fetchAllProducts(page = 1, products = []) {
                              const res = await fetch(`/wp-json/wc/v3/products?per_page=100&page=${page}`, {
                                credentials: 'include',
                                headers: { 'Content-Type': 'application/json', "Authorization": "Basic " + token }
                              });
                            
                              if (!res.ok) {
                                console.error('Failed to fetch products:', await res.text());
                                return [];
                              }
                            
                              const current = await res.json();
                              if (current.length === 0) return products;
                            
                              return fetchAllProducts(page + 1, products.concat(current));
                            }
                            
                            function getSelectedProductIds() {
                              const checkboxes = document.querySelectorAll('table.wp-list-table tbody th.check-column input[type="checkbox"]:checked');
                              return Array.from(checkboxes).map(cb => cb.value); // value همون product_id هست
                            }
                            
                            async function fetchSelectedProducts() {
                              const ids = getSelectedProductIds();
                            
                              if (ids.length === 0) {
                                console.log("هیچ محصولی انتخاب نشده");
                                return [];
                              }
                            
                              // چون wc/v3/products?id=1,2,3 ساپورت نمی‌کنه باید یکی یکی بگیری
                              const requests = ids.map(id => 
                                fetch(`/wp-json/wc/v3/products/${id}`, {
                                  credentials: 'include',
                                  headers: { 
                                    'Content-Type': 'application/json',
                                    "Authorization": "Basic " + token 
                                  }
                                }).then(res => res.json())
                              );
                            
                              return Promise.all(requests);
                            }

                            async function fetchCategories(catnumber, token) {
                              try {
                                const response = await fetch('/wp-json/wp/v2/product_cat?per_page=100', {
                                  credentials: 'include',
                                  headers: {
                                    'Content-Type': 'application/json',
                                    'Authorization': 'Basic ' + token
                                  }
                                });
                            
                                const categories = await response.json();
                            
                                const matchedCategory = categories.find(cat =>
                                  cat.tamshop_product_cat_id == catnumber
                                );
                            
                                if (matchedCategory) {
                                  return matchedCategory.id;
                                } else {
                                  return null; // اگر چیزی پیدا نشد
                                }
                            
                              } catch (error) {
                                console.error('خطا در دریافت دسته‌بندی‌ها:', error);
                                return null;
                              }
                            }

                            
                            async function fetchProductVariations(productId, page = 1, variations = []) {
                              const res = await fetch(`/wp-json/wc/v3/products/${productId}/variations?per_page=100&page=${page}`, {
                                credentials: 'include',
                                headers: { 'Content-Type': 'application/json', "Authorization": "Basic " + token }
                              });
                            
                              if (!res.ok) {
                                console.error(`Failed to fetch variations for product ${productId}:`, await res.text());
                                return [];
                              }
                            
                              const current = await res.json();
                              if (current.length === 0) return variations;
                            
                              return fetchProductVariations(productId, page + 1, variations.concat(current));
                            }
                            /**
                             * Old updateProductData
                             * 
                            async function updateProductData(productId, stockStatus, name, price, imageUrl, categoryId, maxCoin, isVariation, parentId) {
                                let payload = {};
                              // ساخت payload داینامیک
                              payload = {
                                stock_status: stockStatus,
                                manage_stock: false
                              };
                            
                              if (name !== null) payload.name = name;
                              if (price !== null) payload.regular_price = price.toString();
                              let metaData = [];
                              
                                if (maxCoin !== null && maxCoin !== undefined) {
                                    metaData.push({
                                        key: "max_prctamcoins_required",
                                        value: maxCoin.toString()
                                    });
                                }
                              if (imageUrl !== null) {
                                metaData.push({
                                   "key": "_harikrutfiwu_url",
                                    "value": {
                                        "img_url": "https://stream.tamland.ir/tamland/1402/shop/"+imageUrl,
                                        "width": "",
                                        "height": ""
                                    }
                                  });
                              }
                              
                              if (metaData.length > 0) {
                                payload.meta_data = metaData;
                              }
    
                              if (categoryId !== null) {
                                payload.categories = categoryId;
                              }
                              
                            const url = isVariation
                                ? `/wp-json/wc/v3/products/${parentId}/variations/${productId}`
                                : `/wp-json/wc/v3/products/${productId}`;
                                
                              try {
                                   //console.log(JSON.stringify(payload));
                                const res = await fetch(url, {
                                  method: 'PUT',
                                  credentials: 'include',
                                  headers: {
                                    'Content-Type': 'application/json',
                                    'Authorization': 'Basic ' + token
                                  },
                                  body: JSON.stringify(payload)
                                });
                            
                                if (!res.ok) {
                                  const error = await res.text();
                                  console.error(`❌ Failed to update product ${productId}:`, error);
                                } else {
                                  console.log(`✅ Product ${productId} updated successfully`);
                                  
                                }
                              } catch (err) {
                                console.error(`❌ Error updating product ${productId}:`, err);
                              }
                            }*/


                            /*async function updateStockStatus(productId, status, isVariation = false, parentId = null) {
                              const url = isVariation
                                ? `/wp-json/wc/v3/products/${parentId}/variations/${productId}`
                                : `/wp-json/wc/v3/products/${productId}`;
                            
                              const payload = {
                                stock_status: status,
                                manage_stock: false // خیلی مهم برای ورییشن‌ها
                              };
                            
                              const res = await fetch(url, {
                                method: 'PUT',
                                credentials: 'include',
                                headers: {
                                  'Content-Type': 'application/json',
                                  "Authorization": "Basic " + token
                                },
                                body: JSON.stringify(payload)
                              });
                            
                              if (!res.ok) {
                                const error = await res.text();
                                console.error(`❌ Failed to update stock for ${productId}`, error);
                              }
                            }*/
                            async function updateProductData(productId, stockStatus, name, price, imageUrl, categoryId, maxCoin, isVariation, parentId, attributes) {
                                let payload = {
                                    stock_status: stockStatus,
                                    manage_stock: false
                                };
                            
                                // عنوان
                                if (name !== null) payload.name = name;
                            
                                // قیمت
                                if (price !== null) payload.regular_price = price.toString();
                            
                                // متا دیتا
                                let metaData = [];
                                if (maxCoin !== null && maxCoin !== undefined) {
                                    metaData.push({
                                        key: "max_prctamcoins_required",
                                        value: maxCoin.toString()
                                    });
                                }
                                /*if (imageUrl !== null) {
                                    metaData.push({
                                        "key": "_harikrutfiwu_url",
                                        "value": {
                                            "img_url": "https://stream.tamland.ir/tamland/1402/shop/" + imageUrl,
                                            "width": "",
                                            "height": ""
                                        }
                                    });
                                }*/
                                
                                if(imageUrl !== null){
                                    let imageId = null;
                                                        try {
                                                            let fullImageUrl = "https://stream.tamland.ir/tamland/1402/shop/" + imageUrl;
                                                            let fileName = imageUrl.split('/').pop();
                                                        
                                                            const imageResponse = await fetch(fullImageUrl);
                                                            if (!imageResponse.ok) throw new Error("خطا در دریافت تصویر از تملند");
                                                        
                                                            const imageBlob = await imageResponse.blob();
                                                            const formData = new FormData();
                                                            formData.append("file", imageBlob, fileName);
                                                        
                                                            const uploadResponse = await fetch("https://tamyar.ir/wp-json/wp/v2/media", {
                                                                method: "POST",
                                                                headers: {
                                                                    "Content-Disposition": "attachment; filename=" + imageUrl,
                                                                    "Authorization": "Bearer " + tokenImg
                                                                },
                                                                body: formData
                                                            });
                                                        
                                                            if (uploadResponse.ok) {
                                                                const uploadedImage = await uploadResponse.json();
                                                                imageId = uploadedImage.id;
                                                                console.log("✅ تصویر با موفقیت آپلود شد:", imageId);
                                                            } else {
                                                                console.warn("⚠️ خطا در آپلود تصویر:", await uploadResponse.text());
                                                            }
                                                        } catch (err) {
                                                            console.error("❌ خطا در فرآیند دانلود یا آپلود تصویر:", err);
                                                        }
                                                        payload.images = [{ id: imageId }];
                                }
                                
                                if (metaData.length > 0) {
                                    payload.meta_data = metaData;
                                }
                            
                                // دسته‌بندی فقط برای محصول ساده
                                if (!isVariation && categoryId !== null) {
                                    payload.categories = categoryId;
                                }
                            
                                // ویژگی‌ها
                                if (attributes && attributes.length > 0) {
                                    payload.attributes = attributes;
                                }
                                
                                
                                
                                if(isVariation){
                                    // آدرس API
                                    const parent_url = `/wp-json/wc/v3/products/${parentId}`;
                                
                                    try {
                                        const parent_res = await fetch(parent_url, {
                                            method: 'PUT',
                                            credentials: 'include',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'Authorization': 'Basic ' + token
                                            },
                                            body: JSON.stringify(payload)
                                        });
                                
                                        if (!parent_res.ok) {
                                            const error = await parent_res.text();
                                            console.error(`❌ Failed to update product ${parentId}:`, error);
                                        } else {
                                            console.log(`✅ Product ${parentId} updated successfully`);
                                        }
                                    } catch (err) {
                                        console.error(`❌ Error updating product ${parentId}:`, err);
                                    }
                                
                                    const url = `/wp-json/wc/v3/products/${parentId}/variations/${productId}`;
                            
                                    try {
                                        const res = await fetch(url, {
                                            method: 'PUT',
                                            credentials: 'include',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'Authorization': 'Basic ' + token
                                            },
                                            body: JSON.stringify(payload)
                                        });
                                
                                        if (!res.ok) {
                                            const error = await res.text();
                                            console.error(`❌ Failed to update product ${productId}:`, error);
                                        } else {
                                            console.log(`✅ Product ${productId} updated successfully`);
                                        }
                                    } catch (err) {
                                        console.error(`❌ Error updating product ${productId}:`, err);
                                    }
                                }else{
                                    // آدرس API
                                    const url = `/wp-json/wc/v3/products/${productId}`;
                                
                                    try {
                                        const res = await fetch(url, {
                                            method: 'PUT',
                                            credentials: 'include',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'Authorization': 'Basic ' + token
                                            },
                                            body: JSON.stringify(payload)
                                        });
                                
                                        if (!res.ok) {
                                            const error = await res.text();
                                            console.error(`❌ Failed to update product ${productId}:`, error);
                                        } else {
                                            console.log(`✅ Product ${productId} updated successfully`);
                                        }
                                    } catch (err) {
                                        console.error(`❌ Error updating product ${productId}:`, err);
                                    }
                                }
                                
                            }
                            
                            /**
                             * Old processAndUpdateProduct
                             * 
                             * 
                            async function processAndUpdateProduct(item, parentId = null, isVariation = false) {
                                const tamMeta = item.meta_data?.find(meta => meta.key === 'tamshop-product-id');
                                if (!tamMeta) return;
                            
                                const tamId = parseInt(tamMeta.value);
                                let stock = null, newtitle = null, newprice = null, newimg = null, newcoin = null;
                                let needsUpdate = false;
                            
                                // بررسی موجودی
                                if (!shopProductIds.includes(tamId)) {
                                    console.log(`❌ NOT IN API → Marking out of stock:`, tamId);
                                    stock = 'outofstock';
                                    needsUpdate = true;
                                } else {
                                    console.log(`✅ FOUND IN API → Marking in stock:`, tamId);
                                    stock = 'instock';
                                    needsUpdate = true;
                                }
                            
                                // پیدا کردن محصول در دیتای API
                                let matchedProduct = shopProductsDetails.find(p => p.fldPkProduct === tamId);
                            
                                if (matchedProduct) {
                                    // بررسی عنوان
                                    if (item.name !== matchedProduct.fldProductTitle) {
                                        newtitle = matchedProduct.fldProductTitle;
                                        needsUpdate = true;
                                    }
                            
                                    // بررسی قیمت
                                    if (item.regular_price !== matchedProduct.fldAmountTamPriceAdmin) {
                                        newprice = matchedProduct.fldAmountTamPriceAdmin;
                                        needsUpdate = true;
                                    }
                            
                                    // بررسی تصویر
                                    let imgMeta = item.meta_data?.find(meta => meta.key === '_harikrutfiwu_url');
                                    let expectedImg = "https://stream.tamland.ir/tamland/1402/shop/" + matchedProduct.fldProductImageUrl;
                            
                                    if (!imgMeta || imgMeta.value.img_url !== expectedImg) {
                                        newimg = matchedProduct.fldProductImageUrl;
                                        needsUpdate = true;
                                    }
                            
                                    // بررسی کوین
                                    let coinMeta = item.meta_data?.find(meta => meta.key === 'max_prctamcoins_required');
                                    if (!coinMeta || coinMeta.value !== matchedProduct.tamcoinToPrice) {
                                        newcoin = matchedProduct.tamcoinToPrice;
                                        needsUpdate = true;
                                    }
                                }
                            
                                // فقط برای محصول ساده دسته‌بندی لازم هست
                                let wc_categories = null;
                                if (!isVariation && matchedProduct) {
                                    wc_categories = [];
                                    matchedProduct.fldFKFirstCategory.forEach(cat => {
                                        let foundObject = tamshopCatIds.find(obj => obj.hasOwnProperty(cat));
                                        if (foundObject) {
                                            let catId = parseInt(foundObject[cat]);
                                            wc_categories.push({ id: !isNaN(catId) ? catId : 0 });
                                        }
                                    });
                                }
                            
                                // آپدیت محصول در ووکامرس
                                if (needsUpdate) {
                                    await updateProductData(
                                        item.id,
                                        stock,
                                        newtitle,
                                        newprice,
                                        newimg,
                                        wc_categories,
                                        newcoin,
                                        isVariation,
                                        parentId
                                    );
                                    updatedCount++;
                                }
                            }
                            */
                            
                            async function processAndUpdateProduct(item, parentId = null, isVariation = false) {
                                    const tamMeta = item.meta_data?.find(meta => meta.key === 'tamshop-product-id');
                                    if (!tamMeta) return;
                                    let productId;
                                    const tamId = parseInt(tamMeta.value);
                                    let stock = null, newtitle = null, newprice = null, newimg = null, newcoin = null, newAttributes = null, variationId = null;
                                    let needsUpdate = false;
                                
                                    // بررسی موجودی
                                    if (!shopProductIds.includes(tamId)) {
                                        console.log(`❌ NOT IN API → Marking out of stock:`, tamId);
                                        stock = 'outofstock';
                                        needsUpdate = true;
                                    } else {
                                        console.log(`✅ FOUND IN API → Marking in stock:`, tamId);
                                        stock = 'instock';
                                        needsUpdate = true;
                                    }
                                
                                    // پیدا کردن محصول در دیتای API
                                    let matchedProduct = shopProductsDetails.find(p => p.fldPkProduct === tamId);
                                
                                    if (matchedProduct) {
                                        // بررسی عنوان
                                        if (item.name !== matchedProduct.fldProductTitle) {
                                            newtitle = matchedProduct.fldProductTitle;
                                            needsUpdate = true;
                                        }
                                
                                        
                                        if(isVariation){
                                            const variations = await fetchProductVariations(parentId);
                                            for (const variation of variations) {
                                                variationId = variation.id;
                                                if (variation.regular_price !== matchedProduct.fldAmountTamPriceAdmin) {
                                                    newprice = matchedProduct.fldAmountTamPriceAdmin;
                                                    needsUpdate = true;
                                                }
                                            }
                                        }else{
                                            // بررسی قیمت
                                            if (item.regular_price !== matchedProduct.fldAmountTamPriceAdmin) {
                                                newprice = matchedProduct.fldAmountTamPriceAdmin;
                                                needsUpdate = true;
                                            }
                                        }
                                
                                        // بررسی تصویر
                                        
                                        //let imgMeta = item.meta_data?.find(meta => meta.key === '_harikrutfiwu_url');
                                        if(item.images.length == 0){
                                            newimg = matchedProduct.fldProductImageUrl;
                                            console.log('newimg updated: '+newimg);
                                            needsUpdate = true;
                                        }else{
                                            let featuredImageName = item.images[0].src.split('/').pop();
                                            if (!featuredImageName || featuredImageName !== matchedProduct.fldProductImageUrl) {
                                                newimg = matchedProduct.fldProductImageUrl;
                                                console.log('newimg updated: '+newimg);
                                                needsUpdate = true;
                                            }
                                        }
                                        //let expectedImg = "https://stream.tamland.ir/tamland/1402/shop/" + matchedProduct.fldProductImageUrl;
                                        
                                        // بررسی کوین
                                        let coinMeta = item.meta_data?.find(meta => meta.key === 'max_prctamcoins_required');
                                        if (!coinMeta || coinMeta.value !== matchedProduct.tamcoinToPrice) {
                                            newcoin = matchedProduct.tamcoinToPrice;
                                            needsUpdate = true;
                                        }
                                // بررسی ویژگی‌ها (attributes)
                                        if (matchedProduct.fldAttributes && matchedProduct.fldAttributes.length > 0) {
                                            let wcAttributes = matchedProduct.fldAttributes.map(attr => ({
                                                id: parseInt(attr.id),
                                                name: attr.name,
                                                option: attr.value
                                            }));
                                
                                            // مقایسه ویژگی‌ها با محصول فعلی
                                            let currentAttrs = item.attributes || [];
                                            let diff = JSON.stringify(currentAttrs) !== JSON.stringify(wcAttributes);
                                            if (diff) {
                                                newAttributes = wcAttributes;
                                                needsUpdate = true;
                                            }
                                        }
                                    }
                                
                                    // دسته‌بندی فقط برای محصول ساده
                                    let wc_categories = null;
                                    if (!isVariation && matchedProduct) {
                                        wc_categories = [];
                                        matchedProduct.fldFKFirstCategory.forEach(cat => {
                                            let foundObject = tamshopCatIds.find(obj => obj.hasOwnProperty(cat));
                                            if (foundObject) {
                                                let catId = parseInt(foundObject[cat]);
                                                wc_categories.push({ id: !isNaN(catId) ? catId : 0 });
                                            }
                                        });
                                    }
                                    
                                    if(isVariation){
                                        productId = variationId;
                                    }else{
                                        productId = item.id;
                                    }
                                    
                                    // اگر نیاز به آپدیت بود
                                    if (needsUpdate) {
                                        await updateProductData(
                                            productId,
                                            stock,
                                            null, // عنوان
                                            newprice,
                                            newimg,
                                            wc_categories,
                                            newcoin,
                                            isVariation,
                                            parentId,
                                            newAttributes
                                        );
                                    }
                            }

                            async function syncInventory() {
                              //const allProducts = await fetchAllProducts();
                              const selectedProducts = await fetchSelectedProducts();
                                //console.log(selectedProducts);
                              let updatedCount = 0;
                              for (const product of selectedProducts) {
                                  let stock = null;
                                let newtitle = null;
                                let newprice = null;
                                let newcoin = null;
                                let newimg = null;
                                let productCat = null;
                                let needsUpdate = false;
                                  $("#isUpdating p").text("در حال بروزرسانی" + product.id + "- " + product.name);
                                if (product.type === "variable") {
                                    await processAndUpdateProduct(product, product.id, true);
                                    // ورییشن‌ها رو بگیر
                                } else {
                                    // محصول ساده
                                    await processAndUpdateProduct(product, null, false);
                                }

                              }
                            
                              alert(`همگام‌سازی موجودی انجام شد. ${updatedCount} مورد به خارج از موجودی تغییر یافت.`);
                              /*setTimeout(function () {
                                        window.location.reload();
                                    }, 5000);*/
                            }
                            
                            syncInventory();

                        }
                            
                    }
                    catch (error) {
                        console.error("Error in API call:", error);
                    }
                }
                
        
                // Start fetching products from page 1
                getAllProducts(1);


                
            });
        });
    </script>
    <style>
        .custom-action-button {
            background-color: #ff4748 !important;
            border-color:#ff4748 !important;
            color: #fff !important;
            text-decoration: none;
            margin-left: 10px;
        }
        .custom-action-button:hover {
            background-color: #c4161c !important;
            border-color:#c4161c !important;
        }
        .custom-action-button.success {
            background-color: #00e6a4 !important;
            border-color:#00e6a4 !important;
        }
        .custom-action-button.disabled {
            background-color: #666 !important;
            border-color:#666 !important;
            cursor:default;
        }
    </style>
    <?php
}


add_action( 'woocommerce_before_account_orders', 'wp_kama_woocommerce_before_account_orders_action' );

/**
 * Function for `woocommerce_before_account_orders` action-hook.
 * 
 * @param  $has_orders 
 *
 * @return void
 */
function wp_kama_woocommerce_before_account_orders_action( $has_orders ){
$token = $_COOKIE['tamshToken'];
$user_id = get_current_user_id();
$first_name = get_user_meta( $user_id, 'first_name', true ); // نام
$last_name = get_user_meta( $user_id, 'last_name', true ); // نام خانوادگی
$user_info = get_userdata( $user_id );
$username = $user_info->user_login; 

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.tamland.ir/api/shop/getOrders',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer '.$token
  ),
));

$response = curl_exec($curl);

curl_close($curl);

$response_json = json_decode($response, true);
if (!empty($response_json['data']) && is_array($response_json['data'])) {
    
    for($i = 0; $i < count($response_json['data']); $i++){
        $lms_orders = $response_json['data'][$i];
        
        $fldFkUserCo = $lms_orders['fldFkUserCo'];
        $fldCreateDateTime = $lms_orders['fldCreateDateTime'];
        $fldPkOrderCo = $lms_orders['fldPkOrderCo'];
        $fldFinalTamCoin = $lms_orders['fldFinalTamCoin'];
        $fldDiscountTam = $lms_orders['fldDiscountTam'];
        $fldFkOrderStatus = $lms_orders['fldFkOrderStatus'];
        $fldTitleStatus = $lms_orders['fldTitleStatus'];
        $fldPostOrderTrackingId = $lms_orders['fldPostOrderTrackingId'];
        $fldFinalPrice = $lms_orders['fldFinalPrice'];
        $fldOrderPaymentStatus = $lms_orders['fldOrderPaymentStatus'];
        $event = $lms_orders['event'];
        $fldUserOrderTag = $lms_orders['fldUserOrderTag'];
        
        
       // print_r($fldTitleStatus);
    switch ($fldOrderPaymentStatus) {
        case 1:
            $fldTitleStatus_wc = 'Processing';
            break;
            
        default:
            // Code to execute if $variable does not match any case
            break;
    }
     // Check if order with lms_order_id already exists
            $existing_order = wc_get_orders([
                'meta_key' => 'lms_order_id',
                'meta_value' => $fldPkOrderCo,
                'limit' => 1,
            ]);
    //print_r($response_json);
    
    /*if ( ! get_userdata( $user_id ) ) {
            return 'کاربر مورد نظر وجود ندارد!';
        }*/
    if (empty($existing_order)) {
         // ایجاد یک شیء سفارش جدید
        $order = wc_create_order();
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.tamland.ir/api/shop/getOrderDetails/'.$fldPkOrderCo,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$token
          ),
        ));
        
        $response_orderDetails = curl_exec($curl);
        
        curl_close($curl);
        $response_orderDetails_json = json_decode($response_orderDetails, true);
        //print_r($response_orderDetails_json);
        $products = [];
        $fldAddress = "";
        $fldPostalCode = "";
        $fldState = "";
        $fldCity = "";
        $fldDescription = "";
        for($j = 0; $j < count($response_orderDetails_json['data']); $j++){
            $order_detail = $response_orderDetails_json['data'][$j];
            
            $fldPkProduct = $order_detail['fldPkProduct'];
            $fldCountProduct = $order_detail['fldCountProduct'];
            $fldAddress = $order_detail['fldAddress'];
            $fldPostalCode = $order_detail['fldPostalCode'];
            $fldState = $order_detail['fldState'];
            $fldCity = $order_detail['fldCity'];
            $fldDescription = $order_detail['fldDescription'];
            $fldOrderTrackingId = $order_detail['fldOrderTrackingId'];
            $fldPostCompanyType = $order_detail['fldPostCompanyType'];
            
            $args = array(
                'post_type'  => 'product', // نوع پست (محصولات)
                'meta_key'   => 'tamshop-product-id', // نام فیلد سفارشی
                'meta_value' => $fldPkProduct, // مقداری که به دنبال آن هستید
                'posts_per_page' => 1 // فقط یک محصول برگردانده شود
            );
            $query = new WP_Query( $args );
            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();
                    $product_id = get_the_ID(); // گرفتن ID محصول
                }
            }
            
            //print_r($product_id);
                
            $products[] = ['id' => $product_id, 'quantity' => $fldCountProduct];
            
        }
        // حلقه برای اضافه کردن هر محصول به سفارش
        foreach ($products as $product) {
            // افزودن محصول به سفارش
            $order->add_product(wc_get_product($product['id']), $product['quantity']);
        }
       
    
        // تنظیم اطلاعات مشتری
        $order->set_address( array(
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'company'    => '',
            'email'      => $username.'@tamland.ir',
            'phone'      => '',
            'address_1'  => $fldAddress,
            'address_2'  => '',
            'city'       => $fldCity,
            'state'      => $fldState,
            'postcode'   => $fldPostalCode,
            'country'    => 'IR',
        ), 'billing' );
        
        $order->set_address( array(
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'company'    => '',
            'email'      => $username.'@tamland.ir',
            'phone'      => '',
            'address_1'  => $fldAddress,
            'address_2'  => '',
            'city'       => $fldCity,
            'state'      => $fldState,
            'postcode'   => $fldPostalCode,
            'country'    => 'IR',
        ), 'shipping' );
        
        $order->set_total( $fldFinalPrice );
        
        $order->set_date_created( $fldCreateDateTime ); // تنظیم تاریخ ایجاد
        $order->set_date_completed( $fldCreateDateTime ); // تنظیم تاریخ تکمیل
        $order->set_customer_note($fldDescription);
        // اختصاص سفارش به کاربر خاص
        $order->set_customer_id( $user_id );
    
        // افزودن یادداشت به سفارش (اختیاری)
        $order->add_order_note( 'این سفارش برای کاربر با آی‌دی ' . $user_id . ' ایجاد شده است.' );
    
        // تنظیم وضعیت سفارش
        $order->update_status( $fldTitleStatus_wc );
        $order->update_meta_data('lms_order_id', $fldPkOrderCo);
        $order->update_meta_data('lms_tracking_code',$fldOrderTrackingId);
        switch($fldPostCompanyType){
            case 1:
                $order->update_meta_data('lms_tracking_link','https://tracking.post.ir/search.aspx?id='.$fldPostCompanyType);
            case 2:
                $order->update_meta_data('lms_tracking_link','https://tipaxco.com/tracking?id='.$fldPostCompanyType);
            case 3:
                $order->update_meta_data('lms_tracking_link','https://public.boxit.ir/tracking?code='.$fldPostCompanyType);
            case 4:
                $order->update_meta_data('lms_tracking_link','https://chaparnet.com/track/');
        }
        // ذخیره سفارش
        $order_id = $order->save();
    
        // بازگشت آی‌دی سفارش ایجاد شده
        //return $order_id;
    }
        
    }
    
}

}
/*
// Add a new column to the Orders Table in My Account
add_filter('woocommerce_my_account_my_orders_columns', 'add_lms_order_id_column');
function add_lms_order_id_column($columns) {
    $columns['lms_order_id'] = __('شناسه سفارش در تام‌یار', 'textdomain');
    $columns['lms_tracking_code'] = __('کد پیگیری', 'textdomain');
    $columns['lms_tracking_link'] = __('لینک پیگیری', 'textdomain');
    return $columns;
}


// Populate the LMS Order ID column
add_action('woocommerce_my_account_my_orders_column_lms_order_id', 'populate_lms_order_id_column');
function populate_lms_order_id_column($order) {
    $lms_order_id = $order->get_meta('lms_order_id');
    echo $lms_order_id ? esc_html($lms_order_id) : __('ثبت نشده است', 'textdomain');
}

// Populate the LMS Tracking Code column
add_action('woocommerce_my_account_my_orders_column_lms_tracking_code', 'populate_lms_tracking_code_column');
function populate_lms_tracking_code_column($order) {
    $lms_tracking_code = $order->get_meta('lms_tracking_code');
    echo $lms_tracking_code ? esc_html($lms_tracking_code) : __('ثبت نشده است', 'textdomain');
}

// Populate the LMS Tracking Link column
add_action('woocommerce_my_account_my_orders_column_lms_tracking_link', 'populate_lms_tracking_link_column');
function populate_lms_tracking_link_column($order) {
    $lms_tracking_link = $order->get_meta('lms_tracking_link');
    echo $lms_tracking_link ? esc_html('<a href="'.$lms_tracking_link.'">'.__('مشاهده وضعیت پستی', 'textdomain').'</a>') : __('ثبت نشده است', 'textdomain');
}
*/
add_filter('woocommerce_rest_pre_insert_product_object', function ($product, $request) {
    $images = $request->get_param('images');

    if (!empty($images)) {
        foreach ($images as &$image) {
            if (isset($image['src'])) {
                $image_url = $image['src'];
                $image_id = media_sideload_image($image_url, 0, null, 'id');
                if (!is_wp_error($image_id)) {
                    $image['id'] = $image_id;
                    unset($image['src']);
                }
            }
        }
        $request->set_param('images', $images);
    }

    return $product;
}, 10, 2);

function add_custom_meta_to_category_api() {
    register_rest_field(
        'product_cat', // The taxonomy slug.
        'tamshop_product_cat_id', // The key that will appear in the API response.
        array(
            'get_callback' => function ( $object ) {
                // Fetch the term ID.
                $term_id = $object['id'];
                // Get the custom meta value.
                $meta_value = get_term_meta( $term_id, 'tamshop-product-cat-id', true ); // Replace 'your_meta_key' with the actual key.
                return $meta_value;
            },
            'schema' => array(
                'description' => 'Custom meta field description',
                'type'        => 'string', // Adjust type as needed (e.g., string, array, etc.).
                'context'     => array( 'view', 'edit' ),
            ),
        )
    );
    
    register_rest_field(
        'product', // The product.
        'max_prctamcoins_required', // The key that will appear in the API response.
        array(
            'get_callback' => function ( $object ) {
                // Fetch the term ID.
                $p_id = $object['id'];
                // Get the custom meta value.
                $meta_value = get_term_meta( $p_id, 'max_prctamcoins_required', true ); // Replace 'your_meta_key' with the actual key.
                return $meta_value;
            },
            'schema' => array(
                'description' => 'Custom meta field description',
                'type'        => 'string', // Adjust type as needed (e.g., string, array, etc.).
                'context'     => array( 'view', 'edit' ),
            ),
        )
    );
    
    register_rest_field(
        'teacher', // The taxonomy slug.
        'tamshop_product_teacher_id', // The key that will appear in the API response.
        array(
            'get_callback' => function ( $object ) {
                // Fetch the term ID.
                $term_id = $object['id'];
                // Get the custom meta value.
                $meta_value = get_term_meta( $term_id, 'teacher-lms-id', true ); // Replace 'your_meta_key' with the actual key.
                return $meta_value;
            },
            'schema' => array(
                'description' => 'Custom meta field description',
                'type'        => 'string', // Adjust type as needed (e.g., string, array, etc.).
                'context'     => array( 'view', 'edit' ),
            ),
        )
    );
}
add_action( 'rest_api_init', 'add_custom_meta_to_category_api' );

add_action('init', 'register_custom_order_status');
function register_custom_order_status() {
    register_post_status('wc-seller-on-hold', array(
        'label'                     => _x('در حال بررسی توسط فروشنده', 'Order status', 'textdomain'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('حال بررسی توسط فروشنده (%s)','حال بررسی توسط فروشنده (%s)', 'textdomain'),
    ));
}

add_filter('wc_order_statuses', 'add_custom_status_to_order_statuses');
function add_custom_status_to_order_statuses($order_statuses) {
    $order_statuses['wc-seller-on-hold'] = _x(' حال بررسی توسط فروشنده', 'Order status', 'textdomain');
    return $order_statuses;
}


add_filter('bulk_actions-edit-shop_order', 'add_custom_status_to_bulk_actions');
function add_custom_status_to_bulk_actions($bulk_actions) {
    $bulk_actions['mark_custom-status'] = __('تغییر وضعیت به  حال بررسی توسط فروشنده', 'textdomain');
    return $bulk_actions;
}

add_action( 'admin_head', 'theme_admin_css' );
function theme_admin_css() {
echo '
<style>
.mo_api_side_bar{
    left:16px !important;
}
</style>
'; 
}


add_action('woocommerce_payment_complete', 'after_payment_gateway', 10, 1);
function after_payment_gateway($order_id) {
    // Ensure $order is a WC_Order object
    $order = wc_get_order($order_id);
    error_log($order);
    
    if ( $order ) {
        $user_id = $order->get_customer_id();
        // Or, equivalently, $customer_id = $order->get_user_id();
    } else {
        // Handle case where order is not found
        $user_id = 0; // Or other appropriate handling
    }
    $fldUserCo = get_user_meta($user_id, 'fldUserCo', true);
    error_log('userco is: '.$fldUserCo);
    error_log('user type: '.gettype($fldUserCo));
    
    // Initialize an array to store product details
    $products = [];

    // Retrieve order items
    $items = $order->get_items();
    foreach ($items as $item_id => $item) {
        // Get product ID and quantity
        $product = $item->get_product();
        $product_id = (int) $item->get_product_id();
        $tamshop_product_id = get_post_meta($product_id, 'tamshop-product-id', true);
        $product_count = (int) $item->get_quantity();

        // Get product variations (attributes)
        $product_variations = [];
        if ( $product && $product->is_type( 'variation' ) ) {
            $product_variations = $product->get_variation_attributes();
            // ادامه‌ی کد شما با استفاده از $variation_attributes
        }

        $product_properties = implode(', ', $product_variations); // Convert variations to a string

        // Add product details to the $products array
        $products[] = [
            'ProductId' => $tamshop_product_id,
            'PackageId' => 0,
            'ProductCount' => $product_count,
            'ProductProperties' => $product_properties
        ];
    }
    if(!empty($order->get_meta('_tamcoin_discount'))){
        $tamcoin_discount = (int) $order->get_meta('_tamcoin_discount');
    }else{
        $tamcoin_discount = 0;
    }
    
    if(!empty($order->get_transaction_id())){
        $transaction_id = $order->get_transaction_id();
    }else{
        $transaction_id = "";
    }
    
    $price = (int)$order->get_total();
    
    $billing_address = $order->get_billing_address_1() . ' ' . $order->get_billing_address_2();
    $shipping_address = $order->get_shipping_address_1() . ' ' . $order->get_shipping_address_2();

    // مقایسه بین آدرس صورتحساب و حمل و نقل
    if ($shipping_address && $shipping_address !== $billing_address) {
        error_log('کاربر تیک ارسال به آدرس حمل‌ونقل را زده است.');
        $city = $order->get_shipping_city();
        $postal_code = (string)$order->get_shipping_postcode();
        $address = $order->get_shipping_address_1() . ' ' . $order->get_shipping_address_2();
    } else {
        error_log('کاربر از همان آدرس صورتحساب استفاده کرده است.');
        $city = $order->get_billing_city();
        $postal_code = (string)$order->get_billing_postcode();
        $address = $order->get_billing_address_1() . ' ' . $order->get_billing_address_2();
    }
    
    $p_d_td = $price - $tamcoin_discount;
    error_log('Difrent is: '.$p_d_td);
    error_log('Order Status is: '.$order->get_status());
    if ($order->get_status() == 'processing' || $order->get_status() == 'completed') {
        // کدهایی که بعد از پرداخت موفق باید اجرا شوند
        if( $p_d_td < 0 ){
            $paymentStatus = 0;
        }elseif ($p_d_td == $price){
            $paymentStatus = 1;
        }elseif (($p_d_td > 0) && ($p_d_td < $price)){
            $paymentStatus = 2;
        }
        
        //error_log("کاربر به صفحه تشکر از خرید بازگشت: سفارش شماره " . $order_id);
    }
    
    // Example: Build the final payload
    error_log('Tamcoin Discount: '.$tamcoin_discount);
    $payload = [
        'UserCo' => $fldUserCo,
        'Price' => $price,
        'OrderId' => $order_id,
        'Tamcoin' => $tamcoin_discount,
        'TrackingCode' => $transaction_id, // Add tracking code if available
        'Transport' => 1, // Replace with actual transport code
        'PaymentStatus' => $paymentStatus, // Replace with actual payment status
        'City' => $city,
        'PostalCode' => $postal_code,
        'Address' => $address,
        'Description' => $order->get_customer_note(),
        'Products' => $products, // Add the products array here
    ];

    // Example: Log the payload (for debugging)
    error_log('Payload is: '.print_r($payload, true));

    // Now you can use $payload to send to your API
    send_to_api($payload);
}

function send_to_api($payload) {
    // Encode the payload as JSON
    $jsonPayload = json_encode($payload);

    // Initialize cURL
    $ch = curl_init('https://api.tamland.ir/api/payment/saveShopPayment');

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonPayload)
    ]);

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        error_log('cURL error: ' . curl_error($ch));
    } else {
        // Decode the JSON response
        $responseData = json_decode($response, true);
        error_log('API Response: ' . print_r($responseData, true));
    }

    // Close the cURL session
    curl_close($ch);
}

function custom_overlay_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const triggers = document.querySelectorAll('.elementor-element-828ca35 .menu-item a, .mega-menu');
        const overlay = document.querySelector('.menu-hover-overlay');

        function showOverlay() {
            overlay.style.opacity = '1';
            overlay.style.visibility = 'visible';
            overlay.style.pointerEvents = 'auto';
        }

        function hideOverlay() {
            overlay.style.opacity = '0';
            overlay.style.visibility = 'hidden';
            overlay.style.pointerEvents = 'none';
        }

        let isHovering = false;

        triggers.forEach(trigger => {
            trigger.addEventListener('mouseenter', () => {
                isHovering = true;
                showOverlay();
            });

            trigger.addEventListener('mouseleave', () => {
                isHovering = false;
                setTimeout(() => {
                    // Check if any other trigger is still hovered
                    if (![...triggers].some(el => el.matches(':hover'))) {
                        hideOverlay();
                    }
                }, 50); // small delay prevents flicker
            });
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'custom_overlay_script');

function show_wc_stock_status_shortcode() {
    if (!is_product()) {
        return '';
    }

    global $product;
    if (!$product || !is_a($product, 'WC_Product')) {

        $product_id = get_the_ID();
        $product = wc_get_product($product_id);
        if (!$product) {
            return '';
        }
    }


    $in_stock_style = 'background: #e6f9ee; color: #108043; border-radius: 8px; padding: 4px 20px; display: inline-block; font-weight: 600; font-size: 14px; border: 1px solid #a7e2c7; margin: 10px 0;';
    $out_stock_style = 'background: #ffe6e6; color: #cc0000; border-radius: 8px; padding: 4px 20px; display: inline-block; font-weight: 600; font-size: 14px; border: 1px solid #ffcccc; margin: 10px 0;';

    if ($product->is_in_stock()) {
        $status_text = 'موجود';
        $style = $in_stock_style;
    } else {
        $status_text = 'ناموجود';
        $style = $out_stock_style;
    }

    return '<div style="' . $style . '">' . esc_html($status_text) . '</div>';
}
add_shortcode('wc_stock_status', 'show_wc_stock_status_shortcode');

add_action('wp_logout','force_logout_redirect', 9999);

function force_logout_redirect() {

    if ( is_user_logged_in() ) {
        wp_destroy_current_session();
        wp_clear_auth_cookie();
    }

    wp_redirect(home_url());
    exit();
}


function get_tamyar_product_id_from_cart_products() {
    $custom_ids = array();

    foreach (WC()->cart->get_cart() as $cart_item) {
        // اگر محصول متغیر بود، اولویت با variation_id
        $product_id = $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];

        // گرفتن مقدار فیلد سفارشی
        $custom_id = get_post_meta($product_id, 'tamshop-product-id', true);

        if (!empty($custom_id)) {
            $custom_ids[] = $custom_id;
        }
    }

    // خروجی به صورت رشته جدا شده با ویرگول
    return implode(',', $custom_ids);
}




/**
 * حمل و نقل با LMS
 
add_filter('woocommerce_package_rates', 'tamyar_shipping_adjustment', 10, 2);
function tamyar_shipping_adjustment($rates, $package) {
    $token = $_COOKIE['tamshToken'];
    $totalShippingCost = 0;
    $tamyarProductIDs = get_tamyar_product_id_from_cart_products();
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.tamland.ir/api/shop/shippingCosts/'.$tamyarProductIDs,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer '.$token
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    
    $data = json_decode($response, true); // پارامتر true برای تبدیل به آرایه است

    foreach ($data as $item) {
        if (isset($item['fldShippingCost'])) {
            $number = intval($item['fldShippingCost']);
            $totalShippingCost += $number;
        }
    }

    foreach ($rates as $rate_key => $rate) {
        // تغییر هزینه حمل به صورت دستی
        $rates[$rate_key]->cost += $totalShippingCost;

        // اگر مالیات وجود داشته باشه، باید اینم تنظیم کنی:
        if (isset($rates[$rate_key]->taxes)) {
            foreach ($rates[$rate_key]->taxes as $tax_id => $tax) {
                $rates[$rate_key]->taxes[$tax_id] += 0; // یا مقدار جدید
            }
        }
    }
    
    return $rates;
}
*/

/**
 * سینک کردن موجودی کیف پول با LMS
 * 

add_action('init', 'check_login_and_sync_wallet');

function check_login_and_sync_wallet() {
    // فقط یکبار اجرا بشه، نه در هر بار بارگذاری
    if (is_user_logged_in() && !isset($_COOKIE['wallet_synced'])) {
        $user = wp_get_current_user();
        $username = $user->user_login;
        // ذخیره کوکی برای جلوگیری از اجرای مجدد در هر بار رفرش
        setcookie('wallet_synced', '1', time() + 3600, COOKIEPATH, COOKIE_DOMAIN);
        
        // اجرای تابع همگام‌سازی
        update_wallet_balance_from_api($username, $user->ID);
    }
}

function update_wallet_balance_from_api($username, $user) {
    $user_id = $user;
    // آدرس API و اطلاعات ارسالی (در صورت نیاز)
$token = $_COOKIE['tamshToken'];
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.tamland.ir/api/user/myWallet/?user='.$username,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer '.$token
  ),
));

$response = curl_exec($curl);

curl_close($curl);

    $data = json_decode($response, true);

    // بررسی ساختار درست
    if (!isset($data[0]['balanceWallet'])) {
        error_log('Invalid API structure or balanceWallet not found. Response: ' . $response);
        return false;
    }

    $new_balance = intval($data[0]['balanceWallet']);
    $currency = get_woocommerce_currency(); // یا مقدار دلخواه مثل get_woocommerce_currency()
    global $wpdb;
    $table = $wpdb->prefix . 'woo_wallet_transactions';
    
    $existing = $wpdb->get_var(
        $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE user_id = %d AND details = %s", $user_id, 'اعتبار شما در پنل تاملند')
    );
    
    if ($existing > 0) {
        // بروزرسانی رکورد موجود
        $wpdb->update(
            $table,
            [
                'amount'   => $new_balance,
                'balance'  => $new_balance,
                'currency' => $currency,
                'date'     => current_time('mysql')
            ],
            [
                'user_id' => $user_id,
                'details' => 'اعتبار شما در پنل تاملند'
            ],
            ['%f', '%f', '%s', '%s'],
            ['%d', '%s']
        );
    } else {
        // درج رکورد جدید
        $wpdb->insert(
            $table,
            [
                'user_id'  => $user_id,
                'amount'   => $new_balance,
                'balance'  => $new_balance,
                'currency' => $currency,
                'type'     => 'credit',
                'date'     => current_time('mysql'),
                'details'  => 'اعتبار شما در پنل تاملند'
            ],
            ['%d', '%f', '%f', '%s', '%s', '%s', '%s']
        );
    }



}
*/

add_action('wp_footer', function() { ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.price-values .price-value').forEach(function(el) {
        var number = el.textContent.replace(/\D/g, '');
        if(number.length > 3) {
            el.textContent = Number(number).toLocaleString('fa-IR');
        }
    });
});
</script>
<?php });

function randomize_teacher_cards_js() {
    if (is_page('teachers')) {
        ?>
        <style>
            .teachers-list{opacity:0;transition: opacity 0.5s ease;}
        </style>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const teacherList = document.querySelector(".teachers-list");
                const container = document.querySelector(".teachers-list .jet-listing-grid__items"); 
                if (!container) return;
            
                const cards = Array.from(container.querySelectorAll(".jet-listing-grid__item"));
                
                // الگوریتم تصادفی برای جابجا کردن کارت‌ها
                for (let i = cards.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [cards[i], cards[j]] = [cards[j], cards[i]];
                }
            
                // کارت‌ها رو دوباره به DOM اضافه کن
                cards.forEach(card => container.appendChild(card));
                
                teacherList.style.opacity = 1;
            });
        </script>
        <?php
    }
}
add_action('wp_head', 'randomize_teacher_cards_js');

add_action( 'pws_save_order_post_barcode', 'tamyar_pws_save_order_post_barcode', 200, 2 );

function tamyar_pws_save_order_post_barcode(WC_Order $order, $barcode){
    $order_id = $order->get_id();
    
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
        "Username":"'.$TU.'",
        "Password":"'.$TP.'",
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
    
    $response_login_json = json_decode($response_login, true);
    
    // بررسی مقدار status
    if (isset($response_login_json['status']) && $response_login_json['status'] == 0) {
        $token = $response_login_json['token']; // گرفتن توکن
        // ذخیره توکن مثلاً در سشن
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.tamland.ir/api/shop/savePostTrackingCode',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "OrderId": '.$order_id.',
            "TrackingId":"'.$barcode.'"
        }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$token
          ),
        ));
        
        $response_savePostTrackingCode = curl_exec($curl);
        curl_close($curl);
        
        $response_savePostTrackingCode_json = json_decode($response_savePostTrackingCode, true);
        $order->add_order_note( $response_savePostTrackingCode_json[0]['status'] );
        if (isset($response_savePostTrackingCode_json[0]['status']) && $response_savePostTrackingCode_json[0]['status'] == 0) {
            $order->add_order_note( "کد مرسوله $barcode برای سفارش $order_id در پنل LMS تاملند ثبت شد." );
        }   
        
    }
}

add_shortcode('slider', 'slider_func');
function slider_func(){
    if ( wp_is_mobile()) :
	    echo do_shortcode('[elementor-template id="56910"]');
    else :
	    echo do_shortcode('[elementor-template id="56906"]');
     endif;
}

add_shortcode('tamyar_grades_menu', 'tamyar_grades_menu_shortcode');
function tamyar_grades_menu_shortcode() {

    $terms = get_terms(array(
        'taxonomy'   => 'grade',
        'hide_empty' => true, 
        'parent'     => 0
    ));
    
    if (empty($terms) || is_wp_error($terms)) {
        return ''; 
    }
    

    $desired_order = array(1752, 1751, 1750, 1753, 1745, 1755, 1749, 1744, 1754, 1746, 1743, 1748);
    

    usort($terms, function($a, $b) use ($desired_order) {
        $a_pos = array_search($a->term_id, $desired_order);
        $b_pos = array_search($b->term_id, $desired_order);
        

        if ($a_pos === false) $a_pos = 999;
        if ($b_pos === false) $b_pos = 999;
        
        return $a_pos - $b_pos;
    });
    

    $output = '<ul class="tamyar-grades-menu">';
    
    foreach ($terms as $term) {
        $term_link = get_term_link($term);
        
        if (is_wp_error($term_link)) {
            continue; 
        }
        
        $output .= sprintf(
            '<li class="grade-item"><a href="%s">%s</a></li>',
            esc_url($term_link),
            esc_html($term->name)
        );
    }
    
    $output .= '</ul>';
    
    return $output;
}


add_filter('style_loader_tag', 'add_font_display_swap_to_fonts', 10, 2);
function add_font_display_swap_to_fonts($html, $handle) {

    $font_keywords = array('fonts.googleapis.com', 'font-awesome', 'fa-solid', 'fa-regular', 'fa-brands', 'elementor-icons');
    

    foreach ($font_keywords as $keyword) {
        if (strpos($html, $keyword) !== false) {
            $html = preg_replace("/rel='stylesheet'/", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $html);
            $html .= "<noscript><link rel='stylesheet' href='" . esc_url(str_replace(array(" rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\""), '', $html)) . "'></noscript>";
            break; 
        }
    }
    return $html;
}



function optimize_jquery_with_rocket() {

    if (!function_exists('get_rocket_option')) {
        return;
    }
    

    if (!is_admin() && !is_single() && !is_page()) {
        wp_deregister_script('jquery');
        wp_deregister_script('jquery-migrate');
    }
}
add_action('wp_enqueue_scripts', 'optimize_jquery_with_rocket');

function rocket_exclusions() {

    $excluded_files = array(
        'jquery.min.js',
        'jquery-migrate.min.js',
        'bootstrap.min.js',
        'custom-scripts.js'
    );
    
    foreach ($excluded_files as $file) {
        if (!defined('WP_ROCKET_EXCLUDED_JS')) {
            define('WP_ROCKET_EXCLUDED_JS', serialize(array($file)));
        }
    }
}
add_action('wp', 'rocket_exclusions');

add_action('plugins_loaded', function() {
    if (is_plugin_active('autoptimize/autoptimize.php')) {
        deactivate_plugins('autoptimize/autoptimize.php');
    }
    if (is_plugin_active('wp-super-cache/wp-cache.php')) {
        deactivate_plugins('wp-super-cache/wp-cache.php');
    }
});

add_action('woocommerce_checkout_update_order_meta', function($order_id) {
    if (!empty($_POST['billing_phone'])) {
        update_post_meta($order_id, '_billing_phone', sanitize_text_field($_POST['billing_phone']));
    }
});

add_action('woocommerce_checkout_update_order_meta', function($order_id, $posted_data = null) {
    $log_file = WP_CONTENT_DIR . '/wc-phone-debug-log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $user_id = get_current_user_id();


    $post_raw = print_r($_POST, true);
    $phone_post = isset($_POST['billing_phone']) ? sanitize_text_field($_POST['billing_phone']) : '(not found in $_POST)';


    $log = [];
    $log[] = "==============================================";
    $log[] = "🕓 [$timestamp]";
    $log[] = "Order ID: $order_id | User ID: $user_id";
    $log[] = "----------------------------------------------";
    $log[] = "🔹 Step 1 – Raw POST billing_phone: " . $phone_post;


    if ($user_id) {
        $user_phone = get_user_meta($user_id, 'billing_phone', true);
        $log[] = "🔹 Step 2 – User Meta billing_phone: " . ($user_phone ?: '(empty)');
    } else {
        $log[] = "🔹 Step 2 – User Meta: Skipped (guest checkout)";
    }


    $existing_order_phone = get_post_meta($order_id, '_billing_phone', true);
    $log[] = "🔹 Step 3 – Existing Order Meta _billing_phone before save: " . ($existing_order_phone ?: '(empty)');


    if (!empty($phone_post)) {
        update_post_meta($order_id, '_billing_phone', $phone_post);
    }


    $saved_phone = get_post_meta($order_id, '_billing_phone', true);
    $log[] = "🔹 Step 4 – Saved Order Meta _billing_phone after save: " . ($saved_phone ?: '(empty)');


    global $wpdb;
    $meta_exists = $wpdb->get_var($wpdb->prepare(
        "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = '_billing_phone' LIMIT 1",
        $order_id
    ));
    $log[] = "🔹 Step 5 – DB Query direct read: " . ($meta_exists ?: '(not found)');


    if (empty($phone_post)) {
        $log[] = "⚠️ ALERT: billing_phone missing in POST (frontend issue)";
    } elseif (empty($saved_phone)) {
        $log[] = "⚠️ ALERT: billing_phone not saved properly (backend issue)";
    } elseif ($saved_phone !== $phone_post) {
        $log[] = "⚠️ ALERT: mismatch between POST and saved phone!";
    } else {
        $log[] = "✅ billing_phone successfully captured and saved.";
    }

    $log[] = "==============================================\n";


    file_put_contents($log_file, implode("\n", $log), FILE_APPEND);
}, 10, 2);

function tamyar_noindex_product_categories_pages() {

    $current_url = home_url(add_query_arg(NULL, NULL));


    if (strpos($current_url, 'shop/?product-categories=') !== false) {
        echo '<meta name="robots" content="noindex, nofollow">' . "\n";
    }
}
add_action('wp_head', 'tamyar_noindex_product_categories_pages');


add_action('woocommerce_checkout_create_order', function($order) {
    $fields = WC()->session->get('partial_checkout_data');

    if ($fields && is_array($fields)) {
        foreach ($fields as $key => $value) {

            if (strpos($key, 'billing_') === 0 || strpos($key, 'shipping_') === 0) {
                $order->update_meta_data($key, sanitize_text_field($value));
            }
        }


        if (!empty($fields['billing_email'])) {
            $order->set_billing_email(sanitize_email($fields['billing_email']));
        }
        if (!empty($fields['billing_first_name'])) {
            $order->set_billing_first_name(sanitize_text_field($fields['billing_first_name']));
        }
        if (!empty($fields['billing_last_name'])) {
            $order->set_billing_last_name(sanitize_text_field($fields['billing_last_name']));
        }
        if (!empty($fields['billing_phone'])) {
            $order->set_billing_phone(sanitize_text_field($fields['billing_phone']));
        }


        WC()->session->__unset('partial_checkout_data');
    }
});


add_action('wp_footer', 'smart_hide_quantity_buttons');
function smart_hide_quantity_buttons() {
    if (!is_product()) return;

    global $product;
    if (!$product) return;

    $hide_buttons = false;


    if ($product->is_type('simple')) {
        if ($product->get_manage_stock()) {

            if ($product->get_stock_quantity() <= 1 || !$product->is_in_stock()) {
                $hide_buttons = true;
            }
        }

    }


    elseif ($product->is_type('variable')) {
        $has_any_variation_with_stock_gt_1 = false;
        $all_out_of_stock = true;

        foreach ($product->get_available_variations() as $variation_data) {
            $variation = wc_get_product($variation_data['variation_id']);
            if ($variation) {
                if ($variation->get_manage_stock()) {
                    if ($variation->get_stock_quantity() > 1) {
                        $has_any_variation_with_stock_gt_1 = true;
                    }
                    if ($variation->is_in_stock()) {
                        $all_out_of_stock = false;
                    }
                } else {

                    $has_any_variation_with_stock_gt_1 = true;
                }
            }
        }

        if (!$has_any_variation_with_stock_gt_1 || $all_out_of_stock) {
            $hide_buttons = true;
        }
    }


    if ($hide_buttons) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.quantity .plus, .quantity .minus').forEach(btn => {
                btn.style.display = 'none';
            });


            const qtyInput = document.querySelector('.quantity .qty, .quantity input.input-text');
            if (qtyInput) {
                qtyInput.style.width = '50px';
                qtyInput.style.textAlign = 'center';
                qtyInput.value = '1';
                qtyInput.readOnly = true;
            }
        });
        </script>
        <?php
    }
}