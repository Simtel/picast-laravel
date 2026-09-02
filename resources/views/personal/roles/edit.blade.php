@extends('layouts.personal')
@section('title','Редактирование роли')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">Редактирование роли «{{ $role->name }}»</h1>
    </div>

    @php
        $checkedSections = [];
        foreach ($sections as $key => $section) {
            $checkedSections[$key] = $role->hasPermissionTo($section['permission']);
        }
    @endphp

    @include('personal.roles._form', [
        'method' => 'PUT',
        'action' => route('roles.update', $role),
        'cardTitle' => 'Разделы и доступ',
        'role' => $role,
        'checkedSections' => $checkedSections,
        'submitLabel' => 'Сохранить изменения',
    ])
@endsection
