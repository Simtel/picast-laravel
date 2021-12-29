@extends('layouts.personal')
@section('title','Личный кабинет')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
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
        {{ Form::open(['route' => 'invite.user']) }}

        <div class="form-group">
            {{ Form::label('name', 'Имя') }}
            {{ Form::text('name', Form::old('name'), ['class' => 'form-control']) }}
        </div>
        <div class="form-group">
            {{ Form::label('email', 'E-mail') }}
            {{ Form::text('email', Form::old('email'), ['class' => 'form-control']) }}
        </div>

        {{ Form::submit('Пригласить пользователя', ['class' => 'btn btn-primary']) }}

        {{ Form::close() }}
    </main>
@endsection