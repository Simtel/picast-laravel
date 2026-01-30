@extends('layouts.personal')
@section('title','Чат ChadGPT')

@section('content')
    <div class="main-content-header">
        <h1 class="h2">Чат ChadGPT</h1>
    </div>

    <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Чат с ИИ моделями</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="modelSelect">Выберите модель:</label>
                            <select class="form-control" id="modelSelect">
                                @foreach($models as $model)
                                    <option
                                            value="{{ $model->value }}"
                                            {{ $model->isDefault() ? 'selected' : '' }}
                                    >
                                        {{ $model->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="messageInput">Ваше сообщение:</label>
                            <textarea class="form-control" id="messageInput" rows="4"
                                      placeholder="Введите ваше сообщение здесь..."></textarea>
                        </div>

                        <button id="sendMessageBtn" class="btn btn-primary">Отправить сообщение</button>
                        <button id="clearChatBtn" class="btn btn-secondary ml-2">Очистить чат</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Статистика использования</h5>
                    </div>
                    <div class="card-body">
                        <div id="usageStats">
                            <p>Использовано слов: </p>

                            @foreach($word_stats as $word_stat)
                                {{$word_stat->getStatDate()->format('m-Y')}}: {{$word_stat->getWordsUsed()}}
                            @endforeach
                            <p>Всего использовано: <span id="wordsCount">{{$word_stats_sum}}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>История чата</h5>
                    </div>
                    <div class="card-body">
                        <div id="chatHistory" class="chat-history">
                            @if($conversations->count() > 0)
                                @foreach($conversations->reverse() as $conversation)
                                    <div class="user-message">
                                        <div class="message-label">Вы ({{ $conversation->model }}):</div>
                                        <div>{{ $conversation->user_message }}</div>
                                    </div>
                                    <div class="ai-message">
                                        <div class="message-label">ChadGPT:</div>
                                        <div class="ai-message-content">{!! \Illuminate\Mail\Markdown::parse($conversation->ai_response) !!}</div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">Ваш разговор с ChadGPT появится здесь.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

    <style>
        .chat-history {
            max-height: 800px;
            overflow-y: auto;
        }

        .user-message {
            background-color: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .ai-message {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .message-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .error-message {
            background-color: #ffebee;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            color: #c62828;
        }

        /* Markdown styling */
        .ai-message-content pre {
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            overflow-x: auto;
        }

        .ai-message-content code {
            background-color: #f4f4f4;
            padding: 2px 4px;
            border-radius: 3px;
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
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sendMessageBtn = document.getElementById('sendMessageBtn');
            const clearChatBtn = document.getElementById('clearChatBtn');
            const messageInput = document.getElementById('messageInput');
            const modelSelect = document.getElementById('modelSelect');
            const chatHistory = document.getElementById('chatHistory');
            const wordsCount = document.getElementById('wordsCount');

            let totalWords = parseInt(wordsCount.textContent);

            // Check if CSRF token is available
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token not found');
                addMessageToChat('Система', 'Ошибка: CSRF токен не найден. Пожалуйста, обновите страницу.', 'error-message');
                return;
            }

            sendMessageBtn.addEventListener('click', function () {
                const message = messageInput.value.trim();
                const model = modelSelect.value;

                if (!message) {
                    alert('Пожалуйста, введите сообщение');
                    return;
                }

                // Add user message to chat
                addMessageToChat('Вы (' + model + ')', message, 'user-message');

                // Disable button and show loading
                sendMessageBtn.disabled = true;
                sendMessageBtn.textContent = 'Отправка...';

                // Send request to our backend
                const url = '{{ route("chadgpt.send-message") }}';
                console.log('Sending request to:', url);

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
                        console.log('Response status:', response.status);
                        if (!response.ok && response.status !== 422) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.success) {
                            // Add AI response to chat with Markdown rendering
                            addMessageToChat('ChadGPT', data.response, 'ai-message', true);

                            // Update word count
                            totalWords += data.used_words_count || 0;
                            wordsCount.textContent = totalWords;
                        } else {
                            addMessageToChat('Ошибка', JSON.stringify(data.errors) || 'Произошла неизвестная ошибка', 'error-message');
                        }
                    })
                    .catch(error => {
                        console.log(error);
                        console.error('Fetch error:', error);
                        addMessageToChat('Ошибка', 'Не удалось связаться с сервером: ' + error.message, 'error-message');
                    })
                    .finally(() => {
                        // Re-enable button
                        sendMessageBtn.disabled = false;
                        sendMessageBtn.textContent = 'Отправить сообщение';
                        messageInput.value = '';
                    });
            });

            clearChatBtn.addEventListener('click', function () {
                if (!confirm('Вы уверены, что хотите очистить всю историю чата?')) {
                    return;
                }

                // Disable button during request
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
                            // Clear chat display
                            chatHistory.innerHTML = '<div class="alert alert-info">Ваш разговор с ChadGPT появится здесь.</div>';
                            // Reset word count if exists
                            if (wordsCount) wordsCount.textContent = '0';
                            // Show success message
                            alert('История чата успешно очищена');
                        } else {
                            throw new Error(data.error || 'Неизвестная ошибка');
                        }
                    })
                    .catch(error => {
                        console.error('Clear history error:', error);
                        alert('Не удалось очистить историю чата: ' + error.message);
                    })
                    .finally(() => {
                        // Re-enable button
                        clearChatBtn.disabled = false;
                        clearChatBtn.textContent = 'Очистить чат';
                    });
            });

            function addMessageToChat(sender, message, cssClass, isMarkdown = false) {
                // Remove the initial info message if it exists
                const infoAlert = chatHistory.querySelector('.alert-info');
                if (infoAlert) {
                    infoAlert.remove();
                }

                const messageDiv = document.createElement('div');
                messageDiv.className = cssClass;

                if (isMarkdown) {
                    // Render Markdown for AI messages
                    messageDiv.innerHTML = `
                        <div class="message-label">${sender}:</div>
                        <div class="ai-message-content">${marked.parse(message)}</div>
                    `;
                } else {
                    // Plain text for user messages and errors
                    messageDiv.innerHTML = `
                        <div class="message-label">${sender}:</div>
                        <div>${message}</div>
                    `;
                }

                chatHistory.appendChild(messageDiv);

                // Scroll to bottom
                chatHistory.scrollTop = chatHistory.scrollHeight;
            }
        });
    </script>
@endsection