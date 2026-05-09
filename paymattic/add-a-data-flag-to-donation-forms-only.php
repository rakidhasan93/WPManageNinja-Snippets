<?php

add_filter('wppayform/form_attributes', function ($attrs, $form) {
    $fields = get_post_meta($form->ID, 'wppayform_paymentform_builder_settings', true);
    $is_donation_form = false;

    foreach ((array) $fields as $field) {
        if (($field['type'] ?? '') === 'donation_item') {
            $is_donation_form = true;
            break;
        }
    }

    if ($is_donation_form) {
        $attrs['data-wpf-donation-form'] = '1';
    }

    return $attrs;
}, 10, 2);

/**
 * Rewrite the submit button label only on donation forms.
 */
add_action('wp_footer', function () {
    ?>
    <script>
    (function () {
      function patchDonationButtons(root) {
        (root || document).querySelectorAll('form.wpf_form[data-wpf-donation-form="1"]').forEach(function (form) {
          var normal = form.querySelector('button.wpf_submit_button .wpf_txt_normal');
          if (!normal) return;

          if (normal.dataset.wpfTotalPatched === '1') return;
          normal.dataset.wpfTotalPatched = '1';

          normal.innerHTML = 'Donate Now - <span class="wpf_calc_payment_total"></span>';
        });
      }

      document.addEventListener('DOMContentLoaded', function () {
        patchDonationButtons(document);

        if (window.MutationObserver) {
          new MutationObserver(function () {
            patchDonationButtons(document);
          }).observe(document.body, { childList: true, subtree: true });
        }
      });
    })();
    </script>
    <?php
}, 99);
