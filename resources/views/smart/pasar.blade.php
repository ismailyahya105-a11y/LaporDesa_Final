<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold">Pasar Desa</h1>
                <p class="text-sm text-slate-500">Dukung produk UMKM dan peluang kerja lokal.</p>
            </div>
            @unless(auth()->user()->isAdmin())
                <button type="button" class="btn btn-village" data-bs-toggle="modal" data-bs-target="#tambahUmkmModal">
                    <i class="fa-solid fa-plus me-2"></i>Tambah UMKM
                </button>
            @endunless
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <h2 class="mb-4 font-extrabold">Produk UMKM</h2>
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @forelse($produk as $item)
            <article class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="grid h-40 place-items-center bg-emerald-50">
                    @if($item->foto_produk)
                        <img class="h-full w-full object-cover" src="{{ route('pasar.photo', $item) }}" alt="{{ $item->nama_produk }}">
                    @else
                        <i class="fa-solid fa-store text-4xl text-emerald-300"></i>
                    @endif
                </div>
                <div class="p-4">
                    <small class="font-bold text-emerald-600">{{ $item->kategori }}</small>
                    <h3 class="font-extrabold">{{ $item->nama_produk }}</h3>
                    <p class="mb-2 text-xs text-slate-500">{{ $item->nama_usaha }} · {{ $item->pemilik }}</p>
                    @if($item->deskripsi)<p class="mb-3 text-sm text-slate-600">{{ Str::limit($item->deskripsi, 100) }}</p>@endif
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <a href="tel:{{ $item->kontak }}" class="text-sm font-bold text-blue-600">Hubungi penjual</a>
                        @if($item->user_id === auth()->id())
                            <a href="{{ route('pasar.edit', $item) }}" class="rounded-lg bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100"><i class="fa-solid fa-pen me-1"></i>Edit</a>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl bg-white p-8 text-center text-slate-500 shadow-sm ring-1 ring-slate-200">
                <i class="fa-solid fa-store mb-3 text-3xl text-emerald-300"></i>
                <p class="mb-0">Belum ada produk UMKM.</p>
            </div>
        @endforelse
    </div>
    <div class="mt-5">{{ $produk->links() }}</div>

    <h2 class="mb-4 mt-8 font-extrabold">Lowongan Kerja Lokal</h2>
    <div class="grid gap-4 md:grid-cols-2">
        @forelse($lowongan as $item)
            <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <h3 class="font-bold">{{ $item->judul }}</h3>
                <p class="text-sm text-slate-600">{{ $item->deskripsi }}</p>
                <small>{{ $item->tanggal->format('d M Y') }} · {{ $item->kontak }}</small>
            </article>
        @empty
            <p class="text-slate-500">Belum ada lowongan kerja aktif.</p>
        @endforelse
    </div>

    @unless(auth()->user()->isAdmin())
        <div class="modal fade" id="tambahUmkmModal" tabindex="-1" aria-labelledby="tambahUmkmLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow">
                    <form method="POST" action="{{ route('pasar.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header border-0 px-4 pt-4">
                            <div><h2 class="modal-title fs-5 fw-bold" id="tambahUmkmLabel">Tambah UMKM atau Pedagang</h2><p class="mb-0 small text-muted">Daftarkan usaha dan produk yang ingin dipasarkan.</p></div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body px-4">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label fw-semibold" for="nama_usaha">Nama usaha</label><input id="nama_usaha" name="nama_usaha" value="{{ old('nama_usaha') }}" class="form-control @error('nama_usaha') is-invalid @enderror" required>@error('nama_usaha')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="col-md-6"><label class="form-label fw-semibold" for="kategori">Kategori</label><input id="kategori" name="kategori" value="{{ old('kategori') }}" class="form-control @error('kategori') is-invalid @enderror" placeholder="Contoh: Makanan" required>@error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="col-md-6"><label class="form-label fw-semibold" for="nama_produk">Nama produk</label><input id="nama_produk" name="nama_produk" value="{{ old('nama_produk') }}" class="form-control @error('nama_produk') is-invalid @enderror" required>@error('nama_produk')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="col-md-6"><label class="form-label fw-semibold" for="kontak">Nomor WhatsApp/telepon</label><input id="kontak" name="kontak" value="{{ old('kontak') }}" class="form-control @error('kontak') is-invalid @enderror" required>@error('kontak')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="col-12"><label class="form-label fw-semibold" for="deskripsi">Deskripsi</label><textarea id="deskripsi" name="deskripsi" rows="3" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi') }}</textarea>@error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="col-12"><label class="form-label fw-semibold" for="foto_produk">Foto produk <span class="text-muted fw-normal">(opsional, maksimal 2 MB)</span></label><input id="foto_produk" type="file" name="foto_produk" accept="image/*" class="form-control @error('foto_produk') is-invalid @enderror">@error('foto_produk')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 px-4 pb-4"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-village" type="submit"><i class="fa-solid fa-floppy-disk me-2"></i>Simpan UMKM</button></div>
                    </form>
                </div>
            </div>
        </div>
        @if($errors->any())
            @push('scripts')<script>document.addEventListener('DOMContentLoaded',()=>bootstrap.Modal.getOrCreateInstance(document.getElementById('tambahUmkmModal')).show());</script>@endpush
        @endif
    @endunless
</x-app-layout>
