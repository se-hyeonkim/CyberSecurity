<?php
session_start();
include './dbconn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['movie_id'])) {
    $movie_id = intval($_POST['movie_id']);

    // 해당 영화의 모든 상영 정보 삭제 (CASCADE 설정이 되어 있지 않다면 필요)
    $delete_screenings_query = "DELETE FROM screenings WHERE movie_id = ?";
    $delete_screenings_stmt = mysqli_prepare($conn, $delete_screenings_query);
    mysqli_stmt_bind_param($delete_screenings_stmt, "i", $movie_id);
    mysqli_stmt_execute($delete_screenings_stmt);
    mysqli_stmt_close($delete_screenings_stmt);

    // 해당 영화의 모든 예매 정보 삭제 (CASCADE 설정이 되어 있지 않다면 필요)
    $delete_reservations_query = "DELETE FROM reservations WHERE screening_id IN (SELECT id FROM screenings WHERE movie_id = ?)";
    $delete_reservations_stmt = mysqli_prepare($conn, $delete_reservations_query);
    mysqli_stmt_bind_param($delete_reservations_stmt, "i", $movie_id);
    mysqli_stmt_execute($delete_reservations_stmt);
    mysqli_stmt_close($delete_reservations_stmt);
    
    // 삭제 쿼리
    $query = "DELETE FROM movies WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $movie_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
header("Location: now_showing.php");
exit;