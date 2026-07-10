<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $schoolClass->name }} - Çağrı Ekranı</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
        }

        .topbar {
            padding: 22px 36px;
            background: linear-gradient(135deg, #1e293b, #334155);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .title {
            margin: 0;
            font-size: 36px;
        }

        .subtitle {
            margin-top: 6px;
            color: #cbd5e1;
        }

        .right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .counter {
            padding: 12px 20px;
            border-radius: 15px;
            background: rgba(255, 255, 255, .14);
            text-align: center;
        }

        .counter strong {
            display: block;
            font-size: 30px;
        }

        .clock {
            min-width: 115px;
            font-size: 22px;
            font-weight: bold;
        }

        .fullscreen {
            border: 1px solid rgba(255, 255, 255, .3);
            border-radius: 12px;
            padding: 13px 18px;
            background: rgba(255, 255, 255, .12);
            color: white;
            cursor: pointer;
        }

        .content {
            max-width: 1400px;
            margin: auto;
            padding: 30px 36px;
        }

        .connection {
            margin-bottom: 18px;
            font-weight: bold;
            color: #64748b;
        }

        .connection.connected {
            color: #15803d;
        }

        .connection.error {
            color: #dc2626;
        }

        .calls {
            display: grid;
            gap: 20px;
        }

        .call-card {
            position: relative;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 25px;
            align-items: center;
            padding: 28px 30px 28px 38px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 22px;
            box-shadow: 0 12px 35px rgba(15, 23, 42, .08);
            overflow: hidden;
            animation: call-arrived .35s ease;
        }

        .call-card:first-child {
            border: 2px solid #f59e0b;
            background: linear-gradient(135deg, white, #fffbeb);
        }

        .call-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 8px;
            background: #f59e0b;
        }

        @keyframes call-arrived {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .badge {
            display: inline-block;
            margin-bottom: 10px;
            padding: 6px 11px;
            border-radius: 999px;
            background: #fef3c7;
            color: #92400e;
            font-size: 13px;
            font-weight: bold;
        }

        .student-name {
            margin: 0;
            font-size: clamp(34px, 4vw, 52px);
            line-height: 1.1;
        }

        .details {
            display: flex;
            flex-wrap: wrap;
            gap: 15px 25px;
            margin-top: 16px;
            font-size: 18px;
            color: #334155;
        }

        .guardian {
            display: inline-block;
            margin-top: 17px;
            padding: 14px 18px;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: #f8fafc;
        }

        .guardian small {
            display: block;
            color: #64748b;
        }

        .guardian strong {
            display: block;
            margin-top: 4px;
            font-size: 19px;
        }

        .complete-button {
            min-width: 220px;
            border: 0;
            border-radius: 16px;
            padding: 21px 25px;
            background: #16a34a;
            color: white;
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
        }

        .complete-button:hover {
            background: #15803d;
        }

        .empty {
            min-height: 400px;
            padding: 50px;
            border-radius: 24px;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #64748b;
        }

        .empty strong {
            color: #0f172a;
            font-size: 30px;
        }

        @media (max-width: 800px) {
            .topbar {
                align-items: flex-start;
                flex-direction: column;
                padding: 20px;
            }

            .content {
                padding: 20px 15px;
            }

            .call-card {
                grid-template-columns: 1fr;
            }

            .complete-button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
<header class="topbar">
    <div>
        <h1 class="title">{{ $schoolClass->name }} Sınıfı</h1>
        <div class="subtitle">Canlı öğrenci çağrı ekranı</div>
    </div>

    <div class="right">
        <div class="counter">
            <strong id="waiting-count">{{ $calls->count() }}</strong>
            <span>Bekleyen</span>
        </div>

        <div class="clock" id="clock">--:--:--</div>

        <button class="fullscreen" type="button" onclick="toggleFullscreen()">
            ⛶ Tam Ekran
        </button>
        <button
    class="fullscreen"
    id="enable-sound-button"
    type="button"
    onclick="enableSound()"
>
    🔊 Sesi Aktifleştir
</button>
    </div>
</header>

<main class="content">
    <div class="connection" id="connection-status">
        WebSocket bağlantısı kuruluyor...
    </div>

    <div class="calls" id="calls-list">
        @foreach($calls as $call)
            @php
                $guardian = $call->guardian ?? $call->parentGuardian;

                $studentName = $call->student?->full_name
                    ?: $call->student?->name
                    ?: 'Öğrenci bulunamadı';

                $guardianName = $guardian?->full_name
                    ?: $guardian?->name
                    ?: 'Veli bilgisi yok';

                $relationship = match ($guardian?->relationship) {
                    'anne' => 'Anne',
                    'baba' => 'Baba',
                    'dede' => 'Dede',
                    'babaanne' => 'Babaanne',
                    'anneanne' => 'Anneanne',
                    'servis' => 'Servis',
                    'diger' => 'Diğer',
                    default => 'Veli',
                };
            @endphp

            <article class="call-card" id="call-{{ $call->id }}">
                <div>
                    @if($loop->first)
                        <span class="badge">🔔 Son çağrı</span>
                    @endif

                    <h2 class="student-name">{{ $studentName }}</h2>

                    <div class="details">
                        <span>🕒 {{ optional($call->called_at)->format('H:i:s') }}</span>
                        <span>🎓 {{ $call->schoolClass?->name ?? $schoolClass->name }}</span>

                        @if($call->kiosk?->name)
                            <span>🚪 {{ $call->kiosk->name }}</span>
                        @endif
                    </div>

                    <div class="guardian">
                        <small>Gelen veli · {{ $relationship }}</small>
                        <strong>{{ $guardianName }}</strong>
                    </div>
                </div>

               <form

    class="complete-form"

    method="POST"

    action="{{ route('pickup-calls.complete', $call) }}"

    data-call-id="{{ $call->id }}"

>
                    @csrf

                    <button class="complete-button" type="submit">
                        ✓ Teslim Edildi
                    </button>
                </form>
            </article>
        @endforeach
    </div>

    <section
        class="empty"
        id="empty-state"
        @if($calls->count() > 0) style="display:none" @endif
    >
        <strong>Bekleyen çağrı yok</strong>
        <p>Yeni çağrı geldiğinde burada anında görüntülenecek.</p>
    </section>
</main>

<script>
    const schoolId = @json($schoolClass->school_id);
    const classId = @json($schoolClass->id);
    const completeUrlTemplate = @json(url('/pickup-calls/__CALL_ID__/complete'));

    const callsList = document.getElementById('calls-list');
    const emptyState = document.getElementById('empty-state');
    const waitingCount = document.getElementById('waiting-count');
    const connectionStatus = document.getElementById('connection-status');

    const existingCallIds = new Set(
        Array.from(document.querySelectorAll('.call-card'))
            .map(card => Number(card.id.replace('call-', '')))
    );

    function updateClock() {
        document.getElementById('clock').textContent =
            new Date().toLocaleTimeString('tr-TR', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            });
    }

    updateClock();
    setInterval(updateClock, 1000);

    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
            return;
        }

        document.exitFullscreen();
    }

    function escapeHtml(value) {
        const element = document.createElement('div');
        element.textContent = value ?? '';
        return element.innerHTML;
    }

    function translateRelationship(value) {
        const relationships = {
            anne: 'Anne',
            baba: 'Baba',
            dede: 'Dede',
            babaanne: 'Babaanne',
            anneanne: 'Anneanne',
            servis: 'Servis',
            diger: 'Diğer',
        };

        return relationships[value] ?? 'Veli';
    }

        let soundEnabled = localStorage.getItem('class_screen_sound_enabled') === '1';
let availableVoices = [];

function loadVoices() {
    availableVoices = window.speechSynthesis.getVoices();
}

loadVoices();

window.speechSynthesis.addEventListener('voiceschanged', loadVoices);

function updateSoundButton() {
    const button = document.getElementById('enable-sound-button');

    if (!button) {
        return;
    }

    button.textContent = soundEnabled
        ? '🔊 Ses Aktif'
        : '🔇 Sesi Aktifleştir';

    button.style.background = soundEnabled
        ? '#16a34a'
        : 'rgba(255, 255, 255, .12)';
}

function enableSound() {
    const testUtterance = new SpeechSynthesisUtterance('Sesli bildirim aktif.');
    testUtterance.lang = 'tr-TR';
    testUtterance.rate = 0.9;

    const turkishVoice = availableVoices.find(
        voice => voice.lang?.toLowerCase().startsWith('tr')
    );

    if (turkishVoice) {
        testUtterance.voice = turkishVoice;
    }

    window.speechSynthesis.cancel();
    window.speechSynthesis.speak(testUtterance);

    soundEnabled = true;
    localStorage.setItem('class_screen_sound_enabled', '1');

    updateSoundButton();
}

updateSoundButton();



    function speakCall(event) {
    if (!soundEnabled) {
        console.warn('Sesli bildirim henüz aktifleştirilmedi.');
        return;
    }

    const studentName = event.student_name || 'Öğrenci';
    const guardianName = event.guardian_name || 'velisi';

    const text =
        `${studentName} öğrencisinin velisi ${guardianName} geldi. ` +
        `Öğrenciyi çıkışa yönlendirin.`;

    window.speechSynthesis.cancel();

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'tr-TR';
    utterance.rate = 0.9;
    utterance.pitch = 1;
    utterance.volume = 1;

    const turkishVoice = availableVoices.find(
        voice => voice.lang?.toLowerCase().startsWith('tr')
    );

    if (turkishVoice) {
        utterance.voice = turkishVoice;
    }

    utterance.onerror = error => {
        console.error('Sesli anons hatası:', error);
    };

    window.speechSynthesis.speak(utterance);
}

    function removeOldLatestBadge() {
        document
            .querySelectorAll('.badge')
            .forEach(element => element.remove());
    }

    function addCall(event) {
        const callId = Number(event.id);

        if (!callId || existingCallIds.has(callId)) {
            return;
        }

        existingCallIds.add(callId);
        removeOldLatestBadge();

        const completeUrl = completeUrlTemplate.replace('__CALL_ID__', callId);
        const csrfToken = @json(csrf_token());

        const card = document.createElement('article');
        card.className = 'call-card';
        card.id = `call-${callId}`;

        card.innerHTML = `
            <div>
                <span class="badge">🔔 Son çağrı</span>

                <h2 class="student-name">
                    ${escapeHtml(event.student_name || 'Öğrenci')}
                </h2>

                <div class="details">
                    <span>🕒 ${escapeHtml(event.called_at || '')}</span>
                    <span>🎓 ${escapeHtml(event.class_name || '')}</span>

                    ${
                        event.kiosk_name
                            ? `<span>🚪 ${escapeHtml(event.kiosk_name)}</span>`
                            : ''
                    }
                </div>

                <div class="guardian">
                    <small>
                        Gelen veli ·
                        ${escapeHtml(translateRelationship(event.relationship))}
                    </small>

                    <strong>
                        ${escapeHtml(event.guardian_name || 'Veli bilgisi yok')}
                    </strong>
                </div>
            </div>

            <form

    class="complete-form"

    method="POST"

    action="${completeUrl}"

    data-call-id="${callId}"

>
                <input type="hidden" name="_token" value="${csrfToken}">

                <button class="complete-button" type="submit">
                    ✓ Teslim Edildi
                </button>
            </form>
        `;

        callsList.prepend(card);
        emptyState.style.display = 'none';
        waitingCount.textContent = existingCallIds.size;

        speakCall(event);
    }

    function connectToReverb() {
        if (!window.Echo) {
            connectionStatus.textContent =
                'Echo yüklenemedi. Vite dosyalarını kontrol edin.';

            connectionStatus.className = 'connection error';
            return;
        }

        const channelName = `school.${schoolId}.class.${classId}`;

        window.Echo
            .channel(channelName)
            .listen('.pickup-call.created', event => {
                addCall(event);
            });

        connectionStatus.textContent = '● Canlı bağlantı aktif';
        connectionStatus.className = 'connection connected';
    }

    window.addEventListener('load', () => {
        setTimeout(connectToReverb, 300);
    });
    document.addEventListener('submit', async function (event) {
    const form = event.target.closest('.complete-form');

    if (!form) {
        return;
    }

    event.preventDefault();

    const button = form.querySelector('.complete-button');
    const callId = Number(form.dataset.callId);

    if (!callId || button.disabled) {
        return;
    }

    button.disabled = true;
    button.textContent = 'İşleniyor...';

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new FormData(form),
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(
                result.message || 'Teslim işlemi tamamlanamadı.'
            );
        }

        const card = document.getElementById(`call-${callId}`);

        if (card) {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            card.style.transform = 'translateX(50px)';

            setTimeout(() => {
                card.remove();
                existingCallIds.delete(callId);

                const remainingCalls =
                    document.querySelectorAll('.call-card').length;

                waitingCount.textContent = remainingCalls;

                if (remainingCalls === 0) {
                    emptyState.style.display = 'flex';
                } else {
                    const firstCard =
                        document.querySelector('.call-card');

                    if (
                        firstCard &&
                        !firstCard.querySelector('.badge')
                    ) {
                        const badge =
                            document.createElement('span');

                        badge.className = 'badge';
                        badge.textContent = '🔔 Son çağrı';

                        firstCard
                            .querySelector('div')
                            .prepend(badge);
                    }
                }
            }, 300);
        }
    } catch (error) {
        console.error(error);

        alert(error.message);

        button.disabled = false;
        button.textContent = '✓ Teslim Edildi';
    }
});
</script>
</body>
</html>