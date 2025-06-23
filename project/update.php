<?php

//    error_reporting(E_ALL);
//    ini_set("display_errors", 1); 

    include './dbconn.php';

    $uid=$_GET['uid'];
    $userid=$_POST['custid'];
    $userpwd=$_POST['custpwd'];
    $username=$_POST['custname'];
    $userbirth = $_POST['custbirth'];

    $query = "UPDATE info SET id = '$userid', pwd = '$userpwd', name = '$username', birth = '$userbirth' WHERE id = '$uid'";
    echo $query;
    mysqli_query($connect, $query);

    echo"
    <script>
    location.href='./main.php';
    </script>
    ";

 ?>
