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
                <select class="form-control form-control-sm chat-model-select" id="modelSelect">
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
