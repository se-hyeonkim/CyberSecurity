<?php
session_start();
include './dbconn.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='login_form.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// 폼 데이터 받기
$pwd   = $_POST['pwd'];
$name  = $_POST['name'];
$gender = $_POST['gender'];
$genre = $_POST['genre'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$birth = $_POST['birth'];

// DB 업데이트
$stmt = mysqli_prepare($conn, "
    UPDATE info 
    SET pwd = ?, name = ?, gender = ?, genre = ?, email = ?, phone = ?, birth = ?
    WHERE id = ?
");

mysqli_stmt_bind_param($stmt, "ssssssss", $pwd, $name, $gender, $genre, $email, $phone, $birth, $user_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['user_name'] = $name;  // 세션 이름도 반영
    echo "<script>alert('정보가 성공적으로 수정되었습니다.'); location.href='mypage.php';</script>";
} else {
    echo "<script>alert('정보 수정에 실패했습니다.'); history.back();</script>";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
