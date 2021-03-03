@extends('layouts.personal')
@section('title','Личный кабинет')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Домены</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                    Добавить
                </button>
            </div>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Адрес</th>
                <th scope="col">Дата добавления</th>
                <th scope="col">Истекает</th>
                <th scope="col">Последние обновление</th>
                <th scope="col">История whois</th>
            </tr>
            </thead>
            <tbody>
            @foreach($domains as $domain)
                <tr>
                    <th scope="row">{{$loop->iteration}}</th>
                    <td>{{$domain->name}}</td>
                    <td>{{$domain->created_at}}</td>
                    <td>{{$domain->expire_at}}</td>
                    <td>{{$domain->updated_at}}</td>
                    <td><a href="#">Whois</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </main>
@endsection