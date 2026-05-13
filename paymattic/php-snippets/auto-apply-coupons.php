<?php

/**
 * Paymattic: auto-apply coupons with a small status message.
 */

add_filter('wppayform/form_attributes', function ($attrs, $form) {
    if ((int) $form->ID === 193) {
        $attrs['data-wpf-auto-coupon-only'] = '1';
    }
    return $attrs;
}, 10, 2);

add_action('wp_footer', function () {
    ?>
    <style>
      form.wpf_form[data-wpf_form_id="193"] .wpf_item_coupon {
        display: none !important;
      }

      form.wpf_form[data-wpf_form_id="193"] .wpf-auto-coupon-status {
        margin: 0 0 12px;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 13px;
        line-height: 1.4;
        display: none;
      }

      form.wpf_form[data-wpf_form_id="193"] .wpf-auto-coupon-status.is-success {
        display: block;
        background: #eef9f1;
        color: #1f7a3f;
        border: 1px solid #bde5c8;
      }

      form.wpf_form[data-wpf_form_id="193"] .wpf-auto-coupon-status.is-info {
        display: block;
        background: #f5f7fa;
        color: #516173;
        border: 1px solid #d7dde5;
      }
    </style>

    <script>
    (function($) {
      var FORM_ID = 193;

      // Amount in cents => coupon code
      var couponMap = {
        999: 'COUPON_FOR_9_99',
        1999: 'COUPON_FOR_19_99'
      };

      function getSelectedAmountCents($form) {
        var $checked = $form.find('.wpf_subscription_controls_radio input:checked').first();
        if ($checked.length) {
          var radioAmount = parseInt($checked.attr('data-price') || '0', 10) || 0;
          if (radioAmount > 0) return radioAmount;
        }

        var $selected = $form.find('.wpf_subscrion_plans_select select option:selected').first();
        if ($selected.length) {
          var selectAmount = parseInt($selected.attr('data-price') || '0', 10) || 0;
          if (selectAmount > 0) return selectAmount;
        }

        return null;
      }

      function ensureStatus($form) {
        var $status = $form.find('.wpf-auto-coupon-status');
        if ($status.length) return $status;

        $status = $('<div class="wpf-auto-coupon-status" aria-live="polite"></div>');
        var $target = $form.find('.wpf_form_submissions').first();

        if ($target.length) {
          $target.before($status);
        } else {
          $form.prepend($status);
        }

        return $status;
      }

      function setStatus($form, message, type) {
        var $status = ensureStatus($form);

        if (!message) {
          $status.removeClass('is-success is-info').hide().text('');
          return;
        }

        $status
          .removeClass('is-success is-info')
          .addClass(type === 'success' ? 'is-success' : 'is-info')
          .text(message)
          .show();
      }

      function clearAllCoupons($form, done) {
        var $clears = $form.find('.wpf_coupon_responses .error-clear');

        if ($clears.length) {
          $clears.each(function() {
            $(this).trigger('click');
          });
        }

        setTimeout(function() {
          $form.removeData('wpf_auto_coupon_code');
          if (typeof done === 'function') done();
        }, 400);
      }

      function applyCouponCode($form, code) {
        var $input = $form.find('.wpf_item_coupon input.wpf_coupon_field, input.wpf_coupon_field').first();
        var $button = $form.find('.wpf_item_coupon .wpf_coupon_action, .wpf_coupon_action').first();

        if (!$input.length || !$button.length) return;

        $input.val(code).trigger('input').trigger('change');

        setTimeout(function() {
          $button.trigger('click');
          $form.data('wpf_auto_coupon_code', code);
          setStatus($form, 'Discount applied', 'success');
        }, 250);
      }

      function syncCoupon($form) {
        var formId = parseInt($form.attr('data-wpf_form_id'), 10);
        if (formId !== FORM_ID) return;

        if ($form.data('wpf_auto_coupon_busy')) return;
        $form.data('wpf_auto_coupon_busy', true);

        var amount = getSelectedAmountCents($form);
        var desiredCode = amount && couponMap[amount] ? couponMap[amount] : null;
        var currentCode = $form.data('wpf_auto_coupon_code') || null;

        if (!desiredCode) {
          clearAllCoupons($form, function() {
            setStatus($form, '', 'info');
            $form.data('wpf_auto_coupon_busy', false);
          });
          return;
        }

        if (currentCode === desiredCode) {
          setStatus($form, 'Discount applied', 'success');
          $form.data('wpf_auto_coupon_busy', false);
          return;
        }

        clearAllCoupons($form, function() {
          applyCouponCode($form, desiredCode);
          setTimeout(function() {
            $form.data('wpf_auto_coupon_busy', false);
          }, 500);
        });
      }

      function bindForm($form) {
        if ($form.data('wpf_auto_coupon_bound')) return;
        $form.data('wpf_auto_coupon_bound', true);

        setTimeout(function() {
          syncCoupon($form);
        }, 700);

        $form.on(
          'change input',
          '.wpf_subscription_controls_radio input, .wpf_subscrion_plans_select select',
          function() {
            setTimeout(function() {
              syncCoupon($form);
            }, 350);
          }
        );
      }

      $(function() {
        $('form.wpf_form[data-wpf_form_id="193"]').each(function() {
          bindForm($(this));
        });

        $(document).on('wpf_reinit', function(e, formEl) {
          var $form = $(formEl);
          if ($form.is('form.wpf_form[data-wpf_form_id="193"]')) {
            bindForm($form);
          }
        });
      });
    })(jQuery);
    </script>
    <?php
}, 99);
