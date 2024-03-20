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

            <form action="code.php" method="POST">
                <?php 
                    if(isset($_GET['id']))
                    {
                        if($_GET['id'] != ''){
                            $employeeData = $_GET['id'];
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

                    $employeeData = getById('employee', $employeeData);
                    if($employeeData)
                    {
                        if($employeeData['status'] == 200)
                        {
                            ?>
                            <input type="hidden" name="employeeId" value="<?= $employeeData['data']['id'];?>">
                            <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="">Name *</label>
                                <input type="text" name="name" required value="<?= $employeeData['data']['name']; ?>" class="form-control" readonly />
                            </div>

                                <div class="col-md-6 mb-3">
                                    <label for="">Employee ID  *</label>
                                    <input type="text" name="idnumber" required value="<?= $employeeData['data']['idnumber'];?>" class="form-control"/>
                                </div>
                                <br>
                                <div class="col-md-6 mb-3">
                                    <label for="department">Department </label>
                                    <select name="department" id="department" class="form-control" required readonly>
                                        <option value="">Select Department</option>
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
                                        
                                        $sql = "SELECT departmentName FROM department";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            // Output data of each row
                                            while($row = $result->fetch_assoc()) {
                                                $selected = ($employeeData['data']['department'] == $row['departmentName']) ? 'selected' : '';
                                                echo "<option value='".$row["departmentName"]."' $selected>".$row["departmentName"]."</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="prm">Per month *</label>
                                    <input type="text" name="prm" required value="<?= $employeeData['data']['pMonth'];?>" id="prm" class="form-control" >
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="prc">Per Cutoff *</label>
                                    <?php
                                    // Assuming $employeeData['data']['pMonth'] holds the initial value
                                    $halfValue = $employeeData['data']['pMonth'] / 2;
                                    ?>
                                    <input type="text" name="prc" required value="<?= $halfValue;?>" id="prm" class="form-control" readonly>
                                </div>
                    
                           
                                <br>

                                <div class="col-md-12 mb-3 float-start">
                                    <button type="submit" name="updateEmployee" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                            <?php
                        }
                        else
                        {
                            echo '<h5>'.$employeeData['message'].'</h5>';
                        }
                    }
                    else
                    {
                        echo 'Something Went Wrong';
                        return false;
                    }
                ?>
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

<?php include('includes/footer.php'); ?> 
