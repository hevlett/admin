<?php include('includes/login_template.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
    background-image: url('https://upload.wikimedia.org/wikipedia/commons/thumb/c/c6/1863Pinagsama_Village%2C_Western_Bicutan%2C_08.jpg/2560px-1863Pinagsama_Village%2C_Western_Bicutan%2C_08.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
}

.container {
    position: relative;
}

.card {
    background-color: rgba(255, 255, 255, 0.7);
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.card-header {
    background-color: #007bff;
    color: #fff;
    border-bottom: none;
    border-radius: 10px 10px 0 0;
    text-align: center;
    padding: 20px 0;
}

.card-body {
    padding: 30px;
}

  </style>
</head>

<body>

<div class="container">
    <div class="row justify-content-center align-items-center" style="height: 100vh;">
        <div class="col-md-6">
            <?php alertMessage(); ?>
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Login</h2>
                </div>
                <div class="card-body">
                  
                    <form action="login-code.php" method="POST">
                        <div class="form-group">
                        <?php
                    // Check for login errors
                        if (isset($_GET['error']) && $_GET['error'] == 1) {
                            echo '<div class="alert alert-danger" role="alert">Invalid username or password!</div>';
                        }
                        ?>
                            <label for="exampleInputEmail1">Name</label>
                            <input type="text" class="form-control" name="username" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter username" required>
                            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" class="form-control" name="password" id="exampleInputPassword1" placeholder="Password" required>
                        </div>
                        <button type="submit" name="loginBtn" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
