<?php
    // Centralized mySQL connection for database access
    try {

        
    } catch (mysqli_sql_exception $e) {
        // If connection fails, show a clear error message and stop execution
        die("Database connection failed: " . $e->getMessage());
    }
?>