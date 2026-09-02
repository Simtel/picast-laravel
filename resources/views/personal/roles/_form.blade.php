@if (session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2">
        <i class="fa fa-circle-check"></i>
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-start gap-2">
        <i class="fa fa-circle-exclamation mt-1"></i>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{ Html::form($method, $action)->open() }}
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="fa fa-user-shield text-primary"></i>
        <span>{{ $cardTitle }}</span>
    </div>
    <div class="card-body">
        <div class="mb-3">
            {{ Html::label('Название роли', 'name')->class('form-label fw-semibold') }}
            {{ Html::text('name', $role?->name ?? old('name'))->class('form-control')->required() }}
            <small class="form-text text-muted">Используйте уникальное имя, например <code>manager</code>.</small>
        </div>

        <hr>

        <div class="mb-3">
            <span class="form-label fw-semibold d-block">Доступные разделы</span>
            <small class="form-text text-muted d-block mb-2">Отметьте разделы, доступ к которым получит эта роль.</small>
            @foreach($sections as $key => $section)
                <div class="form-check mb-1">
                    {{ Html::checkbox('sections[]', $checkedSections[$key] ?? false, $key)
                        ->class('form-check-input')
                        ->id('section-' . $key) }}
                    {{ Html::label($section['label'] . ' (' . $section['permission'] . ')', 'section-' . $key)->class('form-check-label') }}
                </div>
            @endforeach
        </div>
    </div>
    <div class="card-footer bg-transparent d-flex justify-content-end gap-2">
        <a href="{{route('roles.index')}}" class="btn btn-light">Отмена</a>
        {{ Html::submit($submitLabel)->class('btn btn-primary') }}
    </div>
</div>
{{ Html::form()->close() }}
