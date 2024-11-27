<?php
class MessageManager
{
    // Database connection instance
    private $db;

    /**
     * Constructor to initialize the database connection.
     *
     * @param PDO $conn Database connection object.
     */
    public function __construct($conn)
    {
        $this->db = $conn;
    }

    /**
     * Sends a message from one user to another.
     *
     * @param int $sender_id ID of the sender (user or admin).
     * @param int $receiver_id ID of the receiver (user or admin).
     * @param string $sender_type Type of the sender ('user' or 'admin').
     * @param string $receiver_type Type of the receiver ('user' or 'admin').
     * @param string $message_content Content of the message.
     * @return bool Returns true on successful insertion, false on failure.
     */
    private function sendMessage($sender_id, $receiver_id, $sender_type, $receiver_type, $message_content)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO messages 
                (sender_id, receiver_id, sender_type, receiver_type, message_content) 
                VALUES 
                (:sender_id, :receiver_id, :sender_type, :receiver_type, :message_content)
            ");
            $stmt->bindParam(':sender_id', $sender_id);
            $stmt->bindParam(':receiver_id', $receiver_id);
            $stmt->bindParam(':sender_type', $sender_type);
            $stmt->bindParam(':receiver_type', $receiver_type);
            $stmt->bindParam(':message_content', $message_content);
            $stmt->execute();
            return true;
        } catch (PDOException $exception) {
            return false;
        }
    }

    /**
     * Allows an admin to send a message to a user.
     *
     * @param int $admin_id ID of the admin sending the message.
     * @param int $user_id ID of the user receiving the message.
     * @param string $message_content Content of the message.
     * @return bool Returns true on successful message send, false on failure.
     */
    public function adminSendMessage($admin_id, $user_id, $message_content)
    {
        return $this->sendMessage($admin_id, $user_id, 'admin', 'user', $message_content);
    }

    /**
     * Allows a user to send a message to an admin.
     *
     * @param int $user_id ID of the user sending the message.
     * @param int $admin_id ID of the admin receiving the message.
     * @param string $message_content Content of the message.
     * @return bool Returns true on successful message send, false on failure.
     */
    public function userSendMessage($user_id, $admin_id, $message_content)
    {
        return $this->sendMessage($user_id, $admin_id, 'user', 'admin', $message_content);
    }

    /**
     * Retrieves all messages sent to or from a specific user or admin.
     *
     * @param int $user_id ID of the user or admin.
     * @param string $user_type Type of the user ('user' or 'admin').
     * @return array|bool Array of messages on success, or an error message on failure.
     */
    private function receiveMessages($user_id, $user_type)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT message_content, created_at FROM messages 
                WHERE 
                    (sender_id = :user_id AND sender_type = :user_type) 
                    OR 
                    (receiver_id = :user_id AND receiver_type = :user_type)
                ORDER BY created_at DESC
            ");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':user_type', $user_type);
            $stmt->execute();
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $messages;
        } catch (PDOException $exception) {
            return array("Error retrieving messages" => $exception->getMessage());
        }
    }

    /**
     * Allows an admin to retrieve all messages sent to or from users.
     *
     * @param int $admin_id ID of the admin retrieving messages.
     * @return array|bool Array of messages on success, or an error message on failure.
     */
    public function adminReceiveMessages($admin_id)
    {
        return $this->receiveMessages($admin_id, 'admin');
    }

    /**
     * Allows a user to retrieve all messages sent to or from admins.
     *
     * @param int $user_id ID of the user retrieving messages.
     * @return array|bool Array of messages on success, or an error message on failure.
     */
    public function userReceiveMessages($user_id)
    {
        return $this->receiveMessages($user_id, 'user');
    }
}
