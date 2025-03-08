<?php
$host = 'localhost';
$dbname = 'database adı';
$username = 'database kullanıcı adı';
$password = 'Veritabanı şifresi.';

// API bilgilerini ekliyoruz
define('GEMINI_API_KEY', 'Buraya Api Key gelecek.');
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent');

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage();
}

function updateUserScore($db, $userId, $isCorrect) {
    try {
        $sql = "INSERT INTO scores (user_id, score) 
                VALUES (:user_id, :score) 
                ON DUPLICATE KEY UPDATE score = score + :score";
        
        $score = $isCorrect ? 1 : -1;
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':score' => $score
        ]);
        
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

function getLeaderboard($db) {
    try {
        $sql = "SELECT u.username, SUM(s.score) as total_score 
                FROM users u 
                LEFT JOIN scores s ON u.id = s.user_id 
                GROUP BY u.id, u.username
                HAVING total_score > 0
                ORDER BY total_score DESC 
                LIMIT 10";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return [];
    }
}

function getUserStats($db, $userId) {
    try {
        $sql = "SELECT SUM(score) as total_correct FROM scores WHERE user_id = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'correct_count' => $result['total_correct'] ?? 0,
            'hint_count' => 3 // İpucu hakkını şimdilik sabit tutuyoruz
        ];
    } catch(PDOException $e) {
        return ['correct_count' => 0, 'hint_count' => 3];
    }
}

// API bilgilerini almak için yeni bir fonksiyon ekliyoruz
function getApiCredentials() {
    return [
        'api_key' => GEMINI_API_KEY,
        'api_url' => GEMINI_API_URL
    ];
}
?> 