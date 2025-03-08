<?php
require_once 'database.php';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $password
        ]);
        $message = "🎉 Kayıt başarıyla tamamlandı! Giriş yapabilirsiniz.";
    } catch(PDOException $e) {
        $message = "❌ Kayıt sırasında bir hata oluştu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sihirli Bilmece Adası - Kayıt</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Sniglet&display=swap" rel="stylesheet">
</head>
<body>
    <div class="floating-clouds"></div>
    <div class="container">
        <header>
            <div class="sun">☀️</div>
            <img src="https://cdn-icons-png.flaticon.com/512/4257/4257674.png" class="wizard" alt="Bilge Büyücü">
            <h1>🌟 Sihirli Dünyaya Katıl 🌟</h1>
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
                    <input type="email" name="email" placeholder="📧 E-posta Adresi" required class="magic-input">
                    <input type="password" name="password" placeholder="🔒 Şifre" required class="magic-input">
                    
                    <div class="button-group">
                        <button type="submit" class="magic-button rainbow">
                            <span>✨ Kayıt Ol</span>
                        </button>
                        <a href="login.php" class="magic-button stardust">
                            <span>🎯 Giriş Yap</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 