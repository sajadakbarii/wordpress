<<<<<<< HEAD
<?php
/**
 * Plugin Name: کیف پول تام‌کوین
 * Plugin URI: https://tamland.ir
 * Description: افزونه ووکامرس کیف پول تام کوین متصل به پنل مدرسه آنلاین تام‌لند.
 * Version: 1.0.0
 * Author: سجاد اکبری
 * Author URI: https://sajadakbari.ir
 * Text Domain: tam-coin-wallet
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * WC requires at least: 4.0
 * WC tested up to: 8.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('TAMCOIN_VERSION', '1.0.0');
define('TAMCOIN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TAMCOIN_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TAMCOIN_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * The core plugin class.
 */
class Tam_Coin_Wallet {

    /**
     * The single instance of the class.
     */
    protected static $_instance = null;

    /**
     * Main instance
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Include required files.
     */
    private function includes() {
        // Admin
        require_once TAMCOIN_PLUGIN_DIR . 'includes/admin/class-tamcoin-admin.php';
        
        // Core classes
        require_once TAMCOIN_PLUGIN_DIR . 'includes/class-tamcoin-api.php';
        require_once TAMCOIN_PLUGIN_DIR . 'includes/class-tamcoin-wallet.php';
        require_once TAMCOIN_PLUGIN_DIR . 'includes/class-tamcoin-product-fields.php';
        
        // Frontend
        require_once TAMCOIN_PLUGIN_DIR . 'includes/frontend/class-tamcoin-account-menu.php';
        //require_once TAMCOIN_PLUGIN_DIR . 'includes/frontend/class-tamcoin-account.php';
        require_once TAMCOIN_PLUGIN_DIR . 'includes/frontend/class-tamcoin-checkout.php';
        
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        // Check if WooCommerce is active
        add_action('plugins_loaded', array($this, 'check_woocommerce'));
        
        // Load textdomain
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
        
        // Register activation hook
        register_activation_hook(__FILE__, array($this, 'activate'));
        
        // Load the plugin when WooCommerce is loaded
        add_action('woocommerce_init', array($this, 'init'));
    }

    /**
     * Check if WooCommerce is active.
     */
    public function check_woocommerce() {
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
    }

    /**
     * Notice for missing WooCommerce.
     */
    public function woocommerce_missing_notice() {
        ?>
        <div class="error">
            <p><?php esc_html_e('افزونه کیف پول تام‌کوین برای نصب و فعالسازی به ووکامرس نیاز دارد.', 'tam-coin-wallet'); ?></p>
        </div>
        <?php
    }

    /**
     * Load textdomain.
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain('tam-coin-wallet', false, dirname(TAMCOIN_PLUGIN_BASENAME) . '/languages');
    }

    /**
     * Activation hook.
     */
    public function activate() {
        // Create any required database tables or options
        if (!get_option('tamcoin_conversion_rate')) {
            update_option('tamcoin_conversion_rate', 1000); // Default 1 Tam Coin = 1000 Tomans
        }
    }

    /**
     * Initialize plugin.
     */
    public function init() {
        // Initialize admin
        new Tamcoin_Admin();
        
        // Initialize product fields
        new Tamcoin_Product_Fields();
        
        // Initialize wallet
        new Tamcoin_Wallet();
        
        // Initialize account page
        //new Tamcoin_Account();
        
        // Initialize Account functionality
        new Tamcoin_Account_Menu();
        
        // Initialize checkout functionality
        new Tamcoin_Checkout();
    }
}

/**
 * Main instance of plugin.
 */
function Tamcoin() {
    return Tam_Coin_Wallet::instance();
}

// Global for backwards compatibility.
=======
<?php
/**
 * Plugin Name: کیف پول تام‌کوین
 * Plugin URI: https://tamland.ir
 * Description: افزونه ووکامرس کیف پول تام کوین متصل به پنل مدرسه آنلاین تام‌لند.
 * Version: 1.0.0
 * Author: سجاد اکبری
 * Author URI: https://sajadakbari.ir
 * Text Domain: tam-coin-wallet
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * WC requires at least: 4.0
 * WC tested up to: 8.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('TAMCOIN_VERSION', '1.0.0');
define('TAMCOIN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TAMCOIN_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TAMCOIN_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * The core plugin class.
 */
class Tam_Coin_Wallet {

    /**
     * The single instance of the class.
     */
    protected static $_instance = null;

    /**
     * Main instance
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Include required files.
     */
    private function includes() {
        // Admin
        require_once TAMCOIN_PLUGIN_DIR . 'includes/admin/class-tamcoin-admin.php';
        
        // Core classes
        require_once TAMCOIN_PLUGIN_DIR . 'includes/class-tamcoin-api.php';
        require_once TAMCOIN_PLUGIN_DIR . 'includes/class-tamcoin-wallet.php';
        require_once TAMCOIN_PLUGIN_DIR . 'includes/class-tamcoin-product-fields.php';
        
        // Frontend
        require_once TAMCOIN_PLUGIN_DIR . 'includes/frontend/class-tamcoin-account-menu.php';
        //require_once TAMCOIN_PLUGIN_DIR . 'includes/frontend/class-tamcoin-account.php';
        require_once TAMCOIN_PLUGIN_DIR . 'includes/frontend/class-tamcoin-checkout.php';
        
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        // Check if WooCommerce is active
        add_action('plugins_loaded', array($this, 'check_woocommerce'));
        
        // Load textdomain
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
        
        // Register activation hook
        register_activation_hook(__FILE__, array($this, 'activate'));
        
        // Load the plugin when WooCommerce is loaded
        add_action('woocommerce_init', array($this, 'init'));
    }

    /**
     * Check if WooCommerce is active.
     */
    public function check_woocommerce() {
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
    }

    /**
     * Notice for missing WooCommerce.
     */
    public function woocommerce_missing_notice() {
        ?>
        <div class="error">
            <p><?php esc_html_e('افزونه کیف پول تام‌کوین برای نصب و فعالسازی به ووکامرس نیاز دارد.', 'tam-coin-wallet'); ?></p>
        </div>
        <?php
    }

    /**
     * Load textdomain.
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain('tam-coin-wallet', false, dirname(TAMCOIN_PLUGIN_BASENAME) . '/languages');
    }

    /**
     * Activation hook.
     */
    public function activate() {
        // Create any required database tables or options
        if (!get_option('tamcoin_conversion_rate')) {
            update_option('tamcoin_conversion_rate', 1000); // Default 1 Tam Coin = 1000 Tomans
        }
    }

    /**
     * Initialize plugin.
     */
    public function init() {
        // Initialize admin
        new Tamcoin_Admin();
        
        // Initialize product fields
        new Tamcoin_Product_Fields();
        
        // Initialize wallet
        new Tamcoin_Wallet();
        
        // Initialize account page
        //new Tamcoin_Account();
        
        // Initialize Account functionality
        new Tamcoin_Account_Menu();
        
        // Initialize checkout functionality
        new Tamcoin_Checkout();
    }
}

/**
 * Main instance of plugin.
 */
function Tamcoin() {
    return Tam_Coin_Wallet::instance();
}

// Global for backwards compatibility.
>>>>>>> cb98cdc711d2ea53ba0421fa77901203fc804fe4
$GLOBALS['tamcoin'] = Tamcoin();