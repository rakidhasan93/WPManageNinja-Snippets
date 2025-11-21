document.addEventListener("DOMContentLoaded", function () {
    const headers = document.querySelectorAll(".calendar-month-year h2");

    headers.forEach(h2 => {
        const h3 = document.createElement("h3");
        h3.innerHTML = h2.innerHTML;
        h2.replaceWith(h3);
    });
});
