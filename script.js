function startHeaderClock() {
    const clockElement = document.getElementById('liveClock');
    const dayElement = document.getElementById('liveDay');
    const dateElement = document.getElementById('liveDate');

    function update() {
        const now = new Date();

        // Time with leading zeros
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        clockElement.textContent = `${h}:${m}:${s}`;

        // Update Day and Date (Optional: keeps it synced if left open at midnight)
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        dayElement.textContent = days[now.getDay()];
        dateElement.textContent = `${months[now.getMonth()]} ${String(now.getDate()).padStart(2, '0')}, ${now.getFullYear()}`;
    }

    setInterval(update, 1000);
    update();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', startHeaderClock);