<?php include_once('includes/header.php'); ?>
<?php

$servername = "localhost"; // Change this if your MySQL server is hosted elsewhere
$username = "root";
$password = "";
$database = "payroll";

$conn = new mysqli($servername, $username, $password, $database);



// Your database connection code here
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if(isset($_GET['month']) && isset($_GET['year'])) {
    $month = $_GET['month'];
    $year = $_GET['year'];
    



}
// Fetch data from employee and salary tables
$sql = "SELECT 
            emp.idnumber,
            emp.name,
            emp.department,
            sub_query.workingdays,
            sub_query.totalsalarycutoff1st,
            sub_query.totalsalarycutoff2nd,
            (sub_query.totalsalarycutoff1st + sub_query.totalsalarycutoff2nd) AS total_salary
        FROM 
            employee emp
        INNER JOIN
            (
                SELECT
                    name,
                    workingdays,
                    MAX(CASE WHEN cutoff = '1st' THEN totalsalary END) AS totalsalarycutoff1st,
                    MAX(CASE WHEN cutoff = '2nd' THEN totalsalary END) AS totalsalarycutoff2nd
                FROM
                    salary
                WHERE
                    month = '$month' AND year = '$year'
                GROUP BY
                    name, workingdays
            ) AS sub_query ON emp.name COLLATE utf8mb4_unicode_ci = sub_query.name COLLATE utf8mb4_unicode_ci";


$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Salary Table</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Payroll for the Month of <?php  echo $month." ".$year;?></h2>
        <table class="table table-striped">
        <thead>
            <tr>
                <th>Barangay</th>
                <th></th>
                <th>City/ Municipality</th>
                <th></th>
                <th>Payroll number</th>
                <th></th>
                <th>Barangay Tresurer</th>
            
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Pinagsama</td>
                <td></td>
                <td>Taguig</td>
                <td></td>
                <td><?php echo $month." ".$year; ?></td>
                <th></th>
                <td>Maria Diossebelle B. Abella</td>
            
            </tr>
            </tbody>
            <thead>
                <tr>  
                    <th>ID Number</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>1st Cutoff</th>
                    <th>2nd Cutoff</th>
                    <th>Total Salary</th>
                    <th>Signature</th>
                  
                   
                </tr>
            </thead>
            <tbody>
    <?php
    // Check if there are rows returned from the query
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['idnumber'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['department'] . "</td>";
            echo "<td>" . intval($row['totalsalarycutoff1st']) . "</td>";
            echo "<td>" . intval($row['totalsalarycutoff2nd']) . "</td>";
            echo "<td>" . intval($row['total_salary']) . "</td>";
            echo "<td></td>"; // Adjust according to your requirement
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No data found</td></tr>";
    }
    ?>
</tbody>

<thead>
    <tr>
        <th colspan="2">A. CERTIFIED as to availability of appropriation for obligation in the</th>
        <th colspan="2">B. CERTIFIED as to availability of Funds and Completeness and propriety of Supporting Documents</th>
        <th colspan="2">C. CERTIFIED as to validity propriety and legality of claim and approved for payment</th>
        <th>D. CERTIFIED that each official / employee whose name appears on the above roll has been paid the amount stated opposite his name</th>
    </tr>
</thead>
<tbody>
    <tr style = "background-color: #93DC5C !Important; ">
        <td colspan="2" style ="color:white !important;">
            <p>Amount of: ___________</p>
            <p>Signature: ___________</p>
            <p>Printed Name: Prudencio Cuaresma</p>
            <p>Position: Chairman, Committee on Appropriations</p>
            <p>Date: ___________</p>
        </td>
        <td colspan="2"  style ="color:white !important;">
            <p>Signature: ___________</p>
            <p>Printed Name: Maria Diossebelle B. Abella</p>
            <p>Position: Barangay Treasurer</p>
            <p>Date: ___________</p>
        </td>
        <td colspan="2"  style ="color:white !important;">
            <p>Signature: ___________</p>
            <p>Printed Name: Ma. Victoria M. Mortel</p>
            <p>Position: Punong Barangay</p>
            <p>Date: ___________</p>
        </td>
        <td  style ="color:white !important;">
            <p>Signature: ___________</p>
            <p>Printed Name: Maria Diossebelle B. Abella</p>
            <p>Position: Barangay Treasurer</p>
            <p>Date: ___________</p>
        </td>
    </tr>
</tbody>


        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
