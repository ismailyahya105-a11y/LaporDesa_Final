<x-app-layout>
    <x-slot name="header"><h1 class="text-2xl font-extrabold">Peta Desa</h1><p class="text-sm text-slate-500">Fasilitas umum, wisata, infrastruktur, dan posko bencana.</p></x-slot>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <div id="villageMap" class="h-[70vh] rounded-2xl shadow-sm ring-1 ring-slate-200"></div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>const points=@json($lokasi);const center=points.length?[points[0].latitude,points[0].longitude]:[-6.2,106.816666];const map=L.map('villageMap').setView(center,13);L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap'}).addTo(map);points.forEach(p=>L.marker([p.latitude,p.longitude]).addTo(map).bindPopup(`<b>${p.nama}</b><br>${p.kategori}<br>${p.deskripsi??''}`));</script>
</x-app-layout>
