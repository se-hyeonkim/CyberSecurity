<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('관리자만 접근 가능합니다.'); history.back();</script>";
    exit;
}

include './dbconn.php';

// 영화 목록 불러오기
$result = mysqli_query($conn, "SELECT id, title FROM movies");

// URL에서 전달된 movie_id 확인
$selected_movie_id = isset($_GET['movie_id']) ? $_GET['movie_id'] : '';
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>상영 일정 등록</title>
    <link href="https://fonts.googleapis.com/css2?family=Pretendard&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Pretendard', sans-serif;
            background: linear-gradient(to right, #e0eafc, #cfdef3); /* Consistent background */
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

        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px; /* Adjust width as needed */
            margin-top: 50px; /* Space below top-bar */
        }
        h2 {
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
        input[type="datetime-local"],
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box; /* Include padding in width */
        }
        input[type="submit"] {
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
        input[type="submit"]:hover {
            background-color: #357ABD;
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

<div class="form-container">
    <h2>🎞️ 상영 일정 등록</h2>
    <form action="insert_screening.php" method="post">
        <label for="movie_id">영화 선택:</label>
        <select name="movie_id" id="movie_id" required>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <option value="<?= htmlspecialchars($row['id']) ?>" <?= $selected_movie_id == $row['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['title']) ?>
                </option>
            <?php } ?>
        </select>
        <br>
        <label for="screening_time">상영일시:</label>
        <input type="datetime-local" name="screening_time" id="screening_time" required>
        <br>
        <input type="submit" value="상영 등록">
    </form>
</div>

</body>
</html>