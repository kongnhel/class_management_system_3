@extends('layouts.app')

@section('content')
<!-- Google Font: Inter សម្រាប់មើលទៅ Modern -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Kantumruy+Pro:wght@300;400;600&display=swap" rel="stylesheet">

<div class="container mx-auto p-2 sm:p-4 h-[calc(100vh-80px)] flex flex-col font-['Kantumruy_Pro','Inter']">
    <div class="bg-white rounded-3xl shadow-2xl flex flex-col flex-grow overflow-hidden border border-gray-100 transition-all">
        
        <!-- Header: Modern Gradient -->
        <div class="bg-gradient-to-r from-green-600 to-emerald-500 p-5 text-white flex items-center justify-between shadow-lg">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/30 shadow-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-green-600 rounded-full"></span>
                </div>
                <div>
                    <h2 class="font-bold text-xl tracking-tight">NMU Smart Assistant</h2>
                    <div class="flex items-center text-xs text-green-100 opacity-90">
                        <span class="mr-1">●</span> ប្រព័ន្ធគ្រប់គ្រងសាលា (Active)
                    </div>
                </div>
            </div>
            <div class="hidden md:block text-right">
                {{-- <p class="text-xs font-medium text-green-100">ជំនាន់ទី ១៨ - NMU</p> --}}
                <p class="text-[10px] opacity-70">Class Management System</p>
            </div>
        </div>

        <!-- Chat Box -->
        <div id="chat-box" class="flex-grow overflow-y-auto p-6 space-y-6 bg-[#f8fafc] scrollbar-thin scrollbar-thumb-gray-200">
            <!-- Welcome Message -->
            <div class="flex justify-start animate-in fade-in duration-700">
                <div class="flex flex-col space-y-1">
                    <div class="bg-white border border-gray-100 text-gray-700 p-4 rounded-2xl rounded-tl-none max-w-[90%] shadow-sm leading-relaxed">
                        សួស្តីបង! ខ្ញុំជា AI ជំនួយការរបស់ **NMU**។ តើបងចង់ឱ្យខ្ញុំជួយពន្យល់ពី Flow ប្រើប្រាស់ប្រព័ន្ធ ឬមានចម្ងល់រឿងមេរៀនមែនទេ? 😊
                    </div>
                    <span class="text-[10px] text-gray-400 ml-1">System Bot</span>
                </div>
            </div>
        </div>

        <!-- Thinking Animation (Hidden by default) -->
        <div id="thinking-indicator" class="hidden px-6 py-3 bg-gray-50 border-t border-gray-100">
            <div class="flex items-center space-x-2 text-gray-500 text-sm italic">
                <div class="flex space-x-1">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-bounce [animation-delay:-.3s]"></div>
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-bounce [animation-delay:-.5s]"></div>
                </div>
                <span>កំពុងត្រិះរិះពិចារណា...</span>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-6 bg-white border-t border-gray-100">
            <form id="chat-form" class="relative flex items-center">
                @csrf
                <input type="text" id="user-input" autocomplete="off"
                    class="w-full bg-gray-50 border-none rounded-2xl pl-6 pr-16 py-4 text-gray-700 focus:ring-2 focus:ring-green-500/20 focus:bg-white transition-all shadow-inner" 
                    placeholder="សួរអំពីការស្រង់វត្តមាន ឬការប្រើប្រាស់ប្រព័ន្ធ..." required>
                
                <button type="submit" id="send-btn" 
                    class="absolute right-2 bg-green-600 text-white p-2.5 rounded-xl hover:bg-green-700 hover:shadow-lg transition-all active:scale-90 group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
            <p class="text-center text-[10px] text-gray-400 mt-3 italic">រក្សាសិទ្ធិដោយ Group 25 - ជំនាន់ទី ១៨</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<style>
    /* Custom Scrollbar */
    #chat-box::-webkit-scrollbar { width: 4px; }
    #chat-box::-webkit-scrollbar-thumb { background-color: #e2e8f0; border-radius: 20px; }
    
    /* Markdown Styling */
    .prose pre { background: #1e293b !important; color: #f8fafc; padding: 1.25rem; border-radius: 12px; border: 1px solid #334155; }
    .prose code { color: #059669; font-weight: 600; background: #f0fdf4; padding: 0.1rem 0.3rem; border-radius: 4px; }
    .prose blockquote { border-left-color: #10b981; color: #475569; }
</style>

<script>
    const chatForm = document.getElementById('chat-form');
    const chatBox = document.getElementById('chat-box');
    const userInput = document.getElementById('user-input');
    const thinkingIndicator = document.getElementById('thinking-indicator');

    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = userInput.value.trim();
        if (!message) return;

        appendMessage('user', message);
        userInput.value = '';
        
        // បង្ហាញ Thinking Animation
        thinkingIndicator.classList.remove('hidden');
        chatBox.scrollTop = chatBox.scrollHeight;

        try {
            const response = await fetch("{{ route('ai.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ message: message })
            });

            const data = await response.json();
            
            // លាក់ Thinking Animation វិញ
            thinkingIndicator.classList.add('hidden');
            
            if (data.message) {
                appendMessage('ai', data.message);
            } else {
                appendMessage('ai', 'សុំទោសបង! Server ប្រហែលជាមានបញ្ហាបន្តិចបន្តួច។');
            }
        } catch (error) {
            thinkingIndicator.classList.add('hidden');
            appendMessage('ai', 'Error: មិនអាចទាក់ទងទៅម៉ាស៊ីនមេបានទេបង!');
        }
    });

    function appendMessage(sender, text) {
        const div = document.createElement('div');
        div.className = sender === 'user' ? 'flex justify-end animate-in slide-in-from-right-5 duration-300' : 'flex justify-start animate-in slide-in-from-left-5 duration-300';
        
        const content = sender === 'user' 
            ? `<div class="bg-green-600 text-white p-4 rounded-2xl rounded-tr-none max-w-[85%] shadow-lg shadow-green-200 text-sm leading-relaxed">${text}</div>`
            : `<div class="flex flex-col space-y-1 max-w-[90%]">
                 <div class="bg-white border border-gray-100 text-gray-800 p-4 rounded-3xl rounded-tl-none shadow-sm prose prose-sm prose-green">${marked.parse(text)}</div>
                 <span class="text-[9px] text-gray-400 ml-2">AI Assistant</span>
               </div>`;
        
        div.innerHTML = content;
        chatBox.appendChild(div);
        chatBox.scrollTop = chatBox.scrollHeight;
    }
</script>
@endsection