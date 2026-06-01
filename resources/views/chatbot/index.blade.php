@extends('layouts.app')

@section('title', 'Chatbot AI')

@section('content')

<div class="max-w-4xl mx-auto h-[calc(100vh-8rem)] flex flex-col">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-primary">Chatbot AI</h1>
        <button id="clear-chat" class="text-sm text-muted hover:text-red-500 transition-colors duration-150">
            Hapus Riwayat
        </button>
    </div>

    <div id="chat-messages" class="flex-1 overflow-y-auto space-y-4 pr-2 scroll-smooth">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-sm font-semibold shrink-0">
                AI
            </div>
            <div class="bg-white rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm max-w-[80%]">
                <p class="text-sm text-primary leading-relaxed">
                    Halo! Saya asisten AI WARIS. Ada yang bisa saya bantu?
                </p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 px-11">
            <button class="quick-chat text-xs bg-white border border-gray-200 rounded-full px-3 py-1.5 text-muted hover:border-primary hover:text-primary transition-colors duration-150">
                Ringkas penjualan hari ini
            </button>
            <button class="quick-chat text-xs bg-white border border-gray-200 rounded-full px-3 py-1.5 text-muted hover:border-primary hover:text-primary transition-colors duration-150">
                Produk paling laris minggu ini
            </button>
        </div>
    </div>

    <div class="mt-4 bg-white rounded-2xl shadow-sm border border-gray-100 p-2 flex items-center gap-2">
        <button id="mic-btn" class="w-10 h-10 rounded-full flex items-center justify-center text-muted hover:text-primary hover:bg-gray-100 transition-colors duration-150 shrink-0" title="Voice input">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
            </svg>
        </button>
        <input id="chat-input" type="text" placeholder="Ketik pesan..." class="flex-1 bg-transparent text-sm text-primary placeholder-muted outline-none px-2">
        <button id="send-btn" class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white hover:bg-primary/90 transition-colors duration-150 shrink-0" title="Kirim">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
            </svg>
        </button>
    </div>

    <p class="text-xs text-muted text-center mt-2">AI lokal — Nous Hermes 7B via Ollama</p>
</div>

@endsection

@push('scripts')
<script>
(() => {
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const sendBtn = document.getElementById('send-btn');
    const micBtn = document.getElementById('mic-btn');
    const clearBtn = document.getElementById('clear-chat');

    let isListening = false;
    let recognition = null;
    let mediaRecorder = null;
    let audioChunks = [];

    function startWhisperSTT() {
        addMessage('assistant', '🎤 Dengarkan... bicara sekarang.');
        audioChunks = [];

        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(stream => {
                mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });
                mediaRecorder.start();

                mediaRecorder.ondataavailable = (e) => {
                    audioChunks.push(e.data);
                };

                mediaRecorder.onstop = () => {
                    stream.getTracks().forEach(t => t.stop());

                    const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    const formData = new FormData();
                    formData.append('audio', audioBlob, 'recording.webm');
                    formData.append('_token', '{{ csrf_token() }}');

                    micBtn.classList.remove('text-red-500', 'bg-red-50');
                    micBtn.classList.add('text-muted');
                    isListening = false;

                    fetch('{{ route("stt.transcribe") }}', {
                        method: 'POST',
                        body: formData,
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success && data.text) {
                            chatInput.value = data.text;
                            sendMessage();
                        } else {
                            addMessage('assistant', 'Maaf, gagal mengenali suara.');
                        }
                    })
                    .catch(() => {
                        addMessage('assistant', 'Maaf, terjadi kesalahan saat memproses suara.');
                    });
                };

                setTimeout(() => {
                    if (mediaRecorder && mediaRecorder.state === 'recording') {
                        mediaRecorder.stop();
                    }
                }, 5000);
            })
            .catch(() => {
                micBtn.classList.remove('text-red-500', 'bg-red-50');
                micBtn.classList.add('text-muted');
                isListening = false;
                addMessage('assistant', 'Maaf, mic tidak dapat diakses. Periksa izin browser.');
            });
    }

    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.lang = 'id-ID';
        recognition.continuous = false;
        recognition.interimResults = true;

        recognition.onresult = (e) => {
            let finalTranscript = '';
            for (let i = e.resultIndex; i < e.results.length; i++) {
                const transcript = e.results[i][0].transcript;
                if (e.results[i].isFinal) {
                    finalTranscript += transcript;
                } else {
                    chatInput.value = transcript;
                }
            }
            if (finalTranscript) {
                chatInput.value = finalTranscript;
                micBtn.classList.remove('text-red-500', 'bg-red-50');
                micBtn.classList.add('text-muted');
                isListening = false;
                sendMessage();
            }
        };

        recognition.onerror = (e) => {
            micBtn.classList.remove('text-red-500', 'bg-red-50');
            micBtn.classList.add('text-muted');
            isListening = false;
            if (e.error === 'network' || e.error === 'not-allowed') {
                startWhisperSTT();
            }
        };

        recognition.onend = () => {
            micBtn.classList.remove('text-red-500', 'bg-red-50');
            micBtn.classList.add('text-muted');
            isListening = false;
        };
    }

    micBtn.addEventListener('click', () => {
        if (isListening) {
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
            } else if (recognition) {
                recognition.stop();
            }
            return;
        }

        isListening = true;
        micBtn.classList.add('text-red-500', 'bg-red-50');
        micBtn.classList.remove('text-muted');

        if (recognition) {
            try { recognition.start(); } catch (e) {
                startWhisperSTT();
            }
        } else {
            startWhisperSTT();
        }
    });

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function speakText(text) {
        if (!('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();
        const utter = new SpeechSynthesisUtterance(text);
        utter.lang = 'id-ID';
        utter.rate = 1.0;
        utter.pitch = 1.0;
        window.speechSynthesis.speak(utter);
    }

    function addMessage(role, content) {
        const wrapper = document.createElement('div');

        if (role === 'user') {
            wrapper.className = 'flex items-start gap-3 justify-end';
            wrapper.innerHTML = `
                <div class="bg-primary text-white rounded-2xl rounded-tr-sm px-4 py-3 shadow-sm max-w-[80%]">
                    <p class="text-sm leading-relaxed">${escapeHtml(content)}</p>
                </div>
                <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-primary text-sm font-semibold shrink-0">
                    U
                </div>
            `;
        } else {
            wrapper.className = 'flex items-start gap-3';
            wrapper.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-sm font-semibold shrink-0">
                    AI
                </div>
                <div class="bg-white rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm max-w-[80%]">
                    <p class="text-sm text-primary leading-relaxed">${escapeHtml(content)}</p>
                    <button class="tts-btn mt-1.5 text-xs text-muted hover:text-primary transition-colors flex items-center gap-1" data-text="${escapeHtml(content)}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" /></svg>
                        Dengarkan
                    </button>
                </div>
            `;
            wrapper.querySelector('.tts-btn')?.addEventListener('click', function() {
                if (window.speechSynthesis.speaking) {
                    window.speechSynthesis.cancel();
                    this.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" /></svg> Dengarkan`;
                } else {
                    speakText(this.dataset.text);
                    this.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" /></svg> Stop`;
                }
            });
        }

        chatMessages.appendChild(wrapper);
        scrollToBottom();
    }

    function addLoading() {
        const wrapper = document.createElement('div');
        wrapper.id = 'loading-msg';
        wrapper.className = 'flex items-start gap-3';
        wrapper.innerHTML = `
            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-sm font-semibold shrink-0">
                AI
            </div>
            <div class="bg-white rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm">
                <div class="flex gap-1">
                    <span class="w-2 h-2 bg-primary/40 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-2 h-2 bg-primary/40 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-2 h-2 bg-primary/40 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                </div>
            </div>
        `;
        chatMessages.appendChild(wrapper);
        scrollToBottom();
    }

    function removeLoading() {
        const el = document.getElementById('loading-msg');
        if (el) el.remove();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;

        addMessage('user', message);
        chatInput.value = '';
        addLoading();

        fetch('{{ route("chatbot.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ message }),
        })
        .then(r => r.json())
        .then(data => {
            removeLoading();
            if (data.success) {
                addMessage('assistant', data.response);
            }
        })
        .catch(() => {
            removeLoading();
            addMessage('assistant', 'Maaf, terjadi kesalahan koneksi. Silakan coba lagi.');
        });
    }

    function loadHistory() {
        fetch('{{ route("chatbot.history") }}')
            .then(r => r.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    const quickContainer = chatMessages.querySelector('.flex-wrap');
                    quickContainer?.remove();

                    document.querySelector('.flex.items-start.gap-3:first-child')?.remove();

                    data.data.forEach(msg => addMessage(msg.role, msg.message));
                }
            })
            .catch(() => {});
    }

    sendBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    document.querySelectorAll('.quick-chat').forEach(btn => {
        btn.addEventListener('click', () => {
            chatInput.value = btn.textContent.trim();
            sendMessage();
        });
    });

    clearBtn.addEventListener('click', () => {
        if (!confirm('Hapus semua riwayat chat?')) return;

        fetch('{{ route("chatbot.clear") }}', {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        })
        .then(() => location.reload());
    });

    setTimeout(scrollToBottom, 100);
    loadHistory();
})();
</script>
@endpush
