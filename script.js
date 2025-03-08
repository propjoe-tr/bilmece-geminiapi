let API_KEY = '';
let API_URL = '';

let correctCount = 0;
let currentRiddle = '';
let currentAnswer = '';
let currentHint = '';
let hintCount = 3;
let isHintVisible = false;

async function initApiCredentials() {
    try {
        const response = await fetch('get_api_credentials.php');
        const data = await response.json();
        API_KEY = data.api_key;
        API_URL = data.api_url;
    } catch (error) {
        document.getElementById('riddle-text').innerHTML = '😢 API bağlantısında bir sorun var!';
    }
}

async function getRiddle() {
    try {
        document.getElementById('riddle-text').innerHTML = '🎲 Bilmece Hazırlanıyor... <br> <small>Sihirli küremde arıyorum!</small>';
        document.getElementById('hint-text').classList.add('hidden');
        isHintVisible = false;
        
        const requestOptions = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                contents: [{
                    parts: [{
                        text: "Çocuklar için basit ve eğlenceli bir Türkçe bilmece oluştur. Yanıtı JSON formatında ver. Örnek: {\"riddle\": \"bilmece metni\", \"answer\": \"cevap\", \"hint\": \"ipucu\"}"
                    }]
                }]
            })
        };

        const apiUrlWithKey = `${API_URL}?key=${encodeURIComponent(API_KEY)}`;
        const response = await fetch(apiUrlWithKey, requestOptions);

        if (!response.ok) {
            throw new Error(`API yanıtı başarısız`);
        }

        const data = await response.json();
        
        if (!data.candidates || !data.candidates[0] || !data.candidates[0].content || !data.candidates[0].content.parts[0].text) {
            throw new Error('API yanıtı beklenen formatta değil');
        }

        const riddleText = data.candidates[0].content.parts[0].text;
        let jsonStr = riddleText;
        const jsonMatch = riddleText.match(/\{[\s\S]*\}/);
        
        if (jsonMatch) {
            jsonStr = jsonMatch[0];
        }
        
        try {
            const riddleData = JSON.parse(jsonStr);
            
            if (!riddleData.riddle || !riddleData.answer || !riddleData.hint) {
                throw new Error('Bilmece verisi eksik');
            }
            
            currentRiddle = riddleData.riddle;
            currentAnswer = riddleData.answer.toLowerCase();
            currentHint = riddleData.hint;
            
            document.getElementById('riddle-text').textContent = currentRiddle;
        } catch (jsonError) {
            throw new Error('Bilmece verisi işlenemedi');
        }
    } catch (error) {
        document.getElementById('riddle-text').innerHTML = '😢 Üzgünüm, sihirli küremde bir sorun var!<br>Yeni Bilmece butonuna tıklayarak tekrar dene!';
    }
}

document.getElementById('hint-button').addEventListener('click', () => {
    if (hintCount > 0 && !isHintVisible) {
        hintCount--;
        document.getElementById('hint-count').textContent = hintCount;
        document.getElementById('hint-text').textContent = currentHint;
        document.getElementById('hint-text').classList.remove('hidden');
        isHintVisible = true;
        
        if (hintCount === 0) {
            document.getElementById('hint-button').disabled = true;
            document.getElementById('hint-button').style.opacity = '0.5';
        }
    }
});

async function updateScore(isCorrect) {
    try {
        const response = await fetch('update_score.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ isCorrect })
        });
        
        if (response.ok) {
            // Doğru veya yanlış cevap mesajını göster
            if (isCorrect) {
                showSuccessMessage();
            } else {
                showErrorMessage();
            }
            
            // Her iki durumda da 5 saniye sonra sayfayı yenile
            setTimeout(() => {
                location.reload();
            }, 5000);
        }
    } catch (error) {
        // Sessizce hata yönetimi
    }
}

document.getElementById('check-button').addEventListener('click', async () => {
    const userAnswer = document.getElementById('answer-input').value.toLowerCase();
    
    if (userAnswer === currentAnswer) {
        correctCount++;
        document.getElementById('correct-count').textContent = correctCount;
        await updateScore(true);
    } else {
        // Yanlış cevap için de puan güncellemesi yap
        await updateScore(false);
    }
    
    document.getElementById('answer-input').value = '';
});

document.getElementById('new-riddle').addEventListener('click', () => {
    getRiddle();
    document.getElementById('answer-input').value = '';
});

function showSuccessMessage() {
    const messages = [
        '🎉 Harika! Doğru bildin!',
        '✨ Muhteşem! Çok zekisin!',
        '🌟 Süpersin! Devam et!',
        '🎈 Vay canına! Doğru cevap!'
    ];
    showNotification(messages[Math.floor(Math.random() * messages.length)], 'success');
}

function showErrorMessage() {
    const messages = [
        '😊 Tekrar dene! Yapabilirsin!',
        '💫 Neredeyse doğruydu! Bir daha dene!',
        '🌈 Çok yaklaştın! Tekrar düşün!',
        '🎭 Yanlış cevap ama denemeye devam et!'
    ];
    showNotification(messages[Math.floor(Math.random() * messages.length)], 'error');
}

function showNotification(message, type) {
    // Varsa eski bildirimi ve zamanlayıcıyı temizle
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    // Yeni bildirim oluştur
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <p>${message}</p>
            <div class="notification-timer"></div>
        </div>
    `;

    // Bildirimi sayfaya ekle
    document.querySelector('.game-area').appendChild(notification);

    // Zamanlayıcı çubuğu için stil ekle
    const timerBar = notification.querySelector('.notification-timer');
    timerBar.style.width = '100%';

    // Hemen göster
    notification.classList.add('show');

    // 5 saniye sonra kaldır
    const duration = 5000; // 5 saniye
    const startTime = Date.now();
    
    const timer = setInterval(() => {
        const elapsedTime = Date.now() - startTime;
        const remainingPercent = Math.max(0, (1 - elapsedTime / duration) * 100);
        timerBar.style.width = `${remainingPercent}%`;
        
        if (elapsedTime >= duration) {
            clearInterval(timer);
            notification.remove();
        }
    }, 10);
}

// Sayfa yüklendiğinde önce API bilgilerini al, sonra bilmeceyi getir
window.addEventListener('load', async () => {
    await initApiCredentials();
    getRiddle();
}); 