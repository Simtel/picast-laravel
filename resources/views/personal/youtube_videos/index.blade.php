@extends('layouts.personal')

@section('title','Личный кабинет')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Видео с YouTube</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="{{ route('youtube.create') }}" class="btn btn-primary">Добавить</a>
            </div>
        </div>

        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Ролик</th>
                <th scope="col">Добавлено</th>
                <th scope="col">Скачено</th>
                <th scope="col">Ссылка</th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($videos as $video)
                <tr>
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td><a href="{{ $video->url }}"
                           target="_blank">{{ $video->title != '' ? $video->title : $video->url }}</a></td>
                    </td>
                    <td>{{ $video->created_at }}</td>
                    <td>{{ $video->is_download ? 'Да' : 'Нет'}}</td>
                    <td>
                        @if($video->is_download)
                            <a href="{{ $video->getFileUrl() }}" target="_blank">Скачать ({{$video->getSize()}})</a>
                        @endif
                    </td>
                    <td>
                        {{-- Компонент кнопки удаления --}}
                        <x-button :route="route('youtube.destroy', ['video' => $video->id])" method="DELETE"
                                  class="btn-warning" text="Удалить"/>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </main>
@endsection