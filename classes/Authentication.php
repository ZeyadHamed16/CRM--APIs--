<?php

/**
 * Handles user authentication and management, including checking user existence,
 * creating new users, and verifying email and password.
 */
class Authentication
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
     * Checks if a user exists in the database by [ USERNAME , EAMIL, URL ].
     *
     * This method checks for existing records that match the given username, email, or URL but ignores a specified
     * user ID to allow updating an existing user's information without duplicate conflicts.
     *
     * @param int $id The user ID to exclude from the search, allowing updates to the current user's record.
     * @param string $username The username to check.
     * @param string $email The email to check.
     * @param string $url The URL to check.
     * @return string|false Returns
     *   - A message indicating which field (username, email, or URL) already exists if a duplicate is found.
     *   - False if no duplicate match is found in the database or if there is an error.
     */
    public function checkUserExists($id, $username, $email, $url)
    {
        try {
            $users_select_query = "SELECT * FROM users WHERE (user_username = :name OR user_email = :email OR user_url = :url) AND user_id != :id";
            $stmt = $this->db->prepare($users_select_query);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":name", $username);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":url", $url);
            $stmt->execute();
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if user exists and which field is a duplicate
            if ($user_data) {
                if ($username == $user_data["user_username"]) {
                    return "Username already exists";
                } elseif ($email == $user_data["user_email"]) {
                    return "Email already exists";
                } elseif ($url == $user_data["user_url"]) {
                    return "URL already exists";
                }
            } else {
                return false; // Return false if no duplicate was found
            }
        } catch (PDOException $exception) {
            return false;
        }
    }

    /**
     * Creates a new user in the database and assigns default projects.
     *
     * This method inserts a new user with the provided username, email, address, URL, and password.
     * It hashes the password before storing it and assigns default projects for the new user.
     *
     * @param string $username The username of the new user.
     * @param string $email The email of the new user.
     * @param string $address The address of the new user.
     * @param string $url The URL associated with the user.
     * @param string $password The password of the new user.
     * @return bool Returns true if the user is successfully created and default projects are assigned, otherwise false.
     */
    public function createUser($username, $email, $address, $url, $password)
    {
        try {
            // => ADDING NEW USER
            // Hash the password before storing it
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into the database
            $users_insert_query = "INSERT INTO users (user_username, user_email, user_address, user_url, user_password) 
                                    VALUES (:name, :email, :address, :url, :password)";
            $stmt = $this->db->prepare($users_insert_query);
            $stmt->bindparam(":name", $username);
            $stmt->bindparam(":email", $email);
            $stmt->bindparam(":address", $address);
            $stmt->bindparam(":url", $url);
            $stmt->bindparam(":password", $hashedPassword);
            $stmt->execute();

            // Get the newly inserted user's ID
            $new_user_id = $this->db->lastInsertId();

            // => ADDING DEFAULT PROJECT FOR THE NEW USER
            $default_project_query = "INSERT INTO projects (proj_user_id, proj_step_name, proj_status, proj_desc) 
                                        VALUES 
                                        (:user_id, 'Account Managment', 'Pending', 'Initial project setup'),
                                        (:user_id, 'Design', 'Pending', 'Initial project setup'),
                                        (:user_id, 'Programming', 'Pending', 'Initial project setup')";
            $project_stmt = $this->db->prepare($default_project_query);
            $project_stmt->bindParam(":user_id", $new_user_id);
            $project_stmt->execute();
            return true;
        } catch (PDOException $exception) {
            return false;
        }
    }

    /**
     * Verifies if the provided email and password match an existing user.
     *
     * This method checks if a user exists in the database with the given email.
     * If the email exists, it compares the provided password with the stored hashed password.
     * The method uses password_verify to ensure the password is correct.
     *
     * @param string $email The email to verify.
     * @param string $password The password to verify.
     * @return bool|string Returns true if the email and password match, or specific error messages:
     * - 'email_exists' if the email is not found in the database.
     * - 'password_exists' if the password is incorrect.
     * - False if an error occurs during the process.
     */
    public function verifyEmailPassword($email, $password)
    {
        try {
            // Check if the user exists by email
            $users_select_query = "SELECT * FROM users WHERE user_email = :email";
            $stmt = $this->db->prepare($users_select_query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            // Check if email exists
            if ($stmt->rowCount() == 0) {
                return 'email_exists'; // Email not found
            }

            // Verify password
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, stripslashes($userData['user_password']))) {
                $_SESSION['user_username'] = $userData["user_username"];
                return true; // Correct password
            } else {
                return 'password_exists'; // Incorrect password
            }
        } catch (PDOException) {
            return false;
        }
    }
}
