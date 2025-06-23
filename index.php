<?php
session_start();
include './dbconn.php';

// 예매율은 movies 테이블의 total_reservations_count와 total_screenings_seats를 사용하여 계산
// 기존 reservation_rate 컬럼은 사용하지 않음
$query = "
    SELECT
        id,
        title,
        genre,
        director,
        runtime,
        release_date,
        poster_path,
        total_reservations_count,
        total_screenings_seats,
        (CASE
            WHEN total_screenings_seats > 0 THEN (total_reservations_count / total_screenings_seats) * 100
            ELSE 0
        END) AS calculated_reservation_rate
    FROM movies
    ORDER BY calculated_reservation_rate DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>CAUBOX 무비차트</title>
  <link href="https://fonts.googleapis.com/css2?family=Pretendard&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Pretendard', sans-serif;
      background-color: #f9f9f9;
    }
    .top-bar {
      background-color: #fff;
      border-bottom: 1px solid #ddd;
      padding: 16px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
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
    .welcome-msg {
      text-align: center;
      margin: 10px 0;
      font-size: 18px;
      color: #333;
    }
    h2 {
      text-align: center;
      margin: 30px 0 20px;
      font-size: 26px;
    }
    .movie-list {
      display: flex;
      justify-content: center;
      gap: 30px;
      flex-wrap: wrap;
      padding: 0 40px 40px;
    }
    .movie-box {
      background: white;
      border-radius: 12px;
      width: 220px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      text-align: center;
      position: relative;
      padding-bottom: 16px;
    }
    .movie-box img {
      width: 100%;
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
    }
    .ranking {
      position: absolute;
      top: 0;
      left: 0;
      background: #ff416c;
      color: white;
      width: 100%;
      padding: 5px;
      font-weight: bold;
    }
    .movie-title {
      font-size: 17px;
      font-weight: bold;
      margin-top: 10px;
    }
    .movie-info {
      font-size: 13px;
      color: #666;
      margin-top: 6px;
      line-height: 1.4;
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


<h2>🎟 무비차트 </h2>
<div class="movie-list">
  <?php $rank = 1; while ($movie = mysqli_fetch_assoc($result)): ?>
    <div class="movie-box">
      <div class="ranking">No.<?= $rank++ ?></div>
      <a href="movie_detail.php?id=<?= $movie['id'] ?>">
        <img src="<?= htmlspecialchars($movie['poster_path']) ?>" alt="<?= htmlspecialchars($movie['title']) ?> 포스터">
      </a>
      <div class="movie-title"><?= htmlspecialchars($movie['title']) ?></div>
      <div class="movie-info">
        예매율 <?= round($movie['calculated_reservation_rate'], 1) ?>%<br>
        개봉일 <?= htmlspecialchars($movie['release_date']) ?>
      </div>
    </div>
  <?php endwhile; ?>
</div>

</body>
</html>

<?php mysqli_close($conn); ?>