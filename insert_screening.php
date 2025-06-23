<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('관리자만 접근 가능합니다.'); history.back();</script>";
    exit;
}

include './dbconn.php';

$movie_id = $_POST['movie_id'];
$screening_time = $_POST['screening_time'];

$fixed_seats = 50; // 고정 좌석 수

$stmt = mysqli_prepare($conn,
    "INSERT INTO screenings (movie_id, screening_time, total_seats, remaining_seats)
     VALUES (?, ?, ?, ?)"
);
mysqli_stmt_bind_param($stmt, "isii", $movie_id, $screening_time, $fixed_seats, $fixed_seats);

if (mysqli_stmt_execute($stmt)) {
    // 영화의 총 상영 가능 좌석 수 증가
    $update_movie_total_seats_query = "UPDATE movies SET total_screenings_seats = total_screenings_seats + ? WHERE id = ?";
    $update_movie_total_seats_stmt = mysqli_prepare($conn, $update_movie_total_seats_query);
    mysqli_stmt_bind_param($update_movie_total_seats_stmt, "ii", $fixed_seats, $movie_id);
    mysqli_stmt_execute($update_movie_total_seats_stmt);
    mysqli_stmt_close($update_movie_total_seats_stmt);

    echo "<script>alert('상영일정이 등록되었습니다.'); location.href='index.php';</script>";
} else {
    echo "<script>alert('등록 실패: " . mysqli_error($conn) . "'); history.back();</script>";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>