<?php
require_once '../logger.php';

function getDatabaseConnection() {
    $dbname = 'dhoffmann';

    try {
        $dsn = "mysql:host=localhost; dbname=$dbname";
        logMessage("Attempting to connect to database with DSN: $dsn");
        $pdo = new PDO($dsn, "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        logMessage("Database connection established successfully");
        return $pdo;
    } catch (PDOException $e) {
        logError("Database connection failed: " . $e->getMessage());
        return false;
    }
}

function initializeDatabase() {
    try {
        $pdo = getDatabaseConnection();
        if (!$pdo) {
            logError("Failed to establish database connection in initializeDatabase");
            return false;
        }

        $pdo->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTO_INCREMENT, username TEXT NOT NULL UNIQUE, password TEXT NOT NULL)");

        $pdo->exec("CREATE TABLE IF NOT EXISTS user_progress (id INTEGER PRIMARY KEY AUTO_INCREMENT, user_id INTEGER NOT NULL, current_step INTEGER NOT NULL DEFAULT 1, name TEXT, destination TEXT, purchase TEXT)");

        logMessage("Database initialized successfully");
        return true;
    } catch (PDOException $e) {
        logError("Error initializing database: " . $e->getMessage());
        return false;
    }
}

function getUserProgress($userId) {
    try {
        $pdo = getDatabaseConnection();
        if (!$pdo) {
            logError("Failed to establish database connection in getUserProgress");
            return false;
        }
        $stmt = $pdo->prepare("SELECT current_step, name, destination, purchase FROM user_progress WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        logMessage("User progress retrieved for user ID: $userId");
        return $result;
    } catch (PDOException $e) {
        logError("Error retrieving user progress: " . $e->getMessage());
        return false;
    }
}

function updateUserProgress($userId, $step, $choice) {
    try {
        $pdo = getDatabaseConnection();
        if (!$pdo) {
            logError("Failed to establish database connection in updateUserProgress");
            return false;
        }
        $column = '';
        switch ($step) {
            case 2:
                $column = 'name';
                break;
            case 3:
                $column = 'destination';
                break;
            case 4:
                $column = 'purchase';
                break;
            default:
                throw new Exception("Invalid step number");
        }
        
        $stmt = $pdo->prepare("UPDATE user_progress SET current_step = ?, $column = ? WHERE user_id = ?");
        $result = $stmt->execute([$step, $choice, $userId]);
        logMessage("User progress updated for user ID: $userId, Step: $step, Choice: $choice");
        return $result;
    } catch (PDOException $e) {
        logError("Error updating user progress: " . $e->getMessage());
        return false;
    } catch (Exception $e) {
        logError("Error updating user progress: " . $e->getMessage());
        return false;
    }
}

function createUser($username, $password) {
    try {
        $pdo = getDatabaseConnection();
        if (!$pdo) {
            logError("Failed to establish database connection in createUser");
            return false;
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO users (name, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashedPassword]);
        $userId = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare("INSERT INTO user_progress (user_id, current_step) VALUES (?, 1)");
        $stmt->execute([$userId]);
        
        $pdo->commit();
        logMessage("New user created with ID: $userId");
        return $userId;
    } catch (PDOException $e) {
        if ($pdo) {
            $pdo->rollBack();
        }
        logError("Error creating user: " . $e->getMessage());
        return false;
    }
}

function getUserByUsername($username) {
    try {
        $pdo = getDatabaseConnection();
        if (!$pdo) {
            logError("Failed to establish database connection in getUserByUsername");
            return false;
        }
        $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE name = ?");
        //echo $username;
        $stmt->execute([$username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        logMessage("User retrieved by username: $username");
        //echo $username;
        return $result;
    } catch (PDOException $e) {
        logError("Error retrieving user by username: " . $e->getMessage());
        return false;
    }
}

function resetUserProgress($userId) {
    try {
        $pdo = getDatabaseConnection();
        if (!$pdo) {
            logError("Failed to establish database connection in resetUserProgress");
            return false;
        }
        $stmt = $pdo->prepare("UPDATE user_progress SET current_step = 1, name = NULL, destination = NULL, purchase = NULL WHERE user_id = ?");
        $result = $stmt->execute([$userId]);
        logMessage("User progress reset for user ID: $userId");
        return $result;
    } catch (PDOException $e) {
        logError("Error resetting user progress: " . $e->getMessage());
        return false;
    }
}

// Initialize the database when this file is included
initializeDatabase();
?>
