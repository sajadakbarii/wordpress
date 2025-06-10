<?php
/**
 * Tam Coin Checkout functionality
 */
if (!defined('WPINC')) {
    die;
}

/**
 * Class for handling checkout functionality
 */
class Tamcoin_Checkout {
    
    /**
     * Wallet instance
     */
    private $wallet;
    
    /**
     * API instance
     */
    private $api;
    
    /**
     * Constructor
     */
    public function __construct() {
        
        $this->wallet = new Tamcoin_Wallet();
        $this->api = new Tamcoin_API();

        // Add toggle to cart and checkout
        //add_action('woocommerce_before_cart_totals', array($this, 'display_coin_toggle'));
        add_action('woocommerce_checkout_before_customer_details', array($this, 'display_coin_toggle'), 10);
        
        // Add discount to cart
        //add_action('woocommerce_cart_calculate_fees', array($this, 'add_coin_discount'));
        add_action('woocommerce_cart_calculate_fees', array($this, 'apply_tamcoin_discount'), 20, 1);

        
        // Apply discount to order
        add_action('woocommerce_checkout_order_processed', array($this, 'apply_coin_discount_to_order'), 10);
        
        add_action('wp_ajax_tamcoin_toggle_use', array($this, 'handle_toggle_use_coins'));
        add_action('wp_ajax_nopriv_tamcoin_toggle_use', '__return_false'); // فقط کاربران واردشده

        // Enqueue scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    public function display_coin_toggle() {
        if (!is_user_logged_in()) return;
        
        $user_id = get_current_user_id();

        $balance_response = $this->api->get_user_balance($user_id);

        if (is_wp_error($balance_response)) {
            error_log('خطا در دریافت موجودی: ' . $balance_response->get_error_message());
            $balance = 0;
        } else {
            $balance = (int) $balance_response;
        }

        //error_log($balance);
        
        if ($balance <= 0) return;
        
        $max_usable = $this->get_max_tamcoins_from_cart();
        $discount_info = $this->wallet->calculate_price_coin_discount();
        $use_coins = WC()->session->get('tamcoin_use_coins') === 'yes';
        $conversion_rate = get_option('tamcoin_conversion_rate', 10000);
        $toman_value = is_wp_error($balance) ? 0 : intval($balance * $conversion_rate);
        ?>
        <div class="tamcoin-toggle-container">
            <h4><?php esc_html_e('کیف پول تام‌کوین', 'tam-coin-wallet'); ?></h4>
            <p class="tamcoin-balance-info">
                <?php 
                printf(
                    __('موجودی: %s ریال', 'tam-coin-wallet'),
                    wc_price($toman_value),
                    wc_price($toman_value)
                ); 
                ?>
            </p>
            <div class="tamcoin-toggle">
                <label for="tamcoin-use-coins">
                    <span class="switch">
                        <input type="checkbox" id="tamcoin-use-coins" name="tamcoin_use_coins" <?php checked($use_coins); ?>>
                        <span class="slider round"></span>
                    </span>
                    <?php esc_html_e('از کیف پول تام‌کوین استفاده میکنم', 'tam-coin-wallet'); ?>
                </label>
                <input type="hidden" id="tamcoin-conversion-rate" value="<?php echo esc_attr($conversion_rate); ?>">
                <input type="hidden" id="tamcoin-user-balance" value="<?php echo esc_attr($balance); ?>">
            </div>

            <div class="tamcoin-discount-info" <?php echo $use_coins ? '' : 'style="display:none;"'; ?>>
                <p>
                    <?php 
                    printf(
                        __('%s ریال استفاده شده','tam-coin-wallet'),
                        wc_price($discount_info['discount']),
                        wc_price($discount_info['discount'])
                    ); 
                    ?>
                </p>
            </div>
            <div>
                <p class="tamcoin-max-usage-info">
                    <?php 
                    printf(
                        __('حداکثر تام‌کوین قابل استفاده در این خرید: %s', 'tam-coin-wallet'),
                        $this->wallet->format_coin_amount($max_usable)
                    );
                    ?>
                </p>
                <input type="hidden" id="tamcoin-max-usable" value="<?php echo esc_attr($this->wallet->format_coin_amount($max_usable)); ?>">
            </div>
        </div>
        <?php
    }

    public function add_coin_discount($cart) {
        if (!is_user_logged_in() || WC()->session->get('tamcoin_use_coins') !== 'yes') return;

        $discount_info = $this->wallet->calculate_price_coin_discount();
        
        if ($discount_info['discount'] > 0) {
            $cart->add_fee(
                sprintf(__('تام‌کوین استفاده‌شده: %s', 'tam-coin-wallet'), $this->wallet->format_coin_amount($discount_info['coins_used'])),
                -$discount_info['discount']
            );
        }
    }
    
    function apply_tamcoin_discount($cart) {
        if (is_admin() && !defined('DOING_AJAX')) return;
        if (did_action('woocommerce_before_calculate_totals') >= 2) return; // برای جلوگیری از تکرار
        if (!is_object($cart)) return;
        
        // مقدار تام‌کوین قابل استفاده (مثلاً محاسبه‌شده قبلاً)
        $discount_info = $this->wallet->calculate_price_coin_discount();
        //error_log('discount: '.print_r($discount_info, true));
        // چک کن کاربر تیک زده یا نه
        $use_coins = WC()->session->get('tamcoin_use_coins');
        
        if ($use_coins === 'yes') {
            $cart->add_fee('تخفیف تام‌کوین', -$discount_info['discount'], true, '');
        }
    }

    public function apply_coin_discount_to_order($order_id) {
        $this->wallet->apply_coin_discount_to_order($order_id);
    }
    
    public function get_max_tamcoins_from_cart() {
        $total_max_tamcoins = 0;
    
        foreach (WC()->cart->get_cart() as $cart_item) {
            $product_id = $cart_item['product_id'];
            $quantity = $cart_item['quantity'];
    
            // مقدار max_prctamcoins_required برای محصول
            $max_tamcoins = (int) get_post_meta($product_id, 'max_prctamcoins_required', true);
    
            // ضرب در تعداد محصول
            $total_max_tamcoins += $max_tamcoins * $quantity;
        }
    
        return $total_max_tamcoins;
    }
    
    public function handle_toggle_use_coins() {
        if (!is_user_logged_in()) wp_send_json_error('user not logged in');
    
        $use_coins = isset($_POST['use_coins']) && $_POST['use_coins'] === 'yes' ? 'yes' : 'no';
        WC()->session->set('tamcoin_use_coins', $use_coins);
        
        wp_send_json_success();
    }

    public function enqueue_scripts() {
        if (!is_cart() && !is_checkout()) return;

        wp_enqueue_style(
            'tamcoin-checkout-style',
            TAMCOIN_PLUGIN_URL . 'assets/css/tamcoin-checkout.css',
            array(),
            '1.2.9'
        );
        
        wp_enqueue_script(
            'tamcoin-checkout-script',
            TAMCOIN_PLUGIN_URL . 'assets/js/tamcoin-checkout.js',
            array('jquery'),
            '1.2.5',
            true
        );
        
        // ارسال آدرس Ajax به جاوااسکریپت
        wp_localize_script('tamcoin-checkout-script', 'tamcoin_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
        ));
    }
}
