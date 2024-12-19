async function updateUnreadBadge() {
    try {
        const response = await fetch('get_unread_count.php');
        const data = await response.json();
        
        const unreadBadge = document.getElementById('unread-badge');
        if (unreadBadge) {
            if (data.unread_count > 0) {
                unreadBadge.style.display = 'block';
                unreadBadge.textContent = data.unread_count;
            } else {
                unreadBadge.style.display = 'none';
            }
        }
    } catch (error) {
        console.error('Error fetching unread count:', error);
    }
}

// Update badge every 30 seconds
function initializeBadgeUpdates() {
    updateUnreadBadge();
    setInterval(updateUnreadBadge, 30000);
}