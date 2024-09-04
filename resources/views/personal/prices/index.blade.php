@extends('layouts.personal')
@section('title','Личный кабинет')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Мониторинг цен</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="{{route('prices.product.create')}}" class="btn btn-primary">Добавить товар</a>
            </div>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Название</th>
                <th scope="col">Дата добавления</th>
                @foreach($marketplaces as $place)
                    <th scope="col">Ссылка на {{$place->name}}</th>
                @endforeach
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($products as $product)
                <tr>
                    <th scope="row">{{$loop->iteration}}</th>
                    <td><a href="{{route('prices.show',['product' => $product])}}">{{$product->name}}</a></td>
                    <td>{{$product->created_at}}</td>
                    @foreach($product->urls as $url)
                        <td>
                            @if($url->url)
                                <a href="{{$url->url}}" target="_blank">Ссылка</a>
                            @endif
                        </td>
                    @endforeach

                    <td>
                        <a href="{{route('prices.product.edit',['product' => $product])}}"
                           class="btn  btn-sm glyphicon">Редактировать</a>
                    </td>
                    <td>
                        {{ Html::form(['url' => route('prices.product.destroy',['product' => $product->id]), 'class' => 'pull-right']) }}
                        {{ Html::hidden('_method', 'DELETE') }}
                        {{ Html::submit('Удалить', ['class' => 'btn btn-warning btn-sm']) }}
                        {{ Html::close() }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </main>
@endsection