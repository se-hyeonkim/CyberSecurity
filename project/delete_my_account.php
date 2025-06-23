<?php
session_start();
include './dbconn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === 'admin') {
    echo "<script>alert('관리자는 회원 탈퇴 기능이 실행되지 않습니다.'); location.href='index.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "DELETE FROM info WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
session_destroy();

echo "<script>alert('탈퇴가 완료되었습니다.'); location.href='index.php';</script>";
?>
