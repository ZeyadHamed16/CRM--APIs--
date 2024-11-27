<?php
class CountManager
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
     * Generic method to retrieve the count of records in a specified table with an optional condition.
     *
     * @param string $table    Table name to query.
     * @param string $column   Column to count.
     * @param string $condition [optional] SQL condition to filter results. Default is an empty string (no condition)
     * @return int|array Returns the count of records as an integer, or an error message as an array if an exception occurs.
     */
    private function getCount($table, $column, $condition = "")
    {
        try {
            // Construct the SQL query for counting records
            $query = "SELECT COUNT($column) AS count FROM $table";
            if ($condition) {
                $query .= " WHERE $condition";
            }
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $count = $stmt->fetch(PDO::FETCH_ASSOC);
            return $count['count'];
        } catch (PDOException $exception) {
            return ["ERROR" => "Something went wrong while fetching the count: " . $exception->getMessage()];
        }
    }

    /**
     * Get the total number of users in the 'users' table.
     *
     * @return int|array Returns the total number of users, or an error message as an array if an exception occurs.
     */
    public function getUsersCount()
    {
        return $this->getCount('users', 'user_id');
    }

    /**
     * Get the count of projects with a 'Pending' status in the 'projects' table.
     *
     * @return int|array Returns the number of pending projects, or an error message as an array if an exception occurs.
     */
    public function getPendingProjectsCount()
    {
        return $this->getCount('projects', 'proj_id', "proj_status = 'Pending'");
    }

    /**
     * Get the count of projects with a 'Completed' status in the 'projects' table.
     *
     * @return int|array Returns the number of completed projects, or an error message as an array if an exception occurs.
     */
    public function getCompletedProjectsCount()
    {
        return $this->getCount('projects', 'proj_id', "proj_status = 'Completed'");
    }
}
