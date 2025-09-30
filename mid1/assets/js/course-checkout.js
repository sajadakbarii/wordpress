jQuery(document).ready(function () {
        let discountClickCount = 0;
        let totalPrice = {
            text: "",
            number: 0,
        };
        let finalPrice = {
            text: "",
            number: 0,
        };

        const resetPrice = () => {
            finalPrice.number = totalPrice.number;
            finalPrice.text = totalPrice.text;
            jQuery('#input_4_6').val(finalPrice.text);
            jQuery('#input_4_18').val(finalPrice.number);
        };

        const applyDiscountSuccess = (discountCode, discountAmount) => {
            finalPrice.number = totalPrice.number - ((totalPrice.number * discountAmount) / 100);
            finalPrice.text = finalPrice.number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + " تومان";
            jQuery('#input_4_6').val(finalPrice.text);
            jQuery('#input_4_18').val(finalPrice.number);
            jQuery('.discount-validate-message')
                .html(`کد تخفیف <b>${discountCode}</b> با موفقیت اعمال شد<button type="button" class="del-discount-code"><i class="fa fa-times"></i></button>`)
                .removeClass('not-valid d-none')
                .addClass('valid');
           setCookie("discountIsSet", "true", 9); // Expires in 9 minutes 
        };

        const applyDiscountFailure = (message) => {
            resetPrice();
            jQuery('.discount-validate-message')
                .text(message)
                .removeClass('valid d-none')
                .addClass('not-valid');
            setCookie("discountIsSet", "false", 9); // Expires in 9 minutes 
        };

        jQuery('#apply_discount').on('click', function () {
            const mobile = jQuery('#input_4_2').val();
            const discountCode = jQuery('#input_4_20').val();
            const courseId = jQuery('#input_4_8').val();
            const courseType = jQuery('#input_4_9').val();
            let courseTypeNum = 0;

            if (courseType === "دوره معمولی" || courseType === "چند استاده") {
                courseTypeNum = 1;
            } else if (courseType === "بسته") {
                courseTypeNum = 2;
            }

            if (discountClickCount === 0) {
                totalPrice.text = jQuery('#input_4_6').val();
                totalPrice.number = parseInt(totalPrice.text.replace(/ تومان|,/g, ''));
                discountClickCount++;
            }

            if (discountCode) {
                fetch("https://api.tamland.ir/api/payment/checkDiscount", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        Mobile: mobile,
                        CourseId: courseId,
                        Type: courseTypeNum,
                        DiscountCode: discountCode,
                    }),
                })
                    .then((response) => response.json())
                    .then((result) => {
                        const dataVal = result['0'];
                        switch (dataVal['status']) {
                            case 0:
                                applyDiscountSuccess(discountCode, dataVal['fldPercentage']);
                                break;
                            case 1:
                                applyDiscountFailure('زمان کد تخفیف به پایان رسیده است');
                                break;
                            case 2:
                                applyDiscountFailure('کد تخفیف وارد شده برای این دوره مجاز نمی‌باشد');
                                break;
                            case 3:
                                applyDiscountFailure('کد تخفیف وارد شده نامعتبر است');
                                break;
                            case 4:
                                applyDiscountFailure('تعداد استفاده از کد تخفیف بیشتر از حد مجاز است');
                                break;
                            default:
                                console.error('وضعیت ناشناخته دریافت شد');
                        }
                    })
                    .catch((error) => console.error('خطا در ارتباط با سرور:', error));
            }
        });

        jQuery(document).on('click', '.del-discount-code', function () {
            resetPrice();
            jQuery('.discount-validate-message').removeClass('valid').addClass('d-none').empty();
            setCookie("discountIsSet", "false", 9); // Expires in 5 minutes 
        });
        
        // Maxlength check for input field
        jQuery('.sa_phonenumberic input').on('input', function () {
            if (jQuery(this).val().length > 11) {
                jQuery(this).val(jQuery(this).val().slice(0, 11)); // Trim extra digits
            }
            jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
            jQuery(this).attr({
                inputmode: 'tel'
            });

        });
    });
            
// Function to validate Iranian mobile number
    function isValidIranianPhone(phone) {
      const iranianPhonePattern = /^(?:\+98|0)?9\d{9}$/; // Matches +98 or 0 followed by 9 and 9 digits
      return iranianPhonePattern.test(phone);
    }

    // Function to enable/disable the submit button
    function toggleButtonState() {
      const phoneInput = document.getElementById("input_4_2");
      const otherInput = document.getElementById("input_4_1");
      const submitButton = document.getElementById("gform_save_4_footer_link");
	  const submitPay = document.getElementById("gform_submit_button_4");
	  const submitDiscount = document.getElementById("apply_discount");
	  const isDiscountAdded = document.getElementById("input_4_18");
      const phoneValue = phoneInput.value.trim();
      const otherValue = otherInput.value.trim();
	  const discountInput = isDiscountAdded.value.trim();
	  
      // Check if phone number is valid and the other input is not empty
      if (isValidIranianPhone(phoneValue) && otherValue !== "") {
        submitButton.disabled = false;
		submitPay.disabled = false;
		submitDiscount.disabled = false;
      } else {
        submitButton.disabled = true;
		submitPay.disabled = true;
		submitDiscount.disabled = true;
      }
	  
	  if(discountInput !== ""){
			submitDiscount.disabled = true;
	  }
    }
	document.addEventListener("DOMContentLoaded", function() {
	document.getElementById("input_4_1").focus();
    // Attach event listeners to inputs
    document.getElementById("input_4_2").addEventListener("input", toggleButtonState);
    document.getElementById("input_4_1").addEventListener("input", toggleButtonState);
	// Call the function once when the page is loaded to set the initial state of the button
      toggleButtonState();
    });
    
function setCookie(name, value, minutes) {
  let expires = "";
  if (minutes) {
    const date = new Date();
    date.setTime(date.getTime() + (minutes * 60 * 1000)); // Convert minutes to ms
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie = name + "=" + (value || "") + expires + "; path=/";
}
