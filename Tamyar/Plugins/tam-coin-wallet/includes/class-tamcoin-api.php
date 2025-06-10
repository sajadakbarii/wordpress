<?php
/**
 * Tam Coin API functionality
 */
if (!defined('WPINC')) {
    die;
}

/**
 * Class for handling API requests to lms.tamland.ir
 */
class Tamcoin_API {
    
    /**
     * The API base URL
     */
    private $api_url = 'https://api.tamland.ir/api';
    
    /**
     * Constructor
     */
    public function __construct() {
        // Add any initialization code here
    }
    
    /**
     * Get user's coin balance from LMS API
     *
     * @param int $user_id User ID
     * @return int|WP_Error User's coin balance or error
     */
    public function get_user_balance($user_id) {
        // گرفتن اطلاعات کاربر
        $user = get_userdata($user_id);
        $token = isset($_COOKIE['tamshToken']) ? sanitize_text_field($_COOKIE['tamshToken']) : '';
    
        if (!$user || empty($token)) {
            return new WP_Error('invalid_user', __('کاربر یا توکن نامعتبر است.', 'tam-coin-wallet'));
        }
    
        // ساخت هدرهای درخواست
        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $token,
            ),
            'timeout' => 15,
        );
    
        // ارسال درخواست GET به API
        $response = wp_remote_get($this->api_url . '/lms/getTamcoin', $args);
    
        // بررسی خطا
        if (is_wp_error($response)) {
            return new WP_Error('api_error', __('خطا در ارتباط با API', 'tam-coin-wallet'));
        }
    
        // بررسی بدنه‌ی پاسخ
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
    
        if (!is_array($data) || !isset($data[0]['tamCoin'])) {
            return new WP_Error('invalid_response', __('پاسخ نامعتبر از API', 'tam-coin-wallet'));
        }
    
        return absint($data[0]['tamCoin']);
    }


    
    /**
     * Update user's balance after a purchase
     *
     * @param int $user_id User ID
     * @param int $coins_spent Number of coins spent
     * @param string $order_id Order ID for reference
     * @return bool|WP_Error True on success or error object
     */
    public function update_user_balance($user_id, $coins_spent, $order_id) {
        $user = get_userdata($user_id);
        
        if (!$user) {
            return new WP_Error('invalid_user', __('کاربر نامعتبر', 'tam-coin-wallet'));
        }
        
        $args = array(
            'timeout'     => 30,
            'headers'     => array(
                'Content-Type' => 'application/json',
            ),
            'body'        => json_encode(array(
                'id'       => $user_id,
                'coins_spent' => $coins_spent,
                'order_id'    => $order_id,
                'site_url'    => site_url(),
            )),
        );
        
        $response = wp_remote_post(
            $this->api_url . '/update_balance', 
            $args
        );
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (empty($data) || !isset($data['success'])) {
            return new WP_Error(
                'update_failed', 
                __('خطا در بروزرسانی کیف پول', 'tam-coin-wallet')
            );
        }
        
        return true;
    }
}