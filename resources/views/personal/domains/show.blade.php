@extends('layouts.personal')
@section('title','Личный кабинет')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">{{$domain->name}}</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="{{route('domains.create')}}" class="btn btn-primary">Добавить</a>
            </div>
        </div>
        </br>
        Дата добавления: {{$domain->created_at}}</br>
        Истекает: {{$domain->expire_at}}</br>
        Дата обновления: {{$domain->updated_at}}</br>
        <br> <br>
        {{ Form::open(['route' => ['domains.delete_old_whois',$domain->id]]) }}
        <div class="form-group">
            {{ Form::label('delete_old_whois', 'Удалить записи whois старше') }}
            {{ Form::select('delete_old_whois', \App\Facades\Domains\WhoisService::getTimeFrameOptions(),null,['class' => 'form-control']) }}
        </div>
        {{ Form::submit('Удалить', array('class' => 'btn btn-primary')) }}
        {{ Form::close() }}
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
    </main>
@endsection