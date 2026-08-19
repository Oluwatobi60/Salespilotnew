{{-- Floating Button to open Copilot --}}
<button type="button" class="ai-copilot-trigger" id="aiCopilotBtn" onclick="toggleCopilotDrawer()" title="Open AI Copilot">
    <div class="copilot-btn-icon">
        <i class="bi bi-chat-left-text-fill"></i>
    </div>
    <span class="copilot-btn-text">AI Copilot</span>
    <span class="copilot-pulse-ring"></span>
</button>

{{-- Slide-out Copilot Drawer --}}
<div class="copilot-drawer" id="copilotDrawer">
    <div class="copilot-header">
        <div class="copilot-header-info">
            <div class="copilot-logo">
                <i class="bi bi-stars"></i>
            </div>
            <div>
                <h4 class="copilot-title">POS AI Copilot</h4>
                <p class="copilot-status">Online • Ask anything</p>
            </div>
        </div>
        <button type="button" class="copilot-close-btn" onclick="toggleCopilotDrawer()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    {{-- Chat History Area --}}
    <div class="copilot-chat-body" id="copilotChatBody">
        <div class="copilot-message bot">
            <div class="message-content">
                Hi! 👋 I'm your real-time POS & Inventory assistant. Try asking me something like:
                <div class="copilot-suggestions">
                    <button type="button" class="suggestion-tag" onclick="useSuggestion('Do we have any standard items left?')">
                        <i class="bi bi-search"></i> Check basic stock
                    </button>
                    <button type="button" class="suggestion-tag" onclick="useSuggestion('Summarize today\'s sales across branches')">
                        <i class="bi bi-graph-up-arrow"></i> Today's sales summary
                    </button>
                    <button type="button" class="suggestion-tag" onclick="useSuggestion('Are there standard blue sneakers left?')">
                        <i class="bi bi-tag"></i> Search specific item stock
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Input Form Area --}}
    <div class="copilot-input-area">
        <form id="copilotForm" onsubmit="submitCopilotQuery(event)">
            <div class="input-group-wrapper">
                {{-- Microphone speech trigger button --}}
                <button type="button" class="copilot-voice-btn" id="copilotVoiceBtn" onclick="toggleVoiceRecognition()" title="Voice Input">
                    <i class="bi bi-mic-fill"></i>
                </button>
                
                <input type="text" id="copilotInput" class="copilot-input-field" placeholder="Ask about stock, today's sales..." required autocomplete="off">
                
                <button type="submit" class="copilot-send-btn" id="copilotSendBtn">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </form>
        <div class="voice-wave-container" id="voiceWaveContainer" style="display: none;">
            <div class="voice-wave-bars">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
            <span class="voice-wave-text">Listening... Speak now</span>
        </div>
    </div>
</div>

<style>
/* 💬 POS Copilot Premium Styles */
.ai-copilot-trigger {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 1050;
    display: flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    color: #fff;
    border: none;
    border-radius: 50px;
    padding: 12px 24px;
    font-weight: 600;
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}
.ai-copilot-trigger:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 12px 30px rgba(102, 126, 234, 0.6);
}
.copilot-btn-icon {
    font-size: 1.2rem;
    display: flex;
    align-items: center;
}
.copilot-pulse-ring {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50px;
    border: 2px solid rgba(102, 126, 234, 0.5);
    animation: copilot-pulse 2s infinite;
}
@keyframes copilot-pulse {
    0% { transform: scale(0.95); opacity: 1; }
    100% { transform: scale(1.25); opacity: 0; }
}

/* Slide-out Drawer */
.copilot-drawer {
    position: fixed;
    top: 0;
    right: -380px;
    width: 380px;
    height: 100vh;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-left: 1px solid rgba(255, 255, 255, 0.25);
    box-shadow: -10px 0 30px rgba(0, 0, 0, 0.15);
    z-index: 1060;
    display: flex;
    flex-direction: column;
    transition: right 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
}
.copilot-drawer.open {
    right: 0;
}
.copilot-header {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    color: #fff;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.copilot-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
}
.copilot-logo {
    background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    box-shadow: 0 4px 10px rgba(139, 92, 246, 0.3);
}
.copilot-title {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}
.copilot-status {
    margin: 0;
    font-size: 0.75rem;
    color: #10b981;
}
.copilot-close-btn {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.6);
    font-size: 1.1rem;
    cursor: pointer;
    transition: color 0.2s;
}
.copilot-close-btn:hover {
    color: #fff;
}

/* Chat Body */
.copilot-chat-body {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.copilot-message {
    max-width: 85%;
    display: flex;
    flex-direction: column;
}
.copilot-message.bot {
    align-self: flex-start;
}
.copilot-message.user {
    align-self: flex-end;
}
.message-content {
    padding: 12px 16px;
    border-radius: 18px;
    font-size: 0.88rem;
    line-height: 1.45;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02);
}
.copilot-message.bot .message-content {
    background: #f3f4f6;
    color: #1f2937;
    border-top-left-radius: 4px;
}
.copilot-message.user .message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border-top-right-radius: 4px;
}

/* Suggested Tags */
.copilot-suggestions {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 12px;
}
.suggestion-tag {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    color: #4b5563;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 0.8rem;
    text-align: left;
    cursor: pointer;
    transition: all 0.2s;
}
.suggestion-tag:hover {
    background: #f9fafb;
    border-color: #667eea;
    color: #667eea;
}

/* Input Area */
.copilot-input-area {
    padding: 16px;
    background: #fff;
    border-top: 1px solid #e5e7eb;
}
.input-group-wrapper {
    display: flex;
    align-items: center;
    background: #f3f4f6;
    border-radius: 30px;
    padding: 4px 8px;
    border: 1px solid #e5e7eb;
}
.copilot-voice-btn {
    background: transparent;
    border: none;
    color: #6b7280;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}
.copilot-voice-btn:hover {
    color: #dc2626;
    background: rgba(220, 38, 38, 0.1);
}
.copilot-voice-btn.recording {
    background: #ef4444 !important;
    color: #fff !important;
    animation: voice-pulse 1.2s infinite;
}
@keyframes voice-pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
.copilot-input-field {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    padding: 8px 12px;
    font-size: 0.88rem;
    color: #1f2937;
}
.copilot-send-btn {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    border: none;
    color: #fff;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
    transition: all 0.2s;
}
.copilot-send-btn:hover {
    transform: scale(1.05);
}

/* Voice wave animations */
.voice-wave-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-top: 10px;
}
.voice-wave-bars {
    display: flex;
    align-items: flex-end;
    gap: 3px;
    height: 20px;
}
.voice-wave-bars span {
    width: 3px;
    background: #ef4444;
    border-radius: 3px;
    animation: bounce 0.8s ease-in-out infinite alternate;
}
.voice-wave-bars span:nth-child(1) { height: 10px; animation-delay: 0.1s; }
.voice-wave-bars span:nth-child(2) { height: 18px; animation-delay: 0.3s; }
.voice-wave-bars span:nth-child(3) { height: 12px; animation-delay: 0.2s; }
.voice-wave-bars span:nth-child(4) { height: 20px; animation-delay: 0.4s; }
.voice-wave-bars span:nth-child(5) { height: 8px;  animation-delay: 0.15s; }

@keyframes bounce {
    0% { transform: scaleY(0.3); }
    100% { transform: scaleY(1); }
}
.voice-wave-text {
    font-size: 0.78rem;
    color: #dc2626;
    font-weight: 500;
}
</style>

<script>
let recognition = null;
let isRecording = false;

function toggleCopilotDrawer() {
    const drawer = document.getElementById('copilotDrawer');
    if (drawer) {
        drawer.classList.toggle('open');
    }
}

function useSuggestion(text) {
    const input = document.getElementById('copilotInput');
    if (input) {
        input.value = text;
        input.focus();
    }
}

function appendMessage(sender, htmlContent) {
    const chatBody = document.getElementById('copilotChatBody');
    if (!chatBody) return;

    const msgDiv = document.createElement('div');
    msgDiv.className = `copilot-message ${sender}`;
    msgDiv.innerHTML = `<div class="message-content">${htmlContent}</div>`;
    chatBody.appendChild(msgDiv);

    // Auto Scroll to bottom
    chatBody.scrollTop = chatBody.scrollHeight;
}

function submitCopilotQuery(event) {
    if (event) event.preventDefault();

    const input = document.getElementById('copilotInput');
    if (!input || !input.value.trim()) return;

    const query = input.value.trim();
    input.value = '';

    // Append User Message
    appendMessage('user', query);

    // Append typing indicator
    const typingId = 'typing_' + Date.now();
    appendMessage('bot', `<div id="${typingId}" style="display:flex; gap:4px; align-items:center;">
        <span class="spinner-border spinner-border-sm text-secondary" role="status" style="width:14px; height:14px;"></span>
        <span>Analyzing system data...</span>
    </div>`);

    // Determine correct endpoint url
    const copilotUrl = "{{ request()->is('staff*') ? route('staff.ai.copilot') : route('manager.ai.copilot') }}";

    fetch(copilotUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ query: query })
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(txt => {
                throw new Error(`HTTP ${response.status}: ${txt.substring(0, 150)}`);
            });
        }
        return response.json();
    })
    .then(data => {
        // Remove typing indicator
        document.getElementById(typingId)?.closest('.copilot-message')?.remove();
        
        if (data.success) {
            appendMessage('bot', data.response);
        } else {
            appendMessage('bot', `<i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Error: ${data.message || 'Unknown response structure'}`);
        }
    })
    .catch(error => {
        document.getElementById(typingId)?.closest('.copilot-message')?.remove();
        appendMessage('bot', `<i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Error: ${error.message || error}`);
        console.error(error);
    });
}

/**
 * Web Speech API Voice Recognition
 */
function initSpeechRecognition() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
        console.warn('Speech Recognition not supported in this browser.');
        return null;
    }

    const rec = new SpeechRecognition();
    rec.continuous = false;
    rec.lang = 'en-US';
    rec.interimResults = false;

    rec.onstart = function() {
        isRecording = true;
        document.getElementById('copilotVoiceBtn').classList.add('recording');
        document.getElementById('voiceWaveContainer').style.display = 'flex';
    };

    rec.onend = function() {
        isRecording = false;
        document.getElementById('copilotVoiceBtn').classList.remove('recording');
        document.getElementById('voiceWaveContainer').style.display = 'none';
    };

    rec.onerror = function(event) {
        console.error('Speech recognition error: ', event.error);
        isRecording = false;
        document.getElementById('copilotVoiceBtn').classList.remove('recording');
        document.getElementById('voiceWaveContainer').style.display = 'none';
    };

    rec.onresult = function(event) {
        const text = event.results[0][0].transcript;
        const input = document.getElementById('copilotInput');
        if (input) {
            input.value = text;
            submitCopilotQuery();
        }
    };

    return rec;
}

function toggleVoiceRecognition() {
    if (!recognition) {
        recognition = initSpeechRecognition();
    }

    if (!recognition) {
        alert('Voice Speech Input is not supported by your browser. Please try Google Chrome or Safari.');
        return;
    }

    if (isRecording) {
        recognition.stop();
    } else {
        recognition.start();
    }
}
</script>
