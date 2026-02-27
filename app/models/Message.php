<?php

require_once APP_ROOT . '/app/core/Model.php';

class Message extends Model
{
    /**
     * Send a message.
     */
    public function send($recipientUserId, $subject, $body, $senderUserId = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO messages (recipient_user_id, sender_user_id, subject, body, is_read, created_at)
            VALUES (:recipient_user_id, :sender_user_id, :subject, :body, 0, CURRENT_TIMESTAMP)
        ");

        $stmt->execute([
            ':recipient_user_id' => $recipientUserId,
            ':sender_user_id'    => $senderUserId,
            ':subject'           => $subject,
            ':body'              => $body,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * All messages *to* this user, newest first.
     */
    public function getForUser($userId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM messages
            WHERE recipient_user_id = :user_id
            ORDER BY created_at DESC
        ");

        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count unread messages for a user (any sender).
     */
    public function countUnreadForUser($userId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS c
            FROM messages
            WHERE recipient_user_id = :user_id
              AND is_read = 0
        ");

        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)($row['c'] ?? 0);
    }

    /**
     * Find one message that belongs to this user.
     */
    public function findForUser($messageId, $userId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM messages
            WHERE id = :id
              AND recipient_user_id = :user_id
            LIMIT 1
        ");

        $stmt->execute([
            ':id'      => $messageId,
            ':user_id' => $userId,
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Mark a single message as read.
     */
    public function markAsRead($messageId, $userId)
    {
        $stmt = $this->db->prepare("
            UPDATE messages
            SET is_read = 1
            WHERE id = :id AND recipient_user_id = :user_id
        ");

        return $stmt->execute([
            ':id'      => $messageId,
            ':user_id' => $userId,
        ]);
    }

    /**
     * Get a chat-style conversation between two users (both directions).
     */
    public function getConversationBetween($userA, $userB)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM messages
            WHERE (sender_user_id = :a AND recipient_user_id = :b)
               OR (sender_user_id = :b AND recipient_user_id = :a)
            ORDER BY created_at ASC
        ");

        $stmt->execute([
            ':a' => $userA,
            ':b' => $userB,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mark all messages from B to A as read (when A opens chat).
     */
    public function markConversationRead($recipientUserId, $otherUserId)
    {
        $stmt = $this->db->prepare("
            UPDATE messages
            SET is_read = 1
            WHERE recipient_user_id = :recipient
              AND sender_user_id    = :other
        ");

        return $stmt->execute([
            ':recipient' => $recipientUserId,
            ':other'     => $otherUserId,
        ]);
    }

    /**
     * Count unread messages from a specific sender to a specific recipient.
     * Used to show red dot on admin dashboard per student.
     */
    public function countUnreadFromUserToUser($senderId, $recipientId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS c
            FROM messages
            WHERE sender_user_id    = :sender
              AND recipient_user_id = :recipient
              AND is_read           = 0
        ");

        $stmt->execute([
            ':sender'    => $senderId,
            ':recipient' => $recipientId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['c'] ?? 0);
    }
}
