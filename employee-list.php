<?php
include_once('includes/header.php');?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Information</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom CSS for additional design */
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        th, td {
            vertical-align: middle !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Employee Information</h2>

        <!-- Dropdown Filter -->
        <div class="form-group">
            <label for="departmentFilter">Filter by Department:</label>
            <select class="form-control" id="departmentFilter">
                <option value="">All Departments</option>
                <?php
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
                
                $sql = "SELECT DISTINCT department FROM employee ORDER BY department ASC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data of each row
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='".$row["department"]."'>".$row["department"]."</option>";
                    }
                }
                ?>
            </select>
            <a href='employee-list-cutoff.php' class='btn btn-danger btn-sm mt-3'>Report</a>
        </div>

        <!-- Employee Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped mt-4">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Department</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="employeeTableBody">
                    <?php
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
                    
                    $sql = "SELECT name, department FROM employee";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>".$row["name"]."</td>";
                            echo "<td>".$row["department"]."</td>";
                            echo "<td>
                                    <a href='employee-edit2.php?id=".$row["name"]."' class='btn btn-success btn-sm'>Edit</a>
                                    <a href='employee-delete.php?id=".$row["name"]."' class='btn btn-danger btn-sm'>Delete</a>
                                    <a href='employee-dtr-date.php?id=".$row["name"]."' class='btn btn-danger btn-sm'>DTR</a>
                                    <a href='employee-payslip-cutoff.php?id=".$row["name"]."' class='btn btn-danger btn-sm'>Payslip</a>
                                </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>0 results</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Custom JavaScript for filtering -->
    <script>
        $(document).ready(function(){
            // Handle department filter change
            $('#departmentFilter').change(function(){
                var selectedDepartment = $(this).val();
                // Show all rows by default
                $('#employeeTableBody tr').show();
                if(selectedDepartment !== ''){
                    // Hide rows that don't match the selected department
                    $('#employeeTableBody tr').each(function(){
                        var department = $(this).find('td:nth-child(2)').text();
                        if(department !== selectedDepartment){
                            $(this).hide();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
