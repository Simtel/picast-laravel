@extends('layouts.personal')
@section('title','ChadGPT Chat')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">ChadGPT Chat</h1>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Chat with AI Models</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="modelSelect">Select Model:</label>
                            <select class="form-control" id="modelSelect">
                                <option value="gpt-4o-mini" selected>GPT-4o Mini (Fast & Cheap)</option>
                                <option value="gpt-4o">GPT-4o (Balanced)</option>
                                <option value="gpt-5">GPT-5 (Smart)</option>
                                <option value="gpt-5-mini">GPT-5 Mini</option>
                                <option value="gpt-5-nano">GPT-5 Nano</option>
                                <option value="claude-4.1-opus">Claude 4.1 Opus (Most Intelligent)</option>
                                <option value="claude-4.5-sonnet">Claude 4.5 Sonnet</option>
                                <option value="claude-3.7-sonnet-thinking">Claude 3.7 Sonnet Thinking</option>
                                <option value="gemini-2.5-pro">Gemini 2.5 Pro</option>
                                <option value="gemini-2.0-flash">Gemini 2.0 Flash</option>
                                <option value="deepseek-v3.1">DeepSeek v3.1</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="messageInput">Your Message:</label>
                            <textarea class="form-control" id="messageInput" rows="4" placeholder="Type your message here..."></textarea>
                        </div>

                        <button id="sendMessageBtn" class="btn btn-primary">Send Message</button>
                        <button id="clearChatBtn" class="btn btn-secondary ml-2">Clear Chat</button>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Chat History</h5>
                    </div>
                    <div class="card-body">
                        <div id="chatHistory" class="chat-history">
                            @if($conversations->count() > 0)
                                @foreach($conversations->reverse() as $conversation)
                                    <div class="user-message">
                                        <div class="message-label">You ({{ $conversation->model }}):</div>
                                        <div>{{ $conversation->user_message }}</div>
                                    </div>
                                    <div class="ai-message">
                                        <div class="message-label">ChadGPT:</div>
                                        <div class="ai-message-content">{!! \Illuminate\Mail\Markdown::parse($conversation->ai_response) !!}</div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">Your conversation with ChadGPT will appear here.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

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
        document.addEventListener('DOMContentLoaded', function() {
            const sendMessageBtn = document.getElementById('sendMessageBtn');
            const clearChatBtn = document.getElementById('clearChatBtn');
            const messageInput = document.getElementById('messageInput');
            const modelSelect = document.getElementById('modelSelect');
            const chatHistory = document.getElementById('chatHistory');
            const wordsCount = document.getElementById('wordsCount');

            let totalWords = 0;

            // Check if CSRF token is available
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token not found');
                addMessageToChat('System', 'Error: CSRF token not found. Please refresh the page.', 'error-message');
                return;
            }

            sendMessageBtn.addEventListener('click', function() {
                const message = messageInput.value.trim();
                const model = modelSelect.value;

                if (!message) {
                    alert('Please enter a message');
                    return;
                }

                // Add user message to chat
                addMessageToChat('You (' + model + ')', message, 'user-message');

                // Disable button and show loading
                sendMessageBtn.disabled = true;
                sendMessageBtn.textContent = 'Sending...';

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
                        addMessageToChat('Error', JSON.stringify(data.errors) || 'An unknown error occurred', 'error-message');
                    }
                })
                .catch(error => {
                    console.log(error);
                    console.error('Fetch error:', error);
                    addMessageToChat('Error', 'Failed to communicate with the server: ' + error.message, 'error-message');
                })
                .finally(() => {
                    // Re-enable button
                    sendMessageBtn.disabled = false;
                    sendMessageBtn.textContent = 'Send Message';
                    messageInput.value = '';
                });
            });

            clearChatBtn.addEventListener('click', function() {
                chatHistory.innerHTML = '<div class="alert alert-info">Your conversation with ChadGPT will appear here.</div>';
                totalWords = 0;
                wordsCount.textContent = '0';
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