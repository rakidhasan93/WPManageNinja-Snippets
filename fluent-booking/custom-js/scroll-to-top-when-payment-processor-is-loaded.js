document.addEventListener('DOMContentLoaded', function () {
    const target = document.querySelector('.fcal_date_event_details');

    if (!target) return;

    // Observe when payment form becomes active
    const observer = new MutationObserver(() => {
        if (target.classList.contains('is_payment')) {
            // Scroll the element into view
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    observer.observe(target, { attributes: true, attributeFilter: ['class'] });
});
