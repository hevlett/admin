<?php
include_once('includes/header.php');
// Assuming you have already established a database connection
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "payroll";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query the employee table
$sql = "SELECT name, dayoff1, dayoff2, dayoff3, dayoff4, dayoff5 FROM employee";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Loop through each row of the result
    while($row = $result->fetch_assoc()) {
        // Check if the name already exists in the schedule table
        $check_sql = "SELECT id FROM schedule WHERE name = '" . $row['name'] . "'";
        $check_result = $conn->query($check_sql);
        
        if ($check_result->num_rows == 0) {
            // If the name doesn't exist, insert the data into the schedule table
            $insert_sql = "INSERT INTO schedule (name, monday, tuesday, wednesday, thursday, friday, saturday, sunday) VALUES ('" . $row['name'] . "', ";
            $insert_sql .= "'" . ($row['dayoff1'] == 'MONDAY' || $row['dayoff2'] == 'MONDAY' || $row['dayoff3'] == 'MONDAY' || $row['dayoff4'] == 'MONDAY' || $row['dayoff5'] == 'MONDAY' ? '1' : 'NULL') . "', ";
            $insert_sql .= "'" . ($row['dayoff1'] == 'TUESDAY' || $row['dayoff2'] == 'TUESDAY' || $row['dayoff3'] == 'TUESDAY' || $row['dayoff4'] == 'TUESDAY' || $row['dayoff5'] == 'TUESDAY' ? '1' : 'NULL') . "', ";
            $insert_sql .= "'" . ($row['dayoff1'] == 'WEDNESDAY' || $row['dayoff2'] == 'WEDNESDAY' || $row['dayoff3'] == 'WEDNESDAY' || $row['dayoff4'] == 'WEDNESDAY' || $row['dayoff5'] == 'WEDNESDAY' ? '1' : 'NULL') . "', ";
            $insert_sql .= "'" . ($row['dayoff1'] == 'THURSDAY' || $row['dayoff2'] == 'THURSDAY' || $row['dayoff3'] == 'THURSDAY' || $row['dayoff4'] == 'THURSDAY' || $row['dayoff5'] == 'THURSDAY' ? '1' : 'NULL') . "', ";
            $insert_sql .= "'" . ($row['dayoff1'] == 'FRIDAY' || $row['dayoff2'] == 'FRIDAY' || $row['dayoff3'] == 'FRIDAY' || $row['dayoff4'] == 'FRIDAY' || $row['dayoff5'] == 'FRIDAY' ? '1' : 'NULL') . "', ";
            $insert_sql .= "'" . ($row['dayoff1'] == 'SATURDAY' || $row['dayoff2'] == 'SATURDAY' || $row['dayoff3'] == 'SATURDAY' || $row['dayoff4'] == 'SATURDAY' || $row['dayoff5'] == 'SATURDAY' ? '1' : 'NULL') . "', ";
            $insert_sql .= "'" . ($row['dayoff1'] == 'SUNDAY' || $row['dayoff2'] == 'SUNDAY' || $row['dayoff3'] == 'SUNDAY' || $row['dayoff4'] == 'SUNDAY' || $row['dayoff5'] == 'SUNDAY' ? '1' : 'NULL') . "')";

            if ($conn->query($insert_sql) === TRUE) {
                // echo "Record inserted successfully for " . $row['name'] . "<br>";
            } else {
                echo "Error inserting record: " . $conn->error;
            }
        } else {
            // If the name exists, update the data in the schedule table
            $update_sql = "UPDATE schedule SET ";
            $update_sql .= "monday = '" . ($row['dayoff1'] == 'MONDAY' || $row['dayoff2'] == 'MONDAY' || $row['dayoff3'] == 'MONDAY' || $row['dayoff4'] == 'MONDAY' || $row['dayoff5'] == 'MONDAY' ? '1' : 'NULL') . "', ";
            $update_sql .= "tuesday = '" . ($row['dayoff1'] == 'TUESDAY' || $row['dayoff2'] == 'TUESDAY' || $row['dayoff3'] == 'TUESDAY' || $row['dayoff4'] == 'TUESDAY' || $row['dayoff5'] == 'TUESDAY' ? '1' : 'NULL') . "', ";
            $update_sql .= "wednesday = '" . ($row['dayoff1'] == 'WEDNESDAY' || $row['dayoff2'] == 'WEDNESDAY' || $row['dayoff3'] == 'WEDNESDAY' || $row['dayoff4'] == 'WEDNESDAY' || $row['dayoff5'] == 'WEDNESDAY' ? '1' : 'NULL') . "', ";
            $update_sql .= "thursday = '" . ($row['dayoff1'] == 'THURSDAY' || $row['dayoff2'] == 'THURSDAY' || $row['dayoff3'] == 'THURSDAY' || $row['dayoff4'] == 'THURSDAY' || $row['dayoff5'] == 'THURSDAY' ? '1' : 'NULL') . "', ";
            $update_sql .= "friday = '" . ($row['dayoff1'] == 'FRIDAY' || $row['dayoff2'] == 'FRIDAY' || $row['dayoff3'] == 'FRIDAY' || $row['dayoff4'] == 'FRIDAY' || $row['dayoff5'] == 'FRIDAY' ? '1' : 'NULL') . "', ";
            $update_sql .= "saturday = '" . ($row['dayoff1'] == 'SATURDAY' || $row['dayoff2'] == 'SATURDAY' || $row['dayoff3'] == 'SATURDAY' || $row['dayoff4'] == 'SATURDAY' || $row['dayoff5'] == 'SATURDAY' ? '1' : 'NULL') . "', ";
            $update_sql .= "sunday = '" . ($row['dayoff1'] == 'SUNDAY' || $row['dayoff2'] == 'SUNDAY' || $row['dayoff3'] == 'SUNDAY' || $row['dayoff4'] == 'SUNDAY' || $row['dayoff5'] == 'SUNDAY' ? '1' : 'NULL') . "' ";
            $update_sql .= "WHERE name = '" . $row['name'] . "'";

            if ($conn->query($update_sql) === TRUE) {
                // echo "Record updated successfully for " . $row['name'] . "<br>";
            } else {
                echo "Error updating record: " . $conn->error;
            }
        }
    }
} else {
    echo "0 results";
}


// Close connection
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day Off Schedule Table</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Custom Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        h2 {
            margin-top: 20px;
            margin-bottom: 20px;
            text-align: center;
            color: #343a40;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        @media (max-width: 576px) {
            table {
                font-size: 14px;
            }
            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2> Day Off Schedule Table</h2>
        <div class="table-responsive">
            <table class="table">
                <thead class="thead-light">
                    <tr>
                        <th>Name</th>
                        <th>Monday</th>
                        <th>Tuesday</th>
                        <th>Wednesday</th>
                        <th>Thursday</th>
                        <th>Friday</th>
                        <th>Saturday</th>
                        <th>Sunday</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Assuming you have already established a database connection
                    $servername = "localhost";
                    $username = "root"; // Replace with your MySQL username
                    $password = ""; // Replace with your MySQL password
                    $dbname = "payroll";

                    // Create connection
                    $conn = new mysqli($servername, $username, $password, $dbname);

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Query the schedule table
                    $sql = "SELECT name, IFNULL(monday, '') AS monday, IFNULL(tuesday, '') AS tuesday, IFNULL(wednesday, '') AS wednesday, IFNULL(thursday, '') AS thursday, IFNULL(friday, '') AS friday, IFNULL(saturday, '') AS saturday, IFNULL(sunday, '') AS sunday FROM schedule";
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
                            echo "<td><a href=\"employee-edit-schedule.php?id={$row['name']}\" class=\"btn btn-success btn-sm\">Edit</a></td>";

                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>0 results</td></tr>";
                    }

                    // Close connection
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>