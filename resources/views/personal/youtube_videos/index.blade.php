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
                <th scope="col">Cтатус</th>
                <th scope="col"></th>
                <th scope="col">Форматы</th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($videos as $video)
                <tr>
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td><a href="{{ $video->url }}"
                           target="_blank">{{ $video->title != '' ? $video->title : $video->url }}</a>
                    </td>
                    <td>{{ $video->created_at }}</td>
                    <td>{{ $video->status->title}}</td>
                    <td>
                        @foreach($video->files as $file)
                            <a href="{{ $file->getFileUrl() }}" target="_blank">Скачать ({{$file->getSize()}})</a>
                        @endforeach
                    </td>
                    <td>
                        @if ($video->hasFormats())
                            {{ Html::form('POST',route('youtube.queue-download',['video' => $video]))->open()}}
                        @endif

                        <select name="video_formats" id="video_formats" class="form-control">
                            <option>---</option>
                            @foreach($video->formats as $format)
                                <option value="{{ $format->getId() }}">{{ $format->format_ext }} {{ $format->resolution }}
                                    ({{$format->vcodec}})
                                </option>
                            @endforeach
                        </select>
                            @if ($video->hasFormats())
                                {{ Html::submit('Скачать выбранный формат')->class('btn')}}
                                {{ Html::form()->close() }}
                        @endif
                    </td>
                    <td>
                        {{-- Компонент кнопки уобновления форматов--}}
                        <x-button :route="route('youtube.refresh_formats', ['video' => $video->id])" method="POST"
                                  class="btn-success" text="Обновить форматы"/>
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