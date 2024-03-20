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

// Define $idnumber
$idnumber = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $conn->real_escape_string($_POST['name']); // Added this line to retrieve the name
    $department = $conn->real_escape_string($_POST['department']);
    $pmonth = $conn->real_escape_string($_POST['prm']); // Assuming 'prm' corresponds to the 'pMonth' column
    $pcutoff = $conn->real_escape_string($_POST['prc']); // Assuming 'prc' corresponds to the 'pcutoff' column

    // Update the database
    $sql = "UPDATE employee SET department='$department', pMonth='$pmonth', pcutoff='$pcutoff' WHERE name='$name'";
    if ($conn->query($sql) === TRUE) {
        // Fetch the updated employee data
        $sql = "SELECT * FROM employee WHERE name = '$name'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                $idnumber = $row['idnumber'];
                $name = $row['name'];
                $department = $row['department'];
                $timein = $row['timein'];
                $timeout = $row['tiemout'];
                $pmonth = $row['pMonth'];
                $pcutoff = $row['pcutoff'];
            }
        }
        echo "<script>alert('Record Updated');</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Fetch employee data from the database
if(isset($_GET['id']) && $_GET['id'] != '') {
    // Sanitize the input to prevent SQL injection
    $employeeID = $conn->real_escape_string($_GET['id']);

    // Fetch employee data from the database
    $sql = "SELECT * FROM employee WHERE name = '$employeeID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row in a Bootstrap table
        while($row = $result->fetch_assoc()) {
            $idnumber = $row['idnumber'];
            $name = $row['name'];
            $department = $row['department'];
            $timein = $row['timein'];
            $timeout = $row['timeout'];
            $pmonth = $row['pMonth'];
            $pcutoff = $row['pcutoff'];
        }
    } else {
        echo '<h5>No data available for the provided ID.</h5>';
    }
} else {
    // echo '<h5>No ID provided in the parameters.</h5>';
}

// Close connection
$conn->close();
?>

<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">
                Edit Employee <a href="employee.php" class="btn btn-danger float-end">Back</a>
            </h4>
        </div>

        <div class="card-body">
            <?php alertMessage(); ?>

            <form action="employee-edit2.php" method="POST">
              
                            <input type="hidden" name="employeeId" >
                            <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="">Name *</label>
                                <input type="text" name="name" required class="form-control" value="<?php echo $name; ?>" readonly />
                            </div>

                                <div class="col-md-6 mb-3">
                                    <label for="">Employee ID  *</label>
                                    <input type="text" name="idnumber" required value="<?php echo $idnumber; ?>" class="form-control" readonly/>
                                </div>
                                <br>
                                <div class="col-md-6 mb-3">
                                <label for="department">Department </label>
                                   <select name="department" id="department" class="form-control" required>
                                        <option value="">Select Department</option>
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
                                <div class="col-md-3 mb-3">
                                    <label for="prm">Per month *</label>
                                    <input type="text" name="prm" required id="prm" value="<?php echo $pmonth; ?>" class="form-control" readonly>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="prc">Per Cutoff *</label>
                                    <input type="text" name="prc" required id="prc" class="form-control" value="<?php echo $pcutoff; ?>" readonly>
                                </div>

                                <br>

                                <div class="col-md-12 mb-3 float-start">
                                    <button type="submit" name="updateEmployee" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        
            </form>
        </div>
    </div>   
</div>

<script>
    function calculate() {
        var phr = parseFloat(document.getElementById("phr").value);
        if(!isNaN(phr)) {
            var prd = phr * 8; // Per Day = Per Hr * 8 hours
            var prm = prd * 22; // Per Month = Per Day * 22 days
            document.getElementById("prd").value = prd.toFixed(2); // Display up to 2 decimal places
            document.getElementById("prm").value = prm.toFixed(2);
        } else {
            document.getElementById("prd").value = "";
            document.getElementById("prm").value = "";
        }
    }
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
