<?php
session_start();
require_once 'config/function.php';

if (isset($_POST['loginBtn'])) {

    $name = validate($_POST['username']);
    $password = validate($_POST['password']);

    if ($name != '' && $password != '') {

        $query = "SELECT * FROM admins WHERE name='$name' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result) {

            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                $hashedPassword = $row['password'];

                // Check if the password is valid
                if (!password_verify($password, $hashedPassword)) {
                    redirect('index.php', 'Invalid Password');
                }

                $_SESSION['loggedin'] = true;

                
                $_SESSION['loggedinUser'] = array(
                    'user_id' => $row['id'],
                    'name' => $row['name'],
                    'password' => $row['password']
                );

                redirect('dashboard2.php', 'Logged in Successfully');
            } else {
                redirect('index.php', 'Invalid Username');
            }
        } else {
            redirect('index.php', 'Something Went Wrong!');
        }
    } else {
        redirect('index.php', 'All fields are mandatory');
    }
}




// Check if the user is logged in
if (isset($_SESSION['loggedin'])) {
    // Unset or destroy the session variables
    unset($_SESSION['loggedin']);
    unset($_SESSION['loggedinUser']);
    
    redirect('index.php', 'Logged out Successfully');
} else {
    redirect('index.php', 'You are not logged in');
}


?>


