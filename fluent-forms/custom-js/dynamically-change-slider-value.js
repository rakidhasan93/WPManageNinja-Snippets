<script>
jQuery(function ($) {
  const $min = $('input[name="min_price"]');
  const $max = $('input[name="max_price"]');

  if (!$min.length || !$max.length) {
    return;
  }

  function syncSliders() {
    const minValue = parseFloat($min.val() || 0);
    const maxValue = parseFloat($max.val() || 0);

    $max.attr('min', minValue);

    if (maxValue < minValue) {
      $max.val(minValue).trigger('input').trigger('change');
    }

    if ($.fn.rangeslider) {
      $max.rangeslider('update', true);
    }
  }

  $min.on('input change', syncSliders);
  syncSliders();
});
</script>
