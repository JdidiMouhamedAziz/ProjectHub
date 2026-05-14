    <?php

//------------------------------------------------------------------------
    // Create Notification ---> createNotification(user_id, task_id, type, message)
    // Get Notifications By User ---> findNotificationsByUser(user_id)
    // Get Unread Notifications By User ---> findUnreadByUser(user_id)
    // Count Unread ---> countUnread(user_id)
    // Mark All Read ---> markAllRead(user_id)
    // Mark One Read ---> markRead(id)
    // Delete Notification ---> deleteNotification(id)
//------------------------------------------------------------------------

class Notification {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create notification
    public function createNotification($user_id, $task_id, $type, $message) {
        $stmt = $this->pdo->prepare("
            INSERT INTO notifications (user_id, task_id, type, message, is_read, created_at)
            VALUES (?, ?, ?, ?, 0, NOW())
        ");
        return $stmt->execute([$user_id, $task_id, $type, $message]);
    }

    // Get all notifications for a user (newest first)
    public function findNotificationsByUser($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM notifications
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 20
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get only unread notifications for a user
    public function findUnreadByUser($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM notifications
            WHERE user_id = ? AND is_read = 0
            ORDER BY created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Count unread notifications
    public function countUnread($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM notifications
            WHERE user_id = ? AND is_read = 0
        ");
        $stmt->execute([$user_id]);
        return (int) $stmt->fetchColumn();
    }

    // Mark all notifications as read for a user
    public function markAllRead($user_id) {
        $stmt = $this->pdo->prepare("
            UPDATE notifications SET is_read = 1
            WHERE user_id = ?
        ");
        return $stmt->execute([$user_id]);
    }

    // Mark one notification as read
    public function markRead($id) {
        $stmt = $this->pdo->prepare("
            UPDATE notifications SET is_read = 1
            WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    // Delete a notification
    public function deleteNotification($id) {
        $stmt = $this->pdo->prepare("
            DELETE FROM notifications WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }
}
?>