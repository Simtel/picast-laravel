<!DOCTYPE html>
<html lang="ru" class="landing-page">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>A&amp;S Tech — платформа для доменов, медиа, турниров и AI</title>
    <meta name="description" content="A&amp;S Tech — технологическая платформа для WHOIS-мониторинга доменов, управления YouTube-видео, отслеживания турниров, AI-чата и набора инструментов разработчика.">

    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/assets/sass/app.scss', 'resources/assets/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body class="landing-page">

<div class="bg-layer" aria-hidden="true"></div>

{{-- Top Navigation --}}
<header class="site-nav">
    <div class="nav-inner">
        <a href="#top" class="brand">
            <span class="brand-mark">A<span>&amp;</span>S</span>
            <span class="brand-name">Tech</span>
        </a>

        <nav class="nav-links" aria-label="Основная навигация">
            <a href="#features">Возможности</a>
            <a href="#tools">Инструменты</a>
            <a href="#platform">Платформа</a>
        </nav>

        <a href="#login" class="nav-cta">
            <i class="fa-solid fa-right-to-bracket"></i> Войти
        </a>
    </div>
</header>

<main id="top">

    {{-- Hero Section --}}
    <section class="hero-section">
        <div class="hero-container">
            {{-- Left: Info --}}
            <div class="hero-info">
                <span class="hero-badge">
                    <span class="dot"></span> Технологическая платформа
                </span>

                <h1 class="hero-title">
                    Одна платформа для<br>
                    <span class="grad">доменов, медиа и AI</span>
                </h1>

                <p class="hero-tagline">
                    A&amp;S Tech объединяет WHOIS-мониторинг, управление YouTube-видео,
                    турнирную аналитику, AI-ассистента и инструменты разработчика
                    в единой защищённой среде.
                </p>

                <div class="hero-actions">
                    <a href="#features" class="btn-primary">
                        Изучить возможности <i class="fa-solid fa-arrow-down"></i>
                    </a>
                    <a href="#platform" class="btn-ghost">
                        <i class="fa-solid fa-code"></i> API и стек
                    </a>
                </div>

                <ul class="hero-features-list">
                    <li><i class="fa-solid fa-globe"></i> WHOIS-мониторинг доменов</li>
                    <li><i class="fa-solid fa-cloud-arrow-down"></i> Загрузка YouTube-видео</li>
                    <li><i class="fa-solid fa-trophy"></i> Танцевальные турниры</li>
                    <li><i class="fa-solid fa-robot"></i> AI-ассистент ChadGPT</li>
                </ul>
            </div>

            {{-- Right: Login Form --}}
            <div class="hero-login" id="login">
                <div class="login-card">
                    <div class="login-glow" aria-hidden="true"></div>

                    <h2>Вход в систему</h2>
                    <p class="login-subtitle">Личный кабинет A&amp;S Tech</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="field">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-envelope"></i>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="you@example.com"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                >
                            </div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="password" class="form-label">Пароль</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-lock"></i>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Введите пароль"
                                    required
                                >
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                id="remember"
                                name="remember"
                            >
                            <label class="form-check-label" for="remember">Запомнить меня</label>
                        </div>

                        <button type="submit" class="btn-login">
                            <i class="fa-solid fa-right-to-bracket"></i> Войти
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="stats-band">
            <div class="stat">
                <span class="stat-value">8</span>
                <span class="stat-label">функциональных модулей</span>
            </div>
            <div class="stat">
                <span class="stat-value">7</span>
                <span class="stat-label">онлайн-инструментов</span>
            </div>
            <div class="stat">
                <span class="stat-value">API</span>
                <span class="stat-label">v1 с токен-доступом</span>
            </div>
            <div class="stat">
                <span class="stat-value">24/7</span>
                <span class="stat-label">автоматизация задач</span>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="features-section" id="features">
        <div class="features-container">
            <div class="section-header">
                <span class="section-kicker">Возможности</span>
                <h2>Всё необходимое в одном месте</h2>
                <p>A&amp;S Tech объединяет инструменты для управления доменами, медиа, турнирами, AI и разработкой</p>
            </div>

            <div class="features-grid">
                {{-- 1. Domains --}}
                <article class="feature-card" style="--accent:#22d3ee">
                    <div class="feature-icon"><i class="fa-solid fa-globe"></i></div>
                    <h3>Домены и WHOIS</h3>
                    <p>
                        Автоматический мониторинг WHOIS-данных, ежедневные проверки
                        и уведомления об истечении срока регистрации.
                    </p>
                </article>

                {{-- 2. YouTube --}}
                <article class="feature-card" style="--accent:#ff4d4d">
                    <div class="feature-icon"><i class="fa-brands fa-youtube"></i></div>
                    <h3>YouTube-видео</h3>
                    <p>
                        Загрузка видео, управление форматами и очередью скачивания
                        с поддержкой различных качеств.
                    </p>
                </article>

                {{-- 3. Tournaments --}}
                <article class="feature-card" style="--accent:#f59e0b">
                    <div class="feature-icon"><i class="fa-solid fa-trophy"></i></div>
                    <h3>Турниры</h3>
                    <p>
                        Отслеживание танцевальных турниров и групп, автоматическая
                        загрузка данных из внешних источников.
                    </p>
                </article>

                {{-- 4. ChadGPT --}}
                <article class="feature-card" style="--accent:#a855f7">
                    <div class="feature-icon"><i class="fa-solid fa-robot"></i></div>
                    <h3>ChadGPT AI-чат</h3>
                    <p>
                        Встроенный AI-ассистент для консультаций и автоматизации,
                        с историей диалогов и статистикой использования.
                    </p>
                </article>

                {{-- 5. Tools --}}
                <article class="feature-card" style="--accent:#10b981">
                    <div class="feature-icon"><i class="fa-solid fa-toolbox"></i></div>
                    <h3>Инструменты разработчика</h3>
                    <p>
                        Штрих-коды, конвертеры времени и цветов, хэши, UUID,
                        случайные строки и сравнение JSON.
                    </p>
                </article>

                {{-- 6. Users --}}
                <article class="feature-card" style="--accent:#6366f1">
                    <div class="feature-icon"><i class="fa-solid fa-user-shield"></i></div>
                    <h3>Пользователи и роли</h3>
                    <p>
                        Гибкая ролевая модель с правами доступа, система приглашений
                        и управление профилями.
                    </p>
                </article>

                {{-- 7. Images --}}
                <article class="feature-card" style="--accent:#ec4899">
                    <div class="feature-icon"><i class="fa-solid fa-image"></i></div>
                    <h3>Медиагалерея</h3>
                    <p>
                        Загрузка и хранение изображений, автоматическая генерация
                        миниатюр и облачное хранилище.
                    </p>
                </article>

                {{-- 8. REST API --}}
                <article class="feature-card" style="--accent:#3b82f6">
                    <div class="feature-icon"><i class="fa-solid fa-plug"></i></div>
                    <h3>REST API</h3>
                    <p>
                        Публичное API v1 с токен-авторизацией для интеграций,
                        внешних клиентов и автоматизации.
                    </p>
                </article>
            </div>
        </div>
    </section>

    {{-- Tools Section --}}
    <section class="tools-section" id="tools">
        <div class="features-container">
            <div class="section-header">
                <span class="section-kicker">Developer Tools</span>
                <h2>Семь инструментов — без установки</h2>
                <p>Готовые утилиты для повседневных задач разработчика и аналитика</p>
            </div>

            <div class="tools-grid">
                <span class="tool-chip"><i class="fa-solid fa-qrcode"></i> Штрих-коды</span>
                <span class="tool-chip"><i class="fa-solid fa-clock"></i> Конвертер времени</span>
                <span class="tool-chip"><i class="fa-solid fa-shuffle"></i> Случайные строки</span>
                <span class="tool-chip"><i class="fa-solid fa-hashtag"></i> Хэш-генератор</span>
                <span class="tool-chip"><i class="fa-solid fa-fingerprint"></i> UUID</span>
                <span class="tool-chip"><i class="fa-solid fa-palette"></i> Конвертер цветов</span>
                <span class="tool-chip"><i class="fa-solid fa-code-compare"></i> Сравнение JSON</span>
            </div>
        </div>
    </section>

    {{-- Platform Section --}}
    <section class="platform-section" id="platform">
        <div class="features-container">
            <div class="platform-panel">
                <div class="platform-copy">
                    <span class="section-kicker">Платформа</span>
                    <h2>Современный технологичный фундамент</h2>
                    <p>
                        A&amp;S Tech построен на актуальном стеке с чистой архитектурой,
                        строгой типизацией и покрытием тестами. Планировщик задач и
                        очередь обеспечивают автоматическую работу 24/7.
                    </p>

                    <ul class="platform-points">
                        <li><i class="fa-solid fa-check"></i> Domain-Driven Design и CQRS-подход</li>
                        <li><i class="fa-solid fa-check"></i> Ролевая модель доступа к каждому разделу</li>
                        <li><i class="fa-solid fa-check"></i> API-документация и токен-авторизация</li>
                        <li><i class="fa-solid fa-check"></i> Автоматические фоновые задачи и очереди</li>
                    </ul>
                </div>

                <div class="tech-badges">
                    <span class="tech-badge">Laravel</span>
                    <span class="tech-badge">PHP 8.5</span>
                    <span class="tech-badge">MySQL</span>
                    <span class="tech-badge">REST API</span>
                    <span class="tech-badge">RBAC</span>
                    <span class="tech-badge">Queue</span>
                    <span class="tech-badge">S3 Storage</span>
                    <span class="tech-badge">Swagger / Scribe</span>
                </div>
            </div>
        </div>
    </section>
</main>

{{-- Footer --}}
<footer class="landing-footer">
    <div class="footer-inner">
        <a href="#top" class="brand">
            <span class="brand-mark">A<span>&amp;</span>S</span>
            <span class="brand-name">Tech</span>
        </a>
        <p class="footer-tagline">Домены · Медиа · Турниры · AI · Инструменты</p>
        <p class="footer-copy">&copy; {{ date('Y') }} A&amp;S Tech. Все права защищены.</p>
    </div>
</footer>

</body>
</html>
