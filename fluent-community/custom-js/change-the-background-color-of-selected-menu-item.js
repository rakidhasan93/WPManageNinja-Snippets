<script>
(function() {
  const MENU_SELECTOR = '.fcom_user_context_menu_items .fcom_profile_sub_menu li a';

  function normalize(url) {
    try {
      const u = new URL(url, location.origin);
      return (u.origin + u.pathname.replace(/\/$/, '') + (u.hash || '')).replace(/\/$/, '');
    } catch (e) {
      return (url || '').replace(/\/$/, '');
    }
  }

  function queryLinks() {
    return Array.from(document.querySelectorAll(MENU_SELECTOR));
  }

  function clearAllActive() {
    queryLinks().forEach(l => l.classList.remove('active-link'));
  }

  function setActiveLink(url) {
    const normalizedUrl = normalize(url);
    let found = false;
    queryLinks().forEach(l => {
      if (normalize(l.href) === normalizedUrl) {
        l.classList.add('active-link');
        found = true;
      } else {
        l.classList.remove('active-link');
      }
    });
    return found;
  }

  function syncActive() {
    const currentUrl = window.location.href;

    // If the current URL matches a menu link, highlight it
    const matched = setActiveLink(currentUrl);

    // Otherwise, clear all highlights (works for homepage or external pages)
    if (!matched) clearAllActive();
  }

  // Run on page load
  document.addEventListener('DOMContentLoaded', syncActive);
  window.addEventListener('popstate', syncActive);
  window.addEventListener('hashchange', syncActive);

  // SPA pushState/replaceState detection
  (function() {
    const push = history.pushState;
    const replace = history.replaceState;
    history.pushState = function() {
      push.apply(history, arguments);
      window.dispatchEvent(new Event('location-changed'));
    };
    history.replaceState = function() {
      replace.apply(history, arguments);
      window.dispatchEvent(new Event('location-changed'));
    };
  })();
  window.addEventListener('location-changed', syncActive);

  // Click handling: highlight immediately if link matches
  document.addEventListener('click', e => {
    const link = e.target.closest(MENU_SELECTOR);
    if (!link) return;
    setActiveLink(link.href);
  }, true);
})();
</script>
