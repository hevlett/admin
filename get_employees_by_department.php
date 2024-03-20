<?php

// get_employees_by_department.php
include ('config/db.php'); // Include your database connection
include ('config/function.php');

if (isset($_GET['department'])) {
    $department = $_GET['department'];

    $employees = getEmployeesByDepartment($conn, $department);

    echo json_encode($employees);
} else {
    echo "Error: Department parameter not set.";
}

// Add this for debugging
error_log("Department: " . $department);
error_log("Employees: " . json_encode($employees));
?>


