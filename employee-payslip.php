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
        $employeeName = $_GET['id'];
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

echo $employeeName ." ". $month." " . $year. " " . $cutoffs;

// Prepare and bind the SQL statement
$sql = "SELECT * FROM salary WHERE name = ? AND month = ? AND year = ? AND cutoff = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $employeeName, $month, $year, $cutoffs);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Check if there are any results
if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        // Access data from $row array
        $workingDays = $row["workingdays"];
        $totalSalary = $row["totalsalary"];
        $totalworkinghrs = $row["totalhrs"];
        $totallate = $row["totallate"];
        $hours = $totallate / 60;

       // Access other fields as needed
$totalabsent = 15 - $workingDays;

          // Fetching pcutoff from employee table
          $employeesql = "SELECT * FROM employee WHERE name = ?";
          $stmts = $conn->prepare($employeesql);
          $stmts->bind_param("s", $employeeName);

          // Execute the statement
          $stmts->execute();

          // Get the result
          $results = $stmts->get_result();

          if ($results->num_rows > 0) {
              // Output data of each row
              while ($rows = $results->fetch_assoc()) {
                  // Access pcutoff from $rows array, not $row
                  $cutoff = $rows["pcutoff"];
                  $worktime = $rows["totalhrswork"];
                  $pday = $cutoff / 15;
                  $phr = $pday /$worktime;
                  $phrInt = (int)$phr;
                  $hoursInt = (int)$hours;
                  $totalLateDeduction = $hoursInt * $phrInt;
                  $totalAbsentDeduction = $pday * $totalabsent;
                  // You might want to do something with $cutoff here
                  $netpay = $workingDays * $pday;
                  $totaldeductions = $totalLateDeduction + $totalAbsentDeduction;
                  }
          }

    }
} else {
    echo "No data available for this criteria";
}

// Close statement and connection
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
            <p><strong>Name: </strong><?php echo $employeeName; ?></p>
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
                <p><i class="far fa-clock icon mt-5"></i><strong>Total Working Hours:</strong>  <?php if(isset($totalworkinghrs)){ echo $totalworkinghrs ;}else{ echo "00";} ?>  </p>
                <!-- Working Days Section -->
                <p><i class="far fa-calendar-alt icon mt-5"></i><strong>Total Working Days:</strong>  <?php if(isset($workingDays)){ echo $workingDays ;}else{ echo "00";} ?> </p>
                <!-- Total Deduction of Late Section -->
                <p><i class="fas fa-minus-circle icon mt-5"></i><strong>Total Deduction of Late : </strong> <br>
                Total Hours of late  <?php if(isset($hoursInt)){ echo $hoursInt ;}else{ echo "00";} ?>  * 
                <?php if(isset($phrInt)){ echo $phrInt ;}else{ echo "00";}  echo " = ". $totalLateDeduction;?> </p> 
            </div>
        </div>
        <div class="col-md-6">
            <div class="payslip-details">
                <!-- Total Hours of Late Section -->
                <p><i class="far fa-clock icon " style="margin-top:8%;"></i><strong>Total Hours of Late: </strong> <?php if(isset($hoursInt)){ echo $hoursInt ;}else{ echo "00";} ?> </p>
                <!-- Total Days of Absent Section -->
                <p><i class="fas fa-calendar-times icon mt-5"></i><strong>Total Days of Absent: </strong><?php if(isset($totalabsent)){ echo $totalabsent ;}else{ echo "00";} ?></p>
                <!-- Total Deduction of Absent Section -->
                <p><i class="fas fa-minus-circle icon mt-5"></i><strong>Total Deduction of Absent: </strong>
                <br>
                <?php if(isset($totalabsent)){ echo $totalabsent ;}else{ echo "00";} ?> (number of absent) *  <?php if(isset($pday)){ echo $pday ;}else{ echo "00";} ?> (per day)
               = <?php if(isset($totalAbsentDeduction)){ echo $totalAbsentDeduction ;}else{ echo "00";} ?>
              </p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
            <div class="payslip-details">
                <!-- Total Hours of Late Section -->
                <p><i class="fas fa-minus-circle icon mt-5"></i><strong>Salary: </strong><?php if(isset($cutoff)){ echo $cutoff ;}else{ echo "00";} ?></p>
            </div>
        </div>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <div class="total-deductions">
                <p><strong>Total Deductions: </strong> <?php if(isset($totaldeductions)){ echo $totaldeductions ;}else{ echo "00";} ?></p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="net-pay mb-5">
                <p><strong>Net Pay: </strong><?php if(isset($netpay)){ echo $netpay ;}else{ echo "00";} ?> </p>
            </div>
        </div>
    </div>
</div>

</body>
</html>