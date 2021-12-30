@extends('layouts.personal')
@section('title','Личный кабинет')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Мониторинг цен</h1>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{ Form::open(['url' => route('prices.product.update',['product' => $product]),'files' => true]) }}

        <div class="form-group">
            {{ Form::label('name', 'Название') }}
            {{ Form::text('name', $product->name, ['class' => 'form-control']) }}
        </div>

        @foreach($market_places as $place)
            <div class="form-group">
                {{ Form::label('urls['.$place->id.']', 'Адрес на '.$place->name) }}
                {{ Form::text(
                            'urls['.$place->id.']',
                            $product->urls->firstWhere('marketplace_id',$place->id)->url,
                            ['class' => 'form-control']
                            )
                }}
            </div>
        @endforeach
        {{ Form::submit('Сохранить', ['class' => 'btn btn-primary']) }}

        {{ Form::close() }}
    </main>
@endsection