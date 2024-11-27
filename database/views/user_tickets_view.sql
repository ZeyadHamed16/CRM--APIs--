--
-- Database: `CRM`
-- Tables: users, tickets
--

-- --------------------------------------------------------

CREATE VIEW user_tickets_view AS
SELECT 
	tickets.ticket_id,
    users.user_username,
    tickets.ticket_heading,
    tickets.ticket_description,
    tickets.created_at
FROM 
    tickets
JOIN 
    users ON tickets.ticket_user_id = users.user_id;