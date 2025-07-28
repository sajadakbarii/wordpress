jQuery(document).ready(function($) {
    function bindCouponForms() {
        $('form.woocommerce-coupon-form, form.woocommerce-form-coupon').off('submit').on('submit', function(e) {
            e.preventDefault();

            let $form = $(this);
            let $input = $form.find('#coupon_code');
            let coupon = $input.val();

            // حذف پیام‌های قبلی
            $('.woocommerce-error, .woocommerce-message').remove();

            $.ajax({
                url: tamyar_coupon_data.ajax_url,
                type: 'POST',
                data: {
                    action: 'validate_coupon_via_api',
                    coupon_code: coupon
                },
                success: function(response) {
                    if (response.success) {
                        // جلوی ووکامرس رو بگیر که دوباره این کد رو ارسال نکنه
                        $input.val('tamyar_dummy_code'); // مقدار نامعتبر که باعث بشه ووکامرس دیگه پردازش نکنه
                        location.reload();
                    } else {
                        const errorHtml = '<ul class="woocommerce-error"><li>' + response.data.message + '</li></ul>';
                        $form.before(errorHtml);
                    }
                },
                error: function() {
                    const errorHtml = '<ul class="woocommerce-error"><li>خطای عمومی در بررسی کد تخفیف</li></ul>';
                    $form.before(errorHtml);
                }
            });
        });
    }

    bindCouponForms();

    $(document.body).on('updated_checkout updated_wc_div', function() {
        bindCouponForms();
    });
});
