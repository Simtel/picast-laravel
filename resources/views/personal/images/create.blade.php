@extends('layouts.personal')
@section('title','Личный кабинет')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
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
    </main>
@endsection