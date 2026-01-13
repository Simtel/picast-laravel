@extends('layouts.personal')
@section('title','Личный кабинет')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">Изображения</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{route('domains.create')}}" class="btn btn-primary">Добавить</a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{ Html::form('POST', route('images.store'))->acceptsFiles()->open()}}

    <div class="form-group">
        {{ Html::label('Название', 'name') }}
        {{ Html::text('name')->class('form-control')}}
    </div>
    <div class="form-group">
        {{ Html::label('Изображение', 'image') }}
        {{ Html::file('image')->class('form-control')  }}
    </div>

    {{ Html::submit('Сохранить')->class('btn btn-primary')}}

    {{ Html::form()->close() }}
@endsection