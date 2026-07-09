<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="5">
    <title>{{ $schoolClass->name }} - Sınıf Ekranı</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f3f4f6; margin:0; }
        header { background:#111827; color:white; padding:24px 40px; font-size:32px; font-weight:bold; }
        .wrap { padding:30px 40px; }
        .call { background:white; border-radius:18px; padding:24px; margin-bottom:18px; box-shadow:0 10px 30px #0001; display:flex; justify-content:space-between; gap:20px; align-items:center; }
        .student { font-size:36px; font-weight:bold; color:#111827; }
        .text { font-size:22px; color:#374151; margin-top:8px; }
        button { background:#16a34a; color:white; border:0; border-radius:12px; padding:16px 24px; font-size:20px; font-weight:bold; cursor:pointer; }
        .empty { font-size:28px; color:#6b7280; background:white; padding:40px; border-radius:18px; }
    </style>
</head>
<body>
<header>{{ $schoolClass->name }} Sınıf Ekranı</header>
<div class="wrap">
    @forelse($calls as $call)
        <div class="call">
            <div>
                <div class="student">🔔 {{ $call->student->name }}</div>
                <div class="text">
                    {{ $call->parent?->name ?? 'Velisi' }} geldi. Öğrenciyi çıkışa yönlendirin.
                </div>
            </div>
            <form method="POST" action="{{ route('pickup-calls.complete', $call) }}">
                @csrf
                <button>Teslim Edildi</button>
            </form>
        </div>
    @empty
        <div class="empty">Bekleyen çağrı yok.</div>
    @endforelse
</div>

@if($calls->count() > 0)
<script>
    const text = @json($calls->first()->student->name . ' öğrencisinin velisi geldi. Öğrenciyi çıkışa yönlendirin.');
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'tr-TR';
    speechSynthesis.speak(utterance);
</script>
@endif
</body>
</html>
