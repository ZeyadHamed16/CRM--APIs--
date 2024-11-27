<?php
class AdminManager
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
     * Retrieves all records from a specified table.
     *
     * @param string $table The name of the table to query.
     * @param string $columns A comma-separated string of column names to retrieve.
     * @return array An array of records from the specified table or an error message.
     */
    private function getAllRecords($columns, $table)
    {
        try {
            $query = "SELECT $columns FROM $table";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            return array("Error retrieving data from $table:" => $exception->getMessage());
        }
    }

    /**
     * Retrieves all user data from the users table.
     *
     * @return array An array of user records or an error message.
     */
    public function getAllUsers()
    {
        return $this->getAllRecords('user_id, user_username, user_url, user_email', 'users');
    }

    // Method to retrieve all ticket records
    /**
     * Retrieves all ticket data from the tickets table.
     *
     * @return array An array of ticket records or an error message.
     */
    public function getAllTickets()
    {
        return $this->getAllRecords('user_username, ticket_heading, ticket_description', 'user_tickets_view');
    }

    /**
     * Creates a new ticket for a user if the user exists in the database.
     *
     * This method first checks if a user with the provided user ID exists.
     * If the user exists, it inserts a new ticket with the specified heading and description.
     * If the user does not exist, it returns false.
     *
     * @param int $user_id The ID of the user creating the ticket.
     * @param string $heading The heading of the ticket.
     * @param string $description The description of the ticket.
     * @return bool Returns true if the ticket was created successfully, false if the user does not exist or if an error occurs.
     */
    public function createTickets($user_id, $heading, $description)
    {
        try {
            $select_user_by_id = "SELECT user_id FROM users WHERE user_id = :id";
            $stmt_select = $this->db->prepare($select_user_by_id);
            $stmt_select->bindParam(":id", $user_id);
            $stmt_select->execute();

            // Check if user exists
            if ($stmt_select->rowCount() > 0) {
                $insert_ticket_query = "INSERT INTO tickets (ticket_user_id, ticket_heading, ticket_description) VALUES (:id, :heading, :description)";
                $stmt_insert = $this->db->prepare($insert_ticket_query);
                $stmt_insert->bindParam(":id", $user_id);
                $stmt_insert->bindParam(":heading", $heading);
                $stmt_insert->bindParam(":description", $description);
                $stmt_insert->execute();
                return true;
            }

            // Return false if no user exists
            return false;
        } catch (PDOException $exception) {
            return false;
        }
    }


    /**
     * Update a user's details in the 'users' table by user ID.
     *
     * @param int $user_id User's ID.
     * @param string $username New username.
     * @param string $email New email.
     * @param string $address New address.
     * @param string $url New URL.
     * @return bool Returns true if update is successful, false otherwise.
     */
    public function updateUsers($user_id, $username, $email, $address, $url)
    {
        try {
            $users_update_query = "UPDATE users SET user_username = :name, user_email = :email, user_address = :address, user_url = :url WHERE user_id = :id";
            $stmt = $this->db->prepare($users_update_query);
            $stmt->bindParam(":name", $username);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":address", $address);
            $stmt->bindParam(":url", $url);
            $stmt->bindParam(":id", $user_id);
            $stmt->execute();
            return true;
        } catch (PDOException) {
            return false;
        }
    }


    /**
     * Updates the project status and description for each step of a user’s projects.
     *
     * This method checks if the user has. If projects exist, it performs a conditional
     * update using a CASE statement, allowing different values for proj_status 
     * and proj_desc based on the proj_step_name for each project.
     *
     * @param int $user_id - The ID of the user whose projects are being updated.
     * @param array $projectUpdates - An associative array containing the status and description
     *                                updates for each project step. Each project step (e.g., 
     *                                'Account Managment', 'Design', 'Programming') has 'status'
     *                                and 'description' keys with the corresponding new values.
     *
     * @return bool - Returns true if the update is successful, false if the user has no projects 
     *                or if an error occurs during execution.
     *
     * Example of $projectUpdates:
     * [
     *   'Account Managment' => ['status' => 'In process', 'description' => 'Updated description'],
     *   'Design' => ['status' => 'Completed', 'description' => 'Design phase complete'],
     *   'Programming' => ['status' => 'Pending', 'description' => 'Programming to start soon']
     * ]
     */
    public function updateProjects($user_id, $projectUpdates)
    {
        try {
            $user_select_query = "SELECT proj_user_id FROM projects WHERE proj_user_id = :id";
            $select_stmt = $this->db->prepare($user_select_query);
            $select_stmt->bindParam(":id", $user_id);
            $select_stmt->execute();
            if ($select_stmt->rowCount() > 0) {
                $query = "
                UPDATE projects
                SET 
                    proj_status = CASE proj_step_name
                        WHEN 'Account Managment' THEN :status_account
                        WHEN 'Design' THEN :status_design
                        WHEN 'Programming' THEN :status_programming
                    END,
                    proj_desc = CASE proj_step_name
                        WHEN 'Account Managment' THEN :desc_account
                        WHEN 'Design' THEN :desc_design
                        WHEN 'Programming' THEN :desc_programming
                    END
                WHERE proj_user_id = :user_id";

                $stmt = $this->db->prepare($query);

                // Bind the parameters for each step
                $stmt->bindParam(':status_account', $projectUpdates['Account Managment']['status']);
                $stmt->bindParam(':desc_account', $projectUpdates['Account Managment']['description']);

                $stmt->bindParam(':status_design', $projectUpdates['Design']['status']);
                $stmt->bindParam(':desc_design', $projectUpdates['Design']['description']);

                $stmt->bindParam(':status_programming', $projectUpdates['Programming']['status']);
                $stmt->bindParam(':desc_programming', $projectUpdates['Programming']['description']);

                $stmt->bindParam(':user_id', $user_id);

                $stmt->execute();
                return true;
            } else {
                return false;
            }
        } catch (PDOException $exception) {
            return false;
        }
    }


    /**
     * Delete a user from the 'users' table by user ID if they exist.
     *
     * @param int $user_id User's ID.
     * @return bool Returns true if deletion is successful, false otherwise.
     */
    public function deleteUsers($user_id)
    {
        try {
            $user_select_query = "SELECT user_id FROM users WHERE user_id = :id";
            $select_stmt = $this->db->prepare($user_select_query);
            $select_stmt->bindParam(":id", $user_id);
            $select_stmt->execute();
            if ($select_stmt->rowCount() > 0) {
                $user_delete_query = "DELETE FROM users WHERE user_id = :id";
                $delete_stmt = $this->db->prepare($user_delete_query);
                $delete_stmt->bindParam(":id", $user_id);
                $delete_stmt->execute();
                return true;
            } else {
                return false;
            }
        } catch (PDOException) {
            return false;
        }
    }
}
