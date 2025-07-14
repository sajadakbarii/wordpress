<<<<<<< HEAD
<?php
/**
 * Tam Coin Product Fields
 */
if (!defined('WPINC')) {
    die;
}

class Tamcoin_Product_Fields {
    
    public function __construct() {
        add_filter('woocommerce_product_data_tabs', array($this, 'add_product_data_tab'));
        add_action('woocommerce_product_data_panels', array($this, 'add_product_data_fields'));
        add_action('woocommerce_admin_process_product_object', array($this, 'save_product_meta_fields'));
    }
    
    public function add_product_data_tab($tabs) {
        $tabs['tamcoin'] = array(
            'label'    => __('تام‌کوین', 'tam-coin-wallet'),
            'target'   => 'tamcoin_product_data',
            'class'    => array(),
            'priority' => 90
        );
        return $tabs;
    }
    
    public function add_product_data_fields() {
        global $post;
        ?>
        <div id="tamcoin_product_data" class="panel woocommerce_options_panel">
            <div class="options_group">
                <?php
                // Max Tam Coins field
                woocommerce_wp_text_input(array(
                    'id'                => 'max_tamcoins_required',
                    'label'             => __('حداکثر تام‌کوین مجاز', 'tam-coin-wallet'),
                    'description'       => __('حداکثر تام‌کوینی که کاربر برای این محصول می‌تواند استفاده کند.', 'tam-coin-wallet'),
                    'desc_tip'          => true,
                    'type'              => 'number',
                    'custom_attributes' => array(
                        'step' => '1',
                        'min'  => '0'
                    )
                ));
                
                // Max Tam Coin (by amount in Rial)
                woocommerce_wp_text_input(array(
                    'id'                => 'max_prctamcoins_required',
                    'label'             => __('حداکثر مبلغ تام‌کوین قابل استفاده (ریال)', 'tam-coin-wallet'),
                    'description'       => __('حداکثر مبلغ تام‌کوینی که می‌توان برای این محصول به ریال استفاده کرد.', 'tam-coin-wallet'),
                    'desc_tip'          => true,
                    'type'              => 'number',
                    'custom_attributes' => array(
                        'step' => '1',
                        'min'  => '0'
                    )
                ));

                ?>
            </div>
        </div>
        <?php
    }
    
    public function save_product_meta_fields($product) {
        if (isset($_POST['max_tamcoins_required'])) {
            $max_coins = absint($_POST['max_tamcoins_required']);
            $product->update_meta_data('max_tamcoins_required', $max_coins);
        }

        if (isset($_POST['max_prctamcoins_required'])) {
            $max_prc = absint($_POST['max_prctamcoins_required']);
            $product->update_meta_data('max_prctamcoins_required', $max_prc);
        }
    }
}
=======
<?php
/**
 * Tam Coin Product Fields
 */
if (!defined('WPINC')) {
    die;
}

class Tamcoin_Product_Fields {
    
    public function __construct() {
        add_filter('woocommerce_product_data_tabs', array($this, 'add_product_data_tab'));
        add_action('woocommerce_product_data_panels', array($this, 'add_product_data_fields'));
        add_action('woocommerce_admin_process_product_object', array($this, 'save_product_meta_fields'));
    }
    
    public function add_product_data_tab($tabs) {
        $tabs['tamcoin'] = array(
            'label'    => __('تام‌کوین', 'tam-coin-wallet'),
            'target'   => 'tamcoin_product_data',
            'class'    => array(),
            'priority' => 90
        );
        return $tabs;
    }
    
    public function add_product_data_fields() {
        global $post;
        ?>
        <div id="tamcoin_product_data" class="panel woocommerce_options_panel">
            <div class="options_group">
                <?php
                // Max Tam Coins field
                woocommerce_wp_text_input(array(
                    'id'                => 'max_tamcoins_required',
                    'label'             => __('حداکثر تام‌کوین مجاز', 'tam-coin-wallet'),
                    'description'       => __('حداکثر تام‌کوینی که کاربر برای این محصول می‌تواند استفاده کند.', 'tam-coin-wallet'),
                    'desc_tip'          => true,
                    'type'              => 'number',
                    'custom_attributes' => array(
                        'step' => '1',
                        'min'  => '0'
                    )
                ));
                
                // Max Tam Coin (by amount in Rial)
                woocommerce_wp_text_input(array(
                    'id'                => 'max_prctamcoins_required',
                    'label'             => __('حداکثر مبلغ تام‌کوین قابل استفاده (ریال)', 'tam-coin-wallet'),
                    'description'       => __('حداکثر مبلغ تام‌کوینی که می‌توان برای این محصول به ریال استفاده کرد.', 'tam-coin-wallet'),
                    'desc_tip'          => true,
                    'type'              => 'number',
                    'custom_attributes' => array(
                        'step' => '1',
                        'min'  => '0'
                    )
                ));

                ?>
            </div>
        </div>
        <?php
    }
    
    public function save_product_meta_fields($product) {
        if (isset($_POST['max_tamcoins_required'])) {
            $max_coins = absint($_POST['max_tamcoins_required']);
            $product->update_meta_data('max_tamcoins_required', $max_coins);
        }

        if (isset($_POST['max_prctamcoins_required'])) {
            $max_prc = absint($_POST['max_prctamcoins_required']);
            $product->update_meta_data('max_prctamcoins_required', $max_prc);
        }
    }
}
>>>>>>> cb98cdc711d2ea53ba0421fa77901203fc804fe4
