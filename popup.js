document.addEventListener("DOMContentLoaded", function () {
    let popup = document.getElementById("popup");
    let overlay = document.getElementById("overlay");

    function openPopup() {
        popup.classList.add("open-popup");
        overlay.classList.add("show"); // Show overlay
    }

    function closePopup() {
        popup.classList.remove("open-popup");
        overlay.classList.remove("show"); // Hide overlay
    }

    // Open the popup when the page loads
    openPopup();

    // Attach closePopup to any elements that should close the popup (optional)
    document.getElementById("closeButton")?.addEventListener("click", closePopup);
});
