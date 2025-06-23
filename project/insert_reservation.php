<?php
session_start();
include './dbconn.php';

$user_id = $_SESSION['user_id'] ?? 1; 
$screening_id = $_POST['screening_id'];
$seats = $_POST['seats'];

foreach ($seats as $seat) {
    $check = mysqli_query($conn,
        "SELECT * FROM reservations WHERE screening_id = $screening_id AND seat_number = '$seat'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('이미 예약된 좌석이 있습니다.'); history.back();</script>";
        exit;
    }

    mysqli_query($conn, 
        "INSERT INTO reservations (user_id, screening_id, seat_number) 
         VALUES ($user_id, $screening_id, '$seat')");
}

echo "<script>alert('예매가 완료되었습니다.'); location.href='index.php';</script>";
?>