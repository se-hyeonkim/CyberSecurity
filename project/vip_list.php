<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('관리자만 접근 가능합니다.'); location.href='index.php';</script>";
    exit;
}

include './dbconn.php';

// VIP 회원: 마일리지가 전체 평균 이상인 사람 (관리자 제외)
$vip_query = "
SELECT i.id, i.name, i.phone, i.mileage, COUNT(r.id) AS reservation_count
FROM info i
LEFT JOIN reservations r ON i.id = r.user_id
WHERE i.role != 'admin'
  AND i.mileage >= (
    SELECT AVG(mileage) FROM info WHERE role != 'admin'
)
GROUP BY i.id, i.name, i.phone, i.mileage
ORDER BY i.mileage DESC
";

$vip_result = mysqli_query($conn, $vip_query);

// 일반 회원: 마일리지가 평균 미만인 사람 (관리자 제외)
$normal_query = "
SELECT i.id, i.name, i.phone, i.mileage, COUNT(r.id) AS reservation_count
FROM info i
LEFT JOIN reservations r ON i.id = r.user_id
WHERE i.role != 'admin'
  AND i.mileage < (
    SELECT AVG(mileage) FROM info WHERE role != 'admin'
)
GROUP BY i.id, i.name, i.phone, i.mileage
ORDER BY i.mileage DESC
";

$normal_result = mysqli_query($conn, $normal_query);

?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>고객 목록</title>
  <link href="https://fonts.googleapis.com/css2?family=Pretendard&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Pretendard', sans-serif;
      background-color: #f9f9f9;
    }
    /* Top bar styles from index.php */
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
    /* End top bar styles */

    h2 {
      text-align: center;
      margin: 30px 0 20px;
      font-size: 26px;
    }
    table {
      border-collapse: collapse;
      width: 80%;
      margin: 20px auto;
      background-color: white;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    th, td {
      border: 1px solid #ccc;
      padding: 10px;
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

<h2>🌟 VIP 고객 목록 (마일리지 ≥ 평균)</h2>
<table>
  <tr>
    <th>회원 ID</th>
    <th>이름</th>
    <th>전화번호</th>
    <th>마일리지</th>
    <th>예매 수</th>
  </tr>
  <?php while ($row = mysqli_fetch_assoc($vip_result)) : ?>
    <tr>
      <td><?= htmlspecialchars($row['id']) ?></td>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= htmlspecialchars($row['phone']) ?></td>
      <td><?= $row['mileage'] ?></td>
      <td><?= $row['reservation_count'] ?></td>
    </tr>
  <?php endwhile; ?>
</table>

<h2>👤 일반 고객 목록 (마일리지 < 평균)</h2>
<table>
  <tr>
    <th>회원 ID</th>
    <th>이름</th>
    <th>전화번호</th>
    <th>마일리지</th>
    <th>예매 수</th>
  </tr>
  <?php while ($row = mysqli_fetch_assoc($normal_result)) : ?>
    <tr>
      <td><?= htmlspecialchars($row['id']) ?></td>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= htmlspecialchars($row['phone']) ?></td>
      <td><?= $row['mileage'] ?></td>
      <td><?= $row['reservation_count'] ?></td>
    </tr>
  <?php endwhile; ?>
</table>

</body>
</html>

<?php mysqli_close($conn); ?>