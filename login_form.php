<?php
session_start();
// dbconn.php는 로그인 프로세스에서 필요하므로 여기서는 포함하지 않습니다.
// include './dbconn.php';
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>로그인</title>
  <link href="https://fonts.googleapis.com/css2?family=Pretendard&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      font-family: 'Pretendard', sans-serif;
      background: linear-gradient(to right, #e0eafc, #cfdef3);
      display: flex;
      flex-direction: column; /* Added for top-bar */
      justify-content: flex-start; /* Aligned for top-bar */
      align-items: center;
      min-height: 100vh; /* Changed height to min-height */
      margin: 0;
    }
    /* Top bar styles from index.php */
    .top-bar {
      background-color: #fff;
      border-bottom: 1px solid #ddd;
      padding: 16px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%; /* Ensure top-bar spans full width */
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
      background-color: #ffffff;
      padding: 40px 30px;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
      margin-top: 50px; /* Adjust as needed to position below top-bar */
    }
    .form-box h2 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 24px;
      color: #333;
    }
    label {
      font-weight: 600;
      margin-top: 15px;
      display: block;
      color: #333;
    }
    input {
      width: 100%;
      padding: 12px;
      margin-top: 8px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }
    button {
      width: 100%;
      margin-top: 25px;
      padding: 12px;
      background-color: #4a90e2;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    button:hover {
      background-color: #357ABD;
    }
    /* Removed .home-link styles as top-bar replaces it */
  </style>
  <script>
    function checkform() {
      const form = document.login_form;
      // 일반 로그인 시도
      if (!form.user_id.value) {
        alert('아이디가 입력되지 않았습니다.');
        form.user_id.focus();
        return;
      }
      if (!form.user_password.value) {
        alert('비밀번호가 입력되지 않았습니다.');
        form.user_password.focus();
        return;
      }

      // 추가 질문 모드일 때 추가 질문 답변 확인
      if (form.additional_Youtube && !form.additional_Youtube.value) {
        alert('추가 질문에 답변해주세요.');
        form.additional_Youtube.focus();
        return;
      }

      form.submit();
    }
  </script>
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
    <h2>로그인</h2>
    <form name="login_form" action="./login.php" method="post">
      <label for="user_id">아이디</label>
      <input type="text" name="user_id" id="user_id" required value="<?= htmlspecialchars($_GET['user_id'] ?? '') ?>">

      <label for="user_password">비밀번호</label>
      <input type="password" name="user_password" id="user_password" required>

      <?php if (isset($_GET['challenge']) && $_GET['challenge'] === 'true' && isset($_GET['user_id']) && $_GET['user_id'] === 'admin'): ?>
        <label for="additional_Youtube" style="margin-top: 25px;">추가 질문: 반려견 이름은?</label>
        <input type="text" name="additional_Youtube" id="additional_Youtube" required autofocus>
      <?php endif; ?>

      <button type="button" onclick="checkform();">로그인</button>
    </form>
  </div>
</body>
</html>