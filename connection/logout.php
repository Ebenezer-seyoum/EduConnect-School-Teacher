<?php
session_start();  
if (isset($_SESSION["uid"])) {
    include 'function.php';  
    
    session_unset();
    session_destroy();
}
header('Location: ../login.php');
exit(); 
?>

