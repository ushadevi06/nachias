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
                                    <span class="badge bg-label-info fs-tiny fw-normal px-2 py-1"
                                        title="Tamil voice notes automatically translated to English">
                                        <i class="ri ri-mic-line me-1" style="font-size: 10px;"></i>
                                        Tamil Voice Note
                                    </span>
                                </h5>
                                <small class="text-muted">
                                    <span class="mx-1">•</span> Tamil Voice & Workflow Analysis
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
                                    You can ask questions in <strong>English</strong> or record a <strong>Tamil Voice Note
                                        (குரல் பதிவு)</strong> — your Tamil speech will be automatically translated to
                                    English and answered by the AI.<br>
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
                                        <button type="button" class="btn btn-sm btn-outline-info quick-prompt-btn"
                                            data-query="கொள்முதல் ஆணை உருவான பிறகு அடுத்த கட்டம் என்ன?">
                                            <i class="ri ri-translate-2 me-1"></i> தமிழ்: கொள்முதல் ஆணைக்கு அடுத்த கட்டம்?
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info quick-prompt-btn"
                                            data-query="விற்பனை ஆணை உறுதி செய்யப்பட்ட பிறகு என்ன நடக்கும்?">
                                            <i class="ri ri-translate-2 me-1"></i> தமிழ்: விற்பனை ஆணைக்கு அடுத்த படி?
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
                        <!-- Image Attachment Preview Bar -->
                        <div id="imagePreviewBar"
                            class="d-none border rounded-3 p-2 px-3 mb-2 bg-light d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="position-relative">
                                    <img id="imagePreviewThumb" src="" alt="Attached Screenshot"
                                        class="rounded border shadow-sm" style="width: 44px; height: 44px; object-fit: cover; cursor: pointer;"
                                        title="Click to preview full size">
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-label-primary fs-tiny py-0 px-1">
                                            <i class="ri ri-image-line me-1"></i> Attached Image
                                        </span>
                                        <span class="fw-semibold text-dark small" id="imageFileName"
                                            style="max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        </span>
                                    </div>
                                    <div class="text-muted" style="font-size: 11px;">
                                        <span id="imageFileSize"></span>
                                        <span class="mx-1">•</span>
                                        <span id="ocrStatusText" class="text-secondary fst-italic">Ready to send with text or voice note</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-icon" id="removeImageBtn" title="Remove attached image">
                                    <i class="ri ri-close-line fs-5"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Active Tamil Voice Note Recording Bar -->
                        <div id="voiceRecordingBar"
                            class="d-none border border-danger rounded-3 p-2 px-3 mb-2 bg-label-danger d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="voice-recording-pulse"></div>
                                <span class="fw-bold text-danger voice-rec-time" id="voiceTimer">00:00</span>
                                <span class="badge bg-danger text-white fs-tiny px-2 py-1">Tamil (தமிழ்)</span>
                                <div class="voice-wave-animation ms-1 d-none d-sm-flex">
                                    <span></span><span></span><span></span><span></span><span></span>
                                </div>
                                <span class="text-dark small ms-2 fst-italic" id="liveTranscript"
                                    style="max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    Listening in Tamil (தமிழில் பேசவும்)...
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button"
                                    class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                    id="cancelVoiceBtn" title="Discard voice note">
                                    <i class="ri ri-close-line"></i> <span class="d-none d-sm-inline">Cancel</span>
                                </button>
                                <button type="button"
                                    class="btn btn-sm btn-danger d-flex align-items-center gap-1 shadow-sm"
                                    id="sendVoiceBtn" title="Stop and send Tamil voice note">
                                    <i class="ri ri-send-plane-fill"></i> <span>Send Note</span>
                                </button>
                            </div>
                        </div>

                        <form id="chatForm" onsubmit="return false;">
                            <div class="d-flex align-items-end gap-2">
                                <!-- Image Upload Button -->
                                <button type="button" id="imageUploadBtn"
                                    class="btn btn-outline-primary d-flex align-items-center justify-content-center px-3"
                                    style="height: 52px;" title="Upload Image or Screenshot (படத்தைப் பதிவேற்றவும்)">
                                    <i class="ri ri-image-add-line fs-5" id="imageIcon"></i>
                                </button>
                                <input type="file" id="imageFileInput" accept="image/png, image/jpeg, image/jpg, image/webp" class="d-none">

                                <!-- Tamil Voice Record Button -->
                                <button type="button" id="voiceRecordBtn"
                                    class="btn btn-outline-primary d-flex align-items-center justify-content-center px-3"
                                    style="height: 52px;" title="Record Tamil Voice Note (தமிழில் குரல் பதிவு)">
                                    <i class="ri ri-mic-line fs-5" id="micIcon"></i>
                                </button>
                                <div class="flex-grow-1 position-relative">
                                    <textarea id="userMessageInput" class="form-control border shadow-none" rows="2"
                                        placeholder="Ask doubt in English or Tamil, or upload screenshot and speak voice note (Enter to send)..."
                                        style="resize: none; font-size: 14px; min-height: 52px; max-height: 120px;"></textarea>
                                </div>
                                <button type="submit" id="sendBtn"
                                    class="btn btn-primary d-flex align-items-center justify-content-center gap-1 px-4"
                                    style="height: 52px;">
                                    <i class="ri ri-send-plane-2-fill fs-5" id="sendIcon"></i>
                                    <span class="d-none d-sm-inline fw-semibold">Send</span>
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                <small class="text-muted d-flex align-items-center gap-1 flex-wrap" style="font-size: 12px;">
                                    <i class="ri ri-information-line"></i>
                                    <span>Press <strong>Enter</strong> to send, click <i class="ri ri-image-add-line text-primary"></i> to attach <strong>Image</strong> (or paste <strong>Ctrl+V</strong>), or click <i class="ri ri-mic-line text-primary"></i> for <strong>Tamil Voice Note</strong></span>
                                </small>
                                <small class="text-muted" id="charCount" style="font-size: 11px;">0 / 2000</small>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Image Lightbox Modal for Full View -->
    <div class="modal fade" id="imageLightboxModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header py-2 px-3">
                    <h6 class="modal-title fw-bold" id="imageLightboxTitle">
                        <i class="ri ri-image-line me-1 text-primary"></i> Attached Image / Screenshot
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-2 text-center bg-dark rounded-bottom">
                    <img id="imageLightboxImg" src="" alt="Screenshot Full View" class="img-fluid rounded" style="max-height: 80vh; object-fit: contain;">
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

        /* Tamil Voice Note Animations & Audio Player */
        .voice-recording-pulse {
            width: 12px;
            height: 12px;
            background-color: #ff3e1d;
            border-radius: 50%;
            animation: pulse-red 1.2s infinite;
            display: inline-block;
        }

        @keyframes pulse-red {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(255, 62, 29, 0.7);
            }

            70% {
                transform: scale(1.15);
                box-shadow: 0 0 0 10px rgba(255, 62, 29, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(255, 62, 29, 0);
            }
        }

        .voice-wave-animation {
            display: flex;
            align-items: center;
            gap: 3px;
            height: 18px;
        }

        .voice-wave-animation span {
            width: 3px;
            height: 100%;
            background: #ff3e1d;
            border-radius: 3px;
            animation: wave 0.8s ease-in-out infinite alternate;
        }

        .voice-wave-animation span:nth-child(2) {
            animation-delay: 0.15s;
            height: 60%;
        }

        .voice-wave-animation span:nth-child(3) {
            animation-delay: 0.3s;
            height: 95%;
        }

        .voice-wave-animation span:nth-child(4) {
            animation-delay: 0.45s;
            height: 40%;
        }

        .voice-wave-animation span:nth-child(5) {
            animation-delay: 0.6s;
            height: 80%;
        }

        @keyframes wave {
            0% {
                transform: scaleY(0.25);
            }

            100% {
                transform: scaleY(1);
            }
        }

        .voice-audio-box audio {
            height: 38px;
            max-width: 100%;
            border-radius: 20px;
            outline: none;
        }

        .voice-pill-original {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 8px;
            padding: 8px 12px;
            margin-top: 6px;
        }

        .voice-pill-translation {
            background: rgba(255, 255, 255, 0.22);
            border: 1px solid rgba(255, 255, 255, 0.35);
            border-radius: 8px;
            padding: 8px 12px;
            margin-top: 6px;
            color: #ffffff;
        }

        .voice-btn-active {
            background-color: #ff3e1d !important;
            border-color: #ff3e1d !important;
            color: #ffffff !important;
            animation: pulse-red 1.5s infinite;
        }

        /* Image Attachment & Lightbox Styles */
        .chat-uploaded-img {
            transition: transform 0.2s ease, opacity 0.2s ease, box-shadow 0.2s ease;
        }
        .chat-uploaded-img:hover {
            transform: scale(1.02);
            opacity: 0.95;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25) !important;
        }
        .chat-card.drag-over {
            outline: 2px dashed #8c57ff !important;
            outline-offset: -4px;
            background-color: rgba(140, 87, 255, 0.02) !important;
        }
    </style>

    <!-- Client-side Tesseract.js OCR for in-browser ERP screen text recognition -->
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>

    <script>
        $(document).ready(function () {
            const $chatArea = $('#chatMessagesArea');
            const $messageInput = $('#userMessageInput');
            const $sendBtn = $('#sendBtn');
            const $sendIcon = $('#sendIcon');
            const $loadingRow = $('#loadingBubbleRow');
            const $clearBtn = $('#clearChatBtn');
            const $charCount = $('#charCount');

            // Voice recording elements
            const $voiceRecordBtn = $('#voiceRecordBtn');
            const $micIcon = $('#micIcon');
            const $voiceRecordingBar = $('#voiceRecordingBar');
            const $voiceTimer = $('#voiceTimer');
            const $liveTranscript = $('#liveTranscript');
            const $cancelVoiceBtn = $('#cancelVoiceBtn');
            const $sendVoiceBtn = $('#sendVoiceBtn');

            // Image upload elements
            const $imageUploadBtn = $('#imageUploadBtn');
            const $imageFileInput = $('#imageFileInput');
            const $imagePreviewBar = $('#imagePreviewBar');
            const $imagePreviewThumb = $('#imagePreviewThumb');
            const $imageFileName = $('#imageFileName');
            const $imageFileSize = $('#imageFileSize');
            const $ocrStatusText = $('#ocrStatusText');
            const $removeImageBtn = $('#removeImageBtn');
            const $imageLightboxModal = $('#imageLightboxModal');
            const $imageLightboxImg = $('#imageLightboxImg');
            const $imageLightboxTitle = $('#imageLightboxTitle');

            // Staged image attachment state
            let currentAttachedImage = null; // { base64: '...', name: '...', size: 123, ocrText: '...' }

            // In-memory conversation history
            let conversationHistory = [];
            let isProcessing = false;
            let msgCounter = 0;

            // Voice recording state
            let isRecordingVoice = false;
            let mediaRecorder = null;
            let mediaStream = null;
            let audioChunks = [];
            let voiceTimerInterval = null;
            let voiceSeconds = 0;
            let capturedTamilTranscript = '';
            let recognition = null;
            let lastSpeechError = null;
            let speechSessionActive = false;

            // Helper to format file sizes nicely
            function formatFileSize(bytes) {
                if (!bytes || bytes === 0) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            }

            // Clear the currently attached image
            function clearAttachedImage() {
                currentAttachedImage = null;
                $imageFileInput.val('');
                $imagePreviewBar.addClass('d-none');
                $imagePreviewThumb.attr('src', '');
                $imageFileName.text('');
                $imageFileSize.text('');
                $ocrStatusText.text('Ready to send with text or voice note');
            }

            // Attach and process an image file
            function attachImageFile(file) {
                if (!file || !file.type.match(/^image\//)) {
                    appendErrorMessage('Please select a valid image file (PNG, JPG, JPEG, WEBP).');
                    return;
                }

                if (file.size > 10 * 1024 * 1024) { // 10MB limit
                    appendErrorMessage('Image file is too large (maximum 10MB). Please select a smaller screenshot.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    const base64Data = e.target.result;
                    currentAttachedImage = {
                        base64: base64Data,
                        name: file.name || 'screenshot.png',
                        size: file.size,
                        ocrText: ''
                    };

                    // UI Updates
                    $imagePreviewThumb.attr('src', base64Data);
                    $imageFileName.text(file.name || 'screenshot.png');
                    $imageFileSize.text(formatFileSize(file.size));
                    $ocrStatusText.html('<span class="spinner-border spinner-border-sm me-1" style="width: 10px; height: 10px;"></span> Reading screen text...');
                    $imagePreviewBar.removeClass('d-none');

                    // Asynchronous client-side OCR extraction for ERP screens
                    if (typeof Tesseract !== 'undefined') {
                        Tesseract.recognize(base64Data, 'eng')
                            .then(function (result) {
                                if (currentAttachedImage && currentAttachedImage.base64 === base64Data) {
                                    const extracted = (result && result.data && result.data.text) ? result.data.text.trim() : '';
                                    currentAttachedImage.ocrText = extracted;
                                    $ocrStatusText.text(extracted ? 'Text recognized (' + extracted.length + ' chars)' : 'Ready to send');
                                }
                            })
                            .catch(function (err) {
                                console.warn('OCR note:', err);
                                if (currentAttachedImage && currentAttachedImage.base64 === base64Data) {
                                    $ocrStatusText.text('Ready to send');
                                }
                            });
                    } else {
                        $ocrStatusText.text('Ready to send with text or voice note');
                    }
                };
                reader.readAsDataURL(file);
            }

            // Helper to create a fresh SpeechRecognition instance for each recording session
            function createSpeechRecognizer() {
                const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                if (!SpeechRecognition) {
                    return null;
                }

                try {
                    const rec = new SpeechRecognition();
                    rec.lang = 'ta-IN'; // Tamil (India)
                    rec.continuous = true;
                    rec.interimResults = true;
                    rec.maxAlternatives = 1;

                    rec.onstart = function () {
                        speechSessionActive = true;
                        lastSpeechError = null;
                    };

                    rec.onresult = function (event) {
                        let finalPart = '';
                        let interimPart = '';
                        for (let i = 0; i < event.results.length; ++i) {
                            if (event.results[i].isFinal) {
                                finalPart += event.results[i][0].transcript + ' ';
                            } else {
                                interimPart += event.results[i][0].transcript;
                            }
                        }
                        const currentTranscript = (finalPart + interimPart).trim();
                        if (currentTranscript) {
                            capturedTamilTranscript = currentTranscript;
                            $liveTranscript.text(currentTranscript);
                            $messageInput.val(currentTranscript);
                            $charCount.text(currentTranscript.length + ' / 2000');
                        }
                    };

                    rec.onerror = function (event) {
                        console.warn('Speech recognition event:', event.error);
                        lastSpeechError = event.error;
                        if (event.error === 'not-allowed') {
                            stopVoiceRecording(false);
                            appendErrorMessage('Microphone access was denied. Please allow microphone permissions in your browser to record Tamil voice notes.');
                        }
                    };

                    rec.onend = function () {
                        speechSessionActive = false;
                        // Auto-restart if user is still actively recording voice and hasn't stopped
                        if (isRecordingVoice) {
                            try {
                                rec.start();
                            } catch (e) { }
                        }
                    };

                    return rec;
                } catch (e) {
                    console.error('Failed to create SpeechRecognition:', e);
                    return null;
                }
            }

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
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Format text with line breaks, safe tags, bolding and navigation highlights
            function formatMessageText(text) {
                let escaped = escapeHtml(text);
                escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                escaped = escaped.replace(/(ERP Module:|Page Name:|Page:|Menu Navigation:|Current Menu Navigation:|Next Expected Stage:|Next Menu Navigation:|Field Name:|Field Behavior:|Field Behavior \(புலத்தின் செயல்பாடு\):|Purpose:|Purpose \(நோக்கம்\):|Reason:|Reason \(காரணம்\):|Reason \/ Flow:|Explanation:|Explanation \(விளக்கம்\):|Answer:|Answer \(பதில்\):|Current Stage:|Related Workflow:|Flow:|Evidence:|Confidence:|Restrictions:|Restrictions \(கட்டுப்பாடு\):|URL:)/g, '<strong class="text-primary">$1</strong>');
                escaped = escaped.replace(/^(\s*)[*\-]\s+(.*)$/gm, '$1<span class="text-secondary">•</span> $2');
                escaped = escaped.replace(/\n/g, '<br>');
                return escaped;
            }

            // Check if string contains Tamil characters
            function hasTamilCharacters(text) {
                return /[\u0B80-\u0BFF]/.test(text);
            }

            // Append User Message to UI
            function appendUserMessage(text, options = {}) {
                const time = getCurrentTime();
                const formatted = formatMessageText(text);
                const msgId = 'user_msg_' + (++msgCounter);
                const isVoice = !!options.isVoice;
                const audioUrl = options.audioUrl || null;
                const imageSrc = options.image || options.imageUrl || null;

                let imageHtml = '';
                if (imageSrc) {
                    imageHtml = `
                        <div class="user-attached-image mb-2 text-end">
                            <img src="${imageSrc}" alt="Uploaded screenshot"
                                class="rounded border shadow-sm chat-uploaded-img"
                                style="max-width: 260px; max-height: 200px; object-fit: cover; cursor: pointer; display: inline-block; border-color: rgba(255,255,255,0.35) !important;"
                                title="Click to preview full image">
                        </div>
                    `;
                }

                let audioHtml = '';
                if (audioUrl) {
                    audioHtml = `
                        <div class="voice-audio-box my-2">
                            <audio controls src="${audioUrl}" class="w-100"></audio>
                        </div>
                    `;
                }

                let badgeHtml = '';
                if (isVoice) {
                    badgeHtml = `
                        <div class="voice-pill-original small mb-2">
                            <div class="d-flex align-items-center gap-1 text-white-50 fs-tiny mb-1">
                                <i class="ri ri-mic-fill text-warning"></i> <strong>Recorded Tamil Voice Note:</strong>
                            </div>
                            <div class="chat-text text-white" style="line-height: 1.5;">${formatted}</div>
                        </div>
                    `;
                } else if (hasTamilCharacters(text)) {
                    badgeHtml = `
                        <div class="voice-pill-original small mb-2">
                            <div class="d-flex align-items-center gap-1 text-white-50 fs-tiny mb-1">
                                <i class="ri ri-translate-2 text-warning"></i> <strong>Tamil Text Input:</strong>
                            </div>
                            <div class="chat-text text-white" style="line-height: 1.5;">${formatted}</div>
                        </div>
                    `;
                } else {
                    badgeHtml = `
                        <div class="chat-text" style="line-height: 1.6;">${formatted}</div>
                    `;
                }

                const html = `
                    <div class="d-flex justify-content-end mb-3 chat-bubble-row user-msg-row" id="${msgId}">
                        <div class="chat-bubble user-bubble shadow-sm p-3 rounded-3" style="max-width: 80%;">
                            <div class="fw-semibold text-white-50 mb-1 small d-flex align-items-center justify-content-end gap-1">
                                <span>You</span>
                                ${imageSrc ? '<span class="badge bg-white text-primary fs-tiny py-0 px-1 ms-1"><i class="ri ri-image-line"></i> Image</span>' : ''}
                                ${isVoice ? '<span class="badge bg-white text-primary fs-tiny py-0 px-1 ms-1"><i class="ri ri-mic-line"></i> Voice Note</span>' : (!imageSrc ? '<i class="ri ri-user-3-fill text-white ms-1"></i>' : '')}
                            </div>
                            ${imageHtml}
                            ${audioHtml}
                            ${badgeHtml}
                            <!-- Container for Translation pill once response returns -->
                            <div class="user-msg-translation mt-2" style="display: none;"></div>
                            <div class="text-start text-white-50 mt-1" style="font-size: 11px;">
                                ${time}
                            </div>
                        </div>
                    </div>
                `;
                $loadingRow.before(html);
                scrollToBottom();
                return msgId;
            }

            // Append AI Message to UI
            function appendAiMessage(text, options = {}) {
                const time = getCurrentTime();
                const formatted = formatMessageText(text);
                const isTamil = options.responseLanguage === 'ta';
                const englishOriginal = options.englishOriginal || null;
                const ragSources = options.ragSources || [];

                let badgeHtml = isTamil
                    ? '<span class="badge bg-label-info fs-tiny py-0 px-1 ms-1"><i class="ri ri-translate-2 me-1"></i>தமிழ் வெளியீடு (Tamil)</span>'
                    : '<span class="badge bg-label-secondary fs-tiny py-0 px-1 ms-1">English Output</span>';

                let ragSourcesHtml = '';
                if (ragSources.length > 0) {
                    const navSource = ragSources.find(s => s.menu_path && s.url);
                    ragSourcesHtml = `
                                            <div class="mt-2 pt-2 border-top d-flex flex-wrap gap-1 align-items-center">
                                                ${navSource ? `
                                                    <a href="${escapeHtml(navSource.url)}" class="badge bg-label-primary fs-tiny py-1 px-2 text-decoration-none d-inline-flex align-items-center gap-1" style="cursor: pointer;">
                                                        <i class="ri ri-compass-3-line"></i> ${escapeHtml(navSource.menu_path)} &nbsp;<span class="text-decoration-underline fw-bold">Open Screen →</span>
                                                    </a>
                                                ` : ''}
                                            </div>
                                        `;
                }

                let originalToggleHtml = '';
                if (englishOriginal && isTamil) {
                    originalToggleHtml = `
                                            <div class="mt-3 pt-2 border-top">
                                                <button type="button" class="btn btn-xs btn-outline-secondary toggle-eng-btn py-1 px-2 d-inline-flex align-items-center gap-1 shadow-none" style="font-size: 11px;">
                                                    <i class="ri ri-global-line"></i> <span class="toggle-eng-text">Show English Original / ஆங்கில வடிவம்</span>
                                                </button>
                                                <div class="english-original-box mt-2 p-2 bg-light rounded text-secondary small d-none" style="line-height: 1.5; word-break: break-word;">
                                                    ${formatMessageText(englishOriginal)}
                                                </div>
                                            </div>
                                        `;
                }

                const html = `
                                        <div class="d-flex justify-content-start mb-3 chat-bubble-row ai-msg-row">
                                            <div class="avatar avatar-sm rounded-circle bg-label-primary me-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                <i class="ri ri-robot-2-line text-primary"></i>
                                            </div>
                                            <div class="chat-bubble ai-bubble bg-white text-dark shadow-sm border p-3 rounded-3" style="max-width: 80%;">
                                                <div class="fw-semibold text-primary mb-1 small d-flex align-items-center gap-1">
                                                    <i class="ri ri-sparkling-fill text-warning"></i> ERP Flow Navigator
                                                    ${badgeHtml}
                                                </div>
                                                <div class="chat-text" style="line-height: 1.6;">
                                                    ${formatted}
                                                </div>
                                                ${ragSourcesHtml}
                                                ${originalToggleHtml}
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
                    $voiceRecordBtn.prop('disabled', true);
                    $imageUploadBtn.prop('disabled', true);
                    $messageInput.prop('disabled', true);
                    $sendIcon.removeClass('ri-send-plane-2-fill').addClass('ri-loader-4-line spinner-border-sm');
                    scrollToBottom();
                } else {
                    $loadingRow.addClass('d-none');
                    $sendBtn.prop('disabled', false);
                    $voiceRecordBtn.prop('disabled', false);
                    $imageUploadBtn.prop('disabled', false);
                    $messageInput.prop('disabled', false);
                    $sendIcon.removeClass('ri-loader-4-line spinner-border-sm').addClass('ri-send-plane-2-fill');
                    $messageInput.focus();
                }
            }

            // Voice Recording Controls
            function startVoiceRecording() {
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    appendErrorMessage('Your browser does not support audio recording. Please use Google Chrome, Microsoft Edge, or Safari.');
                    return;
                }

                // Reset state for new recording session
                lastSpeechError = null;
                capturedTamilTranscript = '';
                audioChunks = [];
                voiceSeconds = 0;
                isRecordingVoice = true;

                // Stop any lingering recognition instance cleanly
                if (recognition) {
                    try {
                        recognition.onend = null;
                        recognition.abort();
                    } catch (e) { }
                    recognition = null;
                }

                // Initialize fresh recognizer instance for this session and start synchronously on user gesture
                recognition = createSpeechRecognizer();
                if (recognition) {
                    try {
                        recognition.start();
                    } catch (e) {
                        console.warn('Recognition start exception:', e);
                    }
                }

                // Acquire microphone stream for audio playback and recording
                navigator.mediaDevices.getUserMedia({
                    audio: {
                        echoCancellation: true,
                        noiseSuppression: true,
                        autoGainControl: true
                    }
                })
                    .then(function (stream) {
                        if (!isRecordingVoice) {
                            stream.getTracks().forEach(track => track.stop());
                            return;
                        }

                        mediaStream = stream;

                        // Start MediaRecorder
                        try {
                            mediaRecorder = new MediaRecorder(stream);
                        } catch (e) {
                            try {
                                mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });
                            } catch (err) {
                                mediaRecorder = null;
                            }
                        }

                        if (mediaRecorder) {
                            mediaRecorder.ondataavailable = function (e) {
                                if (e.data && e.data.size > 0) {
                                    audioChunks.push(e.data);
                                }
                            };
                            mediaRecorder.start(200);
                        }

                        // If recognition didn't start yet (e.g. pending permission), trigger start
                        if (recognition && !speechSessionActive) {
                            try {
                                recognition.start();
                            } catch (e) { }
                        }

                        // UI Updates
                        $voiceRecordingBar.removeClass('d-none');
                        $voiceRecordBtn.addClass('voice-btn-active');
                        $micIcon.removeClass('ri-mic-line').addClass('ri-mic-fill');
                        $voiceTimer.text('00:00');
                        $liveTranscript.text('Listening in Tamil (தமிழில் பேசவும்)...');

                        voiceTimerInterval = setInterval(function () {
                            voiceSeconds++;
                            const mins = String(Math.floor(voiceSeconds / 60)).padStart(2, '0');
                            const secs = String(voiceSeconds % 60).padStart(2, '0');
                            $voiceTimer.text(`${mins}:${secs}`);
                        }, 1000);
                    })
                    .catch(function (err) {
                        console.error('Microphone error:', err);
                        isRecordingVoice = false;
                        if (recognition) {
                            try { recognition.abort(); } catch (e) { }
                            recognition = null;
                        }
                        $voiceRecordingBar.addClass('d-none');
                        $voiceRecordBtn.removeClass('voice-btn-active');
                        $micIcon.removeClass('ri-mic-fill').addClass('ri-mic-line');
                        appendErrorMessage('Microphone access was not granted. Please check your browser microphone permissions.');
                    });
            }

            function stopVoiceRecording(shouldSend = false) {
                if (!isRecordingVoice) return;
                isRecordingVoice = false;

                clearInterval(voiceTimerInterval);
                $voiceRecordingBar.addClass('d-none');
                $voiceRecordBtn.removeClass('voice-btn-active');
                $micIcon.removeClass('ri-mic-fill').addClass('ri-mic-line');

                const activeRec = recognition;
                if (activeRec) {
                    activeRec.onend = null; // Prevent auto-restarting
                    try {
                        activeRec.stop();
                    } catch (e) { }
                }

                if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                    try {
                        mediaRecorder.requestData();
                        mediaRecorder.stop();
                    } catch (e) { }
                }

                if (!shouldSend) {
                    if (mediaStream) {
                        mediaStream.getTracks().forEach(track => track.stop());
                        mediaStream = null;
                    }
                    audioChunks = [];
                    capturedTamilTranscript = '';
                    recognition = null;
                    return;
                }

                $liveTranscript.text('Processing Tamil voice note...');

                const finalizeAndSend = function () {
                    // Release microphone stream once recording and recognition processing are complete
                    if (mediaStream) {
                        mediaStream.getTracks().forEach(track => track.stop());
                        mediaStream = null;
                    }

                    const audioBlob = (audioChunks.length > 0) ? new Blob(audioChunks, { type: 'audio/webm' }) : null;
                    const audioUrl = audioBlob ? URL.createObjectURL(audioBlob) : null;

                    const proceedWithTranscript = function (base64Audio) {
                        const finalMessage = (capturedTamilTranscript || $messageInput.val()).trim();

                        // Reset session audio and transcript
                        audioChunks = [];
                        capturedTamilTranscript = '';
                        recognition = null;

                        if (!finalMessage) {
                            const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
                            if (!SpeechRec) {
                                appendErrorMessage('Tamil voice recorded, but this browser does not support Speech Recognition. Please use Google Chrome or Microsoft Edge, or type your question in the text box.');
                            } else if (lastSpeechError === 'network') {
                                appendErrorMessage('Tamil voice recorded, but speech recognition could not reach Google speech services (Network Error). Please check your internet connection, or type your question in the box below.');
                            } else if (lastSpeechError === 'service-not-allowed') {
                                appendErrorMessage('Tamil voice recorded, but speech recognition is not permitted on this address (requires HTTPS or localhost). Please type your question in the box below.');
                            } else {
                                appendErrorMessage('No Tamil speech was detected. Please try recording again while speaking clearly into your microphone, or type your question in the box below.');
                            }
                            $messageInput.focus();
                            return;
                        }

                        sendMessage(finalMessage, true, base64Audio, audioUrl);
                    };

                    if (audioBlob && audioBlob.size > 0) {
                        const reader = new FileReader();
                        reader.onloadend = function () {
                            proceedWithTranscript(reader.result);
                        };
                        reader.readAsDataURL(audioBlob);
                    } else {
                        proceedWithTranscript(null);
                    }
                };

                // If transcript has already been captured during speech, finalize quickly
                if (capturedTamilTranscript || $messageInput.val().trim()) {
                    setTimeout(finalizeAndSend, 350);
                } else {
                    // Give recognizer up to 1200ms to return final speech recognition result
                    let hasFinalized = false;
                    const fallbackTimer = setTimeout(function () {
                        if (!hasFinalized) {
                            hasFinalized = true;
                            finalizeAndSend();
                        }
                    }, 1200);

                    if (activeRec) {
                        activeRec.onend = function () {
                            if (!hasFinalized) {
                                hasFinalized = true;
                                clearTimeout(fallbackTimer);
                                setTimeout(finalizeAndSend, 200);
                            }
                        };
                    }
                }
            }

            // Send Message Handler
            function sendMessage(customMessage = null, isVoice = false, audioBase64 = null, localAudioUrl = null) {
                if (isProcessing) return;

                const inputMsg = (customMessage !== null ? customMessage : $messageInput.val()).trim();
                const attachedImage = currentAttachedImage;

                // User must provide either text/voice or an attached image
                if (!inputMsg && !attachedImage) {
                    $messageInput.focus();
                    return;
                }

                const finalPrompt = inputMsg || 'Please analyze this uploaded ERP image/screenshot and explain the screen, fields, or next workflow step.';

                // Display user message in UI and capture element id
                const msgId = appendUserMessage(finalPrompt, {
                    isVoice: isVoice,
                    audioUrl: localAudioUrl,
                    image: attachedImage ? attachedImage.base64 : null
                });

                // Clear input, staged image, and reset char count
                $messageInput.val('');
                $charCount.text('0 / 2000');
                if (attachedImage) {
                    clearAttachedImage();
                }

                // Set UI loading state
                setLoading(true);

                // Prepare request payload with recent history and image data
                const payload = {
                    message: finalPrompt,
                    is_voice: isVoice,
                    audio: audioBase64,
                    image: attachedImage ? attachedImage.base64 : null,
                    image_name: attachedImage ? attachedImage.name : null,
                    image_text: attachedImage ? attachedImage.ocrText : null,
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
                            const translatedMsg = response.translated_message;
                            const isTranslated = response.is_translated;

                            // If text was translated, display translation pill in user bubble
                            if (isTranslated && translatedMsg) {
                                const $transContainer = $(`#${msgId}`).find('.user-msg-translation');
                                const safeTranslation = escapeHtml(translatedMsg);
                                $transContainer.html(`
                                    <div class="voice-pill-translation small">
                                        <div class="d-flex align-items-center gap-1 text-white-50 fs-tiny mb-1">
                                            <i class="ri ri-translate-2 text-warning"></i> <strong>Translated to English (Input for LLM):</strong>
                                        </div>
                                        <div class="text-white fw-semibold" style="line-height: 1.5;">${safeTranslation}</div>
                                    </div>
                                `).slideDown(200);
                            }

                            // Append AI response (Tamil or English based on user input)
                            appendAiMessage(aiReply, {
                                responseLanguage: response.response_language || 'en',
                                englishOriginal: response.english_message || null,
                                ragSources: response.rag_sources || [],
                                ragIntent: response.rag_intent || 'general'
                            });

                            // Update session conversation history (store English query for optimal LLM reasoning)
                            conversationHistory.push({
                                role: 'user',
                                content: isTranslated ? translatedMsg : finalPrompt
                            });
                            conversationHistory.push({
                                role: 'assistant',
                                content: response.english_message || aiReply
                            });
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

            // Image Upload Trigger (click button opens file browser)
            $imageUploadBtn.on('click', function () {
                $imageFileInput.trigger('click');
            });

            // Handle file selected from file input
            $imageFileInput.on('change', function () {
                if (this.files && this.files[0]) {
                    attachImageFile(this.files[0]);
                }
            });

            // Discard attached image
            $removeImageBtn.on('click', function () {
                clearAttachedImage();
            });

            // Clicking preview thumbnail opens lightbox modal
            $imagePreviewThumb.on('click', function () {
                if (currentAttachedImage) {
                    $imageLightboxTitle.html('<i class="ri ri-image-line me-1 text-primary"></i> ' + escapeHtml(currentAttachedImage.name));
                    $imageLightboxImg.attr('src', currentAttachedImage.base64);
                    $imageLightboxModal.modal('show');
                }
            });

            // Clicking any uploaded image in chat opens lightbox modal
            $(document).on('click', '.chat-uploaded-img', function () {
                const src = $(this).attr('src');
                $imageLightboxTitle.html('<i class="ri ri-image-line me-1 text-primary"></i> Attached Image / Screenshot');
                $imageLightboxImg.attr('src', src);
                $imageLightboxModal.modal('show');
            });

            // Clipboard Paste Support (Ctrl+V allows pasting screenshot directly)
            $(document).on('paste', function (e) {
                const clipboardData = (e.originalEvent || e).clipboardData;
                if (!clipboardData || !clipboardData.items) return;

                for (let i = 0; i < clipboardData.items.length; i++) {
                    const item = clipboardData.items[i];
                    if (item.type.indexOf('image') !== -1) {
                        const file = item.getAsFile();
                        if (file) {
                            attachImageFile(file);
                            break;
                        }
                    }
                }
            });

            // Drag & Drop Support on chat card
            const $chatCard = $('.chat-card');
            $chatCard.on('dragover dragenter', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('drag-over');
            });

            $chatCard.on('dragleave dragend', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('drag-over');
            });

            $chatCard.on('drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('drag-over');
                const dt = (e.originalEvent || e).dataTransfer;
                if (dt && dt.files && dt.files.length > 0) {
                    const file = dt.files[0];
                    if (file.type.match(/^image\//)) {
                        attachImageFile(file);
                    }
                }
            });

            // Voice Record Button Click (Toggle)
            $voiceRecordBtn.on('click', function () {
                if (isRecordingVoice) {
                    stopVoiceRecording(true);
                } else {
                    startVoiceRecording();
                }
            });

            // Cancel Voice Note
            $cancelVoiceBtn.on('click', function () {
                stopVoiceRecording(false);
            });

            // Send Voice Note
            $sendVoiceBtn.on('click', function () {
                stopVoiceRecording(true);
            });

            // Form submit
            $('#chatForm').on('submit', function (e) {
                e.preventDefault();
                if (isRecordingVoice) {
                    stopVoiceRecording(true);
                } else {
                    sendMessage();
                }
            });

            // Quick prompt button click handler
            $(document).on('click', '.quick-prompt-btn', function () {
                const query = $(this).data('query');
                if (query && !isProcessing) {
                    if (isRecordingVoice) {
                        stopVoiceRecording(false);
                    }
                    sendMessage(query);
                }
            });

            // Toggle English original view for Tamil AI responses
            $(document).on('click', '.toggle-eng-btn', function () {
                const $btn = $(this);
                const $box = $btn.siblings('.english-original-box');
                $box.toggleClass('d-none');
                const isHidden = $box.hasClass('d-none');
                $btn.find('.toggle-eng-text').text(isHidden ? 'Show English Original / ஆங்கில வடிவம்' : 'Hide English Original / மறைக்கவும்');
            });

            // Enter key sends, Shift+Enter creates newline
            $messageInput.on('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (isRecordingVoice) {
                        stopVoiceRecording(true);
                    } else {
                        sendMessage();
                    }
                }
            });

            // Escape cancels recording
            $(document).on('keydown', function (e) {
                if (e.key === 'Escape' && isRecordingVoice) {
                    stopVoiceRecording(false);
                }
            });

            // Character counter & auto-expand height
            $messageInput.on('input', function () {
                const len = $(this).val().length;
                $charCount.text(len + ' / 2000');
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            });

            // Clear Chat
            $clearBtn.on('click', function () {
                if (conversationHistory.length === 0 && $('.user-msg-row').length === 0 && !currentAttachedImage) {
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
                clearAttachedImage();
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