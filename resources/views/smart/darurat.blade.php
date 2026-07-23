<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-extrabold">Panic Button</h1>
        <p class="text-sm text-slate-500">Gunakan hanya dalam kondisi darurat nyata.</p>
    </x-slot>

    @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <strong>Sinyal SOS belum terkirim.</strong>
            <ul class="mb-0 mt-2 list-disc ps-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="mx-auto max-w-2xl rounded-3xl border border-red-200 bg-white p-6 text-center shadow-sm md:p-8">
        <span class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-red-100 text-3xl text-red-600"><i class="fa-solid fa-triangle-exclamation"></i></span>
        <h2 class="mt-4 text-xl font-black">Butuh Bantuan Darurat?</h2>
        <p class="text-sm text-slate-500">Lokasi GPS, nama, nomor telepon, dan waktu akan dikirim ke admin desa.</p>

        <form method="POST" action="{{ route('darurat.store') }}" id="sosForm" class="mt-6 space-y-3">
            @csrf
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <select name="jenis_darurat" class="w-full rounded-xl border-slate-300" required aria-label="Jenis keadaan darurat">
                @foreach(['Kecelakaan','Kebakaran','Kriminal','Bencana','Medis'] as $jenis)
                    <option value="{{ $jenis }}" @selected(old('jenis_darurat') === $jenis)>{{ $jenis }}</option>
                @endforeach
            </select>
            <input type="tel" name="nomor_telepon" value="{{ old('nomor_telepon') }}" placeholder="Nomor telepon aktif" autocomplete="tel" inputmode="tel" minlength="8" maxlength="30" class="w-full rounded-xl border-slate-300" required>
            <button type="button" id="sosButton" class="w-full rounded-2xl bg-red-600 px-6 py-5 text-xl font-black text-white shadow-lg transition hover:bg-red-700 disabled:cursor-wait disabled:opacity-60">
                <i class="fa-solid fa-location-crosshairs me-2"></i><span id="sosButtonText">SOS DARURAT</span>
            </button>
            <div id="gpsStatus" class="min-h-5 text-sm text-slate-500" role="status" aria-live="polite">Tekan tombol untuk mengambil lokasi GPS Anda.</div>
        </form>
    </div>

    <section class="mx-auto mt-7 max-w-2xl">
        <h2 class="mb-3 text-lg font-extrabold">Riwayat SOS Saya</h2>
        <div class="space-y-3">
            @forelse($riwayat as $item)
                <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div><strong>{{ $item->jenis_darurat }}</strong><p class="mb-0 text-xs text-slate-500">Dikirim {{ $item->created_at->format('d M Y, H:i') }}</p></div>
                        <span @class(['rounded-full px-3 py-1 text-xs font-bold','bg-red-100 text-red-700'=>$item->status==='aktif','bg-amber-100 text-amber-700'=>$item->status==='ditangani','bg-emerald-100 text-emerald-700'=>$item->status==='selesai'])>{{ ucfirst($item->status) }}</span>
                    </div>
                </article>
            @empty
                <p class="rounded-2xl bg-white p-5 text-center text-sm text-slate-500 shadow-sm">Belum ada sinyal SOS yang dikirim.</p>
            @endforelse
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('sosForm');
                const button = document.getElementById('sosButton');
                const buttonText = document.getElementById('sosButtonText');
                const status = document.getElementById('gpsStatus');
                const latitude = document.getElementById('latitude');
                const longitude = document.getElementById('longitude');

                const resetButton = () => {
                    button.disabled = false;
                    buttonText.textContent = 'SOS DARURAT';
                };

                button.addEventListener('click', () => {
                    if (!form.reportValidity()) return;

                    if (!window.isSecureContext) {
                        status.innerHTML = 'GPS diblokir karena halaman belum aman. Buka melalui <a class="font-bold text-blue-600 underline" href="https://lapordesa.test/panic-button">https://lapordesa.test</a> lalu izinkan lokasi.';
                        return;
                    }

                    if (!('geolocation' in navigator)) {
                        status.textContent = 'Browser ini tidak mendukung GPS. Gunakan browser atau perangkat lain.';
                        return;
                    }

                    button.disabled = true;
                    buttonText.textContent = 'MENGAMBIL LOKASI...';
                    status.textContent = 'Mohon izinkan akses lokasi pada browser Anda.';

                    navigator.geolocation.getCurrentPosition(position => {
                        latitude.value = position.coords.latitude;
                        longitude.value = position.coords.longitude;
                        status.textContent = `Lokasi ditemukan (akurasi ±${Math.round(position.coords.accuracy)} meter).`;

                        if (window.confirm('Kirim sinyal SOS dan lokasi Anda ke admin desa sekarang?')) {
                            buttonText.textContent = 'MENGIRIM SOS...';
                            form.requestSubmit();
                        } else {
                            resetButton();
                            status.textContent = 'Pengiriman SOS dibatalkan.';
                        }
                    }, error => {
                        const messages = {
                            1: 'Izin lokasi ditolak. Aktifkan izin lokasi untuk situs ini, lalu coba lagi.',
                            2: 'Lokasi tidak dapat ditemukan. Aktifkan GPS dan koneksi internet, lalu coba lagi.',
                            3: 'Pencarian lokasi terlalu lama. Pindah ke area yang lebih terbuka, lalu coba lagi.'
                        };
                        status.textContent = messages[error.code] || 'Terjadi kesalahan saat mengambil lokasi.';
                        resetButton();
                    }, {enableHighAccuracy: true, timeout: 15000, maximumAge: 0});
                });
            });
        </script>
    @endpush
</x-app-layout>
