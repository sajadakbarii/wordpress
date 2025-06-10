<?php
/**
 * Tam Coin Admin
 */
if (!defined('WPINC')) {
    die;
}

/**
 * Class for handling admin-specific functionality
 */
class Tamcoin_Admin {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Register settings
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __('تنظیمات تام کوین', 'tam-coin-wallet'),
            __('تام کوین', 'tam-coin-wallet'),
            'manage_woocommerce',
            'tamcoin-settings',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('tamcoin_settings', 'tamcoin_conversion_rate', 'absint');
        
        add_settings_section(
            'tamcoin_general_settings',
            __('تنظیمات عمومی', 'tam-coin-wallet'),
            array($this, 'general_settings_section_callback'),
            'tamcoin_settings'
        );
        
        add_settings_field(
            'tamcoin_conversion_rate',
            __('ارزش ریالی هر تام کوین', 'tam-coin-wallet'),
            array($this, 'conversion_rate_callback'),
            'tamcoin_settings',
            'tamcoin_general_settings'
        );
    }
    
    /**
     * General settings section callback
     */
    public function general_settings_section_callback() {
        echo '<p>' . esc_html__('تنظیمات تام کوین را در بخش زیر انجام دهید.', 'tam-coin-wallet') . '</p>';
    }
    
    /**
     * Conversion rate field callback
     */
    public function conversion_rate_callback() {
        $value = get_option('tamcoin_conversion_rate', 10000);
        ?>
        <input type="hidden" name="tamcoin_conversion_rate" value="<?php echo esc_attr($value); ?>" min="1" step="1" />
        <p class="description">
            <?php esc_html_e('در حال حاضر معادل 10,000 ریال است', 'tam-coin-wallet'); ?>
        </p>
        <?php
    }
    
    /**
     * Settings page content
     */
    public function settings_page() {
        // Check user capabilities
        if (!current_user_can('manage_woocommerce')) {
            return;
        }
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('tamcoin_settings');
                do_settings_sections('tamcoin_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}