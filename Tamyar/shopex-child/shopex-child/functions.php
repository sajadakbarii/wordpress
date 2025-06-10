<?php
// Enqueue styles from parent and child theme
function shopex_child_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'), wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'shopex_child_enqueue_styles');

/*
// تغییر آدرس ورود ووکامرس
add_filter('woocommerce_login_redirect', 'custom_login_redirect', 10, 2);
function custom_login_redirect($redirect, $user) {
    return site_url('/lms-login/'); // آدرس صفحه سفارشی شما
}

// تغییر آدرس ثبت‌نام ووکامرس
add_action('template_redirect', 'custom_registration_redirect');
function custom_registration_redirect() {
    if (is_account_page() && isset($_GET['action']) && $_GET['action'] === 'register') {
        wp_redirect(site_url('/lms-login/')); // آدرس صفحه سفارشی شما
        exit;
    }
}
*/

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
        var tamshopCatIds = [];
        var hasNewProduct = false;
        var addedProduct = 0;
        let tamshopAttr = {};
        const token = btoa("ck_f92acec278157ab36d8fd804322801e517a2a14d:cs_6526bfdff3b0afcd441a17f00431d7e9d52432f9");
        // Function to get product properties
        
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
            const customButton = $('<a href="<?php echo admin_url("admin-post.php?action=add_new_woocommerce_product"); ?>" id="tamshop-update-product-button" class="page-title-action custom-action-button">افزودن محصولات جدید تامشاپ</a>');
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
                            for(i = 0;i <= getShopProducts_response.data.length;i++){
                                if(i < getShopProducts_response.data.length){
                                    // ساختن آرایه shopProductIds از پاسخ API
                                shopProductIds = getShopProducts_response.data.map(function (product) {
                                    return product['fldPkProduct'];
                                });
                                    var product = getShopProducts_response.data[i];  // آیتم جاری از آرایه getShopProducts_response.data
                                    let tamshopId = product['fldPkProduct'];
                                    if ($.inArray(Number(getShopProducts_response.data[i]['fldPkProduct']), tamshopIds.map(Number)) == -1) {
                                        hasNewProduct = true;
                                        $('#tamshop-update-product-button').text('در حال بروزرسانی ...').addClass('disabled');
                                        addedProduct++;
                                        //console.log(addedProduct);
                                        let title = product['fldProductTitle'];
                                        let regularPrice = product['fldAmountTamPriceAdmin'].toString();
                                        let featureImg = product['fldProductImageUrl'];
                                        let category = product['fldFKFirstCategory'];
                                        let productDetailsId = product['fldPkDetailProduct'];
                                        let tamcoinPercentage = parseInt(product['fldCoinPercentage']);
                                        let tamcoinToPrice = (tamcoinPercentage * regularPrice) / 100;
                                        
                                        var productDetails_settings = {
                                            "url": "https://api.tamland.ir/api/shop/GetProdDetail/"+productDetailsId,
                                            "method": "GET",
                                            "timeout": 0,
                                        };
                                        
                                        const productDetails_response = await callApi(productDetails_settings.url, productDetails_settings.method);
                                        console.log("Next API productDetails_response:", productDetails_response);
                                        let productDescription = productDetails_response[0]?.fldProductDescription;
                                        
                                        let foundObject = tamshopCatIds.find(obj => obj.hasOwnProperty(category));
                                        let wc_cat_id = foundObject ? foundObject[category] : 0;
                                        
                                        tamshopAttr = {};
                                        var ProductPropertySettings = {
                                            url: "https://api.tamland.ir/api/shop/getProductProperty/" + tamshopId + "/-1",
                                            method: "GET",
                                            timeout: 0,
                                        };
                                        $("#isUpdating p").text("در حال افزودن محصول " + title);
                                        const ProductPropertyResponse = await callApi(ProductPropertySettings.url, ProductPropertySettings.method);
                                        //console.log("Next API ProductPropertyResponse:", ProductPropertyResponse);
                                        ProductPropertyResponse.forEach(function(property) {
                                            if (property['fldFkProperty'] == 1) {
                                                tamshopAttr["size"] = property['fldPropertyValueTitle'].split(',');
                                            } else if (property['fldFkProperty'] == 2) {
                                                tamshopAttr["color"] = property['fldPropertyValueTitle'].split(',');
                                            }
                                        });
                                        
                                        if($.isEmptyObject(tamshopAttr)){
                                                        
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
                                                            "categories": [
                                                              {
                                                                "id": wc_cat_id
                                                              }
                                                            ],
                                                            "meta_data": [
                                                              {
                                                                "key": "tamshop-product-id",
                                                                "value": tamshopId
                                                              },
                                                              {
                                                                "key": "_harikrutfiwu_url",
                                                                "value": {
                                                                    "img_url": "https://stream.tamland.ir/tamland/1402/shop/"+featureImg,
                                                                    "width": "",
                                                                    "height": ""
                                                                }
                                                              },
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
                                                            "categories": [
                                                              {
                                                                "id": wc_cat_id
                                                              }
                                                            ],
                                                            "attributes": [
                                                                {
                                                                  "id": 1,
                                                                  "name": "رنگ",
                                                                  "position": 0,
                                                                  "visible": false,
                                                                  "variation": true,
                                                                  "options": tamshopAttr.color ? tamshopAttr.color : '',
                                                                },
                                                                {
                                                                  "id": 5,
                                                                  "name": "سایز",
                                                                  "position": 1,
                                                                  "visible": false,
                                                                  "variation": true,
                                                                  "options": tamshopAttr.size ? tamshopAttr.size : '',
                                                                }
                                                              ],
                                                            "meta_data": [
                                                              {
                                                                "key": "tamshop-product-id",
                                                                "value": tamshopId
                                                              },
                                                              {
                                                                "key": "_harikrutfiwu_url",
                                                                "value": {
                                                                    "img_url": "https://stream.tamland.ir/tamland/1402/shop/"+featureImg,
                                                                    "width": "",
                                                                    "height": ""
                                                                }
                                                              },
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
                            
                            async function updateStockStatus(productId, status, isVariation = false, parentId = null) {
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
                            }
                            
                            
                            async function syncInventory() {
                              const allProducts = await fetchAllProducts();
                              let updatedCount = 0;
                              for (const product of allProducts) {
                                  $("#isUpdating p").text("در حال بروزرسانی" + product.id + "- " + product.name);
                                if (product.type === "variable") {
                                    //console.log('yes is variable');
                                  const variations = await fetchProductVariations(product.id);
                                  for (const variation of variations) {
                                    const tamMeta = product.meta_data?.find(meta => meta.key === 'tamshop-product-id');
                                    if (!tamMeta) continue;
                                    //console.log("tamMeta: "+ tamMeta);
                                    const tamId = parseInt(tamMeta.value);
                                    //console.log("tamId: "+ tamId);
                                    if (!shopProductIds.includes(tamId)) {
                                      //console.log(`❌ NOT IN API → Marking out of stock:`, tamId);
                                      await updateStockStatus(variation.id, 'outofstock', true, product.id);
                                      updatedCount++;
                                    } else {
                                      //console.log(`✅ FOUND IN API → Marking in stock:`, tamId);
                                      await updateStockStatus(variation.id, 'instock', true, product.id);
                                    }
                                  }
                                } else {
                                  const tamMeta = product.meta_data?.find(meta => meta.key === 'tamshop-product-id');
                                  if (!tamMeta) continue;
                            
                                  const tamId = parseInt(tamMeta.value);
                            
                                  if (!shopProductIds.includes(tamId)) {
                                    //console.log(`❌ NOT IN API → Marking out of stock:`, tamId);
                                    await updateStockStatus(product.id, 'outofstock');
                                    updatedCount++;
                                  } else {
                                    //console.log(`✅ FOUND IN API → Marking in stock:`, tamId);
                                    await updateStockStatus(product.id, 'instock');
                                  }
                                }
                              }
                            
                              alert(`همگام‌سازی موجودی انجام شد. ${updatedCount} مورد به خارج از موجودی تغییر یافت.`);
                              setTimeout(function () {
                                        window.location.reload();
                                    }, 5000);
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

// Add a new column to the Orders Table in My Account
add_filter('woocommerce_my_account_my_orders_columns', 'add_lms_order_id_column');
function add_lms_order_id_column($columns) {
    $columns['lms_order_id'] = __('شناسه سفارش در تامشاپ', 'textdomain');
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
    print_r($order);
    if ($order->get_status() == 'processing' || $order->get_status() == 'completed') {
        // کدهایی که بعد از پرداخت موفق باید اجرا شوند
        $paymentStatus = 1;
        error_log("کاربر به صفحه تشکر از خرید بازگشت: سفارش شماره " . $order_id);
    }
    $user_id = get_current_user_id(); // دریافت شناسه کاربری کاربر فعلی
    $fldUserCo = get_user_meta($user_id, 'fldUserCo', true);
    
    // Initialize an array to store product details
    $products = [];

    // Retrieve order items
    $items = $order->get_items();
    foreach ($items as $item_id => $item) {
        // Get product ID and quantity
        $product_id = $item->get_product_id();
        $product_count = $item->get_quantity();

        // Get product variations (attributes)
        $product_variations = [];
        $variation_data = $item->get_variation_attributes(); // Get selected variations
        if (!empty($variation_data)) {
            foreach ($variation_data as $attribute => $value) {
                $product_variations[] = ucfirst(str_replace('attribute_', '', $attribute)) . ': ' . ucfirst($value);
            }
        }
        $product_properties = implode(', ', $product_variations); // Convert variations to a string

        // Add product details to the $products array
        $products[] = [
            'ProductId' => $product_id,
            'ProductCount' => $product_count,
            'ProductProperties' => $product_properties
        ];
    }
    
    $tamcoin_discount = $order->get_meta('_tamcoin_discount');

    // Example: Build the final payload
    $payload = [
        'UserCo' => $fldUserCo,
        'Price' => $order->get_total(),
        'OrderId' => $order_id,
        'Tamcoin' => $tamcoin_discount,
        'TrackingCode' => '', // Add tracking code if available
        'Transport' => 1, // Replace with actual transport code
        'PaymentStatus' => $paymentStatus, // Replace with actual payment status
        'City' => $order->get_billing_city(),
        'PostalCode' => $order->get_billing_postcode(),
        'Address' => $order->get_billing_address_1() . ' ' . $order->get_billing_address_2(),
        'Description' => $order->get_customer_note(),
        'Products' => $products // Add the products array here
    ];

    // Example: Log the payload (for debugging)
    error_log(print_r($payload, true));

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





/* function custom_menu_hover_script() {
//     ?>
//     <script>
//     document.addEventListener("DOMContentLoaded", function () {
//         const menuItems = document.querySelectorAll(".elementor-element-828ca35 .menu-item a, .mega-menu");
//         const overlay = document.querySelector(".menu-hover-overlay");

//         let hoverTimeout;

//         function showOverlay() {
//             overlay.style.opacity = "1";
//             overlay.style.visibility = "visible";
//             overlay.style.pointerEvents = "auto";
//         }

//         function hideOverlay() {
//             overlay.style.opacity = "0";
//             overlay.style.visibility = "hidden";
//             overlay.style.pointerEvents = "none";
//         }

//         function checkHoverState() {
//             clearTimeout(hoverTimeout);
//             hoverTimeout = setTimeout(() => {
//                 const menuHovered = Array.from(menuItems).some(item => item.matches(':hover'));
//                 const overlayHovered = overlay.matches(':hover');
//                 if (!menuHovered && !overlayHovered) {
//                     hideOverlay();
//                 }
//             }, 1); // Delay avoids flicker
//         }

//         menuItems.forEach(item => {
//             item.addEventListener("mouseenter", showOverlay);
//             item.addEventListener("mouseleave", checkHoverState);
//         });

//         overlay.addEventListener("mouseleave", checkHoverState);
//         overlay.addEventListener("mouseenter", showOverlay);
//     });
//     </script>
//     <?php
// }
// add_action('wp_footer', 'custom_menu_hover_script', 100);
*/
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

add_action('admin_footer', 'add_custom_button_to_update_products');
function add_custom_button_to_update_products() {
    // Check if we are on the All Products page
    $screen = get_current_screen();
    if ($screen->id !== 'edit-product') {
        return;
    }

    ?>
    <script type="text/javascript">
        //const token = btoa("ck_f92acec278157ab36d8fd804322801e517a2a14d:cs_6526bfdff3b0afcd441a17f00431d7e9d52432f9");
        // Function to get product properties
        
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
            const customButton = $('<a href="<?php echo admin_url("admin-post.php?action=add_new_woocommerce_product"); ?>" id="tamshop-update-product-button" class="page-title-action update-custom-action-button">بروزرسانی اطلاعات محصولات تامشاپ</a>');
            $('.wrap .page-title-action:last').after(customButton);
            
            // Woocommerce Product Sync Script
            // Supports: Simple, Variable, and Downloadable Products
            
            const axios = require('axios');
            
            const woocommerceAPI = axios.create({
              baseURL: 'https://yourstore.com/wp-json/wc/v3/',
              auth: {
                username: 'ck_your_consumer_key',
                password: 'cs_your_consumer_secret'
              },
              headers: { 'Content-Type': 'application/json' }
            });
            
            // Sample external product data (you can replace this with actual API call)
            const externalProducts = [];
            
            async function getAllWCProducts() {
              let allProducts = [], page = 1;
              while (true) {
                const res = await woocommerceAPI.get('products', { params: { per_page: 100, page } });
                if (res.data.length === 0) break;
                allProducts.push(...res.data);
                page++;
              }
              return allProducts;
            }
            
            async function syncProducts() {
              try {
                const existingProducts = await getAllWCProducts();
                const wcProductsBySku = Object.fromEntries(existingProducts.map(p => [p.sku, p]));
            
                for (const product of externalProducts) {
                  const existing = wcProductsBySku[product.sku];
                  await updateProductIfNeeded(existing, product);
                }
            
                console.log('✅ Product sync completed.');
              } catch (err) {
                console.error('❌ Sync error:', err.response?.data || err);
              }
            }
            
            async function updateProductIfNeeded(existing, newData) {
              const changes = {};
              if (existing.name !== newData.name) changes.name = newData.name;
              if (existing.regular_price !== newData.price) changes.regular_price = newData.price.toString();
              if (existing.stock_quantity !== newData.stock_quantity) changes.stock_quantity = newData.stock_quantity;
            
              if (newData.downloadable && JSON.stringify(existing.downloads) !== JSON.stringify(newData.downloads)) {
                changes.downloadable = true;
                changes.downloads = newData.downloads;
              }
            
              if (Object.keys(changes).length > 0) {
                await woocommerceAPI.put(`products/${existing.id}`, changes);
                console.log(`🔄 Updated: ${newData.name}`);
              } else {
                console.log(`✅ No changes: ${newData.name}`);
              }
            }
            
            syncProducts();

        });
    </script>
    
    <style>
        .update-custom-action-button {
            background-color: #7247ff !important;
            border-color:#7247ff !important;
            color: #fff !important;
            text-decoration: none;
            margin-left: 10px;
        }
        .update-custom-action-button:hover {
            background-color: #c4161c !important;
            border-color:#c4161c !important;
        }
        .update-custom-action-button.success {
            background-color: #00e6a4 !important;
            border-color:#00e6a4 !important;
        }
        .update-custom-action-button.disabled {
            background-color: #666 !important;
            border-color:#666 !important;
            cursor:default;
        }
    </style>
<?php
}