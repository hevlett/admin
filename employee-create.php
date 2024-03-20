<?php
// Database connection parameters
$servername = "localhost";
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$database = "payroll"; // Your MySQL database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define variables and initialize with empty values
$name = $department = $pmonth = $pcutoff = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $conn->real_escape_string($_POST['name']);
    $idnumber = $conn->real_escape_string($_POST['idnumber']);
    $department = $conn->real_escape_string($_POST['department']);
    $pmonth = $conn->real_escape_string($_POST['prm']);
    $pcutoff = $conn->real_escape_string($_POST['prc']);
    $timein = $conn->real_escape_string($_POST['timein']);
    $timeout = $conn->real_escape_string($_POST['timeout']);

    // Prepare and bind the SQL statement for inserting a new employee
    $sql = "INSERT INTO employee (name, idnumber, department, pMonth, pcutoff,timein,timeout) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $name, $idnumber, $department, $pmonth, $pcutoff, $timein, $timeout);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<script>alert('New employee added successfully');</script>";
        // Redirect to a success page or perform any other actions
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Employee</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">
                Add Employee <a href="dashboard.php" class="btn btn-primary float-end">Back</a>
            </h4>
        </div>

        <div class="card-body">
            <?php alertMessage(); ?>
            <form id="employeeForm" action="employee-create.php" method="POST">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="name">Name *</label>
                        <input type="text" name="name"  class="form-control"placeholder="Name" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="id">Employee Number *</label>
                        <input type="text" name="idnumber" placeholder="Employee ID" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="employee_department" type="hidden">Department *</label>
                        <select name="department" id="department" class="form-control" required>
                                    <option>Select Department</option>
                                    <?php 
                                    $sql = "SELECT * FROM department";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        // Output data of each row
                                        while($row = $result->fetch_assoc()) {
                                            $selected = ($department == $row['departmentName']) ? 'selected' : ''; // Corrected variable name
                                            echo "<option value='".$row["departmentName"]."' $selected>".$row["departmentName"]."</option>";
                                        }
                                    }
                                    ?>
                                </select>
                               </div>
                                   <div class="col-md-6 mb-3">
                                    <label for="prm">Per month *</label>
                                    <input type="text" name="prm" required id="prm" value="<?php echo $pmonth; ?>" class="form-control" readonly>
                                </div>
                              
                                <div class="col-md-6 mb-3">
                                    <label for="prc">Per Cutoff *</label>
                                    <input type="text" name="prc" required id="prc" class="form-control" value="<?php echo $pcutoff; ?>" readonly>
                                </div>
                                <br>

                                <div class="col-md-6 mb-3">
                                    <label for="timein">Select a time:</label>
                                    <input type="time" id="timein" name="timein" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                   <label for="timeout">Select a time:</label>
                                    <input type="time" id="timeout" name="timeout" class="form-control" required>
                                </div>

                    <div class="col-md-12 mb-3 text-end">
                        <button type="submit" name="saveEmployee" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const phrInput = document.getElementById('phr');
    const prdInput = document.getElementById('prd');
    const prmInput = document.getElementById('prm');
    const prwInput = document.getElementById('prw');

    phrInput.addEventListener('input', function() {
        const phrValue = parseFloat(this.value);
        if (!isNaN(phrValue)) {
            const prdValue = phrValue * 8;
            const prwValue = prdValue * 5; // Calculate per week value
            const prmValue = prdValue * 22; // Calculate per month value based on per day value

            prdInput.value = prdValue.toFixed(2);
            prwInput.value = prwValue.toFixed(2);
            prmInput.value = prmValue.toFixed(2); // Correctly set per month value
        }
    });
});

</script>
<script>
document.getElementById('department').addEventListener('change', function() {
    var departmentName = this.value;
    if (departmentName !== '') {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var salary = parseFloat(xhr.responseText);
                    document.getElementById('prm').value = salary; // Update the value of prm

                    // Calculate the value of prc
                    var prcValue = salary / 2;
                    document.getElementById('prc').value = prcValue.toFixed(2); // Update the value of prc
                } else {
                    console.error('Request failed: ' + xhr.status);
                }
            }
        };
        xhr.open('GET', 'get_salary.php?department=' + encodeURIComponent(departmentName), true);
        xhr.send();
    } else {
        document.getElementById('prm').value = ''; // Clear the value of prm if no department selected
        document.getElementById('prc').value = ''; // Clear the value of prc if no department selected
    }
});
</script>


<?php include('includes/footer.php'); ?>
</body>
</html>
