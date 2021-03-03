@extends('layouts.app')
@section('title','Последние изображения')
@section('content')
    <div class="row">
        @foreach($images as $image)
            <span id="imagewall-container">
                <a href="{{URL::route('show_image', $image->id)}}" target="_blank" rel="lightbox[p]">
                    <img src="{{$image->getThumbFullPath()}}" class="img-thumbnail">
                </a>
            </span>
        @endforeach

    </div>
    {{ $images->links('vendor.pagination.bootstrap-4') }}
@endsection