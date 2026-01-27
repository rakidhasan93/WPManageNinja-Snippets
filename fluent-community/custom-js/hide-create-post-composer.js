<script>
(function () {
    function userHasSpaces() {
        if (!window.fluentComAdmin) return true; // fail-safe: don't hide
        var slugs = window.fluentComAdmin.user_membership_slugs || [];
        return Array.isArray(slugs) && slugs.length > 0;
    }

    function hideComposerIfNoSpaces() {
        if (userHasSpaces()) return;

        // Hide all instances, in case multiple are rendered
        var composers = document.querySelectorAll('.create_status_holder');
        if (!composers.length) return;

        composers.forEach(function (el) {
            el.style.display = 'none';
        });
    }

    // Run on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', hideComposerIfNoSpaces);
    } else {
        hideComposerIfNoSpaces();
    }

    // Re-run a few times for initial app mount
    [500, 1500, 3000].forEach(function (ms) {
        setTimeout(hideComposerIfNoSpaces, ms);
    });

    // Watch for SPA / Vue re-renders and hide again when composer re-appears
    if (window.MutationObserver) {
        var target = document.querySelector('.fhr_home') || document.body;
        if (target) {
            var observer = new MutationObserver(function (mutations) {
                var needsCheck = mutations.some(function (m) {
                    if (!m.addedNodes) return false;
                    return Array.prototype.some.call(m.addedNodes, function (node) {
                        if (!(node instanceof HTMLElement)) return false;
                        return node.classList.contains('create_status_holder') ||
                               node.querySelector && node.querySelector('.create_status_holder');
                    });
                });
                if (needsCheck) {
                    hideComposerIfNoSpaces();
                }
            });
            observer.observe(target, { childList: true, subtree: true });
        }
    }
})();
</script>
