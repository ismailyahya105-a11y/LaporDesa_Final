<x-app-layout>
    <x-slot name="header">
        <div><span class="eyebrow">Kelola Usaha</span><h1 class="page-title">Edit UMKM</h1><p class="text-muted mb-0">Perbarui informasi usaha dan produk Anda.</p></div>
    </x-slot>

    <div class="mx-auto max-w-3xl rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 md:p-8">
        <form method="POST" action="{{ route('pasar.update', $produkUmkm) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-4">
                <div class="col-md-6"><label class="form-label fw-semibold" for="nama_usaha">Nama usaha</label><input id="nama_usaha" name="nama_usaha" value="{{ old('nama_usaha', $produkUmkm->nama_usaha) }}" class="form-control @error('nama_usaha') is-invalid @enderror" required>@error('nama_usaha')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label fw-semibold" for="kategori">Kategori</label><input id="kategori" name="kategori" value="{{ old('kategori', $produkUmkm->kategori) }}" class="form-control @error('kategori') is-invalid @enderror" required>@error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label fw-semibold" for="nama_produk">Nama produk</label><input id="nama_produk" name="nama_produk" value="{{ old('nama_produk', $produkUmkm->nama_produk) }}" class="form-control @error('nama_produk') is-invalid @enderror" required>@error('nama_produk')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label fw-semibold" for="kontak">Nomor WhatsApp/telepon</label><input id="kontak" name="kontak" value="{{ old('kontak', $produkUmkm->kontak) }}" class="form-control @error('kontak') is-invalid @enderror" required>@error('kontak')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12"><label class="form-label fw-semibold" for="deskripsi">Deskripsi</label><textarea id="deskripsi" name="deskripsi" rows="5" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $produkUmkm->deskripsi) }}</textarea>@error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

                @if($produkUmkm->foto_produk)
                    <div class="col-12"><label class="form-label fw-semibold">Foto saat ini</label><div><img src="{{ route('pasar.photo', $produkUmkm) }}" alt="{{ $produkUmkm->nama_produk }}" class="h-52 w-full max-w-sm rounded-2xl object-cover"></div><div class="form-check mt-2"><input class="form-check-input" type="checkbox" value="1" name="hapus_foto" id="hapus_foto"><label class="form-check-label text-danger" for="hapus_foto">Hapus foto saat ini</label></div></div>
                @endif

                <div class="col-12"><label class="form-label fw-semibold" for="foto_produk">Ganti foto <span class="fw-normal text-muted">(opsional, maksimal 2 MB)</span></label><input id="foto_produk" type="file" name="foto_produk" accept="image/*" class="form-control @error('foto_produk') is-invalid @enderror">@error('foto_produk')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            </div>
            <div class="mt-5 flex flex-wrap gap-2"><button class="btn btn-village" type="submit"><i class="fa-solid fa-floppy-disk me-2"></i>Simpan Perubahan</button><a class="btn btn-light" href="{{ route('pasar.index') }}">Batal</a></div>
        </form>
    </div>
</x-app-layout>
