@props([])

@php
    $user = Auth::user();
@endphp

{{-- FAB Button --}}
<div id="draggableChat" class="fixed flex flex-col items-end group z-[100]"
     style="bottom: 24px; right: 24px; touch-action: none;">

    <div class="mb-3 bg-gray-900 text-white px-4 py-2.5 rounded-2xl shadow-xl text-xs font-medium opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 pointer-events-none relative mr-2 whitespace-nowrap">
        {{ __('AI Chatbot assistant ​​​ហ្នឹងមកដល់ក្នុងពេលឆាប់ៗនេះ') }}
        <div class="absolute -bottom-1 right-4 w-2 h-2 bg-gray-900 rotate-45"></div>
    </div>

    <button id="chatBtn"
            class="relative w-14 h-14 bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-full shadow-[0_8px_30px_-4px_rgba(16,185,129,0.5)] hover:shadow-[0_8px_40px_-4px_rgba(16,185,129,0.6)] hover:scale-110 active:scale-95 transition-all duration-300 flex items-center justify-center cursor-move">
        <div id="unread-badge" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 border-2 border-white rounded-full items-center justify-center text-[9px] font-bold text-white hidden flex items-center justify-center">0</div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <div class="absolute bottom-0.5 right-0.5 w-3.5 h-3.5 bg-green-400 border-2 border-white rounded-full animate-pulse"></div>
    </button>
</div>

{{-- Overlay --}}
<div id="chat-overlay" onclick="toggleAIChat()" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[90] hidden opacity-0 transition-opacity duration-300 ease-in-out"></div>

{{-- Sidebar --}}
<div id="ai-sidebar" class="fixed top-0 right-0 h-full w-full sm:w-[480px] bg-white shadow-[-10px_0_50px_-12px_rgba(0,0,0,0.15)] z-[100] transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col font-['Battambang'] overflow-hidden">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-emerald-600 via-green-500 to-teal-500 p-5 text-white flex items-center justify-between relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white rounded-full"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-white rounded-full"></div>
        </div>
        <div class="flex items-center space-x-4 relative z-10">
            <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/30 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-lg leading-tight tracking-tight">NMU AI Assistant</h2>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <div class="w-1.5 h-1.5 bg-green-300 rounded-full animate-pulse"></div>
                    <span class="text-[11px] text-white/80 font-medium">{{ __('Online') }}</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2 relative z-10">
            <button onclick="showClearConfirm()"
                    class="p-2.5 bg-white/15 hover:bg-white/25 text-white rounded-xl transition-all backdrop-blur-sm border border-white/20"
                    title="{{ __('លុបប្រវត្តិ') }}">
                <i class="fas fa-trash-alt text-sm"></i>
            </button>
            <button onclick="toggleAIChat()" class="p-2.5 hover:bg-white/15 rounded-xl transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Mode Tabs --}}
    <div class="flex border-b border-gray-100 bg-white px-3 py-2 gap-1">
        <button type="button" onclick="setOption('info')" id="btn-info" class="flex-1 py-2.5 rounded-xl text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold transition-all flex items-center justify-center gap-1.5">
            <i class="fas fa-info-circle text-[10px]"></i> {{ __('ព័ត៌មាន') }}
        </button>
        <button type="button" onclick="setOption('search')" id="btn-search" class="flex-1 py-2.5 rounded-xl text-xs text-gray-400 hover:text-gray-600 hover:bg-gray-50 font-bold transition-all flex items-center justify-center gap-1.5">
            <i class="fas fa-search text-[10px]"></i> {{ __('ស្វែងរក') }}
        </button>
        <button type="button" onclick="setOption('process')" id="btn-process" class="flex-1 py-2.5 rounded-xl text-xs text-gray-400 hover:text-gray-600 hover:bg-gray-50 font-bold transition-all flex items-center justify-center gap-1.5">
            <i class="fas fa-lightbulb text-[10px]"></i> {{ __('របៀបប្រើ') }}
        </button>
    </div>

    {{-- Chat Box --}}
    <div id="chat-box" class="flex-grow overflow-y-auto p-5 space-y-4 bg-[#f8fafc] custom-scrollbar">
        {{-- Welcome Message --}}
        <div class="flex items-start gap-3 animate-fade-in">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center flex-shrink-0 shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl rounded-tl-md p-4 shadow-sm max-w-[85%]">
                <p class="text-sm text-gray-700 leading-relaxed">{{ __('ស្វាគមន៍! ខ្ញុំជា AI Assistant របស់ NMU។ ខ្ញុំអាចជួយអ្នកពីព័ត៌មានសិស្ស និងសាស្ត្រាចារ្យ ការស្វែងរក ឬបង្រៀនរបៀបប្រើប្រាស់ប្រព័ន្ធ។') }}</p>
            </div>
        </div>
    </div>

    {{-- Thinking Indicator + Stop Button --}}
    <div id="thinking-indicator" class="hidden px-5 py-3 bg-white border-t border-gray-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="flex space-x-1">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: -0.3s"></div>
                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: -0.5s"></div>
                </div>
                <span class="text-xs text-gray-400 font-medium">{{ __('កំពុងគិត...') }}</span>
            </div>
            <button id="stop-btn" onclick="stopGenerating()" class="px-3 py-1.5 bg-red-50 text-red-500 text-[11px] font-bold rounded-lg hover:bg-red-100 transition-all flex items-center gap-1.5 border border-red-100">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>
                {{ __('ឈប់') }}
            </button>
        </div>
    </div>

    {{-- Input --}}
    <div class="p-4 bg-white border-t border-gray-100">
        <form id="chat-form" class="relative flex items-center gap-2">
            @csrf
            <input type="hidden" id="chat-option" value="info">
            <div class="flex-1 relative">
                <input type="text" id="user-input" autocomplete="off"
                    class="w-full bg-gray-50 border border-gray-200 rounded-2xl pl-5 pr-12 py-3.5 text-sm outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 transition-all placeholder:text-gray-400"
                    placeholder="{{ __('សរសេរសំណួរនៅទីនេះ...') }}" required>
                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-emerald-500 text-white p-2.5 rounded-xl hover:bg-emerald-600 active:scale-95 shadow-md transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Clear History Confirm Modal --}}
<div id="confirm-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-md z-[200] flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full overflow-hidden transform transition-all">
        <div class="p-8 text-center">
            <div class="mx-auto w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mb-5">
                <i class="fas fa-trash-alt text-red-500 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('លុបប្រវត្តិសន្ទនា?') }}</h3>
            <p class="text-sm text-gray-500 leading-relaxed">
                {{ __('សកម្មភាពនេះនឹងលុបប្រវត្តិសន្ទនាទាំងអស់ជាអចិន្ត្រៃយ៍។') }}
            </p>
        </div>
        <div class="px-6 pb-6 flex gap-3">
            <button onclick="hideConfirmModal()" class="flex-1 py-3 font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-2xl transition-all text-sm">{{ __('បោះបង់') }}</button>
            <button onclick="confirmClearHistory()" class="flex-1 py-3 font-semibold text-white bg-red-500 hover:bg-red-600 rounded-2xl transition-all shadow-lg shadow-red-200 text-sm">{{ __('លុបចោល') }}</button>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fade-in 0.3s ease-out; }
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
</style>
