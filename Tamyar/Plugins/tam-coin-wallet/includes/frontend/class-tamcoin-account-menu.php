<<<<<<< HEAD
<?php
/**
 * Add Tamcoin Wallet menu to My Account page
 */
class Tamcoin_Account_Menu {

    /**
     * Constructor
     */
    public function __construct() {
        // Add menu item to WooCommerce My Account page
        add_filter('woocommerce_account_menu_items', array($this, 'add_tamcoin_menu_item'));
        
        // Add endpoint for the menu item
        add_action('init', array($this, 'add_tamcoin_endpoint'));
        
        // Add content for the menu item page
        add_action('woocommerce_account_tamcoin-wallet_endpoint', array($this, 'tamcoin_wallet_content'));
        
        // Flush rewrite rules on plugin activation
        register_activation_hook(TAMCOIN_PLUGIN_BASENAME, array($this, 'flush_rewrite_rules'));
    }

    /**
     * Add menu item to WooCommerce My Account menu
     *
     * @param array $items Menu items
     * @return array Modified menu items
     */
    public function add_tamcoin_menu_item($items) {
        // Insert our custom item before the logout menu item
        $logout_item = false;
        
        if (isset($items['customer-logout'])) {
            $logout_item = $items['customer-logout'];
            unset($items['customer-logout']);
        }
        
        // Add our menu item
        $items['tamcoin-wallet'] = __('کیف پول تام‌کوین', 'tam-coin-wallet');
        
        // Re-add logout to the end if it was there
        if ($logout_item) {
            $items['customer-logout'] = $logout_item;
        }
        
        return $items;
    }

    /**
     * Add endpoint for the menu item
     */
    public function add_tamcoin_endpoint() {
        add_rewrite_endpoint('tamcoin-wallet', EP_ROOT | EP_PAGES);
    }

    /**
     * Flush rewrite rules on plugin activation
     */
    public function flush_rewrite_rules() {
        $this->add_tamcoin_endpoint();
        flush_rewrite_rules();
    }

    /**
     * Content for the wallet page
     */
    public function tamcoin_wallet_content() {
        // Get user ID
        $user_id = get_current_user_id();
        
        // Get user's tamcoin balance
        $tamcoin_api = new Tamcoin_API();
        $balance = $tamcoin_api->get_user_balance($user_id);
        
        // Get the conversion rate
        $conversion_rate = get_option('tamcoin_conversion_rate', 10000);
        
        // Calculate toman value
        $toman_value = is_wp_error($balance) ? 0 : intval($balance * $conversion_rate);
        
        // Output the wallet content
        ?>
        <div class="tamcoin-wallet-container">
            <h2><?php esc_html_e('کیف پول تام‌کوین', 'tam-coin-wallet'); ?></h2>
            
            <?php if (is_wp_error($balance)): ?>
                <div class="tamcoin-error">
                    <p><?php echo esc_html($balance->get_error_message()); ?></p>
                </div>
            <?php else: ?>
                <div class="tamcoin-balance-container">
                    <!--<div class="tamcoin-balance">
                        <span class="tamcoin-balance-label"><?php //esc_html_e('موجودی کیف پول:', 'tam-coin-wallet'); ?></span>
                        <span class="tamcoin-balance-value"><?php //echo esc_html($balance); ?></span>
                        <span class="tamcoin-balance-unit"><?php //esc_html_e('تام‌کوین', 'tam-coin-wallet'); ?></span>
                    </div>-->
                    
                    <div class="tamcoin-toman-value">
                        <span class="tamcoin-balance-label"><?php esc_html_e('موجودی کیف پول:', 'tam-coin-wallet'); ?></span>
                        <!--<span class="tamcoin-toman-label"><?php //esc_html_e('معادل به تومان:', 'tam-coin-wallet'); ?></span>-->
                        <span class="tamcoin-balance-value"><?php echo number_format($toman_value); ?></span>
                        <span class="tamcoin-toman-unit"><?php esc_html_e('ریال', 'tam-coin-wallet'); ?></span>
                    </div>
                </div>
                
                <div class="tamcoin-info">
                    <p><?php esc_html_e('شما می‌توانید از تام‌کوین‌های خود برای خرید محصولات استفاده کنید.', 'tam-coin-wallet'); ?></p>
                </div>
                
                <!--<div class="tamcoin-transaction-history">
                    <h3><?php //esc_html_e('تاریخچه تراکنش‌ها', 'tam-coin-wallet'); ?></h3>
                    <?php //$this->display_transaction_history($user_id); ?>
                </div>-->
            <?php endif; ?>
        </div>
        <?php
        
        // Add some CSS styles
        $this->add_wallet_styles();
    }
    
    /**
     * Display transaction history
     *
     * @param int $user_id User ID
     */
    private function display_transaction_history($user_id) {
        // This is a placeholder function. In a real implementation, you would
        // fetch the transaction history from your API or database.
        
        // Example implementation:
        $transactions = array(
            // Example data - replace with real data from your API
            
            array(
                'date' => '1402/12/10',
                'type' => 'purchase',
                'amount' => -50,
                'description' => 'خرید محصول: کتاب آموزشی'
            ),
            array(
                'date' => '1402/12/01',
                'type' => 'deposit',
                'amount' => 100,
                'description' => 'شارژ کیف پول'
            )
        
        );
        
        if (empty($transactions)) {
            echo '<p class="no-transactions">' . esc_html__('هیچ تراکنشی یافت نشد.', 'tam-coin-wallet') . '</p>';
            return;
        }
        
        ?>
        <table class="tamcoin-transactions">
            <thead>
                <tr>
                    <th><?php esc_html_e('تاریخ', 'tam-coin-wallet'); ?></th>
                    <th><?php esc_html_e('توضیحات', 'tam-coin-wallet'); ?></th>
                    <th><?php esc_html_e('مقدار', 'tam-coin-wallet'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                <tr class="tamcoin-transaction <?php echo esc_attr($transaction['type']); ?>">
                    <td><?php echo esc_html($transaction['date']); ?></td>
                    <td><?php echo esc_html($transaction['description']); ?></td>
                    <td class="amount <?php echo $transaction['amount'] >= 0 ? 'positive' : 'negative'; ?>">
                        <?php echo sprintf('%+d', $transaction['amount']); ?> <?php esc_html_e('تام‌کوین', 'tam-coin-wallet'); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
    
    /**
     * Add styles for the wallet page
     */
    private function add_wallet_styles() {
        ?>
        <style>
            .tamcoin-wallet-container {
                padding: 20px;
                background-color: #f8f8f8;
                border-radius: 5px;
                margin-bottom: 20px;
            }
            
            .tamcoin-balance-container {
                background-color: #fff;
                padding: 15px;
                border-radius: 5px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.05);
                margin-bottom: 20px;
            }
            
            .tamcoin-balance {
                font-size: 18px;
                margin-bottom: 10px;
            }
            
            .tamcoin-balance-value {
                font-weight: bold;
                font-size: 24px;
                color: #2271b1;
                margin: 0 5px;
            }
            
            .tamcoin-toman-value {
                color: #666;
            }
            
            .tamcoin-toman-value .tamcoin-toman-value {
                font-weight: bold;
                margin: 0 5px;
            }
            
            .tamcoin-transaction-history {
                margin-top: 30px;
            }
            
            .tamcoin-transactions {
                width: 100%;
                border-collapse: collapse;
            }
            
            .tamcoin-transactions th, 
            .tamcoin-transactions td {
                padding: 10px;
                text-align: right;
                border-bottom: 1px solid #eee;
            }
            
            .tamcoin-transactions th {
                background-color: #f1f1f1;
            }
            
            .tamcoin-transaction.purchase .amount {
                color: #d63638;
            }
            
            .tamcoin-transaction.deposit .amount {
                color: #00a32a;
            }
            
            .no-transactions {
                color: #666;
                font-style: italic;
            }
            
            .tamcoin-info {
                background-color: #e7f5fa;
                padding: 10px 15px;
                border-right: 4px solid #2271b1;
                margin-bottom: 20px;
            }
            
            .tamcoin-error {
                background-color: #fcf0f1;
                padding: 10px 15px;
                border-right: 4px solid #d63638;
                color: #d63638;
            }
            
            .amount.positive {
                color: #00a32a;
            }
            
            .amount.negative {
                color: #d63638;
            }
        </style>
        <?php
    }
=======
<?php
/**
 * Add Tamcoin Wallet menu to My Account page
 */
class Tamcoin_Account_Menu {

    /**
     * Constructor
     */
    public function __construct() {
        // Add menu item to WooCommerce My Account page
        add_filter('woocommerce_account_menu_items', array($this, 'add_tamcoin_menu_item'));
        
        // Add endpoint for the menu item
        add_action('init', array($this, 'add_tamcoin_endpoint'));
        
        // Add content for the menu item page
        add_action('woocommerce_account_tamcoin-wallet_endpoint', array($this, 'tamcoin_wallet_content'));
        
        // Flush rewrite rules on plugin activation
        register_activation_hook(TAMCOIN_PLUGIN_BASENAME, array($this, 'flush_rewrite_rules'));
    }

    /**
     * Add menu item to WooCommerce My Account menu
     *
     * @param array $items Menu items
     * @return array Modified menu items
     */
    public function add_tamcoin_menu_item($items) {
        // Insert our custom item before the logout menu item
        $logout_item = false;
        
        if (isset($items['customer-logout'])) {
            $logout_item = $items['customer-logout'];
            unset($items['customer-logout']);
        }
        
        // Add our menu item
        $items['tamcoin-wallet'] = __('کیف پول تام‌کوین', 'tam-coin-wallet');
        
        // Re-add logout to the end if it was there
        if ($logout_item) {
            $items['customer-logout'] = $logout_item;
        }
        
        return $items;
    }

    /**
     * Add endpoint for the menu item
     */
    public function add_tamcoin_endpoint() {
        add_rewrite_endpoint('tamcoin-wallet', EP_ROOT | EP_PAGES);
    }

    /**
     * Flush rewrite rules on plugin activation
     */
    public function flush_rewrite_rules() {
        $this->add_tamcoin_endpoint();
        flush_rewrite_rules();
    }

    /**
     * Content for the wallet page
     */
    public function tamcoin_wallet_content() {
        // Get user ID
        $user_id = get_current_user_id();
        
        // Get user's tamcoin balance
        $tamcoin_api = new Tamcoin_API();
        $balance = $tamcoin_api->get_user_balance($user_id);
        
        // Get the conversion rate
        $conversion_rate = get_option('tamcoin_conversion_rate', 10000);
        
        // Calculate toman value
        $toman_value = is_wp_error($balance) ? 0 : intval($balance * $conversion_rate);
        
        // Output the wallet content
        ?>
        <div class="tamcoin-wallet-container">
            <h2><?php esc_html_e('کیف پول تام‌کوین', 'tam-coin-wallet'); ?></h2>
            
            <?php if (is_wp_error($balance)): ?>
                <div class="tamcoin-error">
                    <p><?php echo esc_html($balance->get_error_message()); ?></p>
                </div>
            <?php else: ?>
                <div class="tamcoin-balance-container">
                    <!--<div class="tamcoin-balance">
                        <span class="tamcoin-balance-label"><?php //esc_html_e('موجودی کیف پول:', 'tam-coin-wallet'); ?></span>
                        <span class="tamcoin-balance-value"><?php //echo esc_html($balance); ?></span>
                        <span class="tamcoin-balance-unit"><?php //esc_html_e('تام‌کوین', 'tam-coin-wallet'); ?></span>
                    </div>-->
                    
                    <div class="tamcoin-toman-value">
                        <span class="tamcoin-balance-label"><?php esc_html_e('موجودی کیف پول:', 'tam-coin-wallet'); ?></span>
                        <!--<span class="tamcoin-toman-label"><?php //esc_html_e('معادل به تومان:', 'tam-coin-wallet'); ?></span>-->
                        <span class="tamcoin-balance-value"><?php echo number_format($toman_value); ?></span>
                        <span class="tamcoin-toman-unit"><?php esc_html_e('ریال', 'tam-coin-wallet'); ?></span>
                    </div>
                </div>
                
                <div class="tamcoin-info">
                    <p><?php esc_html_e('شما می‌توانید از تام‌کوین‌های خود برای خرید محصولات استفاده کنید.', 'tam-coin-wallet'); ?></p>
                </div>
                
                <!--<div class="tamcoin-transaction-history">
                    <h3><?php //esc_html_e('تاریخچه تراکنش‌ها', 'tam-coin-wallet'); ?></h3>
                    <?php //$this->display_transaction_history($user_id); ?>
                </div>-->
            <?php endif; ?>
        </div>
        <?php
        
        // Add some CSS styles
        $this->add_wallet_styles();
    }
    
    /**
     * Display transaction history
     *
     * @param int $user_id User ID
     */
    private function display_transaction_history($user_id) {
        // This is a placeholder function. In a real implementation, you would
        // fetch the transaction history from your API or database.
        
        // Example implementation:
        $transactions = array(
            // Example data - replace with real data from your API
            
            array(
                'date' => '1402/12/10',
                'type' => 'purchase',
                'amount' => -50,
                'description' => 'خرید محصول: کتاب آموزشی'
            ),
            array(
                'date' => '1402/12/01',
                'type' => 'deposit',
                'amount' => 100,
                'description' => 'شارژ کیف پول'
            )
        
        );
        
        if (empty($transactions)) {
            echo '<p class="no-transactions">' . esc_html__('هیچ تراکنشی یافت نشد.', 'tam-coin-wallet') . '</p>';
            return;
        }
        
        ?>
        <table class="tamcoin-transactions">
            <thead>
                <tr>
                    <th><?php esc_html_e('تاریخ', 'tam-coin-wallet'); ?></th>
                    <th><?php esc_html_e('توضیحات', 'tam-coin-wallet'); ?></th>
                    <th><?php esc_html_e('مقدار', 'tam-coin-wallet'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                <tr class="tamcoin-transaction <?php echo esc_attr($transaction['type']); ?>">
                    <td><?php echo esc_html($transaction['date']); ?></td>
                    <td><?php echo esc_html($transaction['description']); ?></td>
                    <td class="amount <?php echo $transaction['amount'] >= 0 ? 'positive' : 'negative'; ?>">
                        <?php echo sprintf('%+d', $transaction['amount']); ?> <?php esc_html_e('تام‌کوین', 'tam-coin-wallet'); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
    
    /**
     * Add styles for the wallet page
     */
    private function add_wallet_styles() {
        ?>
        <style>
            .tamcoin-wallet-container {
                padding: 20px;
                background-color: #f8f8f8;
                border-radius: 5px;
                margin-bottom: 20px;
            }
            
            .tamcoin-balance-container {
                background-color: #fff;
                padding: 15px;
                border-radius: 5px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.05);
                margin-bottom: 20px;
            }
            
            .tamcoin-balance {
                font-size: 18px;
                margin-bottom: 10px;
            }
            
            .tamcoin-balance-value {
                font-weight: bold;
                font-size: 24px;
                color: #2271b1;
                margin: 0 5px;
            }
            
            .tamcoin-toman-value {
                color: #666;
            }
            
            .tamcoin-toman-value .tamcoin-toman-value {
                font-weight: bold;
                margin: 0 5px;
            }
            
            .tamcoin-transaction-history {
                margin-top: 30px;
            }
            
            .tamcoin-transactions {
                width: 100%;
                border-collapse: collapse;
            }
            
            .tamcoin-transactions th, 
            .tamcoin-transactions td {
                padding: 10px;
                text-align: right;
                border-bottom: 1px solid #eee;
            }
            
            .tamcoin-transactions th {
                background-color: #f1f1f1;
            }
            
            .tamcoin-transaction.purchase .amount {
                color: #d63638;
            }
            
            .tamcoin-transaction.deposit .amount {
                color: #00a32a;
            }
            
            .no-transactions {
                color: #666;
                font-style: italic;
            }
            
            .tamcoin-info {
                background-color: #e7f5fa;
                padding: 10px 15px;
                border-right: 4px solid #2271b1;
                margin-bottom: 20px;
            }
            
            .tamcoin-error {
                background-color: #fcf0f1;
                padding: 10px 15px;
                border-right: 4px solid #d63638;
                color: #d63638;
            }
            
            .amount.positive {
                color: #00a32a;
            }
            
            .amount.negative {
                color: #d63638;
            }
        </style>
        <?php
    }
>>>>>>> cb98cdc711d2ea53ba0421fa77901203fc804fe4
}