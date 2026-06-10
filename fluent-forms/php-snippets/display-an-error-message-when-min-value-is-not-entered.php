add_action('wp_footer', function () {
    if (is_admin()) {
        return;
    }
    ?>
    <script>
    (function ($) {
        function prepareCustomPaymentMin($form) {
            $form.find('input.ff_payment_item[type="number"][min]').each(function () {
                var $input = $(this);
                var min = $input.attr('min');

                if (min !== undefined && min !== '') {
                    $input.attr('data-ff-original-min', min);
                    $input.removeAttr('min');
                    $input.removeAttr('aria-valuemin');
                }
            });
        }

        function getPaymentValue($input) {
            if (window.ff_helper && typeof window.ff_helper.numericVal === 'function') {
                return parseFloat(window.ff_helper.numericVal($input));
            }

            return parseFloat($input.val());
        }

        function showMinError($input, message) {
            var $group = $input.closest('.ff-el-group');
            var $content = $input.closest('.ff-el-input--content');

            $group.addClass('ff-el-is-error');
            $input.attr('aria-invalid', 'true');

            $content.find('.ff-custom-payment-min-error').remove();
            $content.append(
                $('<div/>', {
                    class: 'error text-danger ff-custom-payment-min-error',
                    role: 'alert',
                    text: message
                })
            );
        }

        function clearMinError($input) {
            var $group = $input.closest('.ff-el-group');
            var $content = $input.closest('.ff-el-input--content');

            $group.removeClass('ff-el-is-error');
            $input.attr('aria-invalid', 'false');
            $content.find('.ff-custom-payment-min-error').remove();
        }

        function validateCustomPaymentMin($form, scrollToError) {
            var isValid = true;
            var $firstError = null;

            $form.find('input.ff_payment_item[type="number"][data-ff-original-min]').each(function () {
                var $input = $(this);

                if ($input.closest('.ff_excluded').length) {
                    clearMinError($input);
                    return;
                }

                var rawValue = $input.val();

                if (rawValue === '') {
                    clearMinError($input);
                    return;
                }

                var value = getPaymentValue($input);
                var min = parseFloat($input.attr('data-ff-original-min'));

                if (!isNaN(value) && !isNaN(min) && value < min) {
                    var message = 'Minimum payment amount is $' + min + '.';

                    showMinError($input, message);

                    if (!$firstError) {
                        $firstError = $input;
                    }

                    isValid = false;
                } else {
                    clearMinError($input);
                }
            });

            if (!isValid && scrollToError && $firstError) {
                $('html, body').animate({
                    scrollTop: $firstError.closest('.ff-el-group').offset().top - 100
                }, 300);

                $firstError.trigger('focus');
            }

            return isValid;
        }

        $(document.body).on('fluentform_init fluentform_init_single', function (event, form) {
            prepareCustomPaymentMin($(form));
        });

        $(document).on('ff_reinit', function (event, form) {
            prepareCustomPaymentMin($(form));
        });

        $(document).on('input change', 'input.ff_payment_item[type="number"][data-ff-original-min]', function () {
            var $form = $(this).closest('form.frm-fluent-form');
            validateCustomPaymentMin($form, false);
            $form.trigger('do_calculation');
        });

        document.addEventListener('submit', function (event) {
            var form = event.target;

            if (!form.matches || !form.matches('form.frm-fluent-form')) {
                return;
            }

            var $form = $(form);
            prepareCustomPaymentMin($form);

            if (!validateCustomPaymentMin($form, true)) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        }, true);

        $(function () {
            $('form.frm-fluent-form').each(function () {
                prepareCustomPaymentMin($(this));
            });
        });
    })(jQuery);
    </script>
    <?php
});
