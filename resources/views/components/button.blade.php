@props(['route', 'method' => 'POST', 'class' => 'btn-primary', 'text'])

<form action="{{ $route }}" method="POST" class="pull-right">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif
    <button type="submit" class="btn {{ $class }} btn-sm">{{ $text }}</button>
</form>