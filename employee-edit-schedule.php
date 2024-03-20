<?php
include_once('includes/header.php');

// Assuming you have already established a database connection
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "payroll";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Check if the update button is clicked
// Check if the update button is clicked
if(isset($_POST['update'])) {
    // Retrieve the selected values
    $dayoff = $_POST['dayoff'];
    $weekday = $_POST['weekday'];

    // Retrieve the employee name from the URL parameter
    if(isset($_GET['id'])) {
        $name = $_GET['id'];
    } else {
        echo "<p>No employee name specified</p>";
        exit(); // Stop further execution
    }
    
    // Prepare the update query
    $sql = "UPDATE employee SET $dayoff = ? WHERE name = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $newweekday = strtoupper($weekday);
    $stmt->bind_param("ss", $newweekday, $name);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<script>alert('Submission successful!'); window.location='employee-schedule.php';</script>";
    } else {
        echo "<p>Error updating dayoff: " . $stmt->error . "</p>";
    }

    // Close statement
    $stmt->close();
}

// Continue with your HTML content below
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Table</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Custom Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
        }
        .container {
            padding: 20px;
        }
        h2 {
            margin-top: 40px;
            margin-bottom: 20px;
            text-align: center;
            color: #007bff;
        }
        .table-responsive {
            margin-top: 20px;
        }
        select {
            margin-bottom: 10px;
        }
        .table-bordered th,
        .table-bordered td {
            border-color: #dee2e6;
            padding: 12px;
            text-align: center;
        }
        .thead-dark th {
            background-color: #007bff;
            color: #fff;
        }
        .btn-update {
            padding: 5px 10px;
            font-size: 14px;
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
        }
        .btn-update:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .btn-update:focus {
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5);
        }
        .no-results {
            font-style: italic;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Schedule Table</h2>
        <div class="row mb-4">
            <div class="col-md-6">
                <form method="post" action="">
                    <select name="dayoff" class="form-control">
                        <option value="dayoff1">Dayoff 1</option>
                        <option value="dayoff2">Dayoff 2</option>
                        <option value="dayoff3">Dayoff 3</option>
                        <option value="dayoff4">Dayoff 4</option>
                        <option value="dayoff5">Dayoff 5</option>
                    </select>
                    <button type="submit" name="update" class="btn btn-update">Update</button>
            </div>
            <div class="col-md-6">
                    <select name="weekday" class="form-control">
                        <option value="monday">Monday</option>
                        <option value="tuesday">Tuesday</option>
                        <option value="wednesday">Wednesday</option>
                        <option value="thursday">Thursday</option>
                        <option value="friday">Friday</option>
                        <option value="saturday">Saturday</option>
                        <option value="sunday">Sunday</option>
                    </select>
                
                </form>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Name</th>
                        <th>Monday</th>
                        <th>Tuesday</th>
                        <th>Wednesday</th>
                        <th>Thursday</th>
                        <th>Friday</th>
                        <th>Saturday</th>
                        <th>Sunday</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Assuming you have already established a database connection
                    $servername = "localhost";
                    $username = "root"; // Replace with your MySQL username
                    $password = ""; // Replace with your MySQL password
                    $dbname = "payroll";

                    // Check if the 'id' parameter is set in the URL
                    if(isset($_GET['id'])) {
                        // Retrieve the value of the 'id' parameter
                        $name = $_GET['id'];

                        // Now you can use the $name variable to fetch the corresponding data from the database or perform any other operations
                        // For example:
                        // echo "Editing schedule for employee with name: $name";
                    } else {
                        // If the 'id' parameter is not set, handle the error or redirect the user
                        echo "<tr><td colspan='9' class='no-results'>No employee name specified</td></tr>";
                        exit(); // Stop further execution
                    }

                    // Create connection
                    $conn = new mysqli($servername, $username, $password, $dbname);

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Query the schedule table
                    $sql = "SELECT * FROM schedule WHERE name = '$name'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // Loop through each row of the result
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            $mon = $row['monday'] == "NULL" ? '' : $row['monday'];
                            $tues = $row['tuesday'] == "NULL" ? '' : $row['tuesday'];
                            $wed = $row['wednesday'] == "NULL" ? '' : $row['wednesday'];
                            $thurs = $row['thursday'] == "NULL" ? '' : $row['thursday'];
                            $fri = $row['friday'] == "NULL" ? '' : $row['friday'];
                            $sat = $row['saturday'] == "NULL" ? '' : $row['saturday'];
                            $sun = $row['sunday'] == "NULL" ? '' : $row['sunday'];
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $mon . "</td>";
                            echo "<td>" . $tues . "</td>";
                            echo "<td>" . $wed . "</td>";
                            echo "<td>" . $thurs . "</td>";
                            echo "<td>" . $fri . "</td>";
                            echo "<td>" . $sat . "</td>";
                            echo "<td>" . $sun . "</td>";
                             echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' class='no-results'>0 results</td></tr>";
                    }

                    // Close connection
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
