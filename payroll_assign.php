<?php include('includes/header.php'); ?>
<?php include_once('config/db.php'); ?>
<?php include_once('config/function.php'); ?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Assign Payroll <a href="dashboard.php" class="btn btn-primary float-end">Back</a></h4>
        </div>

        <div class="card-body">
            <?php alertMessage(); ?>

            <?php
            $employeeId = isset($_GET['id']) ? $_GET['id'] : null;

            if ($employeeId) {
                $employeeInfo = getEmployeeById($conn, $employeeId);

                if ($employeeInfo) {
                    $departmentInfo = getDepartmentByEmployeeId($conn, $employeeInfo['id']);
            ?>
                    <h5>Assign Payroll for Employee: <?= $employeeInfo['name'] ?></h5>

                    <?php if ($departmentInfo): ?>
                        <p>Department: <?= $departmentInfo['department'] ?></p>
                    <?php else: ?>
                        <p>Department information not found.</p>
                    <?php endif; ?>

                    <form action="code.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="day">Day:</label>
                                <input type="number" name="day" class="form-control" min="1" max="31" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="month">Month:</label>
                                <select name="month" class="form-select" required>
                                    <option value="01">January</option>
                                    <option value="02">February</option>
                                    <option value="03">March</option>
                                    <option value="04">April</option>
                                    <option value="05">May</option>
                                    <option value="06">June</option>
                                    <option value="07">July</option>
                                    <option value="08">August</option>
                                    <option value="09">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="year">Year:</label>
                                <input type="number" name="year" class="form-control" min="2022" value="<?= date('Y') ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h5>Time In and Time Out for 15 Days:</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Day</th>
                                            <th>Time In</th>
                                            <th>Time Out</th>
                                            <th>Total Hours Per Day</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for ($day = 1; $day <= 15; $day++) { ?>
                                            <tr>
                                                <td>Day <?= $day ?></td>
                                                <td>
                                                    <input type="time" name="timeIn<?= $day ?>" class="form-control" onchange="updateTotalHours(<?= $day ?>)">
                                                </td>
                                                <td>
                                                    <input type="time" name="timeOut<?= $day ?>" class="form-control" onchange="updateTotalHours(<?= $day ?>)">
                                                </td>
                                                <td id="totalHoursDisplay<?= $day ?>">0.00</td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Overall Total Hours Per Day:</strong></td>
                                            <td id="overallTotalHoursPerDayDisplay">0.00</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Hidden input for total hours per day -->
                        <input type="hidden" name="employeeIdInput" value="<?= $employeeInfo['id'] ?>">
                        <input type="hidden" name="totalHoursPerDay" id="totalHoursPerDay" value="">
                        <!-- Hidden input for overall total hours per day -->
                        <input type="hidden" name="overallTotalHoursPerDay" id="overallTotalHoursPerDay" value="">

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <button type="submit" name="assignPayroll" class="btn btn-primary">Assign Payroll</button>
                            </div>
                        </div>
                    </form>
            <?php
                } else {
                    echo '<div class="alert alert-danger" role="alert">Employee not found.</div>';
                }
            } else {
                echo '<div class="alert alert-danger" role="alert">Employee ID not provided.</div>';
            }
            ?>
        </div>
    </div>
</div>

<script>
    const totalHoursPerDayInput = document.querySelector('#totalHoursPerDay');
    const overallTotalHoursPerDayInput = document.querySelector('#overallTotalHoursPerDay');
    const timeInputs = Array.from(document.querySelectorAll('[name^="timeIn"], [name^="timeOut"]'));

    function parseTime(timeString) {
        const [hours, minutes] = timeString.split(':');
        return new Date(2000, 0, 1, hours, minutes);
    }

    function updateTotalHours(day) {
        let totalHoursPerDay = 0;
        let isValid = true;

        const timeInValue = document.querySelector(`[name='timeIn${day}']`).value;
        const timeOutValue = document.querySelector(`[name='timeOut${day}']`).value;

        if (timeInValue || timeOutValue) {
            const timeIn = timeInValue ? parseTime(timeInValue) : null;
            const timeOut = timeOutValue ? parseTime(timeOutValue) : null;

            if ((timeIn && !isNaN(timeIn.getTime())) || (timeOut && !isNaN(timeOut.getTime()))) {
                if (timeIn && timeOut) {
                    // Check if time out is earlier but crosses into the next day
                    if (timeOut < timeIn) {
                        const timeDiff1 = (new Date('2000-01-02T00:00:00') - timeIn) || 0;
                        const hours1 = timeDiff1 / (1000 * 60 * 60);

                        const timeDiff2 = (timeOut - new Date('2000-01-01T00:00:00')) || 0;
                        const hours2 = timeDiff2 / (1000 * 60 * 60);

                        totalHoursPerDay = hours1 + hours2;
                    } else {
                        const timeDiff = (timeOut - timeIn) || 0;
                        const hours = timeDiff / (1000 * 60 * 60);
                        totalHoursPerDay = hours;
                    }
                }
            } else {
                isValid = false;
                alert(`Error: Invalid time format for Day ${day}.`);
            }
        }

        if (isValid) {
            // Display total hours per day for the specific day
            document.getElementById(`totalHoursDisplay${day}`).textContent = totalHoursPerDay.toFixed(2);
            // Update total hours per day in the hidden input
            totalHoursPerDayInput.value = totalHoursPerDay.toFixed(2);
            // Update overall total hours per day
            updateOverallTotalHoursPerDay();
        } else {
            // Clear the display if there are validation errors
            document.getElementById(`totalHoursDisplay${day}`).textContent = '0.00';
            totalHoursPerDayInput.value = '';
            // Update overall total hours per day
            updateOverallTotalHoursPerDay();
        }
    }

    function updateOverallTotalHoursPerDay() {
        // Calculate and display overall total hours per day
        const overallTotalHoursPerDay = Array.from({ length: 15 }, (_, i) => parseFloat(document.getElementById(`totalHoursDisplay${i + 1}`).textContent || 0))
            .reduce((acc, curr) => acc + curr, 0);
        document.getElementById('overallTotalHoursPerDayDisplay').textContent = overallTotalHoursPerDay.toFixed(2);

        // Update overall total hours per day in the hidden input
        overallTotalHoursPerDayInput.value = overallTotalHoursPerDay.toFixed(2);
    }

    timeInputs.forEach(input => input.addEventListener('change', function () {
        const day = this.name.replace(/\D/g, '');
        updateTotalHours(day);
    }));
</script>

<?php include('includes/footer.php'); ?>
