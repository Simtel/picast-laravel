@extends('layouts.personal')
@section('title','Редактирование пользователя')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Редактировать профиль</h1>

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
        {{ Html::form('POST',route('user.update',$user))->open()}}

        <div class="form-group">
            {{ Html::label('Имя', 'name') }}
            {{ Html::text('name', $user->name)->class('form-control') }}
        </div>
        <div class="form-group">
            {{ Html::label('E-mail', 'email') }}
            {{ Html::text('email', $user->email)->class('form-control') }}
        </div>

        <div class="form-group">
            {{ Html::label('Роли', 'roles[]') }}
            @foreach($roles as $role)
                {{ Html::checkbox('roles[]', $role->name, $user->hasRole($role->name)) }} {{$role->name}}
            @endforeach
        </div>

        {{ Html::submit('Сохранить')->class('btn btn-primary') }}

        {{ Html::form()->close() }}
    </main>

@endsection