<?php
session_start();
include './dbconn.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='login_form.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$movie_id = $_POST['movie_id'];
$seat = $_POST['seat'];
$date = $_POST['date'];

// 날짜 기준으로 screening_id 찾기
$stmt = mysqli_prepare($conn, "SELECT id FROM screenings WHERE movie_id = ? AND DATE(screening_time) = ?");
mysqli_stmt_bind_param($stmt, "is", $movie_id, $date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo "<script>alert('해당 날짜의 상영 정보가 없습니다.'); history.back();</script>";
    exit;
}

$screening_id = $row['id'];

$stmt = mysqli_prepare($conn, "INSERT INTO reservations (user_id, screening_id, seat_number) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sis", $user_id, $screening_id, $seat);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('예매가 완료되었습니다.'); location.href='index.php';</script>";
} else {
    echo "<script>alert('예매 실패: 중복 좌석일 수 있습니다.'); history.back();</script>";
}
$mileagePoint = 100;
$login_id = $_SESSION['user_id'];

$sql = "UPDATE info SET mileage = mileage + ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "is", $mileagePoint, $login_id);
mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
