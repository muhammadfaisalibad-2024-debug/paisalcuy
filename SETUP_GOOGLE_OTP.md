# Setup Google OAuth & OTP Authentication

## Ringkasan Implementasi

Implementasi ini menambahkan fitur:
1. **Login menggunakan Google OAuth** (SSO)
2. **Verifikasi OTP 2-Factor Authentication** untuk semua login (normal & Google)
3. **Email OTP** dikirim otomatis setelah autentikasi berhasil

## Perubahan yang Dilakukan

### 1. Database Migration
File: `database/migrations/2026_02_26_070441_add_google_id_and_otp_to_users_table.php`

Menambahkan kolom:
- `id_google` (varchar 256) - menyimpan Google User ID
- `otp` (varchar 6) - menyimpan kode OTP temporer

### 2. Model User
File: `app/Models/User.php`
- Menambahkan `id_google` dan `otp` ke `$fillable`

### 3. Controllers
- `app/Http/Controllers/Auth/GoogleAuthController.php` - Handle Google OAuth
- `app/Http/Controllers/Auth/OtpController.php` - Handle OTP generation & verification
- `app/Http/Controllers/Auth/LoginController.php` - Dimodifikasi untuk redirect ke OTP

### 4. Views
- `resources/views/auth/otp.blade.php` - Form verifikasi OTP dengan 6 input box
- `resources/views/auth/login.blade.php` - Ditambahkan tombol "Login dengan Google"

### 5. Routes
File: `routes/web.php`
- `GET /auth/google` - Redirect ke Google OAuth
- `GET /auth/google/callback` - Handle callback dari Google
- `GET /otp` - Form input OTP
- `POST /otp/generate` - Generate & kirim OTP via email
- `POST /otp/verify` - Verifikasi OTP

### 6. Configuration
File: `config/services.php`
- Menambahkan konfigurasi Google OAuth

## Cara Setup

### 1. Jalankan Migration
```bash
php artisan migrate
```

### 2. Setup Google OAuth Credentials

#### A. Buat Project di Google Cloud Console
1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Buat project baru atau pilih project yang ada
3. Aktifkan **Google+ API**

#### B. Buat OAuth 2.0 Credentials
1. Masuk ke **APIs & Services > Credentials**
2. Klik **Create Credentials > OAuth client ID**
3. Pilih **Application type: Web application**
4. Isi **Authorized redirect URIs**:
   ```
   http://localhost:8000/auth/google/callback
   ```
   *Sesuaikan dengan URL aplikasi Anda di production*

5. Copy **Client ID** dan **Client Secret**

#### C. Update Environment Variables
Edit file `.env` dan tambahkan:
```env
GOOGLE_CLIENT_ID=your_client_id_here
GOOGLE_CLIENT_SECRET=your_client_secret_here
GOOGLE_REDIRECT_URL=http://localhost:8000/auth/google/callback
```

### 3. Setup Email Configuration (untuk OTP)

Untuk development, Anda bisa gunakan:

#### Option 1: Mailtrap (Recommended untuk testing)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@koleksibuku.com"
MAIL_FROM_NAME="Koleksi Buku"
```

#### Option 2: Gmail (untuk production)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your_email@gmail.com"
MAIL_FROM_NAME="Koleksi Buku"
```

**Catatan**: Untuk Gmail, gunakan [App Password](https://myaccount.google.com/apppasswords), bukan password biasa.

#### Option 3: Log (untuk development only)
```env
MAIL_MAILER=log
```
OTP akan ditulis ke file log di `storage/logs/laravel.log`

### 4. Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
```

## Cara Menggunakan

### Login Normal (Email & Password)
1. User login dengan email & password
2. Setelah autentikasi berhasil → redirect ke halaman OTP
3. Sistem generate OTP 6 karakter & kirim ke email
4. User input OTP
5. Jika OTP benar → login berhasil

### Login dengan Google
1. User klik tombol "Login dengan Google"
2. Redirect ke Google OAuth
3. User pilih/login akun Google
4. Callback ke aplikasi → user dicari/dibuat di database
5. Redirect ke halaman OTP
6. Sistem generate OTP 6 karakter & kirim ke email
7. User input OTP
8. Jika OTP benar → login berhasil

## Alur OTP

```
┌─────────────────┐
│  Login Success  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Logout User    │ (temporary)
│  Store user_id  │ (in session)
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  OTP Page       │
│  Auto-generate  │
│  & Send Email   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  User Input OTP │
└────────┬────────┘
         │
         ▼
    ┌────┴────┐
    │ Valid?  │
    └─┬────┬──┘
      │    │
     Yes   No
      │    │
      ▼    ▼
   Login Error
```

## Fitur OTP View

- **6 Input Box** terpisah untuk setiap karakter OTP
- **Auto-focus** ke input berikutnya setelah input karakter
- **Auto-backspace** ke input sebelumnya saat delete
- **Paste support** - bisa paste 6 karakter sekaligus
- **Resend OTP** - tombol untuk kirim ulang OTP
- **Auto-send** - OTP dikirim otomatis saat halaman dibuka
- **Upper case** - semua input otomatis uppercase

## Testing

### Test Login dengan Email
```bash
# Pastikan ada user di database
php artisan tinker
>>> User::create(['name' => 'Test User', 'email' => 'test@example.com', 'password' => bcrypt('password')]);
```

Kemudian login dengan:
- Email: test@example.com
- Password: password

### Test Login dengan Google
1. Klik "Login dengan Google"
2. Pilih akun Google
3. Check email untuk OTP
4. Input OTP

## Troubleshooting

### OTP tidak diterima
- Check konfigurasi email di `.env`
- Jika `MAIL_MAILER=log`, check file `storage/logs/laravel.log`
- Pastikan email user valid

### Error "Class 'Socialite' not found"
```bash
composer require laravel/socialite
php artisan config:clear
```

### Error "Invalid redirect_uri"
- Pastikan Authorized redirect URIs di Google Cloud Console sesuai dengan `GOOGLE_REDIRECT_URL` di `.env`

### Session expired di OTP page
- Pastikan session driver berfungsi dengan baik
- Check `SESSION_DRIVER` di `.env` (gunakan `database` atau `file`)

## File yang Perlu Diperhatikan

- `.env` - Jangan commit file ini! Sudah ada di `.gitignore`
- `.env.example` - Sudah diupdate dengan variabel Google OAuth
- Database migration - Sudah dibuat, tinggal run `php artisan migrate`

## Keamanan

1. **Password tidak disimpan** untuk user Google (random password di-generate)
2. **OTP dihapus** setelah verifikasi berhasil
3. **Session-based** OTP verification mencegah bypass
4. **One-time use** OTP - setelah dipakai, OTP di-clear dari database

---

**Catatan**: Untuk production, pastikan:
- Gunakan HTTPS untuk semua komunikasi
- Update `GOOGLE_REDIRECT_URL` ke domain production
- Update Authorized redirect URIs di Google Cloud Console
- Gunakan email service yang reliable (Gmail, SendGrid, Mailgun, dll)
