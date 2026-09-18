@extends('layouts.common')
@section('title', 'ERP Flow Navigator - ' . (env('WEBSITE_NAME') ?? 'Nachias ERP'))

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">

                <!-- Card Container -->
                <div class="card shadow-sm border-0 chat-card"
                    style="min-height: 720px; display: flex; flex-direction: column;">

                    <!-- Chat Header -->
                    <div
                        class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2 bg-white">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-md bg-label-primary rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 44px; height: 44px;">
                                <i class="ri ri-robot-2-line fs-4 text-primary"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                    ERP Flow Navigator AI
                                    <span class="badge bg-label-success fs-tiny fw-normal px-2 py-1">
                                        <i class="ri ri-circle-fill me-1" style="font-size: 8px;"></i>
                                        {{ $isAvailable ? 'Online' : 'Offline' }}
                                    </span>
                                </h5>
                                <small class="text-muted">
                                    Model: <strong class="text-secondary">{{ $model }}</strong>
                                    <span class="mx-1">•</span> Local Ollama Inference
                                    <span class="mx-1">•</span> Workflow Analysis
                                </small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button"
                                class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 shadow-sm"
                                id="clearChatBtn" title="Clear current session conversation">
                                <i class="ri ri-delete-bin-line"></i>
                                <span class="d-none d-sm-inline">Clear Chat</span>
                            </button>
                        </div>
                    </div>

                    <!-- Ollama Offline Alert (if unavailable) -->
                    <div id="serviceAlert"
                        class="alert alert-warning alert-dismissible fade show m-3 mb-0 {{ $isAvailable ? 'd-none' : '' }}"
                        role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ri ri-alert-line fs-5"></i>
                            <div>
                                <strong>Ollama Service Notice:</strong>
                                Ollama does not appear to be responding on <code>{{ config('services.ollama.url') }}</code>.
                                Please make sure the local Ollama process is running.
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <!-- Messages Area -->
                    <div class="card-body p-4 chat-messages-container flex-grow-1" id="chatMessagesArea"
                        style="height: 480px; overflow-y: auto; background-color: #f9fbfd;">

                        <!-- Welcome AI Message -->
                        <div class="d-flex justify-content-start mb-3 chat-bubble-row ai-msg-row" id="welcomeMessage">
                            <div class="avatar avatar-sm rounded-circle bg-label-primary me-2 flex-shrink-0 d-flex align-items-center justify-content-center"
                                style="width: 36px; height: 36px;">
                                <i class="ri ri-robot-2-line text-primary"></i>
                            </div>
                            <div class="chat-bubble ai-bubble bg-white text-dark shadow-sm border p-3 rounded-3"
                                style="max-width: 80%;">
                                <div class="fw-semibold text-primary mb-1 small d-flex align-items-center gap-1">
                                    <i class="ri ri-sparkling-fill text-warning"></i> ERP Flow Navigator
                                </div>
                                <div class="chat-text" style="line-height: 1.6; word-break: break-word;">
                                    Hello! I am your <strong>ERP Flow Navigator AI</strong> assistant.<br>
                                    <strong>Click a quick question to test:</strong>
                                    <div class="d-flex flex-wrap gap-2 mt-2" id="quickPrompts">
                                        <button type="button" class="btn btn-sm btn-outline-primary quick-prompt-btn"
                                            data-query="What is the next step after a Purchase Order is created?">
                                            <i class="ri ri-shopping-cart-line me-1"></i> Next step after Purchase Order?
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary quick-prompt-btn"
                                            data-query="What happens after a Sales Order is confirmed?">
                                            <i class="ri ri-order-play-line me-1"></i> Next step after Sales Order?
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary quick-prompt-btn"
                                            data-query="What is the next stage after Goods Receipt (GRN)?">
                                            <i class="ri ri-inbox-archive-line me-1"></i> Next stage after Goods Receipt
                                            (GRN)?
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary quick-prompt-btn"
                                            data-query="How does the production process flow?">
                                            <i class="ri ri-settings-4-line me-1"></i> How does Production process flow?
                                        </button>
                                    </div>
                                </div>
                                <div class="text-end text-muted mt-1" style="font-size: 11px;">
                                    {{ date('h:i A') }}
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic bubbles will be appended here -->

                        <!-- Typing / Loading Indicator Row (Hidden by default) -->
                        <div class="d-flex justify-content-start mb-3 chat-bubble-row d-none" id="loadingBubbleRow">
                            <div class="avatar avatar-sm rounded-circle bg-label-primary me-2 flex-shrink-0 d-flex align-items-center justify-content-center"
                                style="width: 36px; height: 36px;">
                                <i class="ri ri-robot-2-line text-primary"></i>
                            </div>
                            <div class="chat-bubble ai-bubble bg-white text-dark shadow-sm border px-3 py-2 rounded-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"
                                        style="width: 1rem; height: 1rem;">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <span class="text-muted small">AI is analyzing ERP flow...</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Chat Input Footer -->
                    <div class="card-footer bg-white border-top p-3">
                        <form id="chatForm" onsubmit="return false;">
                            <div class="d-flex align-items-end gap-2">
                                <div class="flex-grow-1 position-relative">
                                    <textarea id="userMessageInput" class="form-control border shadow-none" rows="2"
                                        placeholder="Ask a question about the ERP workflow... (Enter to send, Shift+Enter for new line)"
                                        style="resize: none; font-size: 14px; min-height: 52px; max-height: 120px;"
                                        required></textarea>
                                </div>
                                <button type="submit" id="sendBtn"
                                    class="btn btn-primary d-flex align-items-center justify-content-center gap-1 px-4"
                                    style="height: 52px;">
                                    <i class="ri ri-send-plane-2-fill fs-5" id="sendIcon"></i>
                                    <span class="d-none d-sm-inline fw-semibold">Send</span>
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                <small class="text-muted d-flex align-items-center gap-1" style="font-size: 12px;">
                                    <i class="ri ri-information-line"></i>
                                    <span>Press <strong>Enter</strong> to send, <strong>Shift + Enter</strong> for a new
                                        line</span>
                                </small>
                                <small class="text-muted" id="charCount" style="font-size: 11px;">0 / 2000</small>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <style>
        .chat-bubble {
            position: relative;
            word-wrap: break-word;
        }

        .user-bubble {
            background-color: #8c57ff !important;
            color: #ffffff !important;
            border-bottom-right-radius: 4px !important;
        }

        .ai-bubble {
            border-bottom-left-radius: 4px !important;
        }

        .chat-messages-container::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages-container::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
            border-radius: 3px;
        }
    </style>

    <script>
        $(document).ready(function () {
            const $chatArea = $('#chatMessagesArea');
            const $messageInput = $('#userMessageInput');
            const $sendBtn = $('#sendBtn');
            const $sendIcon = $('#sendIcon');
            const $loadingRow = $('#loadingBubbleRow');
            const $clearBtn = $('#clearChatBtn');
            const $charCount = $('#charCount');

            // In-memory conversation history for the current browser session
            let conversationHistory = [];
            let isProcessing = false;

            // Auto-scroll to bottom of chat
            function scrollToBottom(smooth = true) {
                $chatArea.stop().animate({
                    scrollTop: $chatArea[0].scrollHeight
                }, smooth ? 250 : 0);
            }

            // Format current time
            function getCurrentTime() {
                const now = new Date();
                return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }

            // Escape HTML to prevent XSS
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Format text with line breaks, safe tags, bolding and navigation highlights
            function formatMessageText(text) {
                let escaped = escapeHtml(text);

                // Format markdown bold: **text**
                escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

                // Highlight key ERP response labels
                escaped = escaped.replace(/(ERP Module:|Page Name:|Menu Navigation:|Field Name:|Answer:|Explanation:|Current Stage:|Current Menu Navigation:|Next Expected Stage:|Next Menu Navigation:|Reason:|Flow:|Evidence:|Confidence:|Restrictions:)/g, '<strong class="text-primary">$1</strong>');

                // Convert bullet points (* item or - item)
                escaped = escaped.replace(/^(\s*)[*\-]\s+(.*)$/gm, '$1<span class="text-secondary">•</span> $2');

                // Replace newlines with <br>
                escaped = escaped.replace(/\n/g, '<br>');

                return escaped;
            }

            // Append User Message to UI
            function appendUserMessage(text) {
                const time = getCurrentTime();
                const formatted = formatMessageText(text);
                const html = `
                        <div class="d-flex justify-content-end mb-3 chat-bubble-row user-msg-row">
                            <div class="chat-bubble user-bubble shadow-sm p-3 rounded-3" style="max-width: 80%;">
                                <div class="fw-semibold text-white-50 mb-1 small d-flex align-items-center justify-content-end gap-1">
                                    You <i class="ri ri-user-3-fill text-white"></i>
                                </div>
                                <div class="chat-text" style="line-height: 1.6;">
                                    ${formatted}
                                </div>
                                <div class="text-start text-white-50 mt-1" style="font-size: 11px;">
                                    ${time}
                                </div>
                            </div>
                        </div>
                    `;
                $loadingRow.before(html);
                scrollToBottom();
            }

            // Append AI Message to UI
            function appendAiMessage(text) {
                const time = getCurrentTime();
                const formatted = formatMessageText(text);
                const html = `
                        <div class="d-flex justify-content-start mb-3 chat-bubble-row ai-msg-row">
                            <div class="avatar avatar-sm rounded-circle bg-label-primary me-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="ri ri-robot-2-line text-primary"></i>
                            </div>
                            <div class="chat-bubble ai-bubble bg-white text-dark shadow-sm border p-3 rounded-3" style="max-width: 80%;">
                                <div class="fw-semibold text-primary mb-1 small d-flex align-items-center gap-1">
                                    <i class="ri ri-sparkling-fill text-warning"></i> ERP Flow Navigator
                                </div>
                                <div class="chat-text" style="line-height: 1.6;">
                                    ${formatted}
                                </div>
                                <div class="text-end text-muted mt-1" style="font-size: 11px;">
                                    ${time}
                                </div>
                            </div>
                        </div>
                    `;
                $loadingRow.before(html);
                scrollToBottom();
            }

            // Append Error Message to UI
            function appendErrorMessage(errorText) {
                const time = getCurrentTime();
                const formatted = formatMessageText(errorText);
                const html = `
                        <div class="d-flex justify-content-start mb-3 chat-bubble-row error-msg-row">
                            <div class="avatar avatar-sm rounded-circle bg-label-danger me-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="ri ri-error-warning-line text-danger"></i>
                            </div>
                            <div class="chat-bubble bg-label-danger text-danger border border-danger p-3 rounded-3" style="max-width: 80%;">
                                <div class="fw-semibold small d-flex align-items-center gap-1 mb-1">
                                    <i class="ri ri-alert-line"></i> Service Alert
                                </div>
                                <div class="chat-text small" style="line-height: 1.5;">
                                    ${formatted}
                                </div>
                                <div class="text-end text-muted mt-1" style="font-size: 11px;">
                                    ${time}
                                </div>
                            </div>
                        </div>
                    `;
                $loadingRow.before(html);
                scrollToBottom();
            }

            // Set Loading state
            function setLoading(loading) {
                isProcessing = loading;
                if (loading) {
                    $loadingRow.removeClass('d-none');
                    $sendBtn.prop('disabled', true);
                    $messageInput.prop('disabled', true);
                    $sendIcon.removeClass('ri-send-plane-2-fill').addClass('ri-loader-4-line spinner-border-sm');
                    scrollToBottom();
                } else {
                    $loadingRow.addClass('d-none');
                    $sendBtn.prop('disabled', false);
                    $messageInput.prop('disabled', false);
                    $sendIcon.removeClass('ri-loader-4-line spinner-border-sm').addClass('ri-send-plane-2-fill');
                    $messageInput.focus();
                }
            }

            // Send Message Handler
            function sendMessage() {
                if (isProcessing) return;

                const message = $messageInput.val().trim();
                if (!message) {
                    $messageInput.focus();
                    return;
                }

                // Display user message in UI
                appendUserMessage(message);

                // Clear input and reset char count
                $messageInput.val('');
                $charCount.text('0 / 2000');

                // Set UI loading state
                setLoading(true);

                // Prepare request payload with recent history
                const payload = {
                    message: message,
                    history: conversationHistory
                };

                $.ajax({
                    url: '{{ route("chatbot.message") }}',
                    type: 'POST',
                    data: JSON.stringify(payload),
                    contentType: 'application/json; charset=utf-8',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        setLoading(false);
                        if (response && response.success) {
                            const aiReply = response.message;
                            appendAiMessage(aiReply);

                            // Update session conversation history
                            conversationHistory.push({ role: 'user', content: message });
                            conversationHistory.push({ role: 'assistant', content: aiReply });
                        } else {
                            const errorMsg = (response && response.message) ? response.message : 'AI service is currently unavailable.';
                            appendErrorMessage(errorMsg);
                        }
                    },
                    error: function (xhr, status, error) {
                        setLoading(false);
                        let errorMsg = 'AI service is currently unavailable. Please make sure Ollama is running.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (status === 'timeout') {
                            errorMsg = 'The AI response took too long. Please try again.';
                        }

                        appendErrorMessage(errorMsg);
                    }
                });
            }

            // Form submit
            $('#chatForm').on('submit', function (e) {
                e.preventDefault();
                sendMessage();
            });

            // Quick prompt button click handler
            $(document).on('click', '.quick-prompt-btn', function () {
                const query = $(this).data('query');
                if (query && !isProcessing) {
                    $messageInput.val(query);
                    $charCount.text(query.length + ' / 2000');
                    sendMessage();
                }
            });

            // Enter key sends, Shift+Enter creates newline
            $messageInput.on('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            // Character counter & auto-expand height
            $messageInput.on('input', function () {
                const len = $(this).val().length;
                $charCount.text(len + ' / 2000');

                // Auto-expand height
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            });

            // Clear Chat
            $clearBtn.on('click', function () {
                if (conversationHistory.length === 0 && $('.user-msg-row').length === 0) {
                    return;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Clear Conversation?',
                        text: 'This will reset the current session chat history.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#8c57ff',
                        cancelButtonColor: '#8592a3',
                        confirmButtonText: 'Yes, clear it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            resetChat();
                        }
                    });
                } else {
                    if (confirm('Clear current session chat history?')) {
                        resetChat();
                    }
                }
            });

            function resetChat() {
                conversationHistory = [];
                $('.user-msg-row, .ai-msg-row:not(#welcomeMessage), .error-msg-row').remove();
                $messageInput.val('').focus();
                $charCount.text('0 / 2000');
                scrollToBottom(false);
            }

            // Initial scroll and focus
            scrollToBottom(false);
            $messageInput.focus();
        });
    </script>
@endsection