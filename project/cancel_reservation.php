<?php
session_start();
include './dbconn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'])) {
    $reservation_id = intval($_POST['reservation_id']);
    $user_id = $_SESSION['user_id'];

    // 예매된 영화의 movie_id를 가져오기 위해 조인 (예매율 감소용)
    $get_movie_id_query = "SELECT s.movie_id FROM reservations r JOIN screenings s ON r.screening_id = s.id WHERE r.id = ?";
    $get_movie_id_stmt = mysqli_prepare($conn, $get_movie_id_query);
    mysqli_stmt_bind_param($get_movie_id_stmt, "i", $reservation_id);
    mysqli_stmt_execute($get_movie_id_stmt);
    mysqli_stmt_bind_result($get_movie_id_stmt, $canceled_movie_id);
    mysqli_stmt_fetch($get_movie_id_stmt);
    mysqli_stmt_close($get_movie_id_stmt);


    // 사용자 보호: 본인 소유 예매인지 확인
    $check_sql = "SELECT user_id FROM reservations WHERE id = ?";
    $stmt_check = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt_check, "i", $reservation_id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_bind_result($stmt_check, $owner_id);
    mysqli_stmt_fetch($stmt_check);
    mysqli_stmt_close($stmt_check);

    if ($owner_id !== $user_id) {
        echo "<script>alert('권한이 없습니다.'); history.back();</script>";
        exit;
    }

    // 예매 취소 처리
    $delete_sql = "DELETE FROM reservations WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, "i", $reservation_id);
    $delete_success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($delete_success) {
        // 영화의 총 예매된 좌석 수 감소 (0 미만으로 내려가지 않게)
        if ($canceled_movie_id) {
            $update_movie_reservations_query = "UPDATE movies SET total_reservations_count = GREATEST(total_reservations_count - 1, 0) WHERE id = ?";
            $update_movie_reservations_stmt = mysqli_prepare($conn, $update_movie_reservations_query);
            mysqli_stmt_bind_param($update_movie_reservations_stmt, "i", $canceled_movie_id);
            mysqli_stmt_execute($update_movie_reservations_stmt);
            mysqli_stmt_close($update_movie_reservations_stmt);
        }

        // 마일리지 100점 차감 (0점 이하로는 안 내려가게 GREATEST 처리)
        $update_sql = "UPDATE info SET mileage = GREATEST(mileage - 100, 0) WHERE id = ?";
        $stmt2 = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt2, "s", $user_id);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);
    } else {
        echo "<script>alert('예매 취소 중 오류가 발생했습니다.'); history.back();</script>";
        exit;
    }
}

mysqli_close($conn);

header("Location: mypage.php");
exit;