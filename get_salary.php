<?php 
// Database connection parameters
$servername = "localhost";
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$database = "payroll"; // Your MySQL database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize salary variable
$salary = "";

// Check if department is set and not empty
if(isset($_GET['department']) && $_GET['department'] != '') {
    // Sanitize the input to prevent SQL injection
    $departmentName = $conn->real_escape_string($_GET['department']);

    $sql = "SELECT salary FROM department WHERE departmentName = '$departmentName'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of the first row (assuming department names are unique)
        $row = $result->fetch_assoc();
        $salary = $row['salary'];
    }
}

// Output the salary
echo $salary;

// Close the database connection
$conn->close();
?>
