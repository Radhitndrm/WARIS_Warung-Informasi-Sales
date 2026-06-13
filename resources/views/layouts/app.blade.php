<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'WARIS')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>

<body class="bg-cream min-h-screen" x-data="{ sidebarOpen: false }">

    {{-- Mobile sidebar overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
        x-transition:enter="transition-opacity duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/40 z-40 lg:hidden">
    </div>

    <div class="flex min-h-screen">

        <x-sidebar />

        <div class="flex flex-col flex-1 min-w-0 lg:pl-0">

            <x-header />

            <main class="flex-1 p-4 md:p-6 overflow-y-auto">
                @yield('content')
            </main>

        </div>

    </div>

    {{-- Chatbot Widget --}}
    <div x-data="{ open: false }">
        <div x-show="open" x-cloak
            x-transition:enter="transition-all duration-300 ease-out"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition-all duration-200 ease-in"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="fixed bottom-20 right-4 md:right-6 w-[calc(100vw-2rem)] max-w-sm bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden flex flex-col"
            style="max-height: 520px; z-index: 60;">
            <div class="bg-primary px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                    </svg>
                    <span class="text-sm font-semibold text-white">ChatBox AI</span>
                </div>
                <button @click="open = false" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div id="widget-messages" class="flex-1 overflow-y-auto p-4 space-y-3 text-xs" style="min-height: 280px; max-height: 360px;">
                <div class="flex items-start gap-2">
                    <div class="w-6 h-6 rounded-full bg-primary flex items-center justify-center text-white text-[10px] font-semibold shrink-0">AI</div>
                    <div class="bg-gray-50 rounded-2xl rounded-tl-sm px-3 py-2 max-w-[85%]">
                        <p class="text-gray-700 leading-relaxed">Hai! Ada yang bisa saya bantu?</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-1.5 px-8">
                    <button class="quick-widget bg-white border border-gray-200 rounded-full px-2.5 py-1 text-[11px] text-muted hover:border-primary hover:text-primary transition-colors">
                        Ringkas penjualan hari ini
                    </button>
                    <button class="quick-widget bg-white border border-gray-200 rounded-full px-2.5 py-1 text-[11px] text-muted hover:border-primary hover:text-primary transition-colors">
                        Produk paling laris minggu ini
                    </button>
                </div>
            </div>

            <div class="border-t border-gray-100 p-2 flex items-center gap-2 bg-white">
                <button class="widget-mic w-8 h-8 rounded-full flex items-center justify-center text-muted hover:text-primary hover:bg-gray-50 transition-colors shrink-0" title="Voice input">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                    </svg>
                </button>
                <input class="widget-input flex-1 bg-transparent text-xs text-primary placeholder-muted outline-none px-1" type="text" placeholder="Ketik pertanyaan...">
                <button class="widget-send w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white hover:bg-primary/90 transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                </button>
            </div>
        </div>

        <button @click="open = !open"
            class="fixed bottom-4 right-4 md:right-6 z-50 w-12 h-12 rounded-full bg-primary text-white shadow-lg hover:bg-primary/90 transition-all duration-200 flex items-center justify-center"
            :class="{ 'rotate-45': open }">
            <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
            </svg>
            <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

<script>
(function() {
    const widget = document.querySelector('#widget-messages');
    if (!widget) return;

    const input = document.querySelector('.widget-input');
    const sendBtn = document.querySelector('.widget-send');
    const micBtn = document.querySelector('.widget-mic');
    const quickBtns = document.querySelectorAll('.quick-widget');

    let isListening = false;
    let recognition = null;
    let mediaRecorder = null;
    let audioChunks = [];

    function startWhisperSTT() {
        widgetAddMessage('assistant', '🎤 Dengarkan... bicara sekarang.');
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
                            input.value = data.text;
                            sendWidgetMessage();
                        } else {
                            widgetAddMessage('assistant', 'Maaf, gagal mengenali suara.');
                        }
                    })
                    .catch(() => {
                        widgetAddMessage('assistant', 'Maaf, terjadi kesalahan saat memproses suara.');
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
                widgetAddMessage('assistant', 'Maaf, mic tidak dapat diakses. Periksa izin browser.');
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
                    input.value = transcript;
                }
            }
            if (finalTranscript) {
                input.value = finalTranscript;
                micBtn.classList.remove('text-red-500', 'bg-red-50');
                micBtn.classList.add('text-muted');
                isListening = false;
                sendWidgetMessage();
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

    function speakText(text) {
        if (!('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();
        const utter = new SpeechSynthesisUtterance(text);
        utter.lang = 'id-ID';
        utter.rate = 1.0;
        utter.pitch = 1.0;
        window.speechSynthesis.speak(utter);
    }

    function widgetScroll() {
        widget.scrollTop = widget.scrollHeight;
    }

    function widgetAddMessage(role, content) {
        const div = document.createElement('div');
        if (role === 'user') {
            div.className = 'flex items-start gap-2 justify-end';
            div.innerHTML = `
                <div class="bg-primary text-white rounded-2xl rounded-tr-sm px-3 py-2 max-w-[85%]">
                    <p class="leading-relaxed">${escapeHtml(content)}</p>
                </div>
                <div class="w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center text-primary text-[10px] font-semibold shrink-0">U</div>
            `;
        } else {
            div.className = 'flex items-start gap-2';
            div.innerHTML = `
                <div class="w-6 h-6 rounded-full bg-primary flex items-center justify-center text-white text-[10px] font-semibold shrink-0">AI</div>
                <div class="bg-gray-50 rounded-2xl rounded-tl-sm px-3 py-2 max-w-[85%]">
                    <p class="text-gray-700 leading-relaxed">${escapeHtml(content)}</p>
                    <button class="tts-btn mt-1 text-xs text-muted hover:text-primary transition-colors flex items-center gap-1" data-text="${escapeHtml(content)}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" /></svg>
                        Dengarkan
                    </button>
                </div>
            `;
            div.querySelector('.tts-btn')?.addEventListener('click', function() {
                if (window.speechSynthesis.speaking) {
                    window.speechSynthesis.cancel();
                    this.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" /></svg> Dengarkan`;
                } else {
                    speakText(this.dataset.text);
                    this.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" /></svg> Stop`;
                }
            });
        }
        widget.appendChild(div);
        widgetScroll();
    }

    function widgetAddLoading() {
        const div = document.createElement('div');
        div.id = 'widget-loading';
        div.className = 'flex items-start gap-2';
        div.innerHTML = `
            <div class="w-6 h-6 rounded-full bg-primary flex items-center justify-center text-white text-[10px] font-semibold shrink-0">AI</div>
            <div class="bg-gray-50 rounded-2xl rounded-tl-sm px-3 py-2">
                <div class="flex gap-1">
                    <span class="w-1.5 h-1.5 bg-primary/40 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-1.5 h-1.5 bg-primary/40 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-1.5 h-1.5 bg-primary/40 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                </div>
            </div>
        `;
        widget.appendChild(div);
        widgetScroll();
    }

    function widgetRemoveLoading() {
        const el = document.getElementById('widget-loading');
        if (el) el.remove();
    }

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    function removeQuickSuggestions() {
        const suggestions = widget.querySelector('.flex-wrap');
        if (suggestions) suggestions.remove();
        const firstMsg = widget.querySelector('.flex.items-start.gap-2:first-child');
        if (firstMsg) firstMsg.remove();
    }

    function sendWidgetMessage() {
        const message = input.value.trim();
        if (!message) return;

        widgetAddMessage('user', message);
        input.value = '';
        removeQuickSuggestions();
        widgetAddLoading();

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
            widgetRemoveLoading();
            if (data.success) {
                widgetAddMessage('assistant', data.response);
            }
        })
        .catch(() => {
            widgetRemoveLoading();
            widgetAddMessage('assistant', 'Maaf, terjadi kesalahan koneksi. Coba lagi.');
        });
    }

    if (sendBtn) sendBtn.addEventListener('click', sendWidgetMessage);
    if (input) input.addEventListener('keydown', (e) => { if (e.key === 'Enter') sendWidgetMessage(); });

    quickBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            input.value = btn.textContent.trim();
            sendWidgetMessage();
        });
    });
})();
</script>

@stack('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
