<?php
session_start();
include './dbconn.php';

$movie_id = $_GET['id'] ?? null;
$movie = null;

if ($movie_id) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM movies WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $movie_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $movie = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>영화 상세 정보</title>
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
    .movie-detail-container {
        width: 80%;
        max-width: 900px;
        margin: 30px auto;
        background-color: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        display: flex;
        gap: 30px;
        align-items: flex-start;
    }
    .movie-poster {
        flex-shrink: 0;
        width: 200px;
        text-align: center;
    }
    .movie-poster img {
        width: 100%;
        height: auto;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    .movie-info-text {
        flex-grow: 1;
    }
    .movie-info-text h2 {
        text-align: left;
        margin-top: 0;
        margin-bottom: 15px;
        /* 여기를 수정합니다: 제목이 한 줄로 표시되도록 너비를 조정하거나 줄바꿈을 막습니다. */
        white-space: nowrap; /* 텍스트가 컨테이너를 넘어가더라도 줄바꿈되지 않게 합니다. */
        overflow: hidden; /* 넘치는 텍스트를 숨깁니다. */
        text-overflow: ellipsis; /* 넘치는 텍스트를 ...으로 표시합니다. (선택 사항) */
        /* max-width를 조정하여 글자가 줄바꿈될 여유 공간을 더 줍니다.
           flex-grow: 1이 이미 있으므로 flex-basis나 flex-shrink를 조정해볼 수도 있습니다.
           또는 movie-detail-container의 gap이나 movie-poster의 width를 줄여서 공간 확보도 가능합니다.
           우선 white-space: nowrap을 적용해보고, 여백 부족 시 다른 속성을 조정합니다. */
    }
    .movie-info-text p {
        margin-bottom: 8px;
        line-height: 1.6;
    }
    .seat {
      display: inline-block;
      width: 40px;
      height: 40px;
      line-height: 40px;
      text-align: center;
      margin: 3px;
      border: 1px solid #ccc;
      background-color: #eee;
    }
    .reserved {
      background-color: #999;
      color: white;
      pointer-events: none;
    }
    .reserve-btn {
      display: inline-block;
      padding: 10px 20px;
      background-color: #e50914;
      color: white;
      border: none;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s;
      width: 100%;
      box-sizing: border-box;
    }
    .reserve-btn:hover {
      background-color: #c40810;
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

<?php if ($movie): ?>
<div class="movie-detail-container">
  <div class="movie-poster">
    <?php if (!empty($movie['poster_path'])): ?>
      <img src="<?= htmlspecialchars($movie['poster_path']) ?>" alt="<?= htmlspecialchars($movie['title']) ?> 포스터">
    <?php endif; ?>

    <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
      <a href="reserve.php?movie_id=<?= $movie['id'] ?>" class="reserve-btn">
       🎟 예매하기
      </a>
    <?php else: ?>
      <p style="color:gray; font-style:italic; font-size: 0.9em;">(관리자는 예매 기능이 비활성화되어 있습니다)</p>
    <?php endif; ?>
  </div>

  <div class="movie-info-text">
    <h2><?= htmlspecialchars($movie['title']) ?></h2>
    <p><strong>장르:</strong> <?= htmlspecialchars($movie['genre']) ?></p>
    <p><strong>감독:</strong> <?= htmlspecialchars($movie['director']) ?></p>
    <p><strong>상영 시간:</strong> <?= $movie['runtime'] ?>분</p>
    <p><strong>개봉일:</strong> <?= $movie['release_date'] ?></p>
    <p><strong>설명:</strong><br><?= nl2br(htmlspecialchars($movie['description'] ?? '')) ?></p>
  </div>


  <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <div style="width: 100%; margin-top: 40px; clear: both;">
        <h3>🎫 좌석 예약 현황 (관리자용)</h3>
        <?php
          $stmt2 = mysqli_prepare($conn, "SELECT id, screening_time FROM screenings WHERE movie_id = ?");
          mysqli_stmt_bind_param($stmt2, "i", $movie_id);
          mysqli_stmt_execute($stmt2);
          $result2 = mysqli_stmt_get_result($stmt2);

          while ($screening = mysqli_fetch_assoc($result2)) {
              $screening_id = $screening['id'];
              $time = $screening['screening_time'];

              echo "<h4>🕒 상영 시간: $time</h4>";

              $stmt3 = mysqli_prepare($conn, "SELECT seat_number FROM reservations WHERE screening_id = ?");
              mysqli_stmt_bind_param($stmt3, "i", $screening_id);
              mysqli_stmt_execute($stmt3);
              $result3 = mysqli_stmt_get_result($stmt3);

              $reserved = [];
              while ($row = mysqli_fetch_assoc($result3)) {
                  $reserved[] = $row['seat_number'];
              }

              // 좌석 그리드
              $rows = 5;
              $cols = 10;
              echo "<div style='margin-bottom: 20px;'>";
              for ($r = 1; $r <= $rows; $r++) {
                  for ($c = 1; $c <= $cols; $c++) {
                      $seat = chr(64 + $r) . $c;
                      $class = in_array($seat, $reserved) ? 'seat reserved' : 'seat';
                      echo "<div class='$class'>$seat</div>";
                  }
                  echo "<br>";
              }
              echo "</div>";
          }
        ?>
    </div>
  <?php endif; ?>
</div>
<?php else: ?>
  <p>영화 정보를 불러올 수 없습니다.</p>
<?php endif; ?>

</body>
</html>

<?php mysqli_close($conn); ?>