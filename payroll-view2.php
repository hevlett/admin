<?php include_once('includes/header.php'); ?>
<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "payroll";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if month, year, and cutoffs are set
if(isset($_GET['month']) && isset($_GET['year']) && isset($_GET['cutoffs'])) {
    $month = $_GET['month'];
    $year = $_GET['year'];
    $cutoffs = $_GET['cutoffs'];

    $monthsc = $month;
    $yearsc = $year;
    // Extract month number from the provided month string
    $monthNumber = date('m', strtotime($month));

    // Define the day range based on cutoffs
    if ($cutoffs == "2nd") {
        $day_range = "day >= 16 AND day <= 31";
    } elseif ($cutoffs == "1st") {
        $day_range = "day >= 1 AND day <= 15";
    } else {
        echo "Invalid cutoff value.";
        exit; // Terminate the script if cutoff value is invalid
    }

    // SQL query to calculate the total working days for each employee
    $sql = "SELECT name, COUNT(*) AS working_days 
    FROM time_tracker 
    WHERE month = '$month' AND YEAR = '$year' 
    AND $day_range
    AND timein IS NOT NULL AND timeout != 'No Time Out' AND timein != 'Absent' AND totalhrs != '0'
    GROUP BY name
    ORDER BY name ASC";


    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        ?>
        <div class="container mt-5">
            <h2>Employee Payroll</h2>
            <table class="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Working Days</th>
                    <th>Total Salary</th>
                    <th>Total Late (Minute.seconds)</th>
                    <th>Total Hours (Hours.Minute)</th>
                  
                </tr>
                </thead>
                <tbody>
                <?php
            while ($row = $result->fetch_assoc()) {
                $name = $row["name"];
                $working_days = $row["working_days"];
                // $totalhrswork = $row["totalhrswork"];

                // Get pDay from the employee table for the current employee
                $sql_pDay = "SELECT pMonth FROM employee WHERE name='$name'";
                $result_pDay = $conn->query($sql_pDay);

                if ($result_pDay->num_rows > 0) {
                    $row_pDay = $result_pDay->fetch_assoc();
                    $pMOnth = $row_pDay['pMonth'];

                    // Calculate daily wage based on pDay
                    $monthlyWaige = $pMOnth; // Assuming pDay is the daily wage
                    $splitcutOff = $monthlyWaige/2;
                    //$pDays  = $splitcutOff /13;
                    if($cutoffs == "1st"){
                        $pDays  = $splitcutOff /13;
                    }else{
                        $pDays  = $splitcutOff /14;
                    }
                    $total_salary  = $pDays * $working_days;
                    $sqlTimeinTimeTracker = "SELECT * FROM time_tracker WHERE name='$name'";
                    $time_trackResult = $conn->query($sqlTimeinTimeTracker);
                    if ($time_trackResult->num_rows > 0) {
                        $employeeLateTotal = array(); // Initialize an array to store total late time for each employee
                        
                        while ($timeRow = $time_trackResult->fetch_assoc()) {
                            $totalhrs = $timeRow["totalhrs"];
                            $year = $timeRow["totalhrs"];
                            $month = $timeRow["totalhrs"];
                            $cutoff = $timeRow["totalhrs"];
                            $total_hrs_difference = 0; // Initialize the total_hrs_difference outside the employee loop
                            
                            $sqlemployeeTime = "SELECT * FROM employee WHERE name='$name'";
                            $time_employee = $conn->query($sqlemployeeTime);
                            
                            if ($time_employee->num_rows > 0) {
                                while ($timeRowE = $time_employee->fetch_assoc()) {
                                    $totalhrsworkss = $timeRowE["totalhrswork"];
                                    
                                    // Check if timein is not equal to "Absent"
                                    if ($timeRowE["timein"] !== "Absent") {
                                        // Calculate the hours difference
                                        $hrs_difference = abs($totalhrsworkss - $totalhrs);
                                        
                                        // Add the current hrs_difference to the total_hrs_difference
                                        $total_hrs_difference += $hrs_difference;
                                    }
                                }
                            }
                            
                            // Store or accumulate the total late time for the current employee
                            if (!isset($employeeLateTotal[$name])) {
                                $employeeLateTotal[$name] = $total_hrs_difference;
                            } else {
                                $employeeLateTotal[$name] += $total_hrs_difference;
                            }
                        }
                        
                        // Print the total late time for each employee
                   // Print the total late time for each employee
                    foreach ($employeeLateTotal as $employeeName => $lateTotal) {
                        echo "<tr>";
                        echo "<td>" . $employeeName . "</td>";
                        echo "<td>" . $working_days . "</td>";
                        echo "<td>" . $total_salary . "</td>";
                        echo "<td>" . $lateTotal . "</td>";

                        // Calculate and display total hours worked
                        $sqlTotalHrs = "SELECT SUM(totalhrs) AS total_hours FROM time_tracker WHERE name='$employeeName'";
                        $resultTotalHrs = $conn->query($sqlTotalHrs);
                        if ($resultTotalHrs->num_rows > 0) {
                            $rowTotalHrs = $resultTotalHrs->fetch_assoc();
                            $totalHoursWorked = $rowTotalHrs['total_hours'];
                            echo "<td>" . $totalHoursWorked . "</td>";
                        } else {
                            echo "<td>0</td>"; // If no total hours found, display 0
                        }

                        echo "</tr>";

                     // Check if data for this employee already exists in the salary table
// Check if data for this employee already exists in the salary table
$sql_check = "SELECT * FROM salary WHERE name='$employeeName' AND month='$monthsc' AND year='$yearsc' AND cutoff='$cutoffs'";
$result_check = $conn->query($sql_check);

if ($result_check->num_rows == 0) {
    // If data doesn't exist, insert it into the salary table
    $sql_insert = "INSERT INTO salary (name, workingdays, totalsalary, month, year, cutoff, totallate, totalhrs) 
                  VALUES ('$employeeName', '$working_days', '$total_salary', '$monthsc', '$yearsc', '$cutoffs', '$lateTotal', '$totalHoursWorked')";

    if ($conn->query($sql_insert) === TRUE) {
        // Display the newly inserted data
        echo "<tr>";
        echo "<td>$name</td>";
        echo "<td>$working_days</td>";
        echo "<td>$total_salary</td>";
        echo "<td>$lateTotal</td>";
        echo "<td>$totalHoursWorked</td>";
        echo "</tr>";
    } else {
        echo "Error inserting record: " . $conn->error;
    }
} else {
    // If data already exists, you might want to notify or handle it accordingly
    // echo "Data already exists for $employeeName, $month $year $cutoffs";
}

                    }

                    }
                } else {
                    echo "No Pmonth found for employee: $name";
                }
            }
                ?>
                </tbody>
            </table>
        </div>
        <?php
    } else {
        echo "No data available for the specified month and year.";
        echo '<br>';
        echo $month." " .$year." ".$cutoffs;
        ?>
        <?php
    }
} else {
    echo "Month, year, or cutoffs are not set.";
}

// Close connection
$conn->close();
?>
