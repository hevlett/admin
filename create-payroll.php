<?php
include('includes/header.php');
include_once('config/db.php');
include_once('config/function.php');

$recordsPerPage = 15;
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($currentPage - 1) * $recordsPerPage;

// Get the list of unique departments
$departments = getUniqueDepartments($conn);

// Initialize $selectedDepartment
$selectedDepartment = (isset($_GET['department']) && !empty($_GET['department'])) ? $_GET['department'] : '';

// Check if a department is selected
$employeesData = ($selectedDepartment) ?
    getEmployeesByDepartmentWithLimit($conn, $selectedDepartment, $offset, $recordsPerPage, false) :
    getEmployeesWithLimit($conn, $offset, $recordsPerPage);

$employeesResult = $employeesData['data'];
$totalPages = ($selectedDepartment) ?
    ceil(getTotalEmployeeRecordsByDepartment($conn, $selectedDepartment) / $recordsPerPage) :
    ceil($employeesData['numRows'] / $recordsPerPage);

?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Employees</h4>
            <a href="employee-create.php" class="btn btn-primary float-end ms-2">Add Employee</a>
            <a href="dashboard.php" class="btn btn-danger float-end">Back</a>
        </div>

        <div class="card-body">
            <?php alertMessage(); ?>

            <!-- Filter Form -->
            <form method="GET" action="">
                <div class="mb-3">
                    <label for="departmentFilter" class="form-label">Filter by Department:</label>
                    <select class="form-select" id="departmentFilter" name="department">
                        <option value="" <?= empty($selectedDepartment) ? 'selected' : '' ?>>All Departments</option>
                        <?php foreach ($departments as $departmentItem) : ?>
                            <option value="<?= $departmentItem ?>" <?= ($selectedDepartment == $departmentItem) ? 'selected' : '' ?>><?= $departmentItem ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Apply Filter</button>
            </form>
            <!-- End Filter Form -->

            <div class="table-responsive mt-3">
                <table id="employeeTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID Number</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tbody>
    <?php
    // Check if there are records
    if (!empty($employeesResult)) {
        foreach ($employeesResult as $employeeItem) {
            // Check if "id" key exists before accessing it
            $id = isset($employeeItem['id']) ? $employeeItem['id'] : '';
    ?>
            <tr>
                <td><?= $employeeItem['idnumber'] ?></td>
                <td><?= $employeeItem['name'] ?></td>
                <td><?= $employeeItem['department'] ?></td>
                <td>
                    <a href="payroll_assign.php?id=<?= $employeeItem['employee_id'] ?>" class="btn btn-dark btn-sm">Create</a>
                  
                </td>
            </tr>
    <?php
        }
    } else {
    ?>
        <tr>
            <td colspan="4">No Record Found</td>
        </tr>
    <?php
    }
    ?>
</tbody>

                </table>

                <!-- Pagination section -->
                <div class="pagination-container d-flex justify-content-center">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">

                            <?php if ($currentPage > 1) : ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $currentPage - 1 . ($selectedDepartment ? '&department=' . urlencode($selectedDepartment) : '') ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i . ($selectedDepartment ? '&department=' . urlencode($selectedDepartment) : '') ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages) : ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $currentPage + 1 . ($selectedDepartment ? '&department=' . urlencode($selectedDepartment) : '') ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
