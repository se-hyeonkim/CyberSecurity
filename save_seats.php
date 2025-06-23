<?php
session_start();
include './dbconn.php';

$screening_id = $_POST['screening_id'];
$seats = $_POST['seats'] ?? [];

if (empty($seats)) {
    echo "<script>alert('선택된 좌석이 없습니다.'); history.back();</script>";
    exit;
}

$stmt = mysqli_prepare($conn, "INSERT INTO seats (screening_id, seat_number, is_reserved) VALUES (?, ?, 0)");

foreach ($seats as $seat) {
    mysqli_stmt_bind_param($stmt, "is", $screening_id, $seat);
    mysqli_stmt_execute($stmt);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

echo "<script>alert('좌석이 저장되었습니다.'); location.href='index.php';</script>";
?>
