<?php
session_start();
require_once 'database.php';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $db->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit();
        } else {
            $message = "❌ Kullanıcı adı veya şifre hatalı!";
        }
    } catch(PDOException $e) {
        $message = "❌ Giriş sırasında bir hata oluştu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sihirli Bilmece Adası - Giriş</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Sniglet&display=swap" rel="stylesheet">
</head>
<body>
    <div class="floating-clouds"></div>
    <div class="container">
        <header>
            <div class="sun">☀️</div>
            <img src="https://cdn-icons-png.flaticon.com/512/4257/4257674.png" class="wizard" alt="Bilge Büyücü">
            <h1>🌟 Sihirli Dünyaya Hoş Geldin 🌟</h1>
        </header>

        <div class="magic-scroll">
            <div class="scroll-content">
                <?php if($message): ?>
                    <div class="wizard-speech">
                        <p><?php echo $message; ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" class="input-area">
                    <input type="text" name="username" placeholder="✨ Kullanıcı Adı" required class="magic-input">
                    <input type="password" name="password" placeholder="🔒 Şifre" required class="magic-input">
                    
                    <div class="button-group">
                        <button type="submit" class="magic-button rainbow">
                            <span>🎯 Giriş Yap</span>
                        </button>
                        <a href="kayit.php" class="magic-button stardust">
                            <span>✨ Kayıt Ol</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 