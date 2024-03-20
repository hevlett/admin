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

if(isset($_GET['id']))
{
    if($_GET['id'] != ''){
        $employeeData = $_GET['id'];
        $month = $_GET['month'];
        $year = $_GET['year'];
        $cutoffs = $_GET['cutoffs'];

      
        
    }else{
        echo '<h5>No Id Found</h5>';
        return false;
    }
}
else
{
    echo '<h5>No Id given in params</h5>';
    return false;
}

echo $employeeData;
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind the SQL statement
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
      $ids = $row["idnumber"] ;
        // echo "ID Number: " . $row["idnumber"] . "<br>";
       $EmployeeDepartment = $row["department"] ;
    }
} else {
    echo "No data available for this employee ID";
}

// Close prepared statement and database connection
$stmt->close();
$conn->close();






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
        $stmt->bindParam(':day', $day, PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Fetch all rows
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the connection
        $pdo = null;

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
        <td><?php echo $employeeData ?></td>
        <td><?php echo $EmployeeDepartment ?></td>
        <td><?php echo $cutoffs ?></td>
       
      </tr>
    </tbody>

    <thead>
      <tr>
        <th>Day</th>
        <th>Time In</th>
        <th>Time Out</th>
        <th>Late</th>
       
      </tr>
    </thead>

    <tbody>
      <?php
      // Check the pay period
      if ($cutoffs == "1st") {
          // Display days 1-15 for 1st pay period
          for ($day = 1; $day <= 15; $day++) {
              echo "<tr>";
              echo "<td>$day</td>";
              echo "<td>Time In</td>";
              echo "<td>Time Out</td>";
              echo "<td>Late</td>";
              echo "</tr>";
          }
      } elseif ($cutoffs == "2nd") {
          // Display days 16-31 for 2nd pay period
          for ($day = 16; $day <= 31; $day++) {
            // Fetch records for the current day
        
            $records = getTimeTrackerRecords($employeeData, $day); // Adjust date format as needed
        
            // Display table row for the current day
            echo "<tr>";
            echo "<td>$day</td>";
        
            if ($records) {
                // Display time in, time out, and late for each record
                foreach ($records as $record) {
                    echo "<td>" . $record['timein'] . "</td>";
                    echo "<td>" . date("h:i ", strtotime($record['timeout'])) . "</td>";
                    $time_in = strtotime($record['timein']);

                    // Convert time in to timestamp
                    $time_in = strtotime($record['timein']);
                    // Extract hours and minutes
                    $standard_time_in_hours = date("H", $time_in);
                    $standard_time_in_minutes = date("i", $time_in);
                    // Initialize late time
                    $late_time = "No Late";

                    // Check if time in is after 07:00
                    if ($standard_time_in_hours < 7 || ($standard_time_in_hours == 7 && $standard_time_in_minutes > 0)) {
                        // Calculate late time in minutes
                        $late_minutes = (7 - $standard_time_in_hours) * 60 - $standard_time_in_minutes;
                        // Convert negative values to positive
                        $late_minutes = abs($late_minutes);
                        // Convert late time from minutes to HH:MM format
                        $late_time = sprintf("%02d:%02d", floor($late_minutes / 60), $late_minutes % 60);
                    }

                    // Display the late time
                    echo "<td>$late_time</td>";


                 
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

      <div class="text-center mt-5">
              <h2>ADMIN COPY</h2>
      </div>


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
        <td><?php echo $employeeData ?></td>
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
        <th>Late</th>
       
      </tr>
    </thead>

    <tbody>
      <?php
      // Check the pay period
      if ($cutoffs == "1st") {
          // Display days 1-15 for 1st pay period
          for ($day = 1; $day <= 15; $day++) {
              echo "<tr>";
              echo "<td>$day</td>";
              echo "<td>Time In</td>";
              echo "<td>Time Out</td>";
              echo "<td>Late</td>";
              echo "</tr>";
          }
      } elseif ($cutoffs == "2nd") {
          // Display days 16-31 for 2nd pay period
          for ($day = 16; $day <= 31; $day++) {
            // Fetch records for the current day
        
            $records = getTimeTrackerRecords($EmployeeName, $day); // Adjust date format as needed
        
            // Display table row for the current day
            echo "<tr>";
            echo "<td>$day</td>";
        
            if ($records) {
                // Display time in, time out, and late for each record
                foreach ($records as $record) {
                    echo "<td>" . $record['timein'] . "</td>";
                    echo "<td>" . date("h:i ", strtotime($record['timeout'])) . "</td>";
                    $time_in = strtotime($record['timein']);

                    // Convert time in to timestamp
                    $time_in = strtotime($record['timein']);
                    // Extract hours and minutes
                    $standard_time_in_hours = date("H", $time_in);
                    $standard_time_in_minutes = date("i", $time_in);
                    // Initialize late time
                    $late_time = "No Late";

                    // Check if time in is after 07:00
                    if ($standard_time_in_hours < 7 || ($standard_time_in_hours == 7 && $standard_time_in_minutes > 0)) {
                        // Calculate late time in minutes
                        $late_minutes = (7 - $standard_time_in_hours) * 60 - $standard_time_in_minutes;
                        // Convert negative values to positive
                        $late_minutes = abs($late_minutes);
                        // Convert late time from minutes to HH:MM format
                        $late_time = sprintf("%02d:%02d", floor($late_minutes / 60), $late_minutes % 60);
                    }

                    // Display the late time
                    echo "<td>$late_time</td>";


                 
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


</body>
</html>



<!-- <?php include('includes/footer.php'); ?>  -->
