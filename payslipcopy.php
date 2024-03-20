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
        $EmployeeName = $row["name"] ;
        $EmployeeDepartment = $row["department"] ;
        $pMonth = $row["pMonth"] ;
        $pcutoff = $row["pcutoff"] ;
        
    }
} else {
    echo "No data available for this employee ID";
}


// Prepare and bind the SQL statement for retrieving salary information
$sqlSalary = "SELECT * FROM salary WHERE name = ? AND month = ? AND year = ? AND cutoff = ?";
$stmtSalary = $conn->prepare($sqlSalary);

// Bind parameters
$stmtSalary->bind_param("ssss", $EmployeeName, $month, $year, $cutoffs);

// Execute the statement
$stmtSalary->execute();

// Get the result
$resultSalary = $stmtSalary->get_result();

if ($resultSalary->num_rows > 0) {
    // Output data of each row
    while ($rowSalary = $resultSalary->fetch_assoc()) {
        $workingDays = $rowSalary["workingdays"];
        $salarys = $rowSalary["totalsalary"];
    }


    echo $workingDays;

// // SQL query to select and sum late times for a specific name excluding "Not Late" entries
// $sql = "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(late))) AS total_late_seconds
// FROM time_tracker
// WHERE name = '$EmployeeName'
// AND late != 'Not Late'";

// $result = $conn->query($sql);

// if ($result->num_rows > 0) {
// // Fetching the total late seconds and storing it in a variable
// $row = $result->fetch_assoc();
// $totalLateSeconds = $row["total_late_seconds"];

// // Explode the time value into hours, minutes, and seconds
// list($hours, $minutes, $seconds) = explode(':', $totalLateSeconds);

// // Calculate the total hours by converting minutes to hours and adding hours
// $totalHours = ($hours + ($minutes / 60));
// } else {
// echo "0 results";
// }

// // SQL query to count the number of late values for a specific name
// $sqlLateCount = "SELECT COUNT(*) AS late_count
// FROM time_tracker
// WHERE name = '$EmployeeName'
// AND late != 'Not Late'";

// $resultLateCount = $conn->query($sqlLateCount);

// if ($resultLateCount->num_rows > 0) {
// // Fetching the late count
// $rowLateCount = $resultLateCount->fetch_assoc();
// $lateCount = $rowLateCount["late_count"];
// } else {
// echo "0 results for late count";
// }

// // SQL query to select all late times for a specific name
// $sqlLateData = "SELECT late
//         FROM time_tracker
//         WHERE name = '$EmployeeName'";

// $resultLateData = $conn->query($sqlLateData);

// $totalWorkingSeconds = 0;

// if ($resultLateData->num_rows > 0) {
// // Fetching late data and performing calculations
// while ($rowLateData = $resultLateData->fetch_assoc()) {
// $late = $rowLateData["late"];
// if ($late != 'Not Late') {
//     // Convert late time to seconds
//     $lateSeconds = strtotime($late) - strtotime('00:00:00');
//     // Calculate remaining seconds in the workday
//     $remainingSeconds = (8 * 3600) - $lateSeconds;
//     // Add remaining seconds to total working seconds
//     $totalWorkingSeconds += $remainingSeconds;
// }
// }
// // Convert total working seconds to HH:MM:SS format
// $totalWorkingTime = gmdate("H:i:s", $totalWorkingSeconds);
// } else {
// $totalWorkingTime = 0;
// }



// $totalNumberofabsent = 16 - $workingDays;
// $totalLateDeduction = $pHr * $lateCount;
// $totalAbsentDeduction = $pDay * $totalNumberofabsent;
// $totalDeduction = $totalLateDeduction + $totalAbsentDeduction;
// $netPay = $salarys - $totalDeduction ;

} else {
    echo "No Payslip In This Cut OFF";
}

$stmt->close();
$conn->close();

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payslip</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .payslip {
      margin: 20px auto;
      padding: 20px;
      max-width: 50%;
      border: none;
      border-radius: 20px;
      background: linear-gradient(to bottom, #ffffff, #f8f9fa);
      box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
    }
    .payslip-header {
      background: linear-gradient(to bottom, #007bff, #0056b3);
      color: #fff;
      padding: 20px;
      border-top-left-radius: 20px;
      border-top-right-radius: 20px;
      text-align: center;
    }
    .payslip-header h2 {
      margin: 0;
    }
    .payslip-info p {
      margin: 5px 0;
    }
    .payslip-info strong {
      font-weight: bold;
    }
    .payslip-details {
      margin-top: 20px;
    }
    .payslip-details p {
      margin: 5px 0;
    }
    .total {
      font-weight: bold;
      font-size: 18px;
    }
    .total-deductions {
      color: #dc3545;
    }
    .net-pay {
      color: #28a745;
    }
    hr {
      border-top: 2px solid #007bff;
    }
    .icon {
      font-size: 20px;
      margin-right: 10px;
    }
    .payslip-info .col-md-6, .payslip-details .col-md-6 {
     
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 15px;
     
    }
    .total-deductions, .net-pay {
      background-color: #fff;
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 15px;
      box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
      transition: background-color 0.3s ease;
    }
    .total-deductions:hover, .net-pay:hover {
      background-color: #f8f9fa;
    }
  </style>
</head>
<body>
<div class="container payslip mt-5">
    <div class="payslip-header mt-5">
        <h2><i class="fas fa-file-invoice-dollar icon"></i>Payslip</h2>
    </div>
    <!-- Displaying Name, Month, Year, and Cutoff -->
    <div class="row mt-5">
        <div class="col-md-3">
            <p><strong>Name: </strong><?php echo $EmployeeName; ?></p>
        </div>
        <div class="col-md-3">
            <p><strong>Month: </strong> <?php echo $month; ?></p>
        </div>
        <div class="col-md-3">
            <p><strong>Year: </strong> <?php echo $year; ?></p>
        </div>
        <div class="col-md-3">
            <p><strong>Cutoff: </strong> <?php echo $cutoffs; ?></p>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <div class="payslip-info">
                <!-- Working Hours Section -->
                <p><i class="far fa-clock icon mt-5"></i><strong>Working Hours:</strong>  <?php if(isset($totalWorkingTime)){ echo $totalWorkingTime ;}else{ echo "00";} ?> </p>
                <!-- Working Days Section -->
                <p><i class="far fa-calendar-alt icon mt-5"></i><strong>Working Days:</strong>  <?php if(isset($workingDays)){ echo $workingDays ;}else{ echo "00";} ?> </p>
                <!-- Total Deduction of Late Section -->
                <p><i class="fas fa-minus-circle icon mt-5"></i><strong>Total Deduction of Late: </strong> <?php if(isset($totalLateDeduction)){ echo $totalLateDeduction ;}else{ echo "00";} ?> </p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="payslip-details">
                <!-- Total Hours of Late Section -->
                <p><i class="far fa-clock icon " style="margin-top:8%;"></i><strong>Total Hours of Late: </strong> <?php if(isset($totalHours)){ echo $totalHours ;}else{ echo "00";} ?> </p>
                <!-- Total Days of Absent Section -->
                <p><i class="fas fa-calendar-times icon mt-5"></i><strong>Total Days of Absent: </strong><?php if(isset($totalNumberofabsent)){ echo $totalNumberofabsent ;}else{ echo "00";} ?></p>
                <!-- Total Deduction of Absent Section -->
                <p><i class="fas fa-minus-circle icon mt-5"></i><strong>Total Deduction of Absent: </strong><?php if(isset($totalAbsentDeduction)){ echo $totalAbsentDeduction ;}else{ echo "00";} ?> </p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
            <div class="payslip-details">
                <!-- Total Hours of Late Section -->
                <p><i class="fas fa-minus-circle icon mt-5"></i><strong>Salary: </strong><?php if(isset($salarys)){ echo $salarys ;}else{ echo "00";} ?></p>
            </div>
        </div>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <div class="total-deductions">
                <p><strong>Total Deductions: </strong> <?php if(isset($totalDeduction)){ echo $totalDeduction ;}else{ echo "00";} ?></p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="net-pay mb-5">
                <p><strong>Net Pay: </strong><?php if(isset($netPay)){ echo $netPay ;}else{ echo "00";} ?> </p>
            </div>
        </div>
    </div>
</div>

</body>
</html>