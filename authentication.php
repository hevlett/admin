<?php

if(isset($_SESSION['loggedin']))
{
    $name = validate($_SESSION['loggedInUser']);

    $query = "SELECT * FROM admins where name='$name' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) == 0)
    
    {   
        logoutSession();
        redirect('../login.php','Acces Denied!');
    }
}
else
{
    redirect('../login.php','LOgin to Continue...');
}


?>