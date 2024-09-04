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
        {{ Html::form('POST',route('invite.user'))->open() }}

        <div class="form-group">
            {{ Html::label('name', 'Имя') }}
            {{ Html::text('name') }}
        </div>
        <div class="form-group">
            {{ Html::label('email', 'E-mail') }}
            {{ Html::text('email') }}
        </div>

        {{ Html::submit('Пригласить пользователя', ['class' => 'btn btn-primary']) }}

        {{ Html::form()->close() }}
    </main>
@endsection