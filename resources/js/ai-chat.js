// NMU Smart Assistant - AI Chat JavaScript
// ==========================================

(function() {
    const currentUserName = typeof CURRENT_USER_NAME !== 'undefined' ? CURRENT_USER_NAME : '';
    const csrfToken = typeof CSRF_TOKEN !== 'undefined' ? CSRF_TOKEN : '';
    const routes = typeof AI_ROUTES !== 'undefined' ? AI_ROUTES : {};

    let lastMessageId = 0;
    let currentAbortController = null;

    // --- Stop Generating ---
    window.stopGenerating = function() {
        if (currentAbortController) {
            currentAbortController.abort();
            currentAbortController = null;
        }
        document.getElementById('thinking-indicator')?.classList.add('hidden');
        document.getElementById('user-input')?.removeAttribute('disabled');
        document.getElementById('user-input')?.focus();
    };

    // --- Toggle Sidebar ---
    window.toggleAIChat = function() {
        const sidebar = document.getElementById('ai-sidebar');
        const overlay = document.getElementById('chat-overlay');
        if (!sidebar || !overlay) return;

        if (sidebar.classList.contains('translate-x-full')) {
            overlay.classList.remove('hidden');
            setTimeout(() => {
                sidebar.classList.remove('translate-x-full');
                overlay.classList.add('opacity-100');
                loadChatHistory();
                document.getElementById('user-input')?.focus();
            }, 10);
        } else {
            sidebar.classList.add('translate-x-full');
            overlay.classList.remove('opacity-100');
            setTimeout(() => overlay.classList.add('hidden'), 300);
        }
    };

    // --- Set Chat Mode ---
    window.setOption = function(option) {
        const chatOptionInput = document.getElementById('chat-option');
        const userInput = document.getElementById('user-input');
        const btnInfo = document.getElementById('btn-info');
        const btnSearch = document.getElementById('btn-search');
        const btnProcess = document.getElementById('btn-process');
        if (!chatOptionInput || !userInput) return;

        chatOptionInput.value = option;

        // Reset all buttons
        const baseClass = "flex-1 py-2.5 rounded-xl text-xs font-bold transition-all";
        const activeClass = `${baseClass} bg-white shadow-sm text-green-600 border border-green-200`;
        const inactiveClass = `${baseClass} text-gray-500 hover:bg-white`;

        btnInfo.className = inactiveClass;
        btnSearch.className = inactiveClass;
        btnProcess.className = inactiveClass;

        if (option === 'info') {
            btnInfo.className = activeClass;
            userInput.placeholder = "សួរអំពីព័ត៌មានវត្តមាន និងទិន្នន័យ...";
        } else if (option === 'search') {
            btnSearch.className = activeClass;
            userInput.placeholder = "ស្វែងរកសិស្ស សាស្ត្រាចារ្យ មុខវិជ្ជា ព័ត៌មានសាលា...";
        } else {
            btnProcess.className = activeClass;
            userInput.placeholder = "សួរអំពីរបៀបប្រើប្រាស់ប្រព័ន្ធ...";
        }
    };

    // --- Quick Query ---
    window.sendQuickQuery = function(query, option) {
        const userInput = document.getElementById('user-input');
        const chatForm = document.getElementById('chat-form');
        if (userInput && chatForm) {
            if (option) {
                setOption(option);
            }
            userInput.value = query;
            chatForm.dispatchEvent(new Event('submit'));
        }
    };

    // --- Copy Message ---
    window.copyMessage = function(btn) {
        const msgEl = btn.closest('.ai-msg-wrap')?.querySelector('.msg-content');
        if (!msgEl) return;
        const text = msgEl.textContent || msgEl.innerText;
        navigator.clipboard.writeText(text).then(() => {
            btn.innerHTML = '<i class="fas fa-check"></i>';
            setTimeout(() => { btn.innerHTML = '<i class="fas fa-copy"></i>'; }, 1500);
        });
    };

    // --- Retry Last Message ---
    window.retryLastMessage = function() {
        const chatBox = document.getElementById('chat-box');
        const lastUserMsg = chatBox?.querySelector('.user-msg-text');
        if (lastUserMsg) {
            const text = lastUserMsg.textContent;
            document.getElementById('user-input').value = text;
            document.getElementById('chat-form').dispatchEvent(new Event('submit'));
        }
    };

    // --- Feedback (thumbs up/down) ---
    window.sendFeedback = function(messageId, type) {
        fetch(routes.feedback || '/ai/feedback', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ message_id: messageId, feedback: type })
        });
        const wrap = document.querySelector('[data-msg-id="' + messageId + '"]');
        if (wrap) {
            wrap.querySelectorAll('.feedback-btn').forEach(b => b.classList.add('hidden'));
            const selected = wrap.querySelector('.feedback-' + type);
            if (selected) { selected.classList.remove('hidden'); selected.classList.add('text-green-600'); }
        }
    };

    // --- Load History ---
    async function loadChatHistory() {
        const chatBox = document.getElementById('chat-box');
        if (!chatBox) return;

        chatBox.innerHTML = '<div class="flex justify-center py-8"><span class="text-gray-400">កំពុងផ្ទុកប្រវត្តិសន្ទនា...</span></div>';

        try {
            const response = await fetch(routes.history);
            const data = await response.json();
            chatBox.innerHTML = '';

            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => appendMessage(msg.sender, msg.message, false, msg.id));
            } else {
                appendWelcomeMessage();
            }
        } catch (e) {
            console.error("Failed to load history:", e);
            chatBox.innerHTML = '';
            appendWelcomeMessage();
        }
    }

    // --- Welcome Message ---
    function appendWelcomeMessage() {
        const chatBox = document.getElementById('chat-box');
        const userRole = typeof CURRENT_USER_ROLE !== 'undefined' ? CURRENT_USER_ROLE : '';

        let quickActions = '';
        if (userRole === 'admin') {
            quickActions = `
                <button onclick="sendQuickQuery('តើមានសិស្សប៉ុន្មាននាក់ក្នុងប្រព័ន្ធ?', 'search')" class="text-[11px] bg-emerald-50 border border-emerald-100 text-emerald-700 px-3 py-2 rounded-full hover:bg-emerald-100 transition-all">🔍 ស្វែងរកសិស្ស</button>
                <button onclick="sendQuickQuery('តើមានសាស្ត្រាចារ្យប៉ុន្មាននាក់?', 'search')" class="text-[11px] bg-emerald-50 border border-emerald-100 text-emerald-700 px-3 py-2 rounded-full hover:bg-emerald-100 transition-all">👨‍🏫 សាស្ត្រាចារ្យ</button>
                <button onclick="sendQuickQuery('មុខវិជ្ជាទាំងអស់ក្នុងសាលា', 'search')" class="text-[11px] bg-emerald-50 border border-emerald-100 text-emerald-700 px-3 py-2 rounded-full hover:bg-emerald-100 transition-all">📚 មុខវិជ្ជា</button>
                <button onclick="sendQuickQuery('ព័ត៌មានសាកលវិទ្យាល័យជាតិមានជ័យ', 'search')" class="text-[11px] bg-blue-50 border border-blue-100 text-blue-700 px-3 py-2 rounded-full hover:bg-blue-100 transition-all">🏛️ NMU Info</button>`;
        } else if (userRole === 'professor') {
            quickActions = `
                <button onclick="sendQuickQuery('បង្ហាញថ្នាក់ទាំងអស់របស់ខ្ញុំ', 'search')" class="text-[11px] bg-green-50 border border-green-100 text-green-700 px-3 py-2 rounded-full hover:bg-green-100 transition-all">📚 ថ្នាក់របស់ខ្ញុំ</button>
                <button onclick="sendQuickQuery('សិស្សក្នុងថ្នាក់របស់ខ្ញុំ', 'search')" class="text-[11px] bg-green-50 border border-green-100 text-green-700 px-3 py-2 rounded-full hover:bg-green-100 transition-all">👨‍🎓 សិស្សរបស់ខ្ញុំ</button>
                <button onclick="sendQuickQuery('តើខ្ញុំត្រូវស្រង់វត្តមានយ៉ាងដូចម្តេច?', 'process')" class="text-[11px] bg-green-50 border border-green-100 text-green-700 px-3 py-2 rounded-full hover:bg-green-100 transition-all">📝 របៀបស្រង់វត្តមាន</button>
                <button onclick="sendQuickQuery('ព័ត៌មានសាកលវិទ្យាល័យ', 'search')" class="text-[11px] bg-blue-50 border border-blue-100 text-blue-700 px-3 py-2 rounded-full hover:bg-blue-100 transition-all">🏛️ NMU Info</button>`;
        } else if (userRole === 'student') {
            quickActions = `
                <button onclick="sendQuickQuery('ពិន្ទុរបស់ខ្ញុំ', 'search')" class="text-[11px] bg-purple-50 border border-purple-100 text-purple-700 px-3 py-2 rounded-full hover:bg-purple-100 transition-all">📊 ពិន្ទុរបស់ខ្ញុំ</button>
                <button onclick="sendQuickQuery('វត្តមានរបស់ខ្ញុំ', 'search')" class="text-[11px] bg-purple-50 border border-purple-100 text-purple-700 px-3 py-2 rounded-full hover:bg-purple-100 transition-all">🙋 វត្តមានរបស់ខ្ញុំ</button>
                <button onclick="sendQuickQuery('កាលវិភាគសិក្សារបស់ខ្ញុំ', 'search')" class="text-[11px] bg-purple-50 border border-purple-100 text-purple-700 px-3 py-2 rounded-full hover:bg-purple-100 transition-all">📅 កាលវិភាគរៀន</button>
                <button onclick="sendQuickQuery('ព័ត៌មានសាកលវិទ្យាល័យ', 'search')" class="text-[11px] bg-blue-50 border border-blue-100 text-blue-700 px-3 py-2 rounded-full hover:bg-blue-100 transition-all">🏛️ NMU Info</button>`;
        }

        chatBox.innerHTML = `
            <div class="flex justify-start">
                <div class="flex items-start space-x-3 max-w-[90%]">
                    <div class="w-10 h-10 rounded-xl bg-green-600 flex-shrink-0 flex items-center justify-center text-white shadow-sm">
                        <span class="text-xs font-bold">NMU</span>
                    </div>
                    <div class="bg-white border border-gray-100 text-gray-700 p-5 rounded-2xl rounded-tl-none shadow-sm text-base leading-relaxed">
                        សួស្តី <strong>${currentUserName}</strong>! 👋<br>
                        ខ្ញុំជា <strong>NMU Smart Assistant</strong>។ ខ្ញុំអាចជួយអ្នករកទិន្នន័យក្នុងប្រព័ន្ធ និងព័ត៌មានពីសាកលវិទ្យាល័យជាតិមានជ័យ។<br><br>
                        <span class="text-xs text-gray-400">ជ្រើសរើស <strong>ស្វែងរក</strong> ដើម្បីស្វែងរកទិន្នន័យក្នុង Database និងគេហទំព័រសាលា។</span>
                        <div class="flex flex-wrap gap-2 mt-4">${quickActions}</div>
                    </div>
                </div>
            </div>`;
    }

    // --- Append Message ---
    function appendMessage(sender, text, scroll = true, msgId = null) {
        const chatBox = document.getElementById('chat-box');
        if (!chatBox) return;

        const div = document.createElement('div');
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const safeText = sender === 'user' ? escapeHtml(text) : text;

        if (sender === 'user') {
            div.innerHTML = `
                <div class="flex justify-end mb-6">
                    <div class="flex flex-col items-end max-w-[85%]">
                        <div class="bg-green-600 text-white p-4 rounded-2xl rounded-tr-none shadow-sm text-base user-msg-text">${safeText}</div>
                        <span class="text-[10px] text-gray-400 mt-1">${time}</span>
                    </div>
                </div>`;
        } else {
            const msgIdAttr = msgId ? `data-msg-id="${msgId}"` : '';
            div.innerHTML = `
                <div class="flex justify-start mb-6 ai-msg-wrap" ${msgIdAttr}>
                    <div class="flex items-start space-x-3 max-w-[92%] group">
                        <div class="w-10 h-10 rounded-xl bg-green-100 flex-shrink-0 flex items-center justify-center border border-green-200">
                            <span class="text-xs font-bold text-green-600">NMU</span>
                        </div>
                        <div class="flex flex-col">
                            <div class="bg-white border border-gray-100 text-gray-800 p-5 rounded-2xl rounded-tl-none shadow-sm prose prose-sm prose-green max-w-full text-base leading-relaxed msg-content">${marked.parse(safeText)}</div>
                            <div class="flex items-center space-x-3 mt-2 ml-1">
                                <button onclick="copyMessage(this)" class="text-gray-400 hover:text-green-600 transition-colors text-xs" title="Copy"><i class="fas fa-copy"></i></button>
                                ${msgId ? `
                                <button onclick="sendFeedback(${msgId}, 'up')" class="feedback-btn feedback-up text-gray-400 hover:text-green-600 transition-colors text-xs" title="Good"><i class="fas fa-thumbs-up"></i></button>
                                <button onclick="sendFeedback(${msgId}, 'down')" class="feedback-btn feedback-down text-gray-400 hover:text-red-500 transition-colors text-xs" title="Bad"><i class="fas fa-thumbs-down"></i></button>
                                ` : ''}
                                <span class="text-[10px] text-gray-400 italic font-medium">NMU Smart Assistant</span>
                                <span class="text-[10px] text-gray-400">•</span>
                                <span class="text-[10px] text-gray-400">${time}</span>
                            </div>
                        </div>
                    </div>
                </div>`;
        }

        chatBox.appendChild(div);
        if (scroll) chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // --- Clear History ---
    window.showClearConfirm = function() {
        document.getElementById('confirm-modal').classList.remove('hidden');
        document.getElementById('confirm-modal').classList.add('flex');
    };

    window.hideConfirmModal = function() {
        const modal = document.getElementById('confirm-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    window.confirmClearHistory = async function() {
        hideConfirmModal();
        const chatBox = document.getElementById('chat-box');
        chatBox.innerHTML = '<div class="flex justify-center py-12"><span class="text-red-500">កំពុងលុបប្រវត្តិ...</span></div>';

        try {
            const response = await fetch(routes['clear-history'], {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });

            if (response.ok) {
                chatBox.innerHTML = '';
                appendWelcomeMessage();
                if (typeof showToast === 'function') showToast('ប្រវត្តិត្រូវបានលុប។', 'success');
            } else {
                throw new Error();
            }
        } catch (e) {
            console.error(e);
            if (typeof showToast === 'function') showToast("មានបញ្ហាក្នុងការលុបប្រវត្តិ។", 'error');
            loadChatHistory();
        }
    };

    // --- Submit Handler ---
    document.addEventListener('DOMContentLoaded', function() {
        const chatForm = document.getElementById('chat-form');
        const thinkingIndicator = document.getElementById('thinking-indicator');

        if (chatForm) {
            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const messageInput = document.getElementById('user-input');
                const message = messageInput.value.trim();
                if (!message) return;

                appendMessage('user', message);
                messageInput.value = '';
                messageInput.setAttribute('disabled', 'true');
                thinkingIndicator.classList.remove('hidden');

                currentAbortController = new AbortController();

                try {
                    const response = await fetch(routes.send, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ message, option: document.getElementById('chat-option').value }),
                        signal: currentAbortController.signal
                    });

                    currentAbortController = null;
                    const data = await response.json();
                    thinkingIndicator.classList.add('hidden');
                    messageInput.removeAttribute('disabled');
                    messageInput.focus();

                    if (response.status === 429) {
                        appendMessage('ai', data.message || 'សូមរង់ចាំមួយភ្លែត។');
                    } else if (response.ok) {
                        appendMessage('ai', data.message || 'សុំទោស មានបញ្ហា។', true, data.message_id);
                    } else {
                        appendMessage('ai', data.message || 'សុំទោស មានបញ្ហាបច្ចេកទេស។');
                    }
                } catch (error) {
                    currentAbortController = null;
                    thinkingIndicator.classList.add('hidden');
                    messageInput.removeAttribute('disabled');
                    messageInput.focus();

                    if (error.name === 'AbortError') {
                        appendMessage('ai', '_(ការឆ្លើយតបត្រូវបានឈប់។)_');
                    } else {
                        appendMessage('ai', 'មិនអាចភ្ជាប់ទៅ AI បានទេ។ សូមព្យាយាមម្តងទៀត។');
                    }
                }
            });
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            const sidebar = document.getElementById('ai-sidebar');
            if (!sidebar || sidebar.classList.contains('translate-x-full')) return;

            if (e.key === 'Escape') {
                toggleAIChat();
            }
        });

        // Modal click outside
        const modal = document.getElementById('confirm-modal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) hideConfirmModal();
            });
        }

        makeDraggable();
    });

    // --- Draggable FAB ---
    function makeDraggable() {
        const container = document.getElementById('draggableChat');
        if (!container) return;

        let isDragging = false;
        let startX, startY, initialX, initialY;

        const startDrag = (e) => {
            isDragging = true;
            const clientX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
            const clientY = e.type === 'touchstart' ? e.touches[0].clientY : e.clientY;
            const rect = container.getBoundingClientRect();
            startX = clientX;
            startY = clientY;
            initialX = rect.left;
            initialY = rect.top;
            container.style.transition = 'none';
        };

        const onDrag = (e) => {
            if (!isDragging) return;
            const clientX = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
            const clientY = e.type === 'touchmove' ? e.touches[0].clientY : e.clientY;
            let x = Math.max(10, Math.min(initialX + clientX - startX, window.innerWidth - container.offsetWidth - 10));
            let y = Math.max(10, Math.min(initialY + clientY - startY, window.innerHeight - container.offsetHeight - 10));
            container.style.left = x + 'px';
            container.style.top = y + 'px';
            container.style.bottom = 'auto';
            container.style.right = 'auto';
        };

        const stopDrag = () => {
            isDragging = false;
            container.style.transition = 'all 0.3s cubic-bezier(0.18, 0.89, 0.32, 1.28)';
        };

        container.addEventListener('mousedown', startDrag);
        document.addEventListener('mousemove', onDrag);
        document.addEventListener('mouseup', stopDrag);
        container.addEventListener('touchstart', startDrag, { passive: false });
        document.addEventListener('touchmove', onDrag, { passive: false });
        document.addEventListener('touchend', stopDrag);
    }
})();
