<<<<<<< HEAD
<?php
/**
 * Tam Coin Wallet functionality
 */
if (!defined('WPINC')) {
    die;
}

/**
 * Class for handling wallet functionality
 */
class Tamcoin_Wallet {
    
    /**
     * API instance
     */
    private $api;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->api = new Tamcoin_API();
        
        // Update user balance on login
        add_action('wp_login', array($this, 'update_balance_on_login'), 10, 2);
        
        // Register AJAX actions
        add_action('wp_ajax_tamcoin_refresh_balance', array($this, 'ajax_refresh_balance'));
        
        // Set session variable for coin usage
        add_action('woocommerce_init', array($this, 'init_session_variables'));
        
        // Handle AJAX toggle coin usage
        add_action('wp_ajax_tamcoin_toggle_use_coins', array($this, 'ajax_toggle_use_coins'));
    }
    
    /**
     * Initialize session variables
     */
    public function init_session_variables() {
        if (!is_user_logged_in()) {
            return;
        }
        
        if (!WC()->session->get('tamcoin_use_coins')) {
            WC()->session->set('tamcoin_use_coins', 'no');
        }
    }
    
    /**
     * Update user's balance when they log in
     *
     * @param string $user_login Username
     * @param WP_User $user User object
     */
    public function update_balance_on_login($user_login, $user) {
        $this->update_user_balance($user->ID);
    }
    
    /**
     * Update user balance and store it in user meta
     *
     * @param int $user_id User ID
     * @return int|WP_Error Updated balance or error
     */
    public function update_user_balance($user_id) {
        $balance = $this->api->get_user_balance($user_id);
        
        if (!is_wp_error($balance)) {
            update_user_meta($user_id, '_tamcoin_balance', $balance);
        }
        
        return $balance;
    }
    
    /**
     * Get user's current balance from user meta
     *
     * @param int $user_id User ID
     * @return int User's coin balance
     */
    public function get_user_balance($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        return absint(get_user_meta($user_id, '_tamcoin_balance', true));
    }
    
    /**
     * AJAX handler to refresh balance
     */
    public function ajax_refresh_balance() {
        check_ajax_referer('tamcoin_refresh_balance', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array(
                'message' => __('شما باید وارد سایت شوید', 'tam-coin-wallet')
            ));
        }
        
        $user_id = get_current_user_id();
        $balance = $this->update_user_balance($user_id);
        
        if (is_wp_error($balance)) {
            wp_send_json_error(array(
                'message' => $balance->get_error_message()
            ));
        }
        
        wp_send_json_success(array(
            'balance' => $balance,
            'formatted_balance' => $this->format_coin_amount($balance)
        ));
    }
    
    /**
     * Format coin amount for display
     *
     * @param int $amount Coin amount
     * @return string Formatted amount
     */
    public function format_coin_amount($amount) {
        return sprintf(
            _n('%s ریال', '%s ریال', $amount, 'tam-coin-wallet'), 
            number_format_i18n($amount)
        );
    }
    
    /**
     * Calculate the discount amount based on cart contents and user's coin balance
     *
     * @return array Discount information
     */
    public function calculate_coin_discount() {
        if (!is_user_logged_in() || WC()->session->get('tamcoin_use_coins') !== 'yes') {
            return array(
                'coins_used' => 0,
                'discount' => 0
            );
        }
        
        $user_id = get_current_user_id();
        
        $balance = $this->api->get_user_balance($user_id);
        
        if ($balance <= 0) {
            return array(
                'coins_used' => 0,
                'discount' => 0
            );
        }
        
        $conversion_rate = get_option('tamcoin_conversion_rate', 10000);
        $cart = WC()->cart;
        $max_coins_applicable = 0;
        
        // Loop through cart items to calculate max coins for all items
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            $product_id = $cart_item['data']->get_id();
            $quantity = $cart_item['quantity'];
            
            // Get max coins allowed for this product
            $max_product_coins = get_post_meta($product_id, 'max_tamcoins_required', true);
            
            if ($max_product_coins) {
                $max_coins_applicable += (absint($max_product_coins) * $quantity);
            }
        }
        
        // Make sure we don't use more coins than the user has
        $coins_to_use = min($balance, $max_coins_applicable);
        
        // Calculate discount amount based on conversion rate
        $discount_amount = $coins_to_use * $conversion_rate; // Convert to WC price format
        
        // Make sure discount doesn't exceed cart total
        $cart_total = $cart->get_subtotal();
        if ($discount_amount > $cart_total) {
            $discount_amount = $cart_total;
            $coins_to_use = ceil(($discount_amount * 100) / $conversion_rate);
        }
        
        return array(
            'coins_used' => $coins_to_use,
            'discount' => $discount_amount
        );
    }
    
    /**
     * Calculate the discount amount based on cart contents and user's coin balance
     *
     * @return array Discount information
     */
    public function calculate_price_coin_discount() {
        if (!is_user_logged_in() || WC()->session->get('tamcoin_use_coins') !== 'yes') {
            return array(
                'coins_used' => 0,
                'discount' => 0
            );
        }
        
        $user_id = get_current_user_id();
        $conversion_rate = get_option('tamcoin_conversion_rate', 10000);
        $balance = $this->api->get_user_balance($user_id) * $conversion_rate;
        
        if ($balance <= 0) {
            return array(
                'coins_used' => 0,
                'discount' => 0
            );
        }
        
        
        $cart = WC()->cart;
        $max_coins_applicable = 0;
        
        // Loop through cart items to calculate max coins for all items
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            $product_id = $cart_item['data']->get_id();
            $quantity = $cart_item['quantity'];
            
            // Get max coins allowed for this product
            $max_product_price_coins = get_post_meta($product_id, 'max_prctamcoins_required', true);
            
            if ($max_product_price_coins) {
                $max_coins_applicable += (absint($max_product_price_coins) * $quantity);
            }
        }
        
        // Make sure we don't use more coins than the user has
        $coins_to_use = min($balance, $max_coins_applicable);
        
        // Calculate discount amount based on conversion rate
        $discount_amount = $coins_to_use; // Convert to WC price format
        
        // Make sure discount doesn't exceed cart total
        $cart_total = $cart->get_subtotal();
        if ($discount_amount > $cart_total) {
            $discount_amount = $cart_total;
            $coins_to_use = ceil($discount_amount);
        }
        
        return array(
            'coins_used' => $coins_to_use,
            'discount' => $discount_amount
        );
    }
    
    /**
     * AJAX handler to toggle coin usage
     */
    public function ajax_toggle_use_coins() {
        check_ajax_referer('tamcoin_toggle_use', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array(
                'message' => __('شما باید وارد سایت شوید.', 'tam-coin-wallet')
            ));
        }
        
        $use_coins = isset($_POST['use_coins']) ? sanitize_text_field($_POST['use_coins']) : 'no';
        
        // Set session variable
        WC()->session->set('tamcoin_use_coins', $use_coins);
        
        // Calculate new totals
        $discount_info = $this->calculate_price_coin_discount();
        
        wp_send_json_success(array(
            'use_coins' => $use_coins,
            'coins_used' => $discount_info['coins_used'],
            'discount' => wc_price($discount_info['discount']),
            'new_total' => wc_price(WC()->cart->get_total('edit') - $discount_info['discount'])
        ));
    }
    
    /**
     * Apply coin discount to an order
     *
     * @param int $order_id Order ID
     * @return bool Whether discount was applied
     */
    public function apply_coin_discount_to_order($order_id) {
        if (WC()->session->get('tamcoin_use_coins') !== 'yes') {
            return false;
        }
        
        $discount_info = $this->calculate_price_coin_discount();
        
        if ($discount_info['coins_used'] <= 0) {
            return false;
        }
        
        $order = wc_get_order($order_id);
        $user_id = $order->get_user_id();
        
        // Add order meta
        $order->update_meta_data('_tamcoin_used', $discount_info['coins_used']);
        $order->update_meta_data('_tamcoin_discount', $discount_info['discount']);
        
        // Add order note
        $order->add_order_note(
            sprintf(
                __('%s تام کوین استفاده شده', 'tam-coin-wallet'),
                $discount_info['coins_used'],
                wc_price($discount_info['discount'])
            )
        );
        
        // Apply discount to order
        //$order->set_discount_total($order->get_discount_total('edit') + $discount_info['discount']);
        $order->set_total($order->get_total('edit'));
        $order->save();
        
        // Update user's balance via API
        $this->api->update_user_balance($user_id, $discount_info['coins_used'], $order_id);
        
        // Update local balance
        $current_balance = $this->api->get_user_balance($user_id);
        update_user_meta($user_id, '_tamcoin_balance', max(0, $current_balance - $discount_info['coins_used']));
        
        // Reset session
        WC()->session->set('tamcoin_use_coins', 'no');
        
        return true;
    }
=======
<?php
/**
 * Tam Coin Wallet functionality
 */
if (!defined('WPINC')) {
    die;
}

/**
 * Class for handling wallet functionality
 */
class Tamcoin_Wallet {
    
    /**
     * API instance
     */
    private $api;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->api = new Tamcoin_API();
        
        // Update user balance on login
        add_action('wp_login', array($this, 'update_balance_on_login'), 10, 2);
        
        // Register AJAX actions
        add_action('wp_ajax_tamcoin_refresh_balance', array($this, 'ajax_refresh_balance'));
        
        // Set session variable for coin usage
        add_action('woocommerce_init', array($this, 'init_session_variables'));
        
        // Handle AJAX toggle coin usage
        add_action('wp_ajax_tamcoin_toggle_use_coins', array($this, 'ajax_toggle_use_coins'));
    }
    
    /**
     * Initialize session variables
     */
    public function init_session_variables() {
        if (!is_user_logged_in()) {
            return;
        }
        
        if (!WC()->session->get('tamcoin_use_coins')) {
            WC()->session->set('tamcoin_use_coins', 'no');
        }
    }
    
    /**
     * Update user's balance when they log in
     *
     * @param string $user_login Username
     * @param WP_User $user User object
     */
    public function update_balance_on_login($user_login, $user) {
        $this->update_user_balance($user->ID);
    }
    
    /**
     * Update user balance and store it in user meta
     *
     * @param int $user_id User ID
     * @return int|WP_Error Updated balance or error
     */
    public function update_user_balance($user_id) {
        $balance = $this->api->get_user_balance($user_id);
        
        if (!is_wp_error($balance)) {
            update_user_meta($user_id, '_tamcoin_balance', $balance);
        }
        
        return $balance;
    }
    
    /**
     * Get user's current balance from user meta
     *
     * @param int $user_id User ID
     * @return int User's coin balance
     */
    public function get_user_balance($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        return absint(get_user_meta($user_id, '_tamcoin_balance', true));
    }
    
    /**
     * AJAX handler to refresh balance
     */
    public function ajax_refresh_balance() {
        check_ajax_referer('tamcoin_refresh_balance', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array(
                'message' => __('شما باید وارد سایت شوید', 'tam-coin-wallet')
            ));
        }
        
        $user_id = get_current_user_id();
        $balance = $this->update_user_balance($user_id);
        
        if (is_wp_error($balance)) {
            wp_send_json_error(array(
                'message' => $balance->get_error_message()
            ));
        }
        
        wp_send_json_success(array(
            'balance' => $balance,
            'formatted_balance' => $this->format_coin_amount($balance)
        ));
    }
    
    /**
     * Format coin amount for display
     *
     * @param int $amount Coin amount
     * @return string Formatted amount
     */
    public function format_coin_amount($amount) {
        return sprintf(
            _n('%s ریال', '%s ریال', $amount, 'tam-coin-wallet'), 
            number_format_i18n($amount)
        );
    }
    
    /**
     * Calculate the discount amount based on cart contents and user's coin balance
     *
     * @return array Discount information
     */
    public function calculate_coin_discount() {
        if (!is_user_logged_in() || WC()->session->get('tamcoin_use_coins') !== 'yes') {
            return array(
                'coins_used' => 0,
                'discount' => 0
            );
        }
        
        $user_id = get_current_user_id();
        
        $balance = $this->api->get_user_balance($user_id);
        
        if ($balance <= 0) {
            return array(
                'coins_used' => 0,
                'discount' => 0
            );
        }
        
        $conversion_rate = get_option('tamcoin_conversion_rate', 10000);
        $cart = WC()->cart;
        $max_coins_applicable = 0;
        
        // Loop through cart items to calculate max coins for all items
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            $product_id = $cart_item['data']->get_id();
            $quantity = $cart_item['quantity'];
            
            // Get max coins allowed for this product
            $max_product_coins = get_post_meta($product_id, 'max_tamcoins_required', true);
            
            if ($max_product_coins) {
                $max_coins_applicable += (absint($max_product_coins) * $quantity);
            }
        }
        
        // Make sure we don't use more coins than the user has
        $coins_to_use = min($balance, $max_coins_applicable);
        
        // Calculate discount amount based on conversion rate
        $discount_amount = $coins_to_use * $conversion_rate; // Convert to WC price format
        
        // Make sure discount doesn't exceed cart total
        $cart_total = $cart->get_subtotal();
        if ($discount_amount > $cart_total) {
            $discount_amount = $cart_total;
            $coins_to_use = ceil(($discount_amount * 100) / $conversion_rate);
        }
        
        return array(
            'coins_used' => $coins_to_use,
            'discount' => $discount_amount
        );
    }
    
    /**
     * Calculate the discount amount based on cart contents and user's coin balance
     *
     * @return array Discount information
     */
    public function calculate_price_coin_discount() {
        if (!is_user_logged_in() || WC()->session->get('tamcoin_use_coins') !== 'yes') {
            return array(
                'coins_used' => 0,
                'discount' => 0
            );
        }
        
        $user_id = get_current_user_id();
        $conversion_rate = get_option('tamcoin_conversion_rate', 10000);
        $balance = $this->api->get_user_balance($user_id) * $conversion_rate;
        
        if ($balance <= 0) {
            return array(
                'coins_used' => 0,
                'discount' => 0
            );
        }
        
        
        $cart = WC()->cart;
        $max_coins_applicable = 0;
        
        // Loop through cart items to calculate max coins for all items
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            $product_id = $cart_item['data']->get_id();
            $quantity = $cart_item['quantity'];
            
            // Get max coins allowed for this product
            $max_product_price_coins = get_post_meta($product_id, 'max_prctamcoins_required', true);
            
            if ($max_product_price_coins) {
                $max_coins_applicable += (absint($max_product_price_coins) * $quantity);
            }
        }
        
        // Make sure we don't use more coins than the user has
        $coins_to_use = min($balance, $max_coins_applicable);
        
        // Calculate discount amount based on conversion rate
        $discount_amount = $coins_to_use; // Convert to WC price format
        
        // Make sure discount doesn't exceed cart total
        $cart_total = $cart->get_subtotal();
        if ($discount_amount > $cart_total) {
            $discount_amount = $cart_total;
            $coins_to_use = ceil($discount_amount);
        }
        
        return array(
            'coins_used' => $coins_to_use,
            'discount' => $discount_amount
        );
    }
    
    /**
     * AJAX handler to toggle coin usage
     */
    public function ajax_toggle_use_coins() {
        check_ajax_referer('tamcoin_toggle_use', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array(
                'message' => __('شما باید وارد سایت شوید.', 'tam-coin-wallet')
            ));
        }
        
        $use_coins = isset($_POST['use_coins']) ? sanitize_text_field($_POST['use_coins']) : 'no';
        
        // Set session variable
        WC()->session->set('tamcoin_use_coins', $use_coins);
        
        // Calculate new totals
        $discount_info = $this->calculate_price_coin_discount();
        
        wp_send_json_success(array(
            'use_coins' => $use_coins,
            'coins_used' => $discount_info['coins_used'],
            'discount' => wc_price($discount_info['discount']),
            'new_total' => wc_price(WC()->cart->get_total('edit') - $discount_info['discount'])
        ));
    }
    
    /**
     * Apply coin discount to an order
     *
     * @param int $order_id Order ID
     * @return bool Whether discount was applied
     */
    public function apply_coin_discount_to_order($order_id) {
        if (WC()->session->get('tamcoin_use_coins') !== 'yes') {
            return false;
        }
        
        $discount_info = $this->calculate_price_coin_discount();
        
        if ($discount_info['coins_used'] <= 0) {
            return false;
        }
        
        $order = wc_get_order($order_id);
        $user_id = $order->get_user_id();
        
        // Add order meta
        $order->update_meta_data('_tamcoin_used', $discount_info['coins_used']);
        $order->update_meta_data('_tamcoin_discount', $discount_info['discount']);
        
        // Add order note
        $order->add_order_note(
            sprintf(
                __('%s تام کوین استفاده شده', 'tam-coin-wallet'),
                $discount_info['coins_used'],
                wc_price($discount_info['discount'])
            )
        );
        
        // Apply discount to order
        //$order->set_discount_total($order->get_discount_total('edit') + $discount_info['discount']);
        $order->set_total($order->get_total('edit'));
        $order->save();
        
        // Update user's balance via API
        $this->api->update_user_balance($user_id, $discount_info['coins_used'], $order_id);
        
        // Update local balance
        $current_balance = $this->api->get_user_balance($user_id);
        update_user_meta($user_id, '_tamcoin_balance', max(0, $current_balance - $discount_info['coins_used']));
        
        // Reset session
        WC()->session->set('tamcoin_use_coins', 'no');
        
        return true;
    }
>>>>>>> cb98cdc711d2ea53ba0421fa77901203fc804fe4
}