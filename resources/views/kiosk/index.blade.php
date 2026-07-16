<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top, #1e3a5f 0%, transparent 45%),
                var(--background);
        }

        .kiosk {
            width: min(720px, 100%);
        }

        .topbar {
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
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
            background: var(--warning);
            box-shadow: 0 0 0 5px rgba(245, 158, 11, 0.15);
        }

        .connection.online .connection-dot {
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
        }

        .header {
            padding: 34px 38px 22px;
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
        }

        .description {
            margin: 13px 0 0;
            color: var(--muted);
            font-size: 19px;
        }

        .form-area {
            padding: 12px 38px 38px;
        }

        .field {
            margin-top: 15px;
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
            height: 70px;
            border: 2px solid var(--border);
            border-radius: 18px;
            padding: 0 20px;
            background: rgba(15, 23, 42, 0.8);
            color: white;
            outline: none;
            font-size: 22px;
            text-align: center;
        }

        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 5px rgba(34, 197, 94, 0.13);
        }

        .button {
            width: 100%;
            min-height: 66px;
            margin-top: 17px;
            border: 0;
            border-radius: 18px;
            padding: 16px 24px;
            background: var(--primary);
            color: white;
            font-size: 21px;
            font-weight: 900;
            cursor: pointer;
        }

        .button:hover {
            background: var(--primary-dark);
        }

        .button:disabled {
            cursor: wait;
            opacity: 0.6;
        }

        .secondary-button {
            background: var(--surface-light);
        }

        .secondary-button:hover {
            background: #475569;
        }

        .message {
            display: none;
            margin-top: 20px;
            border-radius: 18px;
            padding: 20px;
            text-align: center;
            font-size: 19px;
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

        .kiosk-info {
            margin-bottom: 18px;
            padding: 15px 18px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: rgba(15, 23, 42, 0.55);
            text-align: center;
        }

        .kiosk-info strong {
            display: block;
            font-size: 19px;
        }

        .kiosk-info span {
            display: block;
            margin-top: 5px;
            color: var(--muted);
            font-size: 14px;
        }

        .footer {
            padding: 17px 25px;
            border-top: 1px solid var(--border);
            background: rgba(15, 23, 42, 0.45);
            color: var(--muted);
            text-align: center;
            font-size: 14px;
        }

        .hidden {
            display: none !important;
        }

        details {
            margin-top: 16px;
            text-align: center;
        }

        summary {
            color: var(--muted);
            cursor: pointer;
            font-size: 13px;
        }

        @media (max-width: 600px) {
            body {
                padding: 12px;
            }

            .header {
                padding: 28px 20px 18px;
            }

            .form-area {
                padding: 10px 20px 25px;
            }
        }
    </style>
</head>

<body>
<div class="kiosk">
    <div class="topbar">
        <div class="connection" id="connection-status">
            <span class="connection-dot"></span>
            <span id="connection-text">Kiosk doğrulanıyor...</span>
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

            <h1 id="page-title">Kiosk Aktivasyonu</h1>

            <p class="description" id="page-description">
                Süper admin panelinden oluşturulan cihaz bilgilerini giriniz.
            </p>
        </div>

        <div class="form-area">
            <section id="activation-panel">
                <div class="field">
                    <label class="field-label" for="device_code">
                        16 Haneli Cihaz Kodu
                    </label>

                    <input
                        id="device_code"
                        type="text"
                        inputmode="numeric"
                        maxlength="16"
                        autocomplete="off"
                        placeholder="0000000000000000"
                    >
                </div>

                <div class="field">
                    <label class="field-label" for="api_key">
                        API Anahtarı
                    </label>

                    <input
                        id="api_key"
                        type="password"
                        autocomplete="off"
                        placeholder="API anahtarını giriniz"
                    >
                </div>

                <button
                    id="activate-button"
                    class="button"
                    type="button"
                    onclick="activateKiosk()"
                >
                    Kiosku Aktif Et
                </button>
            </section>

            <section id="scanner-panel" class="hidden">
                <div class="kiosk-info">
                    <strong id="kiosk-name">Kiosk</strong>
                    <span id="kiosk-location"></span>
                </div>

                <label class="field-label" for="qr_code">
                    Veli QR kodunu veya kartını okutunuz
                </label>

                <input
                    id="qr_code"
                    type="text"
                    inputmode="none"
                    autocomplete="off"
                    placeholder="QR okutulması bekleniyor..."
                >

                <button
                    id="send-button"
                    class="button"
                    type="button"
                    onclick="sendCall()"
                >
                    Çağrı Gönder
                </button>

                <details>
                    <summary>Kiosk ayarları</summary>

                    <button
                        class="button secondary-button"
                        type="button"
                        onclick="forgetCredentials()"
                    >
                        Kiosk Bilgilerini Yeniden Gir
                    </button>
                </details>
            </section>

            <div id="message" class="message"></div>
        </div>

        <div class="footer">
            Kiosk Lisans Sistemi · Sürüm 2.0.0
        </div>
    </section>
</div>

<script>
    const APP_VERSION = '2.0.0';

    const activationPanel = document.getElementById('activation-panel');
    const scannerPanel = document.getElementById('scanner-panel');

    const deviceCodeInput = document.getElementById('device_code');
    const apiKeyInput = document.getElementById('api_key');
    const qrInput = document.getElementById('qr_code');

    const activateButton = document.getElementById('activate-button');
    const sendButton = document.getElementById('send-button');

    const messageBox = document.getElementById('message');
    const connectionStatus = document.getElementById('connection-status');
    const connectionText = document.getElementById('connection-text');

    let requestRunning = false;
    let scanTimer = null;
    let lastScannedCode = '';
    let lastScannedAt = 0;

    function createDeviceToken() {
        const bytes = new Uint8Array(32);

        crypto.getRandomValues(bytes);

        return Array.from(bytes)
            .map(byte => byte.toString(16).padStart(2, '0'))
            .join('');
    }

    let deviceToken = localStorage.getItem('kiosk_device_token');

    if (!deviceToken) {
        deviceToken = createDeviceToken();

        localStorage.setItem('kiosk_device_token', deviceToken);
    }

    function getDeviceCode() {
        return localStorage.getItem('kiosk_device_code') || '';
    }

    function getApiKey() {
        return localStorage.getItem('kiosk_api_key') || '';
    }

    function showMessage(type, text) {
        messageBox.className = `message show ${type}`;
        messageBox.textContent = text;
    }

    function clearMessage(delay = 5000) {
        setTimeout(() => {
            messageBox.className = 'message';
            messageBox.textContent = '';
        }, delay);
    }

    function setConnection(type, text) {
        connectionStatus.className = `connection ${type}`;
        connectionText.textContent = text;
    }

    function focusScanner() {
        setTimeout(() => {
            qrInput.focus();
        }, 100);
    }

    function showActivationPanel(message = null) {
        activationPanel.classList.remove('hidden');
        scannerPanel.classList.add('hidden');

        document.getElementById('page-title').textContent =
            'Kiosk Aktivasyonu';

        document.getElementById('page-description').textContent =
            'Süper admin panelinden oluşturulan cihaz bilgilerini giriniz.';

        deviceCodeInput.value = getDeviceCode();
        apiKeyInput.value = getApiKey();

        if (message) {
            showMessage('error', message);
        }
    }

    function showScannerPanel(kiosk) {
        activationPanel.classList.add('hidden');
        scannerPanel.classList.remove('hidden');

        document.getElementById('page-title').textContent =
            'Öğrenci Çağırma';

        document.getElementById('page-description').textContent =
            'Veli QR kodunu veya kartını okuyucuya okutunuz.';

        document.getElementById('kiosk-name').textContent =
            kiosk?.name || 'Öğrenci Çağırma Kiosku';

        document.getElementById('kiosk-location').textContent =
            kiosk?.location || kiosk?.device_code || '';

        setConnection('online', 'Kiosk çevrimiçi');

        focusScanner();
    }

    async function activateKiosk(
        deviceCode = deviceCodeInput.value.trim(),
        apiKey = apiKeyInput.value.trim(),
        silent = false
    ) {
        if (!/^\d{16}$/.test(deviceCode)) {
            if (!silent) {
                showMessage(
                    'error',
                    'Cihaz kodu tam 16 rakam olmalıdır.'
                );
            }

            return false;
        }

        if (!apiKey) {
            if (!silent) {
                showMessage(
                    'error',
                    'API anahtarını giriniz.'
                );
            }

            return false;
        }

        activateButton.disabled = true;

        if (!silent) {
            showMessage(
                'loading',
                'Kiosk etkinleştiriliyor...'
            );
        }

        try {
            const response = await fetch('/api/kiosk/activate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    device_code: deviceCode,
                    api_key: apiKey,
                    device_token: deviceToken,
                    device_name: navigator.platform || 'Web Kiosk',
                    app_version: APP_VERSION,
                }),
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message ||
                    'Kiosk etkinleştirilemedi.'
                );
            }

            localStorage.setItem(
                'kiosk_device_code',
                deviceCode
            );

            localStorage.setItem(
                'kiosk_api_key',
                apiKey
            );

            showScannerPanel(result.kiosk);

            if (!silent) {
                showMessage(
                    'success',
                    '✓ ' + result.message
                );

                clearMessage(3500);
            }

            return true;
        } catch (error) {
            setConnection(
                'offline',
                'Kiosk doğrulanamadı'
            );

            showActivationPanel(
                error.message ||
                'Kiosk etkinleştirilemedi.'
            );

            return false;
        } finally {
            activateButton.disabled = false;
        }
    }

    async function sendCall() {
        const qrCode = qrInput.value.trim();

        if (!qrCode || requestRunning) {
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

        requestRunning = true;

        sendButton.disabled = true;
        sendButton.textContent = 'İşleniyor...';

        showMessage(
            'loading',
            'Öğrenci çağrısı oluşturuluyor...'
        );

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
                    device_code: getDeviceCode(),
                    api_key: getApiKey(),
                    device_token: deviceToken,
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
                '✓ ' +
                (
                    result.message ||
                    'Çağrı başarıyla oluşturuldu.'
                )
            );

            playSuccessSound();

            clearMessage();
        } catch (error) {
            showMessage(
                'error',
                '✕ ' +
                (
                    error.message ||
                    'Bağlantı hatası oluştu.'
                )
            );
        } finally {
            qrInput.value = '';

            requestRunning = false;

            sendButton.disabled = false;
            sendButton.textContent = 'Çağrı Gönder';

            focusScanner();
        }
    }

    qrInput.addEventListener('input', function () {
        clearTimeout(scanTimer);

        scanTimer = setTimeout(() => {
            if (
                qrInput.value.trim() &&
                !requestRunning
            ) {
                sendCall();
            }
        }, 250);
    });

    qrInput.addEventListener('keydown', function (event) {
        if (
            ['Enter', 'Tab', 'Escape'].includes(event.key)
        ) {
            event.preventDefault();

            if (
                qrInput.value.trim() &&
                !requestRunning
            ) {
                clearTimeout(scanTimer);

                sendCall();
            }
        }
    });

    function forgetCredentials() {
        const confirmed = confirm(
            'Kiosk kodu ve API anahtarı bu cihazdan silinsin mi?'
        );

        if (!confirmed) {
            return;
        }

        localStorage.removeItem('kiosk_device_code');
        localStorage.removeItem('kiosk_api_key');

        messageBox.className = 'message';
        messageBox.textContent = '';

        setConnection(
            '',
            'Kiosk etkinleştirilmedi'
        );

        showActivationPanel();
    }

    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();

            return;
        }

        document.exitFullscreen();
    }

    function updateInternetStatus() {
        if (!navigator.onLine) {
            setConnection(
                'offline',
                'İnternet bağlantısı yok'
            );
        }
    }

    window.addEventListener(
        'online',
        bootKiosk
    );

    window.addEventListener(
        'offline',
        updateInternetStatus
    );

    function playSuccessSound() {
        try {
            const AudioContextClass =
                window.AudioContext ||
                window.webkitAudioContext;

            const audioContext =
                new AudioContextClass();

            const oscillator =
                audioContext.createOscillator();

            const gain =
                audioContext.createGain();

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

            oscillator.stop(
                audioContext.currentTime + 0.35
            );
        } catch (error) {
            console.warn(
                'Başarı sesi çalınamadı.',
                error
            );
        }
    }

    async function bootKiosk() {
        if (!navigator.onLine) {
            updateInternetStatus();
            showActivationPanel();

            return;
        }

        const deviceCode = getDeviceCode();
        const apiKey = getApiKey();

        if (!deviceCode || !apiKey) {
            setConnection(
                '',
                'Kiosk etkinleştirilmedi'
            );

            showActivationPanel();

            return;
        }

        setConnection(
            '',
            'Kiosk doğrulanıyor...'
        );

        await activateKiosk(
            deviceCode,
            apiKey,
            true
        );
    }

    bootKiosk();
</script>
</body>
</html>