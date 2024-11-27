--
-- Database: `CRM`
--

-- --------------------------------------------------------

CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,              -- ID of the sender (either a user or an admin)
    receiver_id INT NOT NULL,            -- ID of the receiver (either a user or an admin)
    sender_type ENUM('user', 'admin') NOT NULL,  -- Indicates if the sender is a user or admin
    receiver_type ENUM('user', 'admin') NOT NULL, -- Indicates if the receiver is a user or admin
    message_content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) 
        REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE, 
    FOREIGN KEY (receiver_id) 
        REFERENCES admins(admin_id) ON DELETE CASCADE ON UPDATE CASCADE
);
