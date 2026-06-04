@extends('layouts.auth')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h3 class="font-weight-bold">Verifikasi OTP</h3>
                        <p class="text-muted">Masukkan kode OTP yang telah dikirim ke email Anda</p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div id="email-status" class="alert" style="display:none;"></div>

                    <form method="POST" action="{{ route('otp.verify') }}" id="otp-form">
                        @csrf
                        <div class="form-group">
                            <label class="font-weight-bold">Kode OTP</label>
                            <input type="text"
                                   id="otp-input"
                                   name="otp"
                                   class="form-control form-control-lg text-center @error('otp') is-invalid @enderror"
                                   maxlength="6"
                                   autocomplete="one-time-code"
                                   placeholder="Ketik atau tempel 6 karakter"
                                   autofocus
                                   style="font-size:2rem; letter-spacing:8px; font-weight:bold;">
                            @error('otp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Contoh: <strong>A3F9KZ</strong></small>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-medium">
                                VERIFIKASI
                            </button>
                        </div>

                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-link text-primary" id="resend-otp">
                                Kirim Ulang Kode OTP
                            </button>
                        </div>

                        <div class="text-center mt-2">
                            <a href="{{ route('login') }}" class="text-muted">
                                &larr; Kembali ke Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const otpInput   = document.getElementById('otp-input');
    const resendBtn  = document.getElementById('resend-otp');
    const status     = document.getElementById('email-status');

    // Auto uppercase
    otpInput.addEventListener('input', function () {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });

    function showStatus(msg, type) {
        status.className = 'alert alert-' + type;
        status.textContent = msg;
        status.style.display = 'block';
    }

    function sendOtp() {
        resendBtn.disabled = true;
        resendBtn.textContent = 'Mengirim...';
        showStatus('Mengirim kode OTP ke email Anda...', 'info');

        fetch('{{ route("otp.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.otp_debug) {
                showStatus(data.message, 'warning');
                otpInput.value = data.otp_debug;
                otpInput.focus();
                otpInput.select();
            } else if (data.success) {
                showStatus(data.message, 'success');
                otpInput.focus();
            } else {
                showStatus(data.message, 'danger');
            }
        })
        .catch(() => showStatus('Terjadi kesalahan. Coba lagi.', 'danger'))
        .finally(() => {
            resendBtn.disabled = false;
            resendBtn.textContent = 'Kirim Ulang Kode OTP';
        });
    }

    resendBtn.addEventListener('click', sendOtp);
    sendOtp();
});
</script>
@endpush
