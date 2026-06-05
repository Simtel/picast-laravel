<!DOCTYPE html>
<html lang="ru" class="landing-page">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Picast — Управление доменами, видео и турнирами</title>
    <meta name="description" content="Picast — платформа для WHOIS-мониторинга доменов, управления YouTube-видео, отслеживания турниров и AI-чата.">

    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    @vite(['resources/assets/sass/app.scss', 'resources/assets/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body class="landing-page">

{{-- Hero Section --}}
<section class="hero-section">
    <div class="hero-container">
        {{-- Left: Info --}}
        <div class="hero-info">
            <h1 class="hero-brand">Picast</h1>
            <p class="hero-tagline">
                Единая платформа для управления доменами, YouTube-видео, турнирами и&nbsp;AI-коммуникацией.
                Всё необходимое — в одном месте.
            </p>

            <ul class="hero-features-list">
                <li>
                    <i class="fa-solid fa-globe"></i>
                    WHOIS-мониторинг и отслеживание доменов
                </li>
                <li>
                    <i class="fa-solid fa-cloud-arrow-down"></i>
                    Загрузка и управление YouTube-видео
                </li>
                <li>
                    <i class="fa-solid fa-trophy"></i>
                    Отслеживание танцевальных турниров
                </li>
                <li>
                    <i class="fa-solid fa-robot"></i>
                    AI-ассистент ChadGPT
                </li>
            </ul>
        </div>

        {{-- Right: Login Form --}}
        <div class="hero-login">
            <div class="login-card">
                <h2>Вход</h2>
                <p class="login-subtitle">Войдите в личный кабинет</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    {{ csrf_field() }}

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="Введите email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Введите пароль"
                            required
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="remember"
                            name="remember"
                        >
                        <label class="form-check-label" for="remember">Запомнить меня</label>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fa-solid fa-right-to-bracket me-2"></i>Войти
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- Features Section --}}
<section class="features-section">
    <div class="features-container">
        <div class="features-header">
            <h2>Возможности проекта</h2>
            <p>Picast объединяет инструменты для управления доменами, мультимедиа, турнирами и AI-коммуникацией</p>
        </div>

        <div class="features-grid">
            {{-- 1. WHOIS --}}
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-globe"></i>
                </div>
                <h3>WHOIS-мониторинг доменов</h3>
                <p>
                    Автоматическое отслеживание WHOIS-данных доменов, ежедневные проверки
                    и уведомления об истечении срока регистрации.
                </p>
            </div>

            {{-- 2. YouTube --}}
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-brands fa-youtube"></i>
                </div>
                <h3>YouTube-видео</h3>
                <p>
                    Загрузка видео с YouTube, управление форматами и очередью скачивания.
                    Поддержка различных качеств и форматов.
                </p>
            </div>

            {{-- 3. Tournaments --}}
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <h3>Турниры</h3>
                <p>
                    Отслеживание танцевальных турниров и групп. Автоматическая загрузка
                    данных из внешних источников.
                </p>
            </div>

            {{-- 4. ChadGPT --}}
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <h3>ChadGPT AI-чат</h3>
                <p>
                    Встроенный AI-ассистент для помощи, консультаций и автоматизации задач.
                    История диалогов и статистика использования.
                </p>
            </div>

            {{-- 5. Users --}}
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <h3>Управление пользователями</h3>
                <p>
                    Гибкая ролевая модель с правами доступа, система приглашений,
                    управление профилями и настройками.
                </p>
            </div>

            {{-- 6. Images --}}
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-image"></i>
                </div>
                <h3>Галерея изображений</h3>
                <p>
                    Загрузка, хранение и управление изображениями. Автоматическая
                    генерация миниатюр и облачное хранение.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Footer --}}
<footer class="landing-footer">
    &copy; {{ date('Y') }} Picast. Все права защищены.
</footer>

</body>
</html>
