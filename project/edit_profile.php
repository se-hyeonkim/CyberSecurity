<?php
session_start();
include './dbconn.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='login_form.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// 사용자 정보 가져오기
$stmt = mysqli_prepare($conn, "SELECT * FROM info WHERE id = ?");
mysqli_stmt_bind_param($stmt, "s", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>회원 정보 수정</title>
  <link href="https://fonts.googleapis.com/css2?family=Pretendard&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Pretendard', sans-serif;
      background: linear-gradient(to right, #e0eafc, #cfdef3);
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      align-items: center;
      min-height: 100vh;
      padding: 0;
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

    .form-box {
      width: 100%;
      max-width: 400px;
      margin-top: 50px;
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    label {
      font-weight: bold;
      margin-top: 15px;
      display: block;
    }
    input, select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .gender {
      display: flex;
      gap: 20px;
      margin-top: 5px;
      align-items: center;
    }
    .gender label {
      display: flex;
      align-items: center;
      font-weight: normal;
      margin-top: 0;
    }
    .gender input[type="radio"] {
      width: auto;
      margin-right: 5px;
      margin-top: 0;
    }
    button {
      width: 100%;
      margin-top: 20px;
      padding: 10px;
      background-color: #333;
      color: white;
      border: none;
      border-radius: 5px;
    }
    button:hover {
      background-color: #555;
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

<div class="form-box">
  <h2>회원 정보 수정</h2>

  <form action="update_profile.php" method="post">
    <label for="id">아이디</label>
    <input type="text" name="id" id="id" value="<?= htmlspecialchars($user['id']) ?>" readonly>

    <label for="pwd">비밀번호</label>
    <input type="password" name="pwd" id="pwd" value="<?= htmlspecialchars($user['pwd']) ?>" required>

    <label for="name">이름</label>
    <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required>

    <label>성별</label>
    <div class="gender">
      <label><input type="radio" name="gender" value="M" <?= $user['gender'] === 'M' ? 'checked' : '' ?>> 남성</label>
      <label><input type="radio" name="gender" value="F" <?= $user['gender'] === 'F' ? 'checked' : '' ?>> 여성</label>
    </div>

    <label for="genre">선호 장르</label>
    <select name="genre" id="genre">
      <option value="action" <?= $user['genre'] === 'action' ? 'selected' : '' ?>>액션</option>
      <option value="romance" <?= $user['genre'] === 'romance' ? 'selected' : '' ?>>로맨스</option>
      <option value="comedy" <?= $user['genre'] === 'comedy' ? 'selected' : '' ?>>코미디</option>
      <option value="thriller" <?= $user['genre'] === 'thriller' ? 'selected' : '' ?>>스릴러</option>
    </select>

    <label for="phone">연락처</label>
    <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($user['phone']) ?>">

    <label for="birth">생년월일</label>
    <input type="date" name="birth" id="birth" value="<?= htmlspecialchars($user['birth']) ?>">

    <button type="submit">정보 수정</button>
  </form>
</div>

</body>
</html>