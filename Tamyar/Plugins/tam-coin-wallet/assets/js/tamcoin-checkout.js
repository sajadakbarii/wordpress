jQuery(function($) {
                    $('#tamcoin-use-coins').on('change', function() {
                        let isChecked = $(this).is(':checked');
                        const useCoins = $(this).is(':checked') ? 'yes' : 'no';
                        let totalCoinsUsed = 0;
                        
                        let totalDiscount = 0;
                
                        // دریافت نرخ تبدیل
                        let conversionRate = parseInt($('#tamcoin-conversion-rate').val() || 10000);
                        // دریافت موجودی کل تام‌کوین از عنصر HTML (باید در HTML گذاشته شود)
                        let userCoinBalance = parseInt($('#tamcoin-user-balance').val() || 0);
                        totalCoinsUsed = $('#tamcoin-max-usable').val();
                        // اگر تام‌کوین مصرفی بیشتر از موجودی بود، محدودش کنیم
                        if (totalCoinsUsed > userCoinBalance) {
                            totalCoinsUsed = userCoinBalance;
                        }
                
                        // محاسبه تومان تخفیف
                        totalDiscount = totalCoinsUsed * conversionRate;
                        // نمایش یا مخفی کردن اطلاعات تخفیف
                        if (isChecked) {
                            $('.tamcoin-discount-info').show().html(
                                //`<p>${totalCoinsUsed} تام‌کوین استفاده شده (${totalDiscount.toLocaleString()} تومان تخفیف)</p>`
                                `<p>${totalCoinsUsed} استفاده شده</p>`
                            );
                        } else {
                            $('.tamcoin-discount-info').hide();
                        }
                
                        // اینجا می‌توانید Ajax هم ارسال کنید اگر نیاز به ذخیره یا بازخوانی سمت سرور دارید
                        $.ajax({
                            url: tamcoin_params.ajax_url,
                            type: 'POST',
                            data: {
                                action: 'tamcoin_toggle_use',
                                use_coins: useCoins,
                            },
                            success: function (response) {
                                if(response.success){
                                    if ($('body').hasClass('woocommerce-cart')) {
                                        $('body').trigger('wc_update_cart'); // cart fragments update
                                        location.reload(); // ← در سبد خرید
                                    } else {
                                        $('body').trigger('update_checkout'); // ← در تسویه حساب
                                    }
                                }
                            }
                        });
                    });
                
            });