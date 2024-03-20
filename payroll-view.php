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
            WHERE month = '$monthNumber' AND YEAR = '$year' 
            AND $day_range
            AND timein IS NOT NULL AND timeout != 'No Time Out' AND timein != 'Absent'
            GROUP BY name";

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
                </tr>
                </thead>
                <tbody>
                <?php
            while ($row = $result->fetch_assoc()) {
                $name = $row["name"];
                $working_days = $row["working_days"];

                // Get pDay from the employee table for the current employee
                $sql_pDay = "SELECT pDay FROM employee WHERE name='$name'";
                $result_pDay = $conn->query($sql_pDay);

                if ($result_pDay->num_rows > 0) {
                    $row_pDay = $result_pDay->fetch_assoc();
                    $pDay = $row_pDay['pDay'];

                    // Calculate daily wage based on pDay
                    $daily_wage = $pDay; // Assuming pDay is the daily wage
                    $total_salary = $working_days * $daily_wage;

                    // Check if data for this employee already exists in the salary table
                    $sql_check = "SELECT * FROM salary WHERE name='$name' AND month='$month' AND year='$year' AND cutoff='$cutoffs'";
                    $result_check = $conn->query($sql_check);


                    if ($result_check->num_rows > 0) {
                        // If data exists, fetch and display it
                        while ($row_check = $result_check->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row_check['name'] . "</td>";
                            echo "<td>" . $row_check['workingdays'] . "</td>";
                            echo "<td>" . $row_check['totalsalary'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        // If data doesn't exist, insert it into the salary table
                        $sql_insert = "INSERT INTO salary (name, workingdays, totalsalary, month, year, cutoff) 
                                    VALUES ('$name', '$working_days', '$total_salary', '$month', '$year', '$cutoffs')";

                        if ($conn->query($sql_insert) === TRUE) {
                            // Display the newly inserted data
                            echo "<tr>";
                            echo "<td>$name</td>";
                            echo "<td>$working_days</td>";
                            echo "<td>$total_salary</td>";
                            echo "</tr>";
                        } else {
                            echo "Error inserting record: " . $conn->error;
                        }
                    }
                } else {
                    echo "No pDay found for employee: $name";
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
