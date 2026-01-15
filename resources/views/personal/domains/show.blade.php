@extends('layouts.personal')
@section('title','Личный кабинет')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">Домены</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{route('domains.create')}}" class="btn btn-primary">Добавить</a>
        </div>
    </div>
        <table class="table">
            <tr>
                <td>Дата добавления:</td>
                <td> {{$domain->created_at}}</td>
            </tr>
            <tr>
                <td>Истекает:</td>
                <td>{{$domain->expire_at}}</td>
            </tr>
            <tr>
                <td>Дата обновления:</td>
                <td> {{$domain->updated_at}}</td>
            </tr>
        </table>
        <br> <br>
        {{ Html::form('POST',route('domains.delete_old_whois',$domain->id))->open()}}
        <div class="form-group">
            {{ Html::label('Удалить записи whois старше','delete_old_whois') }}
            {{ Html::select('delete_old_whois', \App\Context\Domains\Infrastructure\Facades\WhoisService::getTimeFrameOptions())->class('form-control') }}
        </div>
        {{ Html::submit('Удалить')->class('btn btn-danger') }}
        {{ Html::form()->close() }}
        <br> <br>
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
                    <th scope="row">{{ ($whois ->currentpage()-1) * $whois ->perpage() + $loop->index + 1 }}</th>
                    <td>{{$w->created_at}}</td>
                    <td>{!! nl2br(e($w->text)) !!}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $whois->links() }}

@endsection