<?php
require_once '/../models/database.php';
require_once '/../logger.php';

try {
    $pdo = getDatabaseConnection();
    if ($pdo) {
        echo "Database connection successful!\n";
        logMessage("Database connection test successful");

        // Test creating a user
        $userId = createUser("testuser", "testpassword");
        if ($userId) {
            echo "User created successfully with ID: $userId\n";
            logMessage("Test user created successfully");

            // Test retrieving user
            $user = getUserByUsername("testuser");
            if ($user) {
                echo "User retrieved successfully: " . $user['username'] . "\n";
                logMessage("Test user retrieved successfully");

                // Test updating user progress
                if (updateUserProgress($userId, 1, "TestName")) {
                    echo "User progress updated successfully\n";
                    logMessage("Test user progress updated successfully");

                    // Test retrieving user progress
                    $progress = getUserProgress($userId);
                    if ($progress) {
                        echo "User progress retrieved successfully: Step " . $progress['current_step'] . "\n";
                        logMessage("Test user progress retrieved successfully");
                    } else {
                        echo "Failed to retrieve user progress\n";
                        logError("Failed to retrieve test user progress");
                    }
                } else {
                    echo "Failed to update user progress\n";
                    logError("Failed to update test user progress");
                }
            } else {
                echo "Failed to retrieve user\n";
                logError("Failed to retrieve test user");
            }
        } else {
            echo "Failed to create user\n";
            logError("Failed to create test user");
        }
    } else {
        echo "Database connection failed\n";
        logError("Database connection test failed");
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    logError("Database error in test script: " . $e->getMessage());
}
?>
