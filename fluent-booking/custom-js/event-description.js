add_action('wp_footer', function () {
    ?>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const originalDesc = document.querySelector(".fcal_slot_description");
        if (!originalDesc) return;

        const descHTML = originalDesc.innerHTML;
        const parent = originalDesc.parentElement;
        const className = originalDesc.className;

        const insertDescription = () => {
            let existing = document.querySelector(".fcal_slot_description");
            if (!existing) {
                const newDesc = document.createElement("div");
                newDesc.className = className;
                newDesc.innerHTML = descHTML;
                newDesc.style.display = "block";
                newDesc.style.visibility = "visible";
                newDesc.style.opacity = "1";
                newDesc.style.maxHeight = "none";
                newDesc.style.margin = "1rem 0";
                parent.appendChild(newDesc);
            }
        };

        const observer = new MutationObserver(() => {
            insertDescription();
        });

        observer.observe(document.body, { childList: true, subtree: true });

        // Initial insert in case it loads late
        setTimeout(insertDescription, 500);
    });
    </script>
    <?php
});
