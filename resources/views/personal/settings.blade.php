@extends('layouts.personal')
@section('title','Личный кабинет')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">Настройки пользователя</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2">
            <i class="fa fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger d-flex align-items-start gap-2">
            <i class="fa fa-circle-exclamation mt-1"></i>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        {{-- Левая колонка --}}
        <div class="col-md-6 d-flex flex-column gap-4">
            {{-- Личная информация --}}
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-user text-primary"></i>
                    <span>Личная информация</span>
                </div>
                {{ Html::form('POST', route('settings.profile'))->open() }}
                <div class="card-body">
                    <div class="mb-3">
                        {{ Html::label('Имя', 'name')->class('form-label fw-semibold') }}
                        {{ Html::text('name', $user->name)->class('form-control') }}
                    </div>
                    <div class="mb-3">
                        {{ Html::label('E-mail', 'email')->class('form-label fw-semibold') }}
                        {{ Html::email('email', $user->email)->class('form-control') }}
                    </div>
                    <div class="mb-3">
                        {{ Html::label('Дата рождения', 'birth_date')->class('form-label fw-semibold') }}
                        {{ Html::date('birth_date', $user->birth_date?->format('Y-m-d'))->class('form-control') }}
                        <small class="form-text text-muted">Укажите вашу дату рождения (необязательно)</small>
                    </div>
                </div>
                <div class="card-footer bg-transparent d-flex justify-content-end">
                    {{ Html::submit('Обновить профиль')->class('btn btn-primary') }}
                </div>
                {{ Html::form()->close() }}
            </div>

            {{-- Изменить пароль --}}
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-lock text-primary"></i>
                    <span>Изменить пароль</span>
                </div>
                {{ Html::form('POST', route('settings.password'))->open() }}
                <div class="card-body">
                    <div class="mb-3">
                        {{ Html::label('Новый пароль', 'password')->class('form-label fw-semibold') }}
                        {{ Html::password('password')->class('form-control') }}
                    </div>
                </div>
                <div class="card-footer bg-transparent d-flex justify-content-end">
                    {{ Html::submit('Обновить пароль')->class('btn btn-primary') }}
                </div>
                {{ Html::form()->close() }}
            </div>
        </div>

        {{-- Правая колонка --}}
        <div class="col-md-6">
            {{-- API Токены --}}
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-key text-primary"></i>
                    <span>API Токены</span>
                </div>
                <div class="card-body">
                    @if ($tokens->isNotEmpty())
                        <div class="table-responsive mb-3">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Имя</th>
                                        <th>Создан</th>
                                        <th>Использован</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tokens as $tok)
                                        <tr>
                                            <td>{{ $tok->name }}</td>
                                            <td>{{ $tok->created_at?->format('d.m.Y H:i') }}</td>
                                            <td>{{ $tok->last_used_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                            <td class="text-end">
                                                {{ Html::form()->route('settings.token.delete', ['id' => $tok->id])->attribute('method', 'DELETE')->open() }}
                                                    {{ Html::submit('<i class="fa fa-trash"></i>')->class('btn btn-sm btn-outline-danger') }}
                                                {{ Html::form()->close() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Нет активных токенов.</p>
                    @endif
                    {{ Html::form()->route('settings.token')->open() }}
                        {{ Html::submit('Создать новый токен')->class('btn btn-primary') }}
                    {{ Html::form()->close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
