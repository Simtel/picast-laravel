@extends('layouts.personal')
@section('title','Создание роли')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">Создание роли</h1>
    </div>

    @include('personal.roles._form', [
        'method' => 'POST',
        'action' => route('roles.store'),
        'cardTitle' => 'Новая роль',
        'role' => null,
        'checkedSections' => [],
        'submitLabel' => 'Создать роль',
    ])
@endsection
