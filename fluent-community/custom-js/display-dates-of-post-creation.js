<script>
(function () {
    function replaceFeedDates() {
        try {
            var items = document.querySelectorAll('.feed_timestamp[title]');

            items.forEach(function (el) {
                var exactDate = el.getAttribute('title');

                if (!exactDate) {
                    return;
                }

                // Extract only YYYY-MM-DD
                var dateOnly = exactDate.split(' ')[0];

                if (!/^\d{4}-\d{2}-\d{2}$/.test(dateOnly)) {
                    return;
                }

                if (el.textContent.trim() === dateOnly) {
                    return;
                }

                el.textContent = dateOnly;
            });
        } catch (e) {
            console.warn('FluentCommunity exact date snippet error:', e);
        }
    }

    window.addEventListener('load', function () {
        replaceFeedDates();

        setInterval(function () {
            replaceFeedDates();
        }, 2000);
    });
})();
</script>
