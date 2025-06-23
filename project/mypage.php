<?php
session_start();
include './dbconn.php';

// 로그인 확인
if (!isset($_SESSION['user_id'])) {
  echo "<script>alert('로그인이 필요합니다.'); location.href='login_form.php';</script>";
  exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'] ?? 'user'; // 사용자 역할 가져오기 (없으면 'user'로 기본값)

// 관리자가 아닐 경우에만 예매 내역 및 마일리지 정보 가져오기
if ($user_role !== 'admin') {
    // 1. 예매 내역 가져오기
    $query = "SELECT r.id AS reservation_id, m.title, s.screening_time, r.seat_number
              FROM reservations r
              JOIN screenings s ON r.screening_id = s.id
              JOIN movies m ON s.movie_id = m.id
              WHERE r.user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // 2. 마일리지 및 평균 마일리지 확인
    $sql = "SELECT mileage FROM info WHERE id = ?";
    $stmt_mileage = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt_mileage, "s", $user_id);
    mysqli_stmt_execute($stmt_mileage);
    mysqli_stmt_bind_result($stmt_mileage, $mileage);
    mysqli_stmt_fetch($stmt_mileage);
    mysqli_stmt_close($stmt_mileage);

    // 전체 평균 마일리지 구하기
    $sql_avg = "SELECT AVG(mileage) AS avg_mileage FROM info";
    $result_avg = mysqli_query($conn, $sql_avg);
    $row_avg = mysqli_fetch_assoc($result_avg);
    $avg_mileage = $row_avg['avg_mileage'];

    // VIP 여부 판단
    $is_vip = ($mileage >= $avg_mileage);
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>마이페이지 - 예매 내역</title>
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
    h2 {
      text-align: center;
      margin: 30px 0 20px;
      font-size: 26px;
    }
    .mypage-container {
      width: 80%;
      max-width: 900px;
      margin: 30px auto;
      background-color: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .mypage-actions {
      text-align: center;
      margin-top: 30px;
    }
    .mypage-actions a,
    .mypage-actions button {
      display: inline-block;
      padding: 10px 20px;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      text-decoration: none;
      font-weight: bold;
      margin: 0 5px;
      transition: background-color 0.3s, color 0.3s, border-color 0.3s;
    }
    .mypage-actions a {
      background-color: #444;
      color: white;
      border: 2px solid #222;
    }
    .mypage-actions a:hover {
      background-color: #222;
      border-color: #222;
    }
    .mypage-actions button {
      background-color: #e74c3c;
      color: white;
      border: none;
    }
    .mypage-actions button:hover {
      background-color: #c0392b;
    }
    .mileage-info {
        text-align: center;
        margin-top: 20px;
        font-size: 1.1em;
        color: #333;
    }
    .mileage-info h3 {
        margin-top: 10px;
        margin-bottom: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 12px 15px;
      text-align: center;
    }
    th {
      background-color: #f2f2f2;
      font-weight: bold;
    }
    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    tr:hover {
      background-color: #f1f1f1;
    }
    td form {
        display: inline-block;
    }
    td button {
        padding: 6px 12px;
        background-color: #e74c3c;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }
    td button:hover {
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

<div class="mypage-container">
  <?php if ($user_role === 'admin'): ?>
    <h2>관리자 마이페이지</h2>
    <p style="text-align: center; font-size: 1.2em; color: #555;">관리자님, 환영합니다. 관리자 기능은 상단 메뉴를 이용해 주세요.</p>
  <?php else: ?>
    <div class="mileage-info">
      <p>💰 나의 마일리지: <strong><?= htmlspecialchars($mileage) ?></strong>점</p>
      <h3>
        <?= $is_vip ? '🌟 <span style="color:gold;">당신은 VIP 회원입니다!</span>' : '일반 회원입니다.' ?>
      </h3>
    </div>

    <h2>📽 예매 내역</h2>
    <table border="1" cellpadding="8" cellspacing="0">
      <tr>
        <th>영화 제목</th>
        <th>상영 시간</th>
        <th>좌석 번호</th>
        <th>예매 취소</th>
      </tr>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
      <tr>
        <td><?= htmlspecialchars($row['title']) ?></td>
        <td><?= htmlspecialchars($row['screening_time']) ?></td>
        <td><?= htmlspecialchars($row['seat_number']) ?></td>
        <td>
          <form action="cancel_reservation.php" method="post" onsubmit="return confirm('정말로 예매를 취소하시겠습니까?');">
            <input type="hidden" name="reservation_id" value="<?= $row['reservation_id'] ?>">
            <button type="submit">예매 취소</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  <?php endif; ?>

  <div class="mypage-actions">
    <?php if ($user_role !== 'admin'): ?>
        <a href="edit_profile.php">회원 정보 수정</a>
        <form action="delete_my_account.php" method="post" onsubmit="return confirm('정말 탈퇴하시겠습니까?')" style="display: inline-block;">
          <button type="submit">회원 탈퇴</button>
        </form>
    <?php else: ?>
        <p style="margin-top: 20px; color: #777;">회원 정보 수정 및 탈퇴는 일반 회원만 가능합니다.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>

<?php mysqli_close($conn); ?>