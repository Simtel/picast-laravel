@extends('layouts.personal')
@section('title','Турниры Dancemanager')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">Список турниров</h1>
    </div>

    <table class="table table-hover">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Название</th>
            <th scope="col">Дата</th>
            <th scope="col">Ссылка</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($tournaments as $tournament)
            <tr>
                <th scope="row">{{ $loop->iteration }}</th>
                <td>{{ $tournament['title'] }}</td>
                <td>{{ $tournament['date'] }}</td>
                <td><a href="{{ $tournament['link'] }}" target="_blank">Подробнее</a></td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">Турниры не найдены.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@endsection
