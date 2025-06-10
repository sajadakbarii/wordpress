/**
 * Tam Coin Account JavaScript
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Handle refresh balance button
        $('#tamcoin-refresh-balance').on('click', function() {
            const $button = $(this);
            const originalText = $button.text();
            
            $button.prop('disabled', true).text(tamcoinAccount.i18n.refreshing);
            
            $.ajax({
                url: tamcoinAccount.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'tamcoin_refresh_balance',
                    nonce: tamcoinAccount.refreshNonce
                },
                success: function(response) {
                    if (response.success) {
                        // Update balance display
                        $('.tamcoin-balance').text(response.data.formatted_balance);
                        $('.tamcoin-balance-value').text(
                            'Value: ' + response.data.balance // TODO: Convert to localized price
                        );
                        
                        // Show success message
                        alert(tamcoinAccount.i18n.refreshSuccess);
                    } else {
                        // Show error message
                        alert(response.data.message || tamcoinAccount.i18n.refreshError);
                    }
                },
                error: function() {
                    alert(tamcoinAccount.i18n.refreshError);
                },
                complete: function() {
                    $button.prop('disabled', false).text(originalText);
                }
            });
        });
    });
})(jQuery);