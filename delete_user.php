<?php
session_start();
include './dbconn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('접근 권한이 없습니다.'); location.href='index.php';</script>";
    exit;
}

$uid = $_GET['id'] ?? '';
if ($uid === 'admin') {
    echo "<script>alert('관리자는 삭제할 수 없습니다.'); location.href='user_list.php';</script>";
    exit;
}

$sql = "DELETE FROM info WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $uid);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo "<script>alert('삭제되었습니다.'); location.href='user_list.php';</script>";
?>
