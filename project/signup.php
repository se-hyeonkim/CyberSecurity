<?php
session_start();
// dbconn.php는 여기서 직접 필요하지 않으므로 포함하지 않습니다.
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>회원가입</title>
  <link href="https://fonts.googleapis.com/css2?family=Pretendard&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Pretendard', sans-serif;
      background: linear-gradient(to right, #e0eafc, #cfdef3); /* Adjust to match login_form.php's background */
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      padding: 0; /* Remove padding from body */
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
      margin-top: 50px; /* Adjust as needed to position below top-bar */
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
      gap: 20px; /* Increased gap between radio buttons */
      margin-top: 5px;
      align-items: center; /* Vertically align items */
    }
    .gender label {
      display: flex; /* Make label a flex container */
      align-items: center; /* Vertically align checkbox and text within label */
      font-weight: normal; /* Reset font-weight from parent label style */
      margin-top: 0; /* Reset margin-top from parent label style */
    }
    .gender input[type="radio"] {
      width: auto; /* Allow radio button to take its natural width */
      margin-right: 5px; /* Space between radio and text */
      margin-top: 0; /* Align with text */
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
    <h2>회원가입</h2>
<form action="post.php" method="post">
  <label for="custom_name">이름</label>
  <input type="text" name="custom_name" id="custom_name" required>

  <label for="custom_id">아이디 (이메일)</label>
  <input type="text" name="custom_id" id="custom_id" required>

  <label for="custom_pwd">비밀번호</label>
  <input type="password" name="custom_pwd" id="custom_pwd" required>

  <label for="custom_birth">생년월일</label>
  <input type="date" name="custom_birth" id="custom_birth"
       min="1900-01-01" max="2025-12-31" required>

  <label>성별</label>
  <div class="gender">
    <label><input type="radio" name="gender" value="M"> 남성</label>
    <label><input type="radio" name="gender" value="F"> 여성</label>
  </div>

  <label for="genre">선호 장르</label>
  <select name="genre" id="genre">
    <option value="action">액션</option>
    <option value="romance">로맨스</option>
    <option value="comedy">코미디</option>
    <option value="thriller">스릴러</option>
  </select>

  <label for="phone">연락처</label>
  <input type="text" name="phone" id="phone" placeholder="010-1234-5678" required>

  <button type="submit">가입하기</button>
</form>

  </div>
</body>
</html>