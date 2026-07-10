<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Öğrenci Çağırma Kiosku</title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --background: #0f172a;
            --surface: #1e293b;
            --surface-light: #334155;
            --text: #f8fafc;
            --muted: #94a3b8;
            --primary: #22c55e;
            --primary-dark: #16a34a;
            --danger: #ef4444;
            --warning: #f59e0b;
            --border: rgba(255, 255, 255, 0.12);
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top, #1e3a5f 0%, transparent 45%),
                var(--background);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .kiosk {
            width: min(720px, 100%);
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 18px;
        }

        .connection {
            display: flex;
            align-items: center;
            gap: 9px;
            color: var(--muted);
            font-size: 15px;
            font-weight: 700;
        }

        .connection-dot {
            width: 11px;
            height: 11px;
            border-radius: 50%;
            background: var(--primary);
            box-shadow: 0 0 0 5px rgba(34, 197, 94, 0.15);
        }

        .connection.offline .connection-dot {
            background: var(--danger);
            box-shadow: 0 0 0 5px rgba(239, 68, 68, 0.15);
        }

        .fullscreen-button {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 11px 16px;
            background: rgba(255, 255, 255, 0.08);
            color: white;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        .card {
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: 28px;
            background: rgba(30, 41, 59, 0.94);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(15px);
        }

        .header {
            padding: 35px 38px 25px;
            text-align: center;
        }

        .icon {
            width: 95px;
            height: 95px;
            margin: 0 auto 20px;
            border-radius: 28px;
            background: rgba(34, 197, 94, 0.14);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
        }

        h1 {
            margin: 0;
            font-size: clamp(30px, 6vw, 47px);
            line-height: 1.1;
        }

        .description {
            margin: 13px 0 0;
            color: var(--muted);
            font-size: 19px;
        }

        .form-area {
            padding: 12px 38px 38px;
        }

        .field-label {
            display: block;
            margin-bottom: 9px;
            color: #cbd5e1;
            font-size: 15px;
            font-weight: 700;
        }

        input {
            width: 100%;
            height: 74px;
            border: 2px solid var(--border);
            border-radius: 18px;
            padding: 0 20px;
            background: rgba(15, 23, 42, 0.8);
            color: white;
            outline: none;
            font-size: 24px;
            text-align: center;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 5px rgba(34, 197, 94, 0.13);
        }

        .send-button {
            width: 100%;
            min-height: 68px;
            margin-top: 16px;
            border: 0;
            border-radius: 18px;
            padding: 16px 24px;
            background: var(--primary);
            color: white;
            font-size: 21px;
            font-weight: 900;
            cursor: pointer;
            transition: transform 0.15s, background 0.15s;
        }

        .send-button:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .send-button:disabled {
            cursor: wait;
            opacity: 0.65;
            transform: none;
        }

        .message {
            display: none;
            margin-top: 20px;
            border-radius: 18px;
            padding: 20px;
            text-align: center;
            font-size: 20px;
            font-weight: 800;
        }

        .message.show {
            display: block;
        }

        .message.success {
            border: 1px solid rgba(34, 197, 94, 0.4);
            background: rgba(34, 197, 94, 0.14);
            color: #86efac;
        }

        .message.error {
            border: 1px solid rgba(239, 68, 68, 0.4);
            background: rgba(239, 68, 68, 0.14);
            color: #fca5a5;
        }

        .message.loading {
            border: 1px solid rgba(245, 158, 11, 0.4);
            background: rgba(245, 158, 11, 0.14);
            color: #fcd34d;
        }

        .footer {
            padding: 17px 25px;
            border-top: 1px solid var(--border);
            background: rgba(15, 23, 42, 0.45);
            color: var(--muted);
            text-align: center;
            font-size: 14px;
        }

        .settings {
            margin-top: 14px;
            text-align: center;
        }

        .settings summary {
            color: var(--muted);
            cursor: pointer;
            font-size: 13px;
        }

        .settings-content {
            margin-top: 12px;
        }

        .settings-content input {
            height: 48px;
            font-size: 17px;
        }

        @media (max-width: 600px) {
            body {
                padding: 12px;
            }

            .header {
                padding: 28px 20px 20px;
            }

            .form-area {
                padding: 10px 20px 25px;
            }

            .topbar {
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
<div class="kiosk">
    <div class="topbar">
        <div class="connection" id="connection-status">
            <span class="connection-dot"></span>
            <span id="connection-text">İnternet bağlantısı aktif</span>
        </div>

        <button
            class="fullscreen-button"
            type="button"
            onclick="toggleFullscreen()"
        >
            ⛶ Tam Ekran
        </button>
    </div>

    <section class="card">
        <div class="header">
            <div class="icon">🎓</div>

            <h1>Öğrenci Çağırma</h1>

            <p class="description">
                Veli QR kodunu veya kartını okuyucuya okutunuz.
            </p>
        </div>

        <div class="form-area">
            <label class="field-label" for="qr_code">
                QR kodu veya kart numarası
            </label>

            <input
                id="qr_code"
                type="text"
                inputmode="none"
                autocomplete="off"
                autofocus
                placeholder="QR okutulması bekleniyor..."
            >

            <button
                id="send-button"
                class="send-button"
                type="button"
                onclick="sendCall()"
            >
                Çağrı Gönder
            </button>

            <div id="message" class="message"></div>

            <details class="settings">
                <summary>Kiosk ayarları</summary>

                <div class="settings-content">
                    <label class="field-label" for="kiosk_id">
                        Kiosk ID
                    </label>

                    <input
                        id="kiosk_id"
                        type="number"
                        min="1"
                        value="1"
                    >
                </div>
            </details>
        </div>

        <div class="footer">
            Okutma tamamlandıktan sonra yeni QR kodu beklenir.
        </div>
    </section>
</div>

<script>
    const qrInput = document.getElementById('qr_code');
    const kioskIdInput = document.getElementById('kiosk_id');
    const messageBox = document.getElementById('message');
    const sendButton = document.getElementById('send-button');

    let requestRunning = false;
    let lastScannedCode = '';
    let lastScannedAt = 0;

    const savedKioskId = localStorage.getItem('kiosk_id');

    if (savedKioskId) {
        kioskIdInput.value = savedKioskId;
    }

    kioskIdInput.addEventListener('change', function () {
        localStorage.setItem('kiosk_id', this.value);
        
        focusScanner();
    });


    function focusScanner() {
        setTimeout(() => qrInput.focus(), 100);
    }

    function showMessage(type, text) {
        messageBox.className = `message show ${type}`;
        messageBox.textContent = text;
    }

    function resetMessageAfterDelay() {
        setTimeout(() => {
            messageBox.className = 'message';
            messageBox.textContent = '';
        }, 5000);
    }

    async function sendCall() {
        const qrCode = qrInput.value.trim();
        const kioskId = Number(kioskIdInput.value);

        if (!qrCode) {
            showMessage('error', 'Lütfen QR kodunu okutunuz.');
            focusScanner();
            return;
        }

        if (!kioskId) {
            showMessage('error', 'Geçerli bir kiosk ID giriniz.');
            return;
        }

        const now = Date.now();

        if (
            qrCode === lastScannedCode &&
            now - lastScannedAt < 3000
        ) {
            showMessage(
                'error',
                'Bu kod az önce okutuldu. Lütfen birkaç saniye bekleyiniz.'
            );

            qrInput.value = '';
            focusScanner();
            return;
        }

        if (requestRunning) {
            return;
        }

        requestRunning = true;
        sendButton.disabled = true;
        sendButton.textContent = 'İşleniyor...';

        showMessage('loading', 'Öğrenci çağrısı oluşturuluyor...');

        try {
            const response = await fetch('/api/kiosk/call', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    qr_code: qrCode,
                    kiosk_id: kioskId,
                }),
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message ||
                    'Çağrı oluşturulamadı.'
                );
            }

            lastScannedCode = qrCode;
            lastScannedAt = Date.now();

            showMessage(
                'success',
                '✓ ' + (result.message || 'Çağrı başarıyla oluşturuldu.')
            );

            playSuccessSound();
            resetMessageAfterDelay();
        } catch (error) {
            showMessage(
                'error',
                '✕ ' + (error.message || 'Bağlantı hatası oluştu.')
            );
        } finally {
            qrInput.value = '';
            requestRunning = false;
            sendButton.disabled = false;
            sendButton.textContent = 'Çağrı Gönder';

            focusScanner();
        }
    }

    qrInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            sendCall();
        }
    });

    document.addEventListener('click', function (event) {
        if (
            event.target !== kioskIdInput &&
            event.target.tagName !== 'SUMMARY'
        ) {
            focusScanner();
        }
    });

    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
            return;
        }

        document.exitFullscreen();
    }

    function updateConnectionStatus() {
        const element = document.getElementById('connection-status');
        const text = document.getElementById('connection-text');

        if (navigator.onLine) {
            element.classList.remove('offline');
            text.textContent = 'İnternet bağlantısı aktif';
        } else {
            element.classList.add('offline');
            text.textContent = 'İnternet bağlantısı yok';
        }
    }

    window.addEventListener('online', updateConnectionStatus);
    window.addEventListener('offline', updateConnectionStatus);

    updateConnectionStatus();

    function playSuccessSound() {
        try {
            const audioContext = new (
                window.AudioContext ||
                window.webkitAudioContext
            )();

            const oscillator = audioContext.createOscillator();
            const gain = audioContext.createGain();

            oscillator.connect(gain);
            gain.connect(audioContext.destination);

            oscillator.frequency.setValueAtTime(
                780,
                audioContext.currentTime
            );

            oscillator.frequency.setValueAtTime(
                1040,
                audioContext.currentTime + 0.12
            );

            gain.gain.setValueAtTime(
                0.18,
                audioContext.currentTime
            );

            gain.gain.exponentialRampToValueAtTime(
                0.001,
                audioContext.currentTime + 0.35
            );

            oscillator.start();
            oscillator.stop(audioContext.currentTime + 0.35);
        } catch (error) {
            console.warn('Başarı sesi çalınamadı.', error);
        }
    }
let scanTimer = null;

qrInput.addEventListener('input', function () {
    clearTimeout(scanTimer);

    scanTimer = setTimeout(() => {
        if (qrInput.value.trim() !== '' && !requestRunning) {
            sendCall();
        }
    }, 250);
});
    focusScanner();
</script>
</body>
</html>