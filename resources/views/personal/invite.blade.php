@extends('layouts.personal')
@section('title','Личный кабинет')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">Пригласить участника</h1>
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

    {{ Html::form('POST',route('invite.user'))->open() }}

    <div class="form-group">
        {{ Html::label('Имя', 'name') }}
        {{ Html::text('name')->class('form-control')}}
    </div>
    <div class="form-group">
        {{ Html::label('E-mail', 'email') }}
        {{ Html::text('email')->class('form-control')}}
    </div>

    {{ Html::submit('Пригласить пользователя')->class('btn btn-primary') }}

    {{ Html::form()->close() }}
@endsection