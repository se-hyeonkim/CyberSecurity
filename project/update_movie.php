<?php
session_start();
include './dbconn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('접근 권한이 없습니다.'); location.href='index.php';</script>";
    exit;
}

$id = $_POST['id'];
$title = $_POST['title'];
$release_date = $_POST['release_date'];
$description = $_POST['description'];
$poster_path = $_POST['existing_poster']; // 기본값은 기존 이미지

if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'C:/Apache24/htdocs/project/images/';
    $web_path = 'images/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $tmp_name = $_FILES['poster']['tmp_name'];
    $name = basename($_FILES['poster']['name']);
    $target_path = $upload_dir . $name;

    if (move_uploaded_file($tmp_name, $target_path)) {
        // DB에는 웹 경로만 저장
        $poster_path = $web_path . $name;
    }
}

$sql = "UPDATE movies SET title = ?, release_date = ?, poster_path = ?, description = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssssi", $title, $release_date, $poster_path, $description, $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo "<script>alert('수정이 완료되었습니다.'); location.href='now_showing.php';</script>";
?>
