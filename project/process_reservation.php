<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='login_form.php';</script>";
    exit;
}

include './dbconn.php';

$user_id = $_SESSION['user_id'];
$screening_id = $_POST['screening_id'];
$seat_number = $_POST['seat'];
$movie_id = $_POST['movie_id']; // movie_id 추가

// 좌석 중복 체크
$check_query = "SELECT COUNT(*) FROM reservations WHERE screening_id = ? AND seat_number = ?";
$check_stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($check_stmt, "is", $screening_id, $seat_number);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_bind_result($check_stmt, $seat_count);
mysqli_stmt_fetch($check_stmt);
mysqli_stmt_close($check_stmt);

if ($seat_count > 0) {
    echo "<script>alert('이미 예약된 좌석입니다.'); history.back();</script>";
    exit;
}

// 예약 정보 삽입
$insert_query = "INSERT INTO reservations (user_id, screening_id, seat_number) VALUES (?, ?, ?)";
$insert_stmt = mysqli_prepare($conn, $insert_query);
mysqli_stmt_bind_param($insert_stmt, "sis", $user_id, $screening_id, $seat_number);
$success = mysqli_stmt_execute($insert_stmt);

if ($success) {
    // 영화의 총 예매된 좌석 수 증가
    $update_movie_reservations_query = "UPDATE movies SET total_reservations_count = total_reservations_count + 1 WHERE id = ?";
    $update_movie_reservations_stmt = mysqli_prepare($conn, $update_movie_reservations_query);
    mysqli_stmt_bind_param($update_movie_reservations_stmt, "i", $movie_id);
    mysqli_stmt_execute($update_movie_reservations_stmt);
    mysqli_stmt_close($update_movie_reservations_stmt);

    // 마일리지 적립
    $update_mileage = "
        UPDATE info
        SET mileage = mileage + 100,
            is_vip = CASE WHEN mileage + 100 >= 1000 THEN 1 ELSE is_vip END
        WHERE id = ?
    ";
    $mile_stmt = mysqli_prepare($conn, $update_mileage);
    mysqli_stmt_bind_param($mile_stmt, "s", $user_id);
    mysqli_stmt_execute($mile_stmt);
    mysqli_stmt_close($mile_stmt);

    echo "<script>alert('예매가 완료되었습니다. 마일리지가 적립되었습니다.'); location.href='mypage.php';</script>";
} else {
    echo "<script>alert('예매 실패: " . mysqli_error($conn) . "'); history.back();</script>";
}

mysqli_close($conn);
?>