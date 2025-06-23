<?php
session_start();
include './dbconn.php';

$id = $_POST['custom_id'];
$pwd = $_POST['custom_pwd'];
$name = $_POST['custom_name'];
$birth = $_POST['custom_birth'];
$gender = $_POST['gender'];
$genre = $_POST['genre'];
$phone = $_POST['phone'];

$sql = "INSERT INTO info (id, pwd, name, birth, gender, genre, phone)
        VALUES ('$id', '$pwd', '$name', '$birth', '$gender', '$genre', '$phone')";

$result = mysqli_query($conn, $sql);

if ($result) {
    echo "<script>alert('회원가입이 완료되었습니다!'); location.href='index.php';</script>";
} else {
    echo "회원가입 실패: " . mysqli_error($conn);
}

mysqli_close($conn);
?>