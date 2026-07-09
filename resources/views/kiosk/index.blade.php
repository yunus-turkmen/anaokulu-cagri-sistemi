<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kiosk - Öğrenci Çağırma</title>
    <style>
        body { font-family: Arial, sans-serif; background:#111827; color:white; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
        .box { width:420px; background:#1f2937; padding:30px; border-radius:20px; box-shadow:0 20px 60px #0007; }
        input, button { width:100%; padding:16px; font-size:18px; border-radius:12px; border:0; margin-top:12px; box-sizing:border-box; }
        button { background:#22c55e; color:white; font-weight:bold; cursor:pointer; }
        .msg { margin-top:16px; font-size:18px; min-height:28px; }
    </style>
</head>
<body>
<div class="box">
    <h1>Öğrenci Çağırma Kiosku</h1>
    <p>QR okuyucuyu bu alana okutun.</p>

    <input id="kiosk_code" value="KIOSK-TEST" placeholder="Kiosk Kodu">
    <input id="qr_code" autofocus placeholder="QR Kod">
    <button onclick="sendCall()">Çağrı Gönder</button>

    <div id="msg" class="msg"></div>
</div>

<script>
async function sendCall() {
    const msg = document.getElementById('msg');
    msg.innerText = 'Gönderiliyor...';

    const response = await fetch('/api/kiosk/call', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({
            kiosk_code: document.getElementById('kiosk_code').value,
            qr_code: document.getElementById('qr_code').value,
        })
    });

    const json = await response.json();
    msg.innerText = json.message ?? 'İşlem tamamlandı.';
    document.getElementById('qr_code').value = '';
    document.getElementById('qr_code').focus();
}

document.getElementById('qr_code').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') sendCall();
});
</script>
</body>
</html>
