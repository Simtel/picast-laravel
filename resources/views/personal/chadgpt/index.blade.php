@extends('layouts.personal')
@section('title','Чат ChadGPT')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">Чат ChadGPT</h1>
        <div class="d-flex align-items-center">
            <span class="text-muted mr-3">
                Использовано: <span id="tokensCount">{{ $word_stats_sum }}</span> токенов
            </span>
            <button id="clearChatBtn" class="btn btn-outline-danger btn-sm">Очистить чат</button>
        </div>
    </div>

    <div class="card chat-card">
        <div class="chat-body" id="chatHistory">
            @if($conversations->count() > 0)
                @foreach($conversations->reverse() as $conversation)
                    <div class="chat-message user-message">
                        <div class="message-label">Вы ({{ $conversation->model }})</div>
                        <div class="message-text">{{ $conversation->user_message }}</div>
                    </div>
                    <div class="chat-message ai-message">
                        <div class="message-label">ChadGPT</div>
                        <div class="ai-message-content">{!! \Illuminate\Mail\Markdown::parse($conversation->ai_response) !!}</div>
                    </div>
                @endforeach
            @else
                <div class="alert alert-info chat-empty">Ваш разговор с ChadGPT появится здесь.</div>
            @endif
        </div>

        <div class="chat-footer">
            <div class="model-row">
                <label class="mr-2 mb-0" for="modelSelect">Модель:</label>
                <div class="model-search">
                    <div class="model-search-select" id="modelSearchSelect" role="button" tabindex="0">
                        <span id="modelSearchCurrent">
    @php
        $selectedModel = collect($models)->firstWhere('isDefault') ?? collect($models)->first();
    @endphp
    {{ $selectedModel?->label ?? 'Модели недоступны' }}
</span>
                        <i class="fa fa-chevron-down"></i>
                    </div>
                    <div class="model-search-dropdown d-none" id="modelSearchDropdown">
                        <input
                            type="text"
                            class="form-control form-control-sm model-search-input"
                            id="modelSearchInput"
                            placeholder="Поиск модели..."
                            autocomplete="off"
                        >
                        <ul class="model-search-list" id="modelSearchList">
                            @forelse($models as $model)
                                <li
                                    class="model-search-item {{ $model->isDefault ? 'active' : '' }}"
                                    data-value="{{ $model->id }}"
                                    data-label="{{ $model->label }}"
                                >
                                    {{ $model->label }}
                                </li>
                            @empty
                                <li class="model-search-empty">Модели недоступны</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                <select class="form-control form-control-sm chat-model-select d-none" id="modelSelect">
                    @forelse($models as $model)
                        <option
                                value="{{ $model->id }}"
                                {{ $model->isDefault ? 'selected' : '' }}
                        >
                            {{ $model->label }}
                        </option>
                    @empty
                        <option value="" disabled>Модели недоступны</option>
                    @endforelse
                </select>
            </div>
            <div class="form-row d-flex">
                <input
                    type="text"
                    class="form-control chat-input"
                    id="messageInput"
                    placeholder="Введите ваше сообщение..."
                    autocomplete="off"
                >
                <button id="sendMessageBtn" class="btn btn-primary ml-2">
                    <i class="fa fa-paper-plane mr-1"></i>Отправить
                </button>
            </div>
        </div>
    </div>

    <style>
        .chat-card {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 210px);
            min-height: 400px;
            overflow: hidden;
        }

        .chat-body {
            flex: 1 1 auto;
            overflow-y: auto;
            padding: 20px;
        }

        .chat-footer {
            flex-shrink: 0;
            padding: 12px 15px;
            border-top: 1px solid #e2e8f0;
            background: #fff;
        }

        .chat-footer .model-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .chat-model-select {
            max-width: 260px;
        }

        .model-search {
            position: relative;
            max-width: 260px;
            width: 100%;
        }

        .model-search-select {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            cursor: pointer;
            user-select: none;
        }

        .model-search-select:focus {
            color: #495057;
            background-color: #fff;
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .model-search-dropdown {
            position: absolute;
            bottom: 100%;
            left: 0;
            right: 0;
            z-index: 1000;
            margin-bottom: 0.25rem;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 0.25rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .model-search-input {
            border: none;
            border-bottom: 1px solid #ced4da;
            border-radius: 0;
            box-shadow: none !important;
        }

        .model-search-list {
            list-style: none;
            margin: 0;
            padding: 0;
            max-height: 200px;
            overflow-y: auto;
        }

        .model-search-item {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            cursor: pointer;
        }

        .model-search-item:hover,
        .model-search-item.active {
            background-color: #e9ecef;
        }

        .model-search-empty {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            color: #6c757d;
        }

        .chat-message {
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .chat-message .message-label {
            font-weight: bold;
            font-size: 0.85rem;
            margin-bottom: 5px;
        }

        .chat-message .message-text {
            white-space: pre-wrap;
            word-break: break-word;
        }

        .user-message {
            background-color: #e3f2fd;
            align-self: flex-end;
        }

        .ai-message {
            background-color: #f5f5f5;
        }

        /* Markdown styling */
        .ai-message-content pre {
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            overflow-x: auto;
            white-space: pre-wrap;
        }

        .ai-message-content code {
            background-color: #f4f4f4;
            padding: 2px 4px;
            border-radius: 3px;
        }

        .ai-message-content pre code {
            background: transparent;
            padding: 0;
        }

        .ai-message-content blockquote {
            border-left: 4px solid #ddd;
            padding-left: 10px;
            margin-left: 0;
            color: #666;
        }

        .ai-message-content table {
            border-collapse: collapse;
            width: 100%;
        }

        .ai-message-content table,
        .ai-message-content th,
        .ai-message-content td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .ai-message-content th {
            background-color: #f5f5f5;
        }

        .error-message {
            background-color: #ffebee;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 12px;
            color: #c62828;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .typing-indicator {
            color: #6c757d;
            font-style: italic;
            margin-bottom: 12px;
        }

        @media (max-width: 768px) {
            .chat-card {
                height: calc(100vh - 180px);
            }
        }
    </style>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const sendMessageBtn = document.getElementById('sendMessageBtn');
                const clearChatBtn = document.getElementById('clearChatBtn');
                const messageInput = document.getElementById('messageInput');
                const modelSelect = document.getElementById('modelSelect');
                const modelSearchSelect = document.getElementById('modelSearchSelect');
                const modelSearchCurrent = document.getElementById('modelSearchCurrent');
                const modelSearchDropdown = document.getElementById('modelSearchDropdown');
                const modelSearchInput = document.getElementById('modelSearchInput');
                const modelSearchList = document.getElementById('modelSearchList');
                const chatHistory = document.getElementById('chatHistory');
                const tokensCount = document.getElementById('tokensCount');

                let totalTokens = parseInt(tokensCount.textContent) || 0;

                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    console.error('CSRF token not found');
                    addMessageToChat('Система', 'Ошибка: CSRF токен не найден. Пожалуйста, обновите страницу.', 'error-message');
                    return;
                }

                // Scroll to bottom on load
                scrollToBottom();

                const modelItems = Array.from(
                    modelSearchList.querySelectorAll('.model-search-item')
                );

                const closeModelSearch = function () {
                    modelSearchDropdown.classList.add('d-none');
                    if (modelSearchInput.value) {
                        modelSearchInput.value = '';
                        filterModelItems('');
                    }
                };

                const filterModelItems = function (query) {
                    const q = query.trim().toLowerCase();
                    modelItems.forEach(item => {
                        const label = item.getAttribute('data-label').toLowerCase();
                        item.style.display = (!q || label.includes(q)) ? '' : 'none';
                    });
                    const visible = modelItems.some(item => item.style.display !== 'none');
                    const empty = modelSearchList.querySelector('.model-search-empty');
                    if (empty) {
                        empty.style.display = visible ? 'none' : '';
                    }
                };

                const setModelSelection = function (value, label) {
                    modelSelect.value = value;
                    modelSearchCurrent.textContent = label;
                    modelItems.forEach(item => {
                        item.classList.toggle(
                            'active',
                            item.getAttribute('data-value') === String(value)
                        );
                    });
                };

                modelSearchSelect.addEventListener('click', function (e) {
                    e.stopPropagation();
                    modelSearchDropdown.classList.toggle('d-none');
                    if (!modelSearchDropdown.classList.contains('d-none')) {
                        modelSearchInput.focus();
                    } else {
                        closeModelSearch();
                    }
                });

                modelSearchSelect.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        modelSearchSelect.click();
                    }
                });

                modelSearchInput.addEventListener('input', function () {
                    filterModelItems(this.value);
                });

                modelSearchList.addEventListener('click', function (e) {
                    const item = e.target.closest('.model-search-item');
                    if (!item || !item.hasAttribute('data-value')) {
                        return;
                    }
                    setModelSelection(
                        item.getAttribute('data-value'),
                        item.getAttribute('data-label')
                    );
                    closeModelSearch();
                });

                document.addEventListener('click', function (e) {
                    if (!e.target.closest('.model-search')) {
                        closeModelSearch();
                    }
                });

                const sendMessage = function () {
                    const message = messageInput.value.trim();
                    const model = modelSelect.value;

                    if (!message) {
                        messageInput.focus();
                        return;
                    }

                    // Add user message to chat
                    addMessageToChat('Вы (' + model + ')', message, 'user-message');

                    // Disable button and show loading
                    sendMessageBtn.disabled = true;
                    sendMessageBtn.innerHTML = 'Отправка...';

                    addTypingIndicator();

                    const url = '{{ route("chadgpt.send-message") }}';

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                        },
                        body: JSON.stringify({
                            message: message,
                            model: model
                        })
                    })
                        .then(response => {
                            if (!response.ok && response.status !== 422) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                addMessageToChat('ChadGPT', data.response, 'ai-message', true);
                                totalTokens += data.used_tokens_count || 0;
                                tokensCount.textContent = totalTokens;
                            } else {
                                addMessageToChat('Ошибка', JSON.stringify(data.errors) || 'Произошла неизвестная ошибка', 'error-message');
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            addMessageToChat('Ошибка', 'Не удалось связаться с сервером: ' + error.message, 'error-message');
                        })
                        .finally(() => {
                            sendMessageBtn.disabled = false;
                            sendMessageBtn.innerHTML = '<i class="fa fa-paper-plane mr-1"></i>Отправить';
                            messageInput.value = '';
                            messageInput.focus();
                        });
                };

                sendMessageBtn.addEventListener('click', sendMessage);

                messageInput.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        sendMessage();
                    }
                });

                clearChatBtn.addEventListener('click', function () {
                    if (!confirm('Вы уверены, что хотите очистить всю историю чата?')) {
                        return;
                    }

                    clearChatBtn.disabled = true;
                    clearChatBtn.textContent = 'Очистка...';

                    fetch('{{ route("chadgpt.clear-history") }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                chatHistory.innerHTML = '<div class="alert alert-info chat-empty">Ваш разговор с ChadGPT появится здесь.</div>';
                                tokensCount.textContent = '0';
                                totalTokens = 0;
                            } else {
                                throw new Error(data.error || 'Неизвестная ошибка');
                            }
                        })
                        .catch(error => {
                            console.error('Clear history error:', error);
                            alert('Не удалось очистить историю чата: ' + error.message);
                        })
                        .finally(() => {
                            clearChatBtn.disabled = false;
                            clearChatBtn.textContent = 'Очистить чат';
                        });
                });

                function scrollToBottom() {
                    chatHistory.scrollTop = chatHistory.scrollHeight;
                }

                function addMessageToChat(sender, message, cssClass, isMarkdown = false) {
                    const infoAlert = chatHistory.querySelector('.chat-empty');
                    if (infoAlert) {
                        infoAlert.remove();
                    }

                    const typingIndicator = chatHistory.querySelector('.typing-indicator');
                    if (typingIndicator) {
                        typingIndicator.remove();
                    }

                    const messageDiv = document.createElement('div');
                    messageDiv.className = cssClass;

                    if (isMarkdown) {
                        messageDiv.innerHTML = `
                            <div class="message-label">${sender}</div>
                            <div class="ai-message-content">${marked.parse(message)}</div>
                        `;
                    } else {
                        const textDiv = document.createElement('div');
                        textDiv.className = 'message-text';
                        textDiv.textContent = message;
                        const label = document.createElement('div');
                        label.className = 'message-label';
                        label.textContent = sender;
                        messageDiv.appendChild(label);
                        messageDiv.appendChild(textDiv);
                    }

                    chatHistory.appendChild(messageDiv);
                    scrollToBottom();
                }

                function addTypingIndicator() {
                    const typingIndicator = document.createElement('div');
                    typingIndicator.className = 'typing-indicator';
                    typingIndicator.textContent = 'ChadGPT печатает...';
                    chatHistory.appendChild(typingIndicator);
                    scrollToBottom();
                }
            });
        </script>
    @endpush
@endsection
