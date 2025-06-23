<?php
session_start();
include './dbconn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('접근 권한이 없습니다.'); location.href='index.php';</script>";
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM info WHERE role != 'admin'");
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>회원 목록</title>
  <link href="https://fonts.googleapis.com/css2?family=Pretendard&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Pretendard', sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 0; /* Remove body padding */
    }
    /* Top bar styles from index.php */
    .top-bar {
      background-color: #fff;
      border-bottom: 1px solid #ddd;
      padding: 16px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
    }
    .logo a {
      font-size: 28px;
      font-weight: 900;
      color: #e50914;
      text-decoration: none;
    }
    .nav-menu a, .auth-menu a {
      text-decoration: none;
      color: #333;
      margin: 0 12px;
      font-weight: 500;
    }
    .nav-menu a:hover, .auth-menu a:hover {
      color: #e50914;
    }
    /* End top bar styles */

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #333;
      margin-top: 50px; /* Space below top-bar */
    }
    table {
      border-collapse: collapse;
      width: 80%;
      margin: 0 auto;
      background-color: #fff;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    th, td {
      padding: 12px 16px;
      border: 1px solid #ddd;
      text-align: center;
    }
    th {
      background-color: #f2f2f2;
      color: #333;
    }
    tr:hover {
      background-color: #f9f9f9;
    }
    a.delete-btn {
      display: inline-block;
      padding: 6px 12px;
      background-color: #e74c3c;
      color: white;
      border-radius: 4px;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }
    a.delete-btn:hover {
      background-color: #c0392b;
    }
  </style>
</head>
<body>

<div class="top-bar">
  <div class="logo">
    <a href="index.php">🎬 CAUBOX</a>
  </div>
  <div class="nav-menu">
    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
      <a href="add_movie.php">영화 등록</a>
      <a href="add_screening.php">상영 등록</a>
      <a href="now_showing.php">영화 수정/삭제</a>
      <a href="vip_list.php">우수 고객</a>
      <a href="user_list.php">회원 목록</a>
    <?php endif; ?>
  </div>
  <div class="auth-menu">
    <?php if (isset($_SESSION['user_id'])): ?>
      <span style="margin-right: 10px; font-weight: bold;">
      <?= htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['user_id']) ?> 님
    </span>
      <a href="mypage.php">마이페이지</a>
      <a href="logout.php">로그아웃</a>
    <?php else: ?>
      <a href="login_form.php">로그인</a>
      <a href="signup.php">회원가입</a>
    <?php endif; ?>
  </div>
</div>

  <h2>가입된 사용자 목록</h2>
  <table>
    <tr><th>ID</th><th>이름</th><th>삭제</th></tr>
    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
      <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td>
          <a class="delete-btn" href="delete_user.php?id=<?= urlencode($row['id']) ?>" onclick="return confirm('정말 삭제하시겠습니까?');">삭제</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>

</body>
</html>