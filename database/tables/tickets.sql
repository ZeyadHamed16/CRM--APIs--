--
-- Database: `CRM`
--

-- --------------------------------------------------------

CREATE TABLE tickets (
    ticket_id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_user_id INT,
    ticket_heading VARCHAR(150) NOT NULL,
    ticket_description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_user_id) REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE
);