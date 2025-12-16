@extends('layouts.personal')

@section('title','Личный кабинет')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Домены</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="{{ route('domains.create') }}" class="btn btn-primary">Добавить</a>
            </div>
        </div>

        {{-- Поиск и фильтры --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="GET" action="{{ route('domains.index') }}" class="d-flex gap-2">
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Поиск по домену..." 
                           value="{{ $search ?? '' }}">
                    <input type="hidden" name="sort" value="{{ $currentSort ?? 'name' }}">
                    <input type="hidden" name="direction" value="{{ $currentDirection ?? 'asc' }}">
                    <button type="submit" class="btn btn-outline-primary">Найти</button>
                    @if($search ?? false)
                        <a href="{{ route('domains.index', ['sort' => $currentSort, 'direction' => $currentDirection]) }}" 
                           class="btn btn-outline-secondary">Сбросить</a>
                    @endif
                </form>
            </div>
        </div>

        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">
                    <a href="{{ route('domains.index', [
                        'sort' => 'name',
                        'direction' => ($currentSort ?? 'name') === 'name' && ($currentDirection ?? 'asc') === 'asc' ? 'desc' : 'asc',
                        'search' => $search ?? null
                    ]) }}" class="text-decoration-none text-dark">
                        Адрес
                        @if(($currentSort ?? 'name') === 'name')
                            <i class="bi bi-arrow-{{ ($currentDirection ?? 'asc') === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </a>
                </th>
                <th scope="col">
                    <a href="{{ route('domains.index', [
                        'sort' => 'created_at',
                        'direction' => ($currentSort ?? 'name') === 'created_at' && ($currentDirection ?? 'asc') === 'asc' ? 'desc' : 'asc',
                        'search' => $search ?? null
                    ]) }}" class="text-decoration-none text-dark">
                        Добавлено
                        @if(($currentSort ?? 'name') === 'created_at')
                            <i class="bi bi-arrow-{{ ($currentDirection ?? 'asc') === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </a>
                </th>
                <th scope="col">
                    <a href="{{ route('domains.index', [
                        'sort' => 'expire_at',
                        'direction' => ($currentSort ?? 'name') === 'expire_at' && ($currentDirection ?? 'asc') === 'asc' ? 'desc' : 'asc',
                        'search' => $search ?? null
                    ]) }}" class="text-decoration-none text-dark">
                        Истекает
                        @if(($currentSort ?? 'name') === 'expire_at')
                            <i class="bi bi-arrow-{{ ($currentDirection ?? 'asc') === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </a>
                </th>
                <th scope="col">
                    <a href="{{ route('domains.index', [
                        'sort' => 'updated_at',
                        'direction' => ($currentSort ?? 'name') === 'updated_at' && ($currentDirection ?? 'asc') === 'asc' ? 'desc' : 'asc',
                        'search' => $search ?? null
                    ]) }}" class="text-decoration-none text-dark">
                        Обновлено
                        @if(($currentSort ?? 'name') === 'updated_at')
                            <i class="bi bi-arrow-{{ ($currentDirection ?? 'asc') === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </a>
                </th>
                <th scope="col">История whois</th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            @forelse($domains as $domain)
                <tr>
                    <th scope="row">{{ $loop->iteration + ($domains->currentPage() - 1) * $domains->perPage() }}</th>
                    <td>{{ $domain->name }}</td>
                    <td>{{ $domain->getCreatedAt()?->format('d.m.Y H:i') }}</td>
                    <td>{{ $domain->getExpireAt()?->format('d.m.Y') }}</td>
                    <td>{{ $domain->getUpdatedAt()?->format('d.m.Y H:i') }}</td>
                    <td><a href="{{ route('domains.show', ['domain' => $domain->id]) }}">Whois</a></td>
                    <td>
                        {{-- Компонент кнопки удаления --}}
                        <x-button :route="route('domains.destroy', ['domain' => $domain->id])" method="DELETE" class="btn-warning" text="Удалить" />
                    </td>
                    <td>
                        {{-- Компонент кнопки обновления --}}
                        <x-button :route="route('domains.update', ['domain' => $domain->id])" method="PUT" class="btn-success" text="Обновить" />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Домены не найдены</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{-- Пагинация --}}
        @if($domains->hasPages())
            <div class="d-flex justify-content-center">
                {{ $domains->links() }}
            </div>
        @endif
    </main>
@endsection