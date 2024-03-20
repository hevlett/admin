<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">
                Admins
                <div>
                    <a href="admins-create.php" class="btn btn-primary float-end ms-2">Add Admin</a>
                    <a href="dashboard.php" class="btn btn-danger float-end">Back</a>
                </div>
            </h4>
        </div>

        <div class="card-body">
            <?php alertMessage(); ?>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $admins = getAll('admins');
                        if (mysqli_num_rows($admins) > 0) {
                            foreach ($admins as $adminItem) :
                        ?>
                                <tr>
                                    <td><?= $adminItem['name'] ?></td>
                                    <td><?= $adminItem['department'] ?></td>
                                    <td>
                                        <a href="admins-edit.php?id=<?= $adminItem['id']; ?>" class="btn btn-success btn-sm">Edit</a>
                                        <a href="admins-delete.php?id=<?= $adminItem['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php
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
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
