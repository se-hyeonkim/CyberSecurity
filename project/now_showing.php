<?php
session_start();
include './dbconn.php';

$query = "SELECT * FROM movies ORDER BY release_date DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>상영 중인 영화</title>
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
    table {
      width: 80%;
      margin: 20px auto;
      border-collapse: collapse;
      background-color: white;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px 15px;
      border: 1px solid #ddd;
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
    td a {
      color: #007bff;
      text-decoration: none;
    }
    td a:hover {
      text-decoration: underline;
    }
    button {
        padding: 8px 12px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        margin: 2px;
    }
    button[type="submit"] {
        background-color: #e74c3c;
        color: white;
    }
    button[type="submit"]:hover {
        background-color: #c0392b;
    }
    form {
        display: inline-block;
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

<h2>상영 중인 영화 목록</h2>

<table border="1" cellpadding="8" cellspacing="0">
  <tr>
    <th>제목</th>
    <th>장르</th>
    <th>감독</th>
    <th>상영 시간</th>
    <th>개봉일</th>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
      <th>관리</th>
    <?php endif; ?>
  </tr>

  <?php while ($row = mysqli_fetch_assoc($result)) : ?>
    <tr>
      <td>
        <a href="movie_detail.php?id=<?= $row['id'] ?>">
          <?= htmlspecialchars($row['title']) ?>
        </a>
      </td>
      <td><?= htmlspecialchars($row['genre']) ?></td>
      <td><?= htmlspecialchars($row['director']) ?></td>
      <td><?= $row['runtime'] ?>분</td>
      <td><?= $row['release_date'] ?></td>

      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <td>
          <form action="delete_movie.php" method="post" style="display:inline" onsubmit="return confirm('정말 삭제하시겠습니까?');">
            <input type="hidden" name="movie_id" value="<?= $row['id'] ?>">
            <button type="submit">삭제</button>
          </form>
          <form action="edit_movie.php" method="get" style="display:inline">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <button type="submit">수정</button>
          </form>
        </td>
      <?php endif; ?>
    </tr>
  <?php endwhile; ?>
</table>

</body>
</html>

<?php mysqli_close($conn); ?>