<?php
$host_name = "localhost";
$db_user_id = "root";
$db_pwd = "0929";
$db_name = "mydb";

$conn = mysqli_connect($host_name, $db_user_id, $db_pwd, $db_name);

if ($conn->connect_error) {
    printf("Connect failed: %s\n", $conn->connect_error);
    exit();
}
?>

