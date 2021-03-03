@extends('layouts.app')
@section('title','Главная')
@section('content')
    <h1>Загрузка изображений</h1>
    <div class="row">
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    <div class="row">
        <div class="col-sm-12 my-15">
            <form id="fileupload" action="<?=URL::route('image_upload');?>" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="exampleInputEmail1">Файлы:</label>
                    <input type="file" class="form-control" name="upload_file[]">
                    <input type="file" class="form-control" name="upload_file[]">
                    <input type="file" class="form-control" name="upload_file[]">
                    <input type="file" class="form-control" name="upload_file[]">
                    <input type="file" class="form-control" name="upload_file[]">
                    <small id="emailHelp" class="form-text text-muted">Можно загрузить и один файл</small>
                </div>

                <button type="submit" class="btn btn-primary">Загрузить</button>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 mt-15">
            <div class="jumbotron">
                <h3>Внимание</h3>
                <ul>
                    <li>Все данные загружаемые в Picast.Ru одновременно копируются на несколько независимых серверов.
                    </li>
                    <li>Пользователи Picast.ru получают картинки на скорости более 10 Гбит/с.</li>
                    <li>Нет рекламы!</li>
                    <li>Максимальный размер файла <strong>5 MB</strong>.</li>
                    <li>Только изображения (<strong>JPG, GIF, PNG</strong>).</li>
                    <li>Запрещается размещение материалов, нарушающих законодательство Российской Федерации.</li>
                    <li>Администрация сервиса оставляет за собой право без уведомления удалять любые изображения,
                        нарушающие
                        законодательство.
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection