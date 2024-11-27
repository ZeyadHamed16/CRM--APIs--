--
-- Database: `CRM`
--

-- --------------------------------------------------------

CREATE TABLE projects (
    proj_id INT AUTO_INCREMENT PRIMARY KEY,
    proj_user_id INT, -- Foreign key column for the user ID
    proj_step_name ENUM(
        'Account Managment',
        'Design',
        'Programming'
    ) NOT NULL,
    proj_status ENUM(
        'Pending',
        'In process',
        'Completed'
    ) NOT NULL,
    proj_desc VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (proj_user_id) REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE
);