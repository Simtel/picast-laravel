@extends('layouts.personal')
@section('title','Личный кабинет')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Изображения</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="{{route('images.create')}}" class="btn btn-primary">Добавить</a>
            </div>
        </div>

        @foreach($images as $image)
            <a href="{{route('images.show',[$image])}}"><img src="{{$image->getPath()}}" style="width:250px;"></a>
        @endforeach
    </main>
@endsection
