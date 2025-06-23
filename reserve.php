<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='login_form.php';</script>";
    exit;
}

include './dbconn.php';

$movie_id = $_GET['movie_id'] ?? null;
$selected_screening_id = $_POST['screening_id'] ?? null;
$movie = null;
$screenings = [];
$reserved_seats = [];

if ($movie_id) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM movies WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $movie_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $movie = mysqli_fetch_assoc($result);

    $stmt2 = mysqli_prepare($conn, "SELECT id, screening_time FROM screenings WHERE movie_id = ? ORDER BY screening_time ASC");
    mysqli_stmt_bind_param($stmt2, "i", $movie_id);
    mysqli_stmt_execute($stmt2);
    $result2 = mysqli_stmt_get_result($stmt2);
    while ($row = mysqli_fetch_assoc($result2)) {
        $screenings[] = $row;
    }

    if (empty($selected_screening_id) && !empty($screenings)) {
        $selected_screening_id = $screenings[0]['id'];
    } else if (!empty($selected_screening_id) && !in_array($selected_screening_id, array_column($screenings, 'id'))) {
        $selected_screening_id = $screenings[0]['id'] ?? null;
    }

    if ($selected_screening_id) {
        $stmt3 = mysqli_prepare($conn, "SELECT seat_number FROM reservations WHERE screening_id = ?");
        mysqli_stmt_bind_param($stmt3, "i", $selected_screening_id);
        mysqli_stmt_execute($stmt3);
        $result3 = mysqli_stmt_get_result($stmt3);
        while ($row = mysqli_fetch_assoc($result3)) {
            $reserved_seats[] = $row['seat_number'];
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🎟 영화 예매</title>
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
        .reserve-container {
            width: 80%;
            max-width: 900px;
            margin: 30px auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .seat-map-wrapper {
            text-align: center;
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
        }
        .screen {
            background-color: #333;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .seat {
            width: 40px; height: 40px; margin: 5px;
            background-color: #eee;
            border: 1px solid #aaa;
            display: inline-block;
            text-align: center;
            line-height: 40px;
            cursor: pointer;
            border-radius: 4px;
        }
        .seat.selected {
            background-color: #4CAF50;
            color: white;
        }
        .seat.reserved {
            background-color: #999;
            color: white;
            pointer-events: none;
        }
        button[type="submit"] {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #e50914;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button[type="submit"]:hover {
            background-color: #c40810;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group select {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
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

<div class="reserve-container">
<h2>🎟 영화 예매하기</h2>

<?php if ($movie): ?>
    <h3>선택한 영화: <?= htmlspecialchars($movie['title']) ?></h3>

    <form action="reserve.php?movie_id=<?= $movie['id'] ?>" method="post" id="screeningSelectForm">
        <div class="form-group">
            <label for="screening_id">상영 일시 선택:</label>
            <select name="screening_id" id="screening_id" onchange="document.getElementById('screeningSelectForm').submit();">
                <?php if (empty($screenings)): ?>
                    <option value="">상영 일정이 없습니다.</option>
                <?php else: ?>
                    <?php foreach ($screenings as $s): ?>
                        <option value="<?= htmlspecialchars($s['id']) ?>"
                            <?= ($s['id'] == $selected_screening_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['screening_time']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <input type="hidden" name="movie_id" value="<?= htmlspecialchars($movie['id']) ?>">
    </form>

    <form action="process_reservation.php" method="post">
        <input type="hidden" name="movie_id" value="<?= htmlspecialchars($movie['id']) ?>">
        <input type="hidden" name="screening_id" id="final_screening_id" value="<?= htmlspecialchars($selected_screening_id) ?>">

        <?php if (!empty($screenings) && $selected_screening_id): ?>
            <div class="seat-map-wrapper">
                <div class="screen">SCREEN</div>
                <div id="seat-map">
                    <?php
                    $rows = 5; $cols = 10;
                    for ($r = 1; $r <= $rows; $r++) {
                        for ($c = 1; $c <= $cols; $c++) {
                            $seat = chr(64 + $r) . $c;
                            $reserved = in_array($seat, $reserved_seats);
                            $class = $reserved ? 'seat reserved' : 'seat'; // 이 부분을 수정했습니다.
                            echo "<div class='$class' data-seat='$seat'>$seat</div>";
                        }
                        echo "<br>";
                    }
                    ?>
                </div>
            </div>
            <input type="hidden" name="seat" id="selected-seat" required>
            <br>
            <button type="submit">예매 확정</button>
        <?php else: ?>
            <p style="text-align: center; color: #e50914;">선택된 영화의 상영 일정이 없거나 유효하지 않습니다.</p>
        <?php endif; ?>
    </form>
<?php else: ?>
    <p>예매할 영화가 선택되지 않았습니다.</p>
<?php endif; ?>
</div>

<script>
    document.querySelectorAll('.seat:not(.reserved)').forEach(seat => {
        seat.addEventListener('click', function () {
            document.querySelectorAll('.seat').forEach(s => s.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('selected-seat').value = this.dataset.seat;
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const screeningDropdown = document.getElementById('screening_id');
        const finalScreeningIdInput = document.getElementById('final_screening_id');
        if (screeningDropdown && finalScreeningIdInput) {
            finalScreeningIdInput.value = screeningDropdown.value;
        }
    });

</script>

</body>
</html>

<?php mysqli_close($conn); ?>