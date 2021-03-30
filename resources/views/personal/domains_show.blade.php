@extends('layouts.personal')
@section('title','Личный кабинет')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Домены</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="{{route('domains.create')}}" class="btn btn-primary">Добавить</a>
            </div>
        </div>
        Домен: {{$domain->name}} </br>
        - {{$domain->created_at}}</br>
        - {{$domain->expire_at}}</br>
        - {{$domain->updated_at}}</br>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Дата проверки</th>
                <th scope="col">Результат</th>
            </tr>
            </thead>
            <tbody>
            @foreach($whois as $w)
                <tr>
                    <th scope="row">{{$loop->iteration}}</th>
                    <td>{{$w->created_at}}</td>
                    <td>{!! nl2br(e($w->text)) !!}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </main>
@endsection