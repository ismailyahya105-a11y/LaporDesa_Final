@props(['status'])
@php
    $classes = ['menunggu' => 'text-bg-warning', 'diproses' => 'text-bg-primary', 'selesai' => 'text-bg-success'];
@endphp
<span {{ $attributes->class(['badge rounded-pill', $classes[$status] ?? 'text-bg-secondary']) }}>
    {{ ucfirst($status) }}
</span>
