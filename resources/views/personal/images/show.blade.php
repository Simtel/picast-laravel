@extends('layouts.personal')
@section('title','Личный кабинет')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Изображения {{$image->filename}}</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="{{route('images.create')}}" class="btn btn-primary">Добавить</a>
            </div>
        </div>

        <div class="container text-center">
            <div class="row align-items-start">
                <div class="col">
                    <img src="/images/{{$image->filename}}" style="max-width: 100%;" alt="">
                </div>
                <div class="col">
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Путь до файла:</label>
                        <input type="text" class="form-control" id="exampleFormControlInput1"
                               value="{{url('')}}/images/{{$image->filename}}">
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Путь до страницы:</label>
                        <input type="text" class="form-control" id="exampleFormControlInput1"
                               value="{{route('images.show',[$image])}}">
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col"></th>

                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th scope="row">Размер:</th>
                            <td>{{$size}}</td>
                        </tr>
                        <tr>
                            <th scope="row">Тип:</th>
                            <td>{{$type}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </main>
@endsection
