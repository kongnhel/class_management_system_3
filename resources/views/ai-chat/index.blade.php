@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Kantumruy+Pro:wght@300;400;600&display=swap" rel="stylesheet">

<button onclick="toggleAIChat()" class="fixed bottom-6 right-6 bg-green-600 text-white p-4 rounded-full shadow-2xl hover:bg-green-700 transition-all z-50 group active:scale-95 flex items-center justify-center">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
    </svg>
</button>

<div id="chat-overlay" onclick="toggleAIChat()" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[59] hidden opacity-0 transition-opacity duration-300"></div>

<div id="ai-sidebar" class="fixed top-0 right-0 h-full w-full sm:w-[450px] bg-white shadow-2xl z-[60] transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col font-['Kantumruy_Pro','Inter']">
    
    <div class="bg-gradient-to-r from-green-600 to-emerald-500 p-5 text-white flex items-center justify-between shadow-md">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-lg tracking-tight leading-tight">NMU Smart Assistant</h2>
                <div class="flex items-center text-[10px] text-green-100 opacity-90">
                    <span class="mr-1">●</span> {{ Auth::user()->role ?? 'User' }} Mode
                </div>
            </div>
        </div>
        <button onclick="toggleAIChat()" class="p-2 hover:bg-white/10 rounded-full transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="flex border-b border-gray-50 bg-gray-50/50 p-2 space-x-2">
        <button type="button" onclick="setOption('info')" id="btn-info" 
            class="flex-1 flex items-center justify-center space-x-2 py-2 rounded-xl text-xs transition-all bg-white shadow-sm text-green-600 border border-green-200 font-semibold">
            <span>ព័ត៌មានទូទៅ</span>
        </button>
        <button type="button" onclick="setOption('process')" id="btn-process" 
            class="flex-1 flex items-center justify-center space-x-2 py-2 rounded-xl text-xs transition-all text-gray-500 hover:bg-white font-medium">
            <span>របៀបប្រើប្រាស់</span>
        </button>
    </div>

    <div id="chat-box" class="flex-grow overflow-y-auto p-5 space-y-5 bg-[#f8fafc] scrollbar-thin scrollbar-thumb-gray-200">
        <div class="flex justify-start">
            <div class="flex flex-col space-y-1">
                <div class="bg-white border border-gray-100 text-gray-700 p-4 rounded-2xl rounded-tl-none max-w-[90%] shadow-sm text-sm leading-relaxed">
                    សួស្តីបង **{{ Auth::user()->name }}**! តើខ្ញុំអាចជួយអ្វីបងបានខ្លះក្នុងប្រព័ន្ធ NMU? 😊
                </div>
                <span class="text-[9px] text-gray-400 ml-1">System Bot</span>
            </div>
        </div>
    </div>

    <div id="thinking-indicator" class="hidden px-6 py-3 bg-gray-50 border-t border-gray-100">
        <div class="flex items-center space-x-2 text-gray-500 text-[11px] italic">
            <div class="flex space-x-1">
                <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-bounce"></div>
                <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-bounce [animation-delay:-.3s]"></div>
                <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-bounce [animation-delay:-.5s]"></div>
            </div>
            <span>កំពុងផ្ទៀងផ្ទាត់...</span>
        </div>
    </div>

    <div class="p-5 bg-white border-t border-gray-100">
        <form id="chat-form" class="relative flex items-center">
            @csrf
            <input type="hidden" id="chat-option" value="info">
            <input type="text" id="user-input" autocomplete="off"
                class="w-full bg-gray-50 border border-gray-100 rounded-2xl pl-5 pr-14 py-3.5 text-sm text-gray-700 focus:ring-2 focus:ring-green-500/20 focus:bg-white transition-all outline-none" 
                placeholder="វាយសំណួររបស់បង..." required>
            
            <button type="submit" class="absolute right-2 bg-green-600 text-white p-2 rounded-xl hover:bg-green-700 transition-all active:scale-90">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </button>
        </form>
        <p class="text-center text-[9px] text-gray-400 mt-3 uppercase tracking-wider italic">NMU Smart Assistant v1.0</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<style>
    #chat-box::-webkit-scrollbar { width: 4px; }
    #chat-box::-webkit-scrollbar-thumb { background-color: #e2e8f0; border-radius: 20px; }
    .prose pre { background: #1e293b !important; color: #f8fafc; padding: 1rem; border-radius: 8px; font-size: 12px; }
    .prose code { color: #059669; font-weight: 600; background: #f0fdf4; padding: 0.1rem 0.2rem; border-radius: 4px; }
</style>

<script>
    // បិទ/បើក Sidebar
    function toggleAIChat() {
        const sidebar = document.getElementById('ai-sidebar');
        const overlay = document.getElementById('chat-overlay');
        
        if (sidebar.classList.contains('translate-x-full')) {
            sidebar.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.add('opacity-100'), 10);
        } else {
            sidebar.classList.add('translate-x-full');
            overlay.classList.remove('opacity-100');
            setTimeout(() => overlay.classList.add('hidden'), 300);
        }
    }

    const chatForm = document.getElementById('chat-form');
    const chatBox = document.getElementById('chat-box');
    const userInput = document.getElementById('user-input');
    const chatOptionInput = document.getElementById('chat-option');
    const thinkingIndicator = document.getElementById('thinking-indicator');

    function setOption(option) {
        chatOptionInput.value = option;
        const btnInfo = document.getElementById('btn-info');
        const btnProcess = document.getElementById('btn-process');

        if(option === 'info') {
            btnInfo.className = "flex-1 flex items-center justify-center space-x-2 py-2 rounded-xl text-xs transition-all bg-white shadow-sm text-green-600 border border-green-200 font-semibold";
            btnProcess.className = "flex-1 flex items-center justify-center space-x-2 py-2 rounded-xl text-xs transition-all text-gray-500 hover:bg-white font-medium";
            userInput.placeholder = "សួរអំពីព័ត៌មានវត្តមាន...";
        } else {
            btnProcess.className = "flex-1 flex items-center justify-center space-x-2 py-2 rounded-xl text-xs transition-all bg-white shadow-sm text-green-600 border border-green-200 font-semibold";
            btnInfo.className = "flex-1 flex items-center justify-center space-x-2 py-2 rounded-xl text-xs transition-all text-gray-500 hover:bg-white font-medium";
            userInput.placeholder = "សួរអំពីរបៀបប្រើប្រាស់...";
        }
    }

    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = userInput.value.trim();
        const option = chatOptionInput.value;
        if (!message) return;

        appendMessage('user', message);
        userInput.value = '';
        thinkingIndicator.classList.remove('hidden');
        chatBox.scrollTop = chatBox.scrollHeight;

        try {
            const response = await fetch("{{ route('ai.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ message: message, option: option })
            });

            const data = await response.json();
            thinkingIndicator.classList.add('hidden');
            
            if (data.message) {
                appendMessage('ai', data.message);
            } else {
                appendMessage('ai', 'សុំទោសបង! មានបញ្ហាបន្តិចបន្តួច។');
            }
        } catch (error) {
            thinkingIndicator.classList.add('hidden');
            appendMessage('ai', 'Error: មិនអាចទាក់ទងម៉ាស៊ីនមេបានទេ!');
        }
    });

    function appendMessage(sender, text) {
        const div = document.createElement('div');
        div.className = sender === 'user' ? 'flex justify-end' : 'flex justify-start';

        const safeText = sender === 'user' ? escapeHtml(text) : text;

        const content = sender === 'user'
            ? `<div class="bg-green-600 text-white p-3 rounded-2xl rounded-tr-none max-w-[85%] shadow-md text-sm">${safeText}</div>`
            : `<div class="flex flex-col space-y-1 max-w-[90%]">
                 <div class="bg-white border border-gray-100 text-gray-800 p-4 rounded-2xl rounded-tl-none shadow-sm prose prose-sm prose-green text-sm">${marked.parse(safeText)}</div>
                 <span class="text-[9px] text-gray-400 ml-1 italic">NMU AI Assistant</span>
               </div>`;

        div.innerHTML = content;
        chatBox.appendChild(div);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
@endsection