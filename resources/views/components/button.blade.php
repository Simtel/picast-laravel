@props(['route', 'method' => 'POST', 'class' => 'btn-primary', 'text', 'icon' => null, 'title' => null])

<form action="{{ $route }}" method="POST" class="pull-right">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif
    <button type="submit" 
            class="btn {{ $class }} btn-sm" 
            @if($title) title="{{ $title }}" @endif>
        @if($icon)
            <i class="fa-solid fa-{{ $icon }}"></i>
        @else
            {{ $text }}
        @endif
    </button>
</form>