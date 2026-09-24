@extends('layouts.common')
@section('title', 'ERP Flow Navigator - ' . (env('WEBSITE_NAME') ?? 'Nachias ERP'))

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row justify-content-center">
            <div class="col-12">

                <!-- Main Chat Wrapper Card (ChatGPT / Gemini Style Layout) -->
                <div class="card shadow-sm border-0 chat-card"
                    style="height: calc(100vh - 170px); min-height: 700px; max-height: 880px; display: flex; flex-direction: column; overflow: hidden;">

                    <div class="d-flex flex-grow-1 h-100 position-relative" style="overflow: hidden;">

                        <!-- Left Sidebar: Chat History (ChatGPT / Gemini Style) -->
                        <div class="chat-sidebar bg-white border-end d-flex flex-column" id="chatSidebar">
                            <!-- Top: New Chat & Search Input -->
                            <div class="p-3 border-bottom bg-white">
                                <button type="button"
                                    class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 shadow-sm py-2 rounded-3"
                                    id="sidebarNewChatBtn" title="Start a fresh conversation (புதிய உரையாடல்)">
                                    <i class="ri ri-add-line fs-5"></i>
                                    <span class="fw-semibold">New Chat</span>
                                </button>
                                <div class="mt-2 position-relative">
                                    <input type="text" class="form-control form-control-sm ps-4 rounded-3 border bg-light"
                                        id="chatSearchInput" placeholder="Search chats..." style="font-size: 13px;">
                                </div>
                            </div>

                            <!-- Scrollable Chat History List -->
                            <div class="chat-history-list flex-grow-1 overflow-auto p-2" id="chatHistoryList">
                                <!-- Dynamic Chat Session Items Grouped By Date Injected Here -->
                            </div>

                            <!-- Bottom: Session Stats & Clear All -->
                            <div class="p-2 px-3 border-top bg-light d-flex align-items-center justify-content-between text-muted"
                                style="font-size: 12px;">
                                <span id="sessionCountText" class="d-flex align-items-center gap-1">
                                    <i class="ri ri-history-line"></i> <span id="chatCountNum">0</span> chats
                                </span>
                                <button type="button"
                                    class="btn btn-xs btn-outline-danger py-1 px-2 d-flex align-items-center gap-1 shadow-none"
                                    id="clearAllHistoryBtn" title="Delete all saved conversations" style="font-size: 11px;">
                                    <i class="ri ri-delete-bin-line"></i> Clear All
                                </button>
                            </div>
                        </div>

                        <!-- Overlay Backdrop for Mobile View -->
                        <div class="chat-sidebar-backdrop" id="chatSidebarBackdrop"></div>

                        <!-- Right Main Area: Active Chat Window -->
                        <div class="chat-main-area d-flex flex-column flex-grow-1 bg-white h-100 position-relative"
                            style="min-width: 0;">

                            <!-- Chat Header -->
                            <div
                                class="card-header border-bottom py-2 px-3 px-sm-4 d-flex align-items-center justify-content-between flex-wrap gap-2 bg-white">
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-secondary"
                                        id="toggleSidebarBtn" title="Toggle Chat History Sidebar">
                                        <i class="ri ri-side-bar-line fs-5"></i>
                                    </button>
                                    <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 38px; height: 38px;">
                                        <i class="ri ri-robot-2-line fs-5 text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2 text-truncate"
                                                id="activeChatHeaderTitle" style="max-width: 320px;">
                                                ERP Flow Navigator AI
                                            </h6>
                                            <span class="badge bg-label-success fs-tiny fw-normal px-2 py-0">
                                                <i class="ri ri-circle-fill me-1" style="font-size: 7px;"></i>
                                                {{ $isAvailable ? 'Online' : 'Offline' }}
                                            </span>
                                            <span
                                                class="badge bg-label-info fs-tiny fw-normal px-2 py-0 d-none d-sm-inline-flex"
                                                title="Tamil voice notes automatically translated to English">
                                                <i class="ri ri-mic-line me-1" style="font-size: 9px;"></i>
                                                Tamil Voice
                                            </span>
                                        </div>
                                        <small class="text-muted d-block" id="activeChatSubtitle" style="font-size: 11px;">
                                            Nachias ERP Workflow Analysis & Navigation
                                        </small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button"
                                        class="btn btn-sm btn-primary d-flex align-items-center gap-1 shadow-sm"
                                        id="headerNewChatBtn" title="Start a fresh chat (புதிய உரையாடல்)">
                                        <i class="ri ri-add-line"></i>
                                        <span class="d-none d-sm-inline">New Chat</span>
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 shadow-sm"
                                        id="clearChatBtn" title="Clear current conversation messages">
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
                                        Ollama does not appear to be responding on
                                        <code>{{ config('services.ollama.url') }}</code>.
                                        Please make sure the local Ollama process is running.
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>

                            <!-- Messages Area -->
                            <div class="card-body p-3 p-sm-4 chat-messages-container flex-grow-1" id="chatMessagesArea"
                                style="overflow-y: auto; background-color: #f9fbfd;">

                                <!-- Welcome AI Message (shown on fresh / empty chat) -->
                                <div class="d-flex justify-content-start mb-3 chat-bubble-row ai-msg-row"
                                    id="welcomeMessage">
                                    <div class="avatar avatar-sm rounded-circle bg-label-primary me-2 flex-shrink-0 d-flex align-items-center justify-content-center"
                                        style="width: 36px; height: 36px;">
                                        <i class="ri ri-robot-2-line text-primary"></i>
                                    </div>
                                    <div class="chat-bubble ai-bubble bg-white text-dark shadow-sm border p-3 rounded-3"
                                        style="max-width: 85%;">
                                        <div class="fw-semibold text-primary mb-1 small d-flex align-items-center gap-1">
                                            <i class="ri ri-sparkling-fill text-warning"></i> ERP Flow Navigator
                                        </div>
                                        <div class="chat-text" style="line-height: 1.6; word-break: break-word;">
                                            Hello! I am your <strong>ERP Flow Navigator AI</strong> assistant.<br>
                                            You can ask questions in <strong>English</strong> or record a <strong>Tamil
                                                Voice Note (குரல் பதிவு)</strong> — your Tamil speech will be automatically
                                            translated to English and answered by the AI.<br>
                                            <strong>Click a quick question to test:</strong>
                                            <div class="d-flex flex-wrap gap-2 mt-2" id="quickPrompts">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary quick-prompt-btn"
                                                    data-query="What is the next step after a Purchase Order is created?">
                                                    <i class="ri ri-shopping-cart-line me-1"></i> Next step after Purchase
                                                    Order?
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary quick-prompt-btn"
                                                    data-query="What happens after a Sales Order is confirmed?">
                                                    <i class="ri ri-order-play-line me-1"></i> Next step after Sales Order?
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary quick-prompt-btn"
                                                    data-query="What is the next stage after Goods Receipt (GRN)?">
                                                    <i class="ri ri-inbox-archive-line me-1"></i> Next stage after Goods
                                                    Receipt
                                                    (GRN)?
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-info quick-prompt-btn"
                                                    data-query="கொள்முதல் ஆணை உருவான பிறகு அடுத்த கட்டம் என்ன?">
                                                    <i class="ri ri-translate-2 me-1"></i> தமிழ்: கொள்முதல் ஆணைக்கு அடுத்த
                                                    கட்டம்?
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-info quick-prompt-btn"
                                                    data-query="விற்பனை ஆணை உறுதி செய்யப்பட்ட பிறகு என்ன நடக்கும்?">
                                                    <i class="ri ri-translate-2 me-1"></i> தமிழ்: விற்பனை ஆணைக்கு அடுத்த
                                                    படி?
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
                                    <div
                                        class="chat-bubble ai-bubble bg-white text-dark shadow-sm border px-3 py-2 rounded-3">
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
                                                class="rounded border shadow-sm"
                                                style="width: 44px; height: 44px; object-fit: cover; cursor: pointer;"
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
                                                <span id="ocrStatusText" class="text-secondary fst-italic">Ready to send
                                                    with
                                                    text or voice note</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-icon"
                                            id="removeImageBtn" title="Remove attached image">
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
                                            style="height: 52px; min-width: 52px;"
                                            title="Upload Image or Screenshot (படத்தைப் பதிவேற்றவும்)">
                                            <i class="ri ri-image-add-line fs-5" id="imageIcon"></i>
                                        </button>
                                        <input type="file" id="imageFileInput"
                                            accept="image/png, image/jpeg, image/jpg, image/webp" class="d-none">

                                        <!-- Tamil Voice Record Button -->
                                        <button type="button" id="voiceRecordBtn"
                                            class="btn btn-outline-primary d-flex align-items-center justify-content-center px-3"
                                            style="height: 52px; min-width: 52px;"
                                            title="Record Tamil Voice Note (தமிழில் குரல் பதிவு)">
                                            <i class="ri ri-mic-line fs-5" id="micIcon"></i>
                                        </button>
                                        <div class="flex-grow-1 position-relative">
                                            <textarea id="userMessageInput" class="form-control border shadow-none" rows="2"
                                                placeholder="Ask doubt in English or Tamil, or upload screenshot and speak voice note (Enter to send)..."
                                                style="resize: none; font-size: 14px; min-height: 52px; max-height: 120px;"></textarea>
                                        </div>
                                        <button type="submit" id="sendBtn"
                                            class="btn btn-primary d-flex align-items-center justify-content-center gap-1 px-4"
                                            style="height: 52px; min-width: 100px;">
                                            <i class="ri ri-send-plane-2-fill fs-5" id="sendIcon"></i>
                                            <span class="d-none d-sm-inline fw-semibold ms-1">Send</span>
                                        </button>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                        <small class="text-muted d-flex align-items-center gap-1 flex-wrap"
                                            style="font-size: 12px;">
                                            <i class="ri ri-information-line"></i>
                                            <span>Press <strong>Enter</strong> to send, click <i
                                                    class="ri ri-image-add-line text-primary"></i> to attach
                                                <strong>Image</strong> (or paste <strong>Ctrl+V</strong>), or click <i
                                                    class="ri ri-mic-line text-primary"></i> for <strong>Tamil Voice
                                                    Note</strong></span>
                                        </small>
                                        <small class="text-muted" id="charCount" style="font-size: 11px;">0 / 2000</small>
                                    </div>
                                </form>
                            </div>

                        </div>

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
                    <img id="imageLightboxImg" src="" alt="Screenshot Full View" class="img-fluid rounded"
                        style="max-height: 80vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <style>
        /* Universal icon styling to ensure all Remix Icons render properly */
        [class^="ri-"],
        [class*=" ri-"],
        .ri {
            display: inline-block;
            width: 1em;
            height: 1em;
            background-color: currentColor;
            -webkit-mask-image: var(--svg);
            mask-image: var(--svg);
            -webkit-mask-repeat: no-repeat;
            mask-repeat: no-repeat;
            -webkit-mask-size: 100% 100%;
            mask-size: 100% 100%;
            vertical-align: -0.15em;
            line-height: 1;
        }

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

        /* Sidebar Styling (ChatGPT & Gemini Style) */
        .chat-sidebar {
            width: 290px;
            min-width: 290px;
            max-width: 290px;
            transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1), left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 10;
        }

        .chat-card.sidebar-collapsed .chat-sidebar {
            margin-left: -290px;
        }

        .chat-history-list::-webkit-scrollbar {
            width: 4px;
        }

        .chat-history-list::-webkit-scrollbar-thumb {
            background-color: #e0e0e0;
            border-radius: 4px;
        }

        .chat-group-header {
            font-size: 11px;
            font-weight: 700;
            color: #a1acb8;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 10px 8px 4px 8px;
        }

        .chat-history-item {
            padding: 8px 10px;
            border-radius: 8px;
            margin-bottom: 3px;
            transition: background-color 0.15s ease, border-color 0.15s ease;
            cursor: pointer;
            border: 1px solid transparent;
            position: relative;
            user-select: none;
        }

        .chat-history-item:hover {
            background-color: #f4f3f9;
        }

        .chat-history-item.active {
            background-color: #f2eefa !important;
            border-color: #dcd2f8 !important;
        }

        .chat-history-item .chat-item-title {
            font-size: 13px;
            color: #4b465c;
            font-weight: 500;
            line-height: 1.35;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-history-item.active .chat-item-title {
            color: #8c57ff;
            font-weight: 600;
        }

        .chat-history-item .chat-item-meta {
            font-size: 11px;
            color: #a1acb8;
        }

        .chat-history-item .chat-item-actions {
            opacity: 0;
            transition: opacity 0.15s ease;
        }

        .chat-history-item:hover .chat-item-actions,
        .chat-history-item.active .chat-item-actions {
            opacity: 1;
        }

        .chat-item-action-btn {
            width: 24px;
            height: 24px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            color: #a1acb8;
            background: transparent;
            border: none;
            transition: all 0.15s ease;
        }

        .chat-item-action-btn:hover {
            background-color: #e9ecef;
            color: #566a7f;
        }

        .chat-item-action-btn.delete-btn:hover {
            background-color: #ffe0db;
            color: #ff3e1d;
        }

        @media (max-width: 991.98px) {
            .chat-sidebar {
                position: absolute;
                top: 0;
                left: -310px;
                bottom: 0;
                width: 290px;
                z-index: 1050;
                box-shadow: 0 0 25px rgba(0, 0, 0, 0.18);
                background: #ffffff;
            }

            .chat-card.mobile-sidebar-open .chat-sidebar {
                left: 0 !important;
                margin-left: 0 !important;
            }

            .chat-sidebar-backdrop {
                display: none;
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.35);
                z-index: 1045;
                backdrop-filter: blur(1px);
            }

            .chat-card.mobile-sidebar-open .chat-sidebar-backdrop {
                display: block !important;
            }

            .chat-history-item .chat-item-actions {
                opacity: 1;
            }
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
            // UI elements
            const $chatArea = $('#chatMessagesArea');
            const $welcomeMsg = $('#welcomeMessage');
            const $messageInput = $('#userMessageInput');
            const $sendBtn = $('#sendBtn');
            const $sendIcon = $('#sendIcon');
            const $loadingRow = $('#loadingBubbleRow');
            const $clearBtn = $('#clearChatBtn');
            const $charCount = $('#charCount');
            const $chatCard = $('.chat-card');

            // Sidebar & Navigation elements
            const $chatSidebar = $('#chatSidebar');
            const $chatHistoryList = $('#chatHistoryList');
            const $sidebarNewChatBtn = $('#sidebarNewChatBtn');
            const $headerNewChatBtn = $('#headerNewChatBtn');
            const $toggleSidebarBtn = $('#toggleSidebarBtn');
            const $chatSidebarBackdrop = $('#chatSidebarBackdrop');
            const $chatSearchInput = $('#chatSearchInput');
            const $chatCountNum = $('#chatCountNum');
            const $clearAllHistoryBtn = $('#clearAllHistoryBtn');
            const $activeChatHeaderTitle = $('#activeChatHeaderTitle');
            const $activeChatSubtitle = $('#activeChatSubtitle');

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

            // Chat Session Management (ChatGPT / Gemini style)
            const STORAGE_KEY = 'erp_chat_sessions_{{ auth()->id() ?? 'guest' }}';
            const serverSavedSessions = @json($savedSessions ?? []);
            let chatSessions = [];
            let currentSessionId = null;
            let conversationHistory = []; // In-memory LLM conversation history for current session
            let isProcessing = false;
            let msgCounter = 0;

            // Helper: Safe LocalStorage Save with quota protection
            function safeSaveSessions() {
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(chatSessions));
                } catch (e) {
                    console.warn('LocalStorage save warning, trying to optimize storage...', e);
                    try {
                        const pruned = JSON.parse(JSON.stringify(chatSessions));
                        pruned.forEach(s => {
                            if (s.id !== currentSessionId && s.messages) {
                                s.messages.forEach(m => {
                                    if (m.image && m.image.length > 40000) {
                                        m.image = null;
                                    }
                                    if (m.audioData && m.audioData.length > 40000) {
                                        m.audioData = null;
                                    }
                                });
                            }
                        });
                        localStorage.setItem(STORAGE_KEY, JSON.stringify(pruned));
                    } catch (err2) {
                        console.error('LocalStorage completely full:', err2);
                    }
                }
            }

            // Sync any existing localStorage sessions to database if DB was empty
            function syncLocalSessionsToServer(sessions) {
                if (!Array.isArray(sessions) || sessions.length === 0) return;
                sessions.forEach(s => {
                    if (s && s.id && s.messages && s.messages.length > 0) {
                        $.ajax({
                            url: '{{ route("chatbot.sessions.save") }}',
                            type: 'POST',
                            data: JSON.stringify({
                                session_id: s.id,
                                title: s.title || 'New Chat',
                                messages: s.messages || [],
                                conversation_history: s.conversationHistory || []
                            }),
                            contentType: 'application/json; charset=utf-8',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                    }
                });
            }

            // Helper: Load all sessions from Database (with LocalStorage cache fallback)
            function loadAllSessions() {
                if (Array.isArray(serverSavedSessions) && serverSavedSessions.length > 0) {
                    chatSessions = serverSavedSessions;
                    safeSaveSessions();
                    return;
                }

                try {
                    const raw = localStorage.getItem(STORAGE_KEY);
                    if (raw) {
                        const parsed = JSON.parse(raw);
                        if (Array.isArray(parsed) && parsed.length > 0) {
                            chatSessions = parsed;
                            // Sync local sessions to database
                            syncLocalSessionsToServer(parsed);
                        }
                    }
                } catch (e) {
                    console.warn('Error reading saved chat sessions:', e);
                    chatSessions = [];
                }
            }

            // Helper: Date grouping label (Today, Yesterday, Previous 7 Days, Older)
            function getGroupLabel(timestamp) {
                const now = new Date();
                const d = new Date(timestamp);
                const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();
                const yesterdayStart = todayStart - 86400000;
                const past7DaysStart = todayStart - (7 * 86400000);
                const past30DaysStart = todayStart - (30 * 86400000);

                const t = d.getTime();
                if (t >= todayStart) return 'Today';
                if (t >= yesterdayStart) return 'Yesterday';
                if (t >= past7DaysStart) return 'Previous 7 Days';
                if (t >= past30DaysStart) return 'Previous 30 Days';
                return 'Older';
            }

            // Helper: Format Time for Chat items
            function formatSessionTime(timestamp) {
                if (!timestamp) return '';
                const d = new Date(timestamp);
                return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }

            // Render Sidebar History List
            function renderHistoryList(searchTerm = '') {
                const filter = (searchTerm || '').toLowerCase().trim();
                let filteredSessions = chatSessions.slice();

                if (filter) {
                    filteredSessions = filteredSessions.filter(s => {
                        const matchTitle = (s.title || '').toLowerCase().includes(filter);
                        const matchMsg = s.messages && s.messages.some(m => (m.text || '').toLowerCase().includes(filter));
                        return matchTitle || matchMsg;
                    });
                }

                // Sort newest updatedAt first
                filteredSessions.sort((a, b) => (b.updatedAt || b.createdAt || 0) - (a.updatedAt || a.createdAt || 0));

                $chatCountNum.text(chatSessions.length);
                $chatHistoryList.empty();

                if (filteredSessions.length === 0) {
                    if (filter) {
                        $chatHistoryList.html(`
                                    <div class="text-center text-muted p-4 small">
                                        <i class="ri ri-search-line fs-3 d-block mb-1 text-secondary"></i>
                                        No chats match "<strong>${escapeHtml(filter)}</strong>"
                                    </div>
                                `);
                    } else {
                        $chatHistoryList.html(`
                                    <div class="text-center text-muted p-4 small">
                                        <i class="ri ri-chat-voice-line fs-3 d-block mb-1 text-primary opacity-50"></i>
                                        No chat history yet.<br>
                                        Start asking questions or test a quick prompt!
                                    </div>
                                `);
                    }
                    return;
                }

                // Group by date
                const groups = {};
                filteredSessions.forEach(s => {
                    const group = getGroupLabel(s.updatedAt || s.createdAt || Date.now());
                    if (!groups[group]) groups[group] = [];
                    groups[group].push(s);
                });

                const groupOrder = ['Today', 'Yesterday', 'Previous 7 Days', 'Previous 30 Days', 'Older'];

                groupOrder.forEach(grp => {
                    if (groups[grp] && groups[grp].length > 0) {
                        $chatHistoryList.append(`<div class="chat-group-header">${grp}</div>`);
                        groups[grp].forEach(s => {
                            const isActive = (s.id === currentSessionId);
                            const msgCount = (s.messages ? s.messages.length : 0);
                            const timeStr = formatSessionTime(s.updatedAt || s.createdAt);

                            const itemHtml = `
                                        <div class="chat-history-item d-flex align-items-center justify-content-between ${isActive ? 'active' : ''}"
                                            data-session-id="${s.id}">
                                            <div class="d-flex align-items-center gap-2 overflow-hidden flex-grow-1 chat-item-click-area"
                                                style="cursor: pointer;">
                                                <i class="ri ri-chat-3-line ${isActive ? 'text-primary' : 'text-secondary'} flex-shrink-0" style="font-size: 15px;"></i>
                                                <div class="overflow-hidden">
                                                    <div class="chat-item-title" title="${escapeHtml(s.title || 'New Chat')}">
                                                        ${escapeHtml(s.title || 'New Chat')}
                                                    </div>
                                                    <div class="chat-item-meta d-flex align-items-center gap-1">
                                                        <span>${timeStr}</span>
                                                        ${msgCount > 0 ? `<span>• ${msgCount} msgs</span>` : ''}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chat-item-actions d-flex align-items-center gap-1 flex-shrink-0 ms-1">
                                                <button type="button" class="chat-item-action-btn rename-btn rename-chat-btn"
                                                    title="Rename chat (பெயரை மாற்றவும்)" data-session-id="${s.id}">
                                                    <i class="ri ri-edit-line" style="font-size: 12px;"></i>
                                                </button>
                                                <button type="button" class="chat-item-action-btn delete-btn delete-chat-btn"
                                                    title="Delete chat (அழிக்கவும்)" data-session-id="${s.id}">
                                                    <i class="ri ri-delete-bin-line" style="font-size: 12px;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    `;
                            $chatHistoryList.append(itemHtml);
                        });
                    }
                });
            }

            // Create a brand New Chat session
            function createNewChat(focusInput = true) {
                const currentSession = chatSessions.find(s => s.id === currentSessionId);
                if (currentSession && (!currentSession.messages || currentSession.messages.length === 0)) {
                    $activeChatHeaderTitle.text(currentSession.title || 'New Chat');
                    $activeChatSubtitle.text('Nachias ERP Workflow Analysis & Navigation');
                    if (focusInput) $messageInput.focus();
                    closeMobileSidebar();
                    return currentSession;
                }

                const newSessionId = 'chat_' + Date.now() + '_' + Math.random().toString(36).substring(2, 7);
                const newSession = {
                    id: newSessionId,
                    title: 'New Chat',
                    createdAt: Date.now(),
                    updatedAt: Date.now(),
                    messages: [],
                    conversationHistory: []
                };

                chatSessions.unshift(newSession);
                safeSaveSessions();
                loadSession(newSessionId);

                if (focusInput) {
                    $messageInput.focus();
                }
                closeMobileSidebar();
                return newSession;
            }

            // Load an existing session into UI
            function loadSession(sessionId) {
                const session = chatSessions.find(s => s.id === sessionId);
                if (!session) return;

                currentSessionId = sessionId;
                conversationHistory = session.conversationHistory ? [...session.conversationHistory] : [];

                // Update Header
                $activeChatHeaderTitle.text(session.title || 'New Chat');
                const msgCount = (session.messages ? session.messages.length : 0);
                if (msgCount > 0) {
                    $activeChatSubtitle.text(`${msgCount} messages • ERP Workflow Analysis`);
                } else {
                    $activeChatSubtitle.text('Nachias ERP Workflow Analysis & Navigation');
                }

                // Clear current messages UI
                $('.user-msg-row, .ai-msg-row:not(#welcomeMessage), .error-msg-row').remove();
                clearAttachedImage();

                if (!session.messages || session.messages.length === 0) {
                    $welcomeMsg.removeClass('d-none');
                } else {
                    $welcomeMsg.addClass('d-none');

                    // Rehydrate stored messages into UI
                    session.messages.forEach(msg => {
                        if (msg.role === 'user') {
                            renderStoredUserMessage(msg);
                        } else if (msg.role === 'assistant') {
                            renderStoredAiMessage(msg);
                        }
                    });
                }

                renderHistoryList($chatSearchInput.val());
                scrollToBottom(false);
            }

            // Delete a session
            function deleteSession(sessionId) {
                const session = chatSessions.find(s => s.id === sessionId);
                const title = session ? (session.title || 'this chat') : 'this chat';

                const performDelete = function () {
                    chatSessions = chatSessions.filter(s => s.id !== sessionId);
                    safeSaveSessions();

                    // Sync delete with backend database table
                    $.ajax({
                        url: '{{ route("chatbot.sessions.delete") }}',
                        type: 'POST',
                        data: JSON.stringify({ session_id: sessionId }),
                        contentType: 'application/json; charset=utf-8',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function (xhr) {
                            console.warn('Failed to delete session on server:', xhr);
                        }
                    });

                    if (currentSessionId === sessionId) {
                        if (chatSessions.length > 0) {
                            loadSession(chatSessions[0].id);
                        } else {
                            createNewChat(false);
                        }
                    } else {
                        renderHistoryList($chatSearchInput.val());
                    }
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Delete Chat?',
                        text: `Are you sure you want to delete "${title}"?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ff3e1d',
                        cancelButtonColor: '#8592a3',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            performDelete();
                        }
                    });
                } else {
                    if (confirm(`Delete "${title}"?`)) {
                        performDelete();
                    }
                }
            }

            // Rename a session
            function renameSession(sessionId) {
                const session = chatSessions.find(s => s.id === sessionId);
                if (!session) return;

                const saveRename = function (newTitle) {
                    session.title = newTitle;
                    safeSaveSessions();
                    renderHistoryList($chatSearchInput.val());
                    if (currentSessionId === sessionId) {
                        $activeChatHeaderTitle.text(session.title);
                    }

                    // Sync rename with backend database table
                    $.ajax({
                        url: '{{ route("chatbot.sessions.rename") }}',
                        type: 'POST',
                        data: JSON.stringify({
                            session_id: sessionId,
                            title: newTitle
                        }),
                        contentType: 'application/json; charset=utf-8',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function (xhr) {
                            console.warn('Failed to rename session on server:', xhr);
                        }
                    });
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Rename Chat',
                        input: 'text',
                        inputValue: session.title || 'New Chat',
                        showCancelButton: true,
                        confirmButtonColor: '#8c57ff',
                        cancelButtonColor: '#8592a3',
                        confirmButtonText: 'Save Title',
                        inputValidator: (val) => {
                            if (!val || !val.trim()) return 'Please enter a chat title';
                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            saveRename(result.value.trim());
                        }
                    });
                } else {
                    const newTitle = prompt('Rename chat:', session.title || 'New Chat');
                    if (newTitle && newTitle.trim()) {
                        saveRename(newTitle.trim());
                    }
                }
            }

            // Clear All Sessions
            function clearAllSessions() {
                if (chatSessions.length === 0) return;

                const performClearAll = function () {
                    chatSessions = [];
                    safeSaveSessions();

                    // Sync clear-all with backend database table
                    $.ajax({
                        url: '{{ route("chatbot.sessions.clear_all") }}',
                        type: 'POST',
                        contentType: 'application/json; charset=utf-8',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function (xhr) {
                            console.warn('Failed to clear sessions on server:', xhr);
                        }
                    });

                    createNewChat(true);
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Clear All History?',
                        text: 'This will delete all saved conversations permanently.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ff3e1d',
                        cancelButtonColor: '#8592a3',
                        confirmButtonText: 'Yes, delete all!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            performClearAll();
                        }
                    });
                } else {
                    if (confirm('Clear all saved chat history? This cannot be undone.')) {
                        performClearAll();
                    }
                }
            }

            // Helper to close mobile sidebar
            function closeMobileSidebar() {
                $chatCard.removeClass('mobile-sidebar-open');
            }

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

            // Render Stored User Message (used when reloading history)
            function renderStoredUserMessage(msg) {
                const time = msg.time || '';
                const formatted = formatMessageText(msg.text || '');
                const msgId = msg.id || ('user_msg_' + (++msgCounter));
                const isVoice = !!msg.isVoice;
                const audioUrl = msg.audioData || msg.audioUrl || null;
                const imageSrc = msg.image || null;

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
                } else if (hasTamilCharacters(msg.text || '')) {
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

                let translationHtml = '';
                if (msg.isTranslated && msg.translatedMsg) {
                    translationHtml = `
                                <div class="user-msg-translation mt-2">
                                    <div class="voice-pill-translation small">
                                        <div class="d-flex align-items-center gap-1 text-white-50 fs-tiny mb-1">
                                            <i class="ri ri-translate-2 text-warning"></i> <strong>Translated to English (Input for LLM):</strong>
                                        </div>
                                        <div class="text-white fw-semibold" style="line-height: 1.5;">${escapeHtml(msg.translatedMsg)}</div>
                                    </div>
                                </div>
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
                                    ${translationHtml}
                                    <div class="text-start text-white-50 mt-1" style="font-size: 11px;">
                                        ${time}
                                    </div>
                                </div>
                            </div>
                        `;
                $loadingRow.before(html);
            }

            // Render Stored AI Message (used when reloading history)
            function renderStoredAiMessage(msg) {
                const time = msg.time || '';
                const formatted = formatMessageText(msg.text || '');
                const isTamil = msg.responseLanguage === 'ta';
                const englishOriginal = msg.englishOriginal || null;
                const ragSources = msg.ragSources || [];

                let badgeHtml = isTamil
                    ? '<span class="badge bg-label-info fs-tiny py-0 px-1 ms-1"><i class="ri ri-translate-2 me-1"></i>தமிழ் வெளியீடு (Tamil)</span>'
                    : '<span class="badge bg-label-secondary fs-tiny py-0 px-1 ms-1">English Output</span>';

                let ragSourcesHtml = '';
                if (ragSources.length > 0) {
                    const navSource = ragSources.find(s => s.menu_path && s.url);
                    if (navSource) {
                        ragSourcesHtml = `
                                    <div class="mt-2 pt-2 border-top d-flex flex-wrap gap-1 align-items-center">
                                        <a href="${escapeHtml(navSource.url)}" class="badge bg-label-primary fs-tiny py-1 px-2 text-decoration-none d-inline-flex align-items-center gap-1" style="cursor: pointer;">
                                            <i class="ri ri-compass-3-line"></i> ${escapeHtml(navSource.menu_path)} &nbsp;<span class="text-decoration-underline fw-bold">Open Screen →</span>
                                        </a>
                                    </div>
                                `;
                    }
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
            }

            // Append User Message to UI during live conversation
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
                                    <div class="user-msg-translation mt-2" style="display: none;"></div>
                                    <div class="text-start text-white-50 mt-1" style="font-size: 11px;">
                                        ${time}
                                    </div>
                                </div>
                            </div>
                        `;
                $loadingRow.before(html);
                $welcomeMsg.addClass('d-none');
                scrollToBottom();
                return msgId;
            }

            // Append AI Message to UI during live conversation
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
                    if (navSource) {
                        ragSourcesHtml = `
                                    <div class="mt-2 pt-2 border-top d-flex flex-wrap gap-1 align-items-center">
                                        <a href="${escapeHtml(navSource.url)}" class="badge bg-label-primary fs-tiny py-1 px-2 text-decoration-none d-inline-flex align-items-center gap-1" style="cursor: pointer;">
                                            <i class="ri ri-compass-3-line"></i> ${escapeHtml(navSource.menu_path)} &nbsp;<span class="text-decoration-underline fw-bold">Open Screen →</span>
                                        </a>
                                    </div>
                                `;
                    }
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
                    $sendIcon.attr('class', 'ri ri-loader-4-line spinner-border-sm fs-5');
                    scrollToBottom();
                } else {
                    $loadingRow.addClass('d-none');
                    $sendBtn.prop('disabled', false);
                    $voiceRecordBtn.prop('disabled', false);
                    $imageUploadBtn.prop('disabled', false);
                    $messageInput.prop('disabled', false);
                    $sendIcon.attr('class', 'ri ri-send-plane-2-fill fs-5');
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

                        // If recognition didn't start yet, trigger start
                        if (recognition && !speechSessionActive) {
                            try {
                                recognition.start();
                            } catch (e) { }
                        }

                        // UI Updates
                        $voiceRecordingBar.removeClass('d-none');
                        $voiceRecordBtn.addClass('voice-btn-active');
                        $micIcon.attr('class', 'ri ri-mic-fill fs-5');
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
                        $micIcon.attr('class', 'ri ri-mic-line fs-5');
                        appendErrorMessage('Microphone access was not granted. Please check your browser microphone permissions.');
                    });
            }

            function stopVoiceRecording(shouldSend = false) {
                if (!isRecordingVoice) return;
                isRecordingVoice = false;

                clearInterval(voiceTimerInterval);
                $voiceRecordingBar.addClass('d-none');
                $voiceRecordBtn.removeClass('voice-btn-active');
                $micIcon.attr('class', 'ri ri-mic-line fs-5');

                const activeRec = recognition;
                if (activeRec) {
                    activeRec.onend = null;
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
                    if (mediaStream) {
                        mediaStream.getTracks().forEach(track => track.stop());
                        mediaStream = null;
                    }

                    const audioBlob = (audioChunks.length > 0) ? new Blob(audioChunks, { type: 'audio/webm' }) : null;
                    const audioUrl = audioBlob ? URL.createObjectURL(audioBlob) : null;

                    const proceedWithTranscript = function (base64Audio) {
                        const finalMessage = (capturedTamilTranscript || $messageInput.val()).trim();

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

                if (capturedTamilTranscript || $messageInput.val().trim()) {
                    setTimeout(finalizeAndSend, 350);
                } else {
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

                if (!inputMsg && !attachedImage) {
                    $messageInput.focus();
                    return;
                }

                const finalPrompt = inputMsg || 'Please analyze this uploaded ERP image/screenshot and explain the screen, fields, or next workflow step.';

                // Ensure an active session exists
                let session = chatSessions.find(s => s.id === currentSessionId);
                if (!session) {
                    session = createNewChat(false);
                }

                // If this is the first message in the session, automatically generate a title
                const isFirstMsg = (!session.messages || session.messages.length === 0);
                if (isFirstMsg || session.title === 'New Chat') {
                    let autoTitle = finalPrompt.replace(/\s+/g, ' ').trim();
                    if (autoTitle.length > 34) {
                        autoTitle = autoTitle.substring(0, 34) + '...';
                    }
                    session.title = autoTitle;
                    $activeChatHeaderTitle.text(autoTitle);
                }

                // Display user message in UI and capture element id
                const msgId = appendUserMessage(finalPrompt, {
                    isVoice: isVoice,
                    audioUrl: localAudioUrl,
                    image: attachedImage ? attachedImage.base64 : null
                });

                // Record user message into current session history
                const userMsgRecord = {
                    id: msgId,
                    role: 'user',
                    text: finalPrompt,
                    time: getCurrentTime(),
                    isVoice: isVoice,
                    audioData: audioBase64 || null,
                    image: attachedImage ? attachedImage.base64 : null,
                    imageName: attachedImage ? attachedImage.name : null,
                    isTranslated: false,
                    translatedMsg: null
                };

                if (!session.messages) session.messages = [];
                session.messages.push(userMsgRecord);
                session.updatedAt = Date.now();
                safeSaveSessions();
                renderHistoryList($chatSearchInput.val());

                // Clear input, staged image, and reset char count
                $messageInput.val('');
                $charCount.text('0 / 2000');
                if (attachedImage) {
                    clearAttachedImage();
                }

                // Set UI loading state
                setLoading(true);

                // Prepare request payload with recent history, session id, and image data
                const payload = {
                    session_id: session.id,
                    chat_title: session.title,
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
                                userMsgRecord.isTranslated = true;
                                userMsgRecord.translatedMsg = translatedMsg;

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

                            // Append AI response to UI
                            appendAiMessage(aiReply, {
                                responseLanguage: response.response_language || 'en',
                                englishOriginal: response.english_message || null,
                                ragSources: response.rag_sources || [],
                                ragIntent: response.rag_intent || 'general'
                            });

                            // Record AI message into session
                            const aiMsgRecord = {
                                id: 'ai_msg_' + Date.now(),
                                role: 'assistant',
                                text: aiReply,
                                time: getCurrentTime(),
                                responseLanguage: response.response_language || 'en',
                                englishOriginal: response.english_message || null,
                                ragSources: response.rag_sources || [],
                                ragIntent: response.rag_intent || 'general'
                            };
                            session.messages.push(aiMsgRecord);

                            // Update in-memory conversation history for LLM
                            conversationHistory.push({
                                role: 'user',
                                content: isTranslated ? translatedMsg : finalPrompt
                            });
                            conversationHistory.push({
                                role: 'assistant',
                                content: response.english_message || aiReply
                            });

                            session.conversationHistory = [...conversationHistory];
                            session.updatedAt = Date.now();
                            safeSaveSessions();
                            renderHistoryList($chatSearchInput.val());

                            $activeChatSubtitle.text(`${session.messages.length} messages • ERP Workflow Analysis`);
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

            // Image Upload Trigger (click button opens native file browser)
            $imageUploadBtn.on('click', function (e) {
                e.preventDefault();
                if ($imageFileInput.length && $imageFileInput[0]) {
                    $imageFileInput[0].click();
                }
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
            $voiceRecordBtn.on('click', function (e) {
                e.preventDefault();
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

            // Sidebar Toggle (Desktop collapse & Mobile slide-over)
            $toggleSidebarBtn.on('click', function () {
                if ($(window).width() < 992) {
                    $chatCard.toggleClass('mobile-sidebar-open');
                } else {
                    $chatCard.toggleClass('sidebar-collapsed');
                }
            });

            // Click backdrop on mobile closes sidebar
            $chatSidebarBackdrop.on('click', function () {
                closeMobileSidebar();
            });

            // Search Filter for Chat History
            $chatSearchInput.on('input', function () {
                renderHistoryList($(this).val());
            });

            // Start New Chat Buttons
            $sidebarNewChatBtn.on('click', function () {
                createNewChat(true);
            });

            $headerNewChatBtn.on('click', function () {
                createNewChat(true);
            });

            // Click on a chat in the sidebar to load/continue conversation
            $(document).on('click', '.chat-item-click-area', function () {
                const sessionId = $(this).closest('.chat-history-item').data('session-id');
                if (sessionId) {
                    loadSession(sessionId);
                    closeMobileSidebar();
                }
            });

            // Rename Chat Button
            $(document).on('click', '.rename-chat-btn', function (e) {
                e.stopPropagation();
                const sessionId = $(this).data('session-id');
                if (sessionId) {
                    renameSession(sessionId);
                }
            });

            // Delete Chat Button
            $(document).on('click', '.delete-chat-btn', function (e) {
                e.stopPropagation();
                const sessionId = $(this).data('session-id');
                if (sessionId) {
                    deleteSession(sessionId);
                }
            });

            // Clear All History Button
            $clearAllHistoryBtn.on('click', function () {
                clearAllSessions();
            });

            // Clear Current Chat Button in Header
            $clearBtn.on('click', function () {
                const session = chatSessions.find(s => s.id === currentSessionId);
                const hasMessages = session && session.messages && session.messages.length > 0;

                if (!hasMessages && $('.user-msg-row').length === 0 && !currentAttachedImage) {
                    return;
                }

                const performReset = function () {
                    if (session) {
                        session.messages = [];
                        session.conversationHistory = [];
                        session.updatedAt = Date.now();
                        safeSaveSessions();

                        // Sync cleared messages to database table
                        $.ajax({
                            url: '{{ route("chatbot.sessions.save") }}',
                            type: 'POST',
                            data: JSON.stringify({
                                session_id: session.id,
                                title: session.title || 'New Chat',
                                messages: [],
                                conversation_history: []
                            }),
                            contentType: 'application/json; charset=utf-8',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                    }
                    conversationHistory = [];
                    clearAttachedImage();
                    $('.user-msg-row, .ai-msg-row:not(#welcomeMessage), .error-msg-row').remove();
                    $welcomeMsg.removeClass('d-none');
                    $messageInput.val('').focus();
                    $charCount.text('0 / 2000');
                    renderHistoryList($chatSearchInput.val());
                    $activeChatSubtitle.text('Nachias ERP Workflow Analysis & Navigation');
                    scrollToBottom(false);
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Clear Conversation?',
                        text: 'This will reset the messages in the current chat.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#8c57ff',
                        cancelButtonColor: '#8592a3',
                        confirmButtonText: 'Yes, clear it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            performReset();
                        }
                    });
                } else {
                    if (confirm('Clear current chat messages?')) {
                        performReset();
                    }
                }
            });

            // Initialize Sessions
            loadAllSessions();

            if (chatSessions.length > 0) {
                chatSessions.sort((a, b) => (b.updatedAt || b.createdAt || 0) - (a.updatedAt || a.createdAt || 0));
                loadSession(chatSessions[0].id);
            } else {
                createNewChat(false);
            }

            // Initial focus
            $messageInput.focus();
        });
    </script>
@endsection