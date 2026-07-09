# Anaokulu Çağırma Sistemi - İlk Modül Paketi

Bu paket temiz Laravel projesine yapıştırılmak için hazırlandı.

## Kurulum

1. ZIP içindeki klasörleri Laravel proje ana dizinine kopyala.
2. `routes/web.php` ve `routes/api.php` dosyaların varsa içeriği kontrol ederek birleştir.
3. Terminalde çalıştır:

```bash
php artisan migrate:fresh --force
php artisan serve
```

## Test adresleri

Kiosk ekranı:

```text
http://127.0.0.1:8000/kiosk
```

Sınıf ekranı:

```text
http://127.0.0.1:8000/class-screen/1
```

## API Test

```bash
curl -X POST http://127.0.0.1:8000/api/kiosk/call \
  -H "Content-Type: application/json" \
  -d '{"kiosk_code":"KIOSK-TEST","qr_code":"QR-TEST-001"}'
```

## Not

Bu ilk paket QR çağırma mantığı içindir. RFID alanları hazırdır. WebSocket ikinci pakette eklenecek.
