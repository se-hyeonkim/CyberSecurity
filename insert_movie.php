<?php
session_start();
include './dbconn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('관리자만 접근 가능합니다.'); history.back();</script>";
    exit;
}

// 입력값 받기
$title = $_POST['title'];
$genre = $_POST['genre'];
$director = $_POST['director'];
$runtime = $_POST['runtime'];
$release_date = $_POST['release_date'];
$description = $_POST['description'];
$seats = $_POST['seats'];

// 이미지 업로드 처리
$upload_dir = "images/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$poster_name = basename($_FILES['poster']['name']);
$target_file = $upload_dir . $poster_name;
$image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// 이미지 유효성 검사
$allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array($image_type, $allowed_types)) {
    echo "<script>alert('지원되지 않는 이미지 형식입니다.'); history.back();</script>";
    exit;
}

// 파일 저장
if (!move_uploaded_file($_FILES['poster']['tmp_name'], $target_file)) {
    echo "<script>alert('이미지 업로드에 실패했습니다.'); history.back();</script>";
    exit;
}

// 영화 정보 삽입
$stmt = mysqli_prepare($conn,
  "INSERT INTO movies (title, genre, director, runtime, release_date, description, poster_path)
   VALUES (?, ?, ?, ?, ?, ?, ?)"
);
mysqli_stmt_bind_param($stmt, "sssisss", $title, $genre, $director, $runtime, $release_date, $description, $target_file);

if (mysqli_stmt_execute($stmt)) {
    $movie_id = mysqli_insert_id($conn); // 방금 등록한 영화의 ID 가져오기
    echo "<script>
        alert('영화가 성공적으로 등록되었습니다. 상영 일정을 이어서 등록해주세요.');
        location.href='add_screening.php?movie_id=$movie_id';
    </script>";
} else {
    echo "<script>alert('영화 등록에 실패했습니다.'); history.back();</script>";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
