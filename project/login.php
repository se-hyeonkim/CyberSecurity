<?php
session_start();
include('./dbconn.php');

error_reporting(E_ALL);
ini_set("display_errors", 1); // PHP 에러 보이게 설정

$id = $_POST['user_id'];
$pwd = $_POST['user_password'];
$additional_answer = $_POST['additional_Youtube'] ?? ''; // 추가 질문 답변 가져오기 (새로 추가)

// 예외 처리: DB 연결 확인
if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
}

$sql = "SELECT * FROM info WHERE id='$id'";
$result = mysqli_query($conn, $sql);

// 예외 처리: 쿼리 실패 시
if (!$result) {
    die("쿼리 실패: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);

// 로그인 로직 수정: 관리자 추가 질문 로직 추가
if ($row && $row['pwd'] === $pwd) {
    if ($id === 'admin') {
        // 관리자 계정인 경우
        if (empty($additional_answer)) {
            // 추가 질문 답변이 없는 경우: 추가 질문 페이지로 리다이렉트
            echo "<script>location.href='login_form.php?challenge=true&user_id=admin';</script>";
            exit;
        } else {
            // 추가 질문 답변이 있는 경우: 답변 확인
            $correct_answer = '락희'; // 정답 설정

            if ($additional_answer === $correct_answer) {
                // 추가 질문 답변이 맞으면 로그인 성공
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['user_name'] = $row['name'];
                header("Location: index.php");
                exit;
            } else {
                // 추가 질문 답변이 틀리면 실패
                echo "<script>alert('추가 질문 답변이 올바르지 않습니다.'); location.href='login_form.php';</script>";
                exit;
            }
        }
    } else {
        // 일반 사용자 계정인 경우: 바로 로그인 성공
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['user_name'] = $row['name'];
        header("Location: index.php");
        exit;
    }
} else {
    // 아이디 또는 비밀번호 불일치 (일반/관리자 공통)
    echo "<script>alert('아이디나 비밀번호가 일치하지 않습니다.'); history.back();</script>";
    exit;
}
?>