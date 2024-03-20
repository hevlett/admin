<?php include('includes/header.php'); ?>
<?php

$servername = "localhost"; // Change this if your MySQL server is hosted elsewhere
$username = "root";
$password = "";
$database = "payroll";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_GET['id']) && isset($_GET['month']) && isset($_GET['year']) && isset($_GET['cutoffs'])) {
    $employeeData = $_GET['id'];
    $month = $_GET['month'];
    $year = $_GET['year'];
    $cutoffs = $_GET['cutoffs'];
} else {
    echo '<h5>Missing Parameters</h5>';
    return false;
}

// Prepare SQL statement to fetch employee data
$sql = "SELECT * FROM employee WHERE name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employeeData);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Check if there are any results
if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $ids = $row["idnumber"];
      
        $EmployeeDepartment = $row["department"];
        // Assuming $EmployeeName should be set here
        $EmployeeName = $employeeData;

        // fix late and timeout  value check for database : SELECT * FROM time_tracker WHERE name = 'LOPEZ, MERVIN VINCE'
        // and check this :http://admin.test/employee-dtr2.php?id=LOPEZ,%20MERVIN%20VINCE&month=February&year=2024&cutoffs=1st
    }
} else {
    echo "No data available for this employee ID";
}

// Close prepared statement and database connection

function getTimeTrackerRecords($employeeName, $day) {
    // Database connection parameters
    $host = "localhost"; // Change this if your MySQL server is hosted elsewhere
    $username = "root";
    $password = "";
    $dbname = "payroll";

    try {
        // Connect to the database
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        
        // Set PDO to throw exceptions on errors
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL query
        $sql = "SELECT * FROM time_tracker WHERE name = :employeeName AND day = :day";
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':employeeName', $employeeName, PDO::PARAM_STR);
        $stmt->bindParam(':day', $day, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch all rows
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $records;
    } catch (PDOException $e) {
        // Handle database errors
        echo "Error: " . $e->getMessage();
        return false; // Or handle the error in a different way as needed
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Pinagsama, City of Taguig</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Barangay Pinagsama, City of Taguig</h2>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Employee ID</th>
            <th>Name</th>
            <th>Department</th>
            <th>Pay Period</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?php echo $ids ?></td>
            <td><?php echo $EmployeeName ?></td>
            <td><?php echo $EmployeeDepartment ?></td>
            <td><?php echo $cutoffs ?></td>
        </tr>
        </tbody>

        <thead>
        <tr>
            <th>Day</th>
            <th>Time In</th>
            <th>Time Out</th>
            <th>Late (Hrs:Minute)</th>
        </tr>
        </thead>

        <tbody>
        <?php
        // Check the pay period
        if ($cutoffs == "1st" || $cutoffs == "2nd") {
            // Display days 1-15 for 1st pay period, days 16-31 for 2nd pay period
            $start_day = ($cutoffs == "1st") ? 1 : 16;
            $end_day = ($cutoffs == "1st") ? 15 : 31;

            for ($day = $start_day; $day <= $end_day; $day++) {
                // Fetch records for the current day
                $records = getTimeTrackerRecords($employeeData, $day);

                // Display table row for the current day
                echo "<tr>";
                echo "<td>$day</td>";

                if ($records) {
                    // Display time in, time out, and late for each record
                    foreach ($records as $record) {
                        echo "<td>" . $record['timein'] . "</td>";
                        $timeinValue = $record['timein'];
                        $timeoutValue =$record['timeout'];

                        if($timeoutValue == $timeinValue){
                            $timeoutValue = "No Time Out";
                            echo "<td>" .$timeoutValue . "</td>";
                           
                        }else if($timeinValue == "Absent"){
                            $timeoutValue = "Absent";
                            echo "<td>" .$timeoutValue . "</td>";
                        }
                        else{
                            $timeoutValue =$record['timeout'];
                            echo "<td>" .$timeoutValue . "</td>";
                        }
                   

                        $time_in = strtotime($record['timein']);
                        // Initialize late time
                        $late_time = "No Late";

                        $sqlForEmployeeSchedule = "SELECT * FROM employee WHERE name = '$EmployeeName'";
                        $resultEmployee = $conn->query($sqlForEmployeeSchedule);
                        
                        if ($resultEmployee->num_rows > 0) {
                            // Output data of each row
                            while ($row = $resultEmployee->fetch_assoc()) {
                                $workignSched = $row['timein']; // Corrected variable name to $row
                                
                                if($timeoutValue == "Absent"){
                                    echo "<td>Absent</td>";
                                }else{
                                    if (strtotime(date("H:i", $time_in)) > strtotime($workignSched)) {
                                        // Calculate late time in minutes
                                        $late_minutes = (strtotime(date("H:i", $time_in)) - strtotime($workignSched)) / 60;
                                        // Convert negative values to positive
                                        $late_minutes = abs($late_minutes);
                                        // Convert late time from minutes to HH:MM format
                                        $late_time = sprintf("%02d:%02d", floor($late_minutes / 60), $late_minutes % 60);
                                        echo "<td> $late_time</td>";
                                    } elseif (strtotime(date("H:i", $time_in)) == strtotime($workignSched)) {
                                        echo "<td>No Time Out</td>"; // Display on time if not late
                                    } else {
                                        echo "<td>On Time</td>"; // Handle case when there is no time out
                                    }
                                }
                                
                                

                            }
                        } else {
                            // echo "<tr><td colspan='4'>No data available</td></tr>";
                        }
                        


                        // Check if time in is after 07:00
           
   // Display the late time
                        // if($timeinValue == "Absent"){
                        //     $late_time = "Absent";
                        //     echo "<td>$late_time</td>";
                        // }else{
                          
                        // }
                     
                        
                    }
                } else {
                    // If no records found for the current day, display empty cells
                    echo "<td></td><td></td><td></td>";
                }

                echo "</tr>";
            }
        } else {
            // Handle invalid pay period
            echo "<tr><td colspan='4'>Invalid pay period</td></tr>";
        }

        $stmt->close();
$conn->close();

        ?>
        </tbody>
    </table>

    <div style="text-align: center;">
        <p style="text-align: center;">I CERTIFY ON MY HONOR THAT THE ABOVE IS A TRUE AND <br> CORRECT REPORT OF THE HOURS OF WORK PERFORMED, RECORD OF WHICH WAS MADE DAILY AT THE <br> TIME OF ARRIVAL AND DEPARTURE FROM OFFICE</p>
        <br>
        <hr style="width: 50%; border-color: black; border-style: solid; margin-left:25%;">
        <p><?php echo $EmployeeName?></p>
        <p>SIGNATURE</p>
    </div>
</div>
</body>
</html>
