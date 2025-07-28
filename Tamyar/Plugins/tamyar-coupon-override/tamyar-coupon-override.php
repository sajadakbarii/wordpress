<?php
/**
 * Plugin Name: کد تخفیف تامیار
 * Description: بررسی و اعمال کد تخفیف ووکامرس با استفاده از API خارجی
 * Version: 1.1.0
 * Author: سجاد اکبری
 */

if (!defined('ABSPATH')) exit;

class Tamyar_Coupon_Override {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('woocommerce_cart_calculate_fees', [$this, 'apply_discount_from_session']);
        
        add_action('wp_ajax_validate_coupon_via_api', [$this, 'handle_coupon']);
        add_action('wp_ajax_nopriv_validate_coupon_via_api', [$this, 'handle_coupon']);
        
        add_action('init', [$this, 'maybe_remove_coupon']);

        // نمایش پیام‌های سشن در صفحات سبد و پرداخت
        add_action('woocommerce_before_cart', [$this, 'display_session_notices']);
        add_action('woocommerce_before_checkout_form', [$this, 'display_session_notices']);
    }

    public function enqueue_scripts() {
        if (is_cart() || is_checkout()) {
            wp_enqueue_script(
                'tamyar-coupon-handler',
                plugin_dir_url(__FILE__) . 'assets/js/coupon-handler.js',
                ['jquery'],
                '1.0.5',
                true
            );

            wp_localize_script('tamyar-coupon-handler', 'tamyar_coupon_data', [
                'ajax_url' => admin_url('admin-ajax.php')
            ]);
        }
    }

    public function handle_coupon() {
        // اینجا به ajax درخواست پاسخ میدیم
        $code = isset($_POST['coupon_code']) ? sanitize_text_field($_POST['coupon_code']) : '';

        if (!$code) {
            wp_send_json_error(['message' => 'لطفا کد تخفیف را وارد کنید.', 'status' => 2]);
        }
        $products_ids = [];

        foreach (WC()->cart->get_cart() as $cart_item) {
            $product = $cart_item['data'];
            $tamshop_id = get_post_meta($product->get_id(), 'tamshop-product-id', true);
            if (!empty($tamshop_id)) {
                $products_ids[] = $tamshop_id;
            }
        }
        
        $products = implode(',', $products_ids);

        $token = isset($_COOKIE['tamshToken']) ? sanitize_text_field($_COOKIE['tamshToken']) : '';

        $response = wp_remote_post('https://api.tamland.ir/api/shop/checkDiscountCode', [
            'body'    => json_encode([
                'Code'    => $code,
                'Product' => $products,
            ]),
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => 'خطا در اتصال به سرور تخفیف.', 'status' => 5]);
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        // بررسی داده‌ها و پیام‌دهی بر اساس status
        if (empty($data) || !isset($data[0]['status'])) {
            wp_send_json_error(['message' => 'پاسخ نامعتبر از سرور.', 'status' => 6]);
        }

        $status = intval($data[0]['status']);
        $message = $data[0]['message'] ?? 'خطایی رخ داده است.';

        switch ($status) {
            case 0: // کد معتبر
                // مقدار تخفیف را می‌گیریم، فرض می‌کنیم در فیلد discount_amount هست
                $discount_amount = isset($data[0]['discount_amount']) ? floatval($data[0]['discount_amount']) : 0;

                if ($discount_amount <= 0) {
                    wp_send_json_error(['message' => 'مقدار تخفیف نامعتبر است.', 'status' => 7]);
                }

                WC()->session->set('custom_coupon_applied', true);
                WC()->session->set('custom_coupon_amount', $discount_amount);
                WC()->session->set('custom_coupon_code', $code);
                WC()->session->set('custom_coupon_message', $message);

                // پیام موفقیت رو در سشن هم می‌گذاریم
                WC()->session->set('custom_coupon_notice', ['type' => 'success', 'message' => $message]);

                wp_send_json_success(['message' => $message, 'status' => 0]);
                break;

            case 2: // کد تخفیف وارد نشده
            case 3: // کد نامعتبر
            case 4: // کد قبلا استفاده شده
                // حذف تخفیف در صورت وجود قبلی
                WC()->session->__unset('custom_coupon_applied');
                WC()->session->__unset('custom_coupon_amount');
                WC()->session->__unset('custom_coupon_code');
                WC()->session->__unset('custom_coupon_notice');

                WC()->session->set('custom_coupon_notice', ['type' => 'error', 'message' => $message]);

                wp_send_json_error(['message' => $message, 'status' => $status]);
                break;

            default:
                wp_send_json_error(['message' => 'خطای نامشخص رخ داده است.', 'status' => $status]);
                break;
        }
    }

    public function apply_discount_from_session($cart) {
        if (is_admin() && !defined('DOING_AJAX')) return;
        if (did_action('woocommerce_cart_calculate_fees') >= 2) return;

        if (WC()->session->get('custom_coupon_applied')) {
            $amount = WC()->session->get('custom_coupon_amount');
            $code = WC()->session->get('custom_coupon_code');

            if ($amount > 0) {
                $cart->add_fee('تخفیف (' . esc_html($code) . ')', -abs($amount));
            }
        }
    }

    public function maybe_remove_coupon() {
        if (isset($_GET['remove_custom_coupon'])) {
            WC()->session->__unset('custom_coupon_applied');
            WC()->session->__unset('custom_coupon_amount');
            WC()->session->__unset('custom_coupon_code');
            WC()->session->__unset('custom_coupon_notice');

            wc_add_notice('کد تخفیف حذف شد.', 'notice');
            wp_safe_redirect(wc_get_cart_url());
            exit;
        }
    }

    public function display_session_notices() {
        $notice = WC()->session->get('custom_coupon_notice');
        if ($notice && isset($notice['type'], $notice['message'])) {
            wc_add_notice($notice['message'], $notice['type']);
            WC()->session->__unset('custom_coupon_notice');
        }
    }
}

new Tamyar_Coupon_Override();
