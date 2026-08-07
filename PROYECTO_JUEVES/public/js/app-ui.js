(function () {
    function initAlerts() {
        const alerts = document.querySelectorAll(".alert:not(.alert-static)");
        if (alerts.length === 0) {
            return;
        }

        alerts.forEach((alert, index) => {
            alert.classList.add("app-toast");
            alert.style.setProperty("--toast-index", index);
            alert.setAttribute("role", "status");

            setTimeout(() => {
                alert.classList.add("app-toast-hide");
            }, 3600 + (index * 180));

            setTimeout(() => {
                alert.remove();
            }, 4300 + (index * 180));
        });

        if (window.history.replaceState && window.location.search) {
            window.history.replaceState(null, "", window.location.pathname + window.location.hash);
        }
    }

    document.addEventListener("DOMContentLoaded", initAlerts);
})();
