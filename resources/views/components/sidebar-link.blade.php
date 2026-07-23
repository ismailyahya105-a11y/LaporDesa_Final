@props(['href'=>'#','icon'=>'fa-circle','active'=>false])
<a href="{{ $href }}" {{ $attributes->class(['flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition','bg-white/15 text-white shadow-sm'=>$active,'text-emerald-100 hover:bg-white/10 hover:text-white'=>!$active]) }}><i class="fa-solid {{ $icon }} w-5 text-center"></i><span>{{ $slot }}</span></a>
