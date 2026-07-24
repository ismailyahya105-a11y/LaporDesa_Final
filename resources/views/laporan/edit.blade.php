<x-app-layout>
    @if(auth()->user()->isAdmin())
        <x-slot name="header"><h1 class="h3 mb-1">Ubah Status Laporan</h1><p class="text-muted mb-0">{{ $laporan->judul }}</p></x-slot>
        <div class="card form-card border-0 shadow-sm"><div class="card-body p-4">
            <form method="POST" action="{{ route('laporan.update', $laporan) }}">@csrf @method('PUT')
                <label class="form-label" for="status">Status</label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">@foreach(['menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'selesai' => 'Selesai'] as $value => $label)<option value="{{ $value }}" @selected(old('status', $laporan->status) === $value)>{{ $label }}</option>@endforeach</select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="d-flex gap-2 mt-4"><button class="btn btn-village">Simpan</button><a class="btn btn-light" href="{{ route('laporan.show', $laporan) }}">Batal</a></div>
            </form>
        </div></div>
    @else
        <x-slot name="header"><div><span class="eyebrow">Perbarui Pengaduan</span><h1 class="page-title">Edit Laporan</h1><p class="text-muted mb-0">Perbaiki informasi laporan yang telah Anda ajukan.</p></div></x-slot>
        <div class="row g-4"><div class="col-xl-8"><div class="card form-modern border-0"><div class="card-body p-4 p-lg-5">
            <form method="POST" action="{{ route('laporan.update', $laporan) }}" enctype="multipart/form-data">@csrf @method('PUT')
                <div class="mb-4"><label class="form-label" for="judul">Judul laporan</label><input class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $laporan->judul) }}" required>@error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-4"><label class="form-label" for="kategori_id">Kategori</label><select class="form-select @error('kategori_id') is-invalid @enderror" id="kategori_id" name="kategori_id" required><option value="">Pilih kategori laporan</option>@foreach($kategoris as $kategori)<option value="{{ $kategori->id }}" @selected((string) old('kategori_id', $laporan->kategori_id) === (string) $kategori->id)>{{ $kategori->nama }}</option>@endforeach</select>@error('kategori_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-4"><label class="form-label" for="isi_laporan">Isi laporan</label><textarea class="form-control @error('isi_laporan') is-invalid @enderror" id="isi_laporan" name="isi_laporan" rows="7" required>{{ old('isi_laporan', $laporan->isi_laporan) }}</textarea>@error('isi_laporan')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                @if($laporan->foto)<div class="mb-3"><p class="form-label">Foto saat ini</p>@if($laporan->hasPhotoFile())<img src="{{ route('laporan.photo', $laporan) }}" alt="Foto {{ $laporan->judul }}" class="h-40 rounded-3 object-cover">@else<p class="text-danger small">File foto tidak ditemukan.</p>@endif<div class="form-check mt-2"><input class="form-check-input" type="checkbox" value="1" name="hapus_foto" id="hapus_foto" @checked(old('hapus_foto'))><label class="form-check-label text-danger" for="hapus_foto">Hapus foto saat ini</label></div></div>@endif
                <div class="mb-4"><label class="form-label" for="foto">Ganti foto <span class="text-muted fw-normal">(opsional, maksimal 2 MB)</span></label><input class="form-control @error('foto') is-invalid @enderror" type="file" accept="image/*" id="foto" name="foto">@error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="d-flex flex-wrap gap-2"><button class="btn btn-village" type="submit"><i class="fa-solid fa-floppy-disk me-2"></i>Simpan Perubahan</button><a class="btn btn-light" href="{{ route('laporan.show', $laporan) }}">Batal</a></div>
            </form>
        </div></div></div></div>
    @endif
</x-app-layout>
