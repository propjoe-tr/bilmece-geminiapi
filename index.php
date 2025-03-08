<?php
session_start();
require_once 'database.php';

// Kullanıcı giriş yapmamışsa login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$leaderboard = getLeaderboard($db);
$userStats = getUserStats($db, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sihirli Bilmece Adası</title>
    <!-- Google Fonts'tan Türkçe karakterleri destekleyen fontları ekleyelim -->
    <link href="https://fonts.googleapis.com/css2?family=Sniglet&family=Baloo+2:wght@400;600&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- Türkçe karakter desteği için ek meta tag -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
    <div class="floating-clouds"></div>
    <div class="container">
        <header>
            <div class="sun">☀️</div>
            <img src="https://cdn-icons-png.flaticon.com/512/4257/4257674.png" class="wizard" alt="Bilge Büyücü">
            <h1>🌟 Sihirli Bilmece Adası 🌟</h1>
            <div class="wizard-speech">
                <p class="wizard-text">Hoş geldin küçük kaşif! Büyülü bilmeceler seni bekliyor!</p>
            </div>
        </header>

        <!-- Lider Tablosu -->
        <div class="leaderboard">
            <div class="leaderboard-content">
                <h3>🏆 Lider Tablosu</h3>
                <div class="leaderboard-list">
                    <?php foreach($leaderboard as $index => $leader): ?>
                        <div class="leader-item">
                            <span class="rank"><?php echo $index + 1; ?></span>
                            <span class="username"><?php echo htmlspecialchars($leader['username']); ?></span>
                            <span class="score"><?php echo $leader['total_score']; ?> puan</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="game-area">
            <div class="magic-scroll">
                <div class="scroll-content">
                    <h3>🎭 Bilmece 🎭</h3>
                    <p id="riddle-text">Bilmece hazırlanıyor...</p>
                    <div class="hint-box">
                        <button id="hint-button" class="magic-button">
                            <span>🔮 İpucu Al (3 Hakkın Var)</span>
                        </button>
                        <p id="hint-text" class="hidden">İpucu burada görünecek...</p>
                    </div>
                </div>
            </div>

            <div class="input-area">
                <input type="text" id="answer-input" placeholder="✨ Cevabını buraya yaz...">
                <div class="button-group">
                    <button id="check-button" class="magic-button rainbow">
                        <span>🎯 Cevabı Kontrol Et</span>
                    </button>
                    <button id="new-riddle" class="magic-button stardust">
                        <span>🎲 Yeni Bilmece</span>
                    </button>
                </div>
            </div>
            
            <div class="stats-container">
                <div class="stats-box">
                    <div class="stat-item">
                        <img src="https://cdn-icons-png.flaticon.com/512/2790/2790358.png" class="trophy" alt="Kupa">
                        <p>Doğru: <span id="correct-count"><?php echo $userStats['correct_count']; ?></span></p>
                    </div>
                    <div class="stat-item">
                        <img src="https://cdn-icons-png.flaticon.com/512/1680/1680012.png" class="hint-icon" alt="İpucu">
                        <p>İpucu Hakkı: <span id="hint-count"><?php echo $userStats['hint_count']; ?></span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="magical-creatures">
            <img src="https://cdn-icons-png.flaticon.com/512/4698/4698190.png" class="creature" alt="Sihirli Yaratık 1">
            <img src="https://cdn-icons-png.flaticon.com/512/3069/3069172.png" class="creature" alt="Sihirli Yaratık 2">
            <img src="https://cdn-icons-png.flaticon.com/512/4698/4698187.png" class="creature" alt="Sihirli Yaratık 3">
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html> 