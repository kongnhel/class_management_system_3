@props([])

@php
    $user = Auth::user();
@endphp

{{-- FAB Button --}}
<div id="draggableChat" class="fixed flex flex-col items-end group z-[100]"
     style="bottom: 24px; right: 24px; touch-action: none;">

    <div class="mb-3 bg-white text-gray-800 px-4 py-2 rounded-2xl shadow-xl border border-gray-100 text-xs font-medium opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 pointer-events-none relative mr-2">
        {{ __('សួរអ្វីមួយទៅកាន់ AI...') }}
        <div class="absolute -bottom-1 right-4 w-2 h-2 bg-white border-r border-b border-gray-100 rotate-45"></div>
    </div>

    <button onclick="toggleAIChat()" id="chatBtn"
            class="relative bg-[#26D741] text-white p-4 rounded-full shadow-[0_8px_25px_-5px_rgba(38,215,65,0.5)] hover:scale-110 active:scale-90 transition-all duration-300 flex items-center justify-center cursor-move">
        <div id="unread-badge" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 border-2 border-white rounded-full items-center justify-center text-[9px] font-bold text-white hidden">0</div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.477 2 2 6.145 2 11.258c0 2.91 1.453 5.503 3.735 7.153V22l3.418-1.875c.915.254 1.883.391 2.847.391 5.523 0 10-4.145 10-9.258C22 6.145 17.523 2 12 2zm1.142 12.358l-2.571-2.742-5.014 2.742 5.513-5.858 2.657 2.742 4.928-2.742-5.513 5.858z"/>
        </svg>
        <div class="absolute bottom-0 right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
    </button>
</div>

{{-- Overlay --}}
<div id="chat-overlay" onclick="toggleAIChat()" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[90] hidden opacity-0 transition-opacity duration-300 ease-in-out"></div>

{{-- Sidebar --}}
<div id="ai-sidebar" class="fixed top-0 right-0 h-full w-full sm:w-[600px] bg-white shadow-[0_0_50px_-12px_rgba(0,0,0,0.3)] z-[100] transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col font-['Battambang']">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-green-600 to-emerald-500 p-6 text-white flex items-center justify-between shadow-md">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-xl leading-none">NMU Smart Assistant</h2>
                <span class="text-xs text-white/80">{{ __('ប្រព័ន្ធគ្រប់គ្រងសាលា') }}</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="showClearConfirm()"
                    class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-sm rounded-2xl transition-all flex items-center gap-2">
                <i class="fas fa-trash-alt"></i>
                <span class="hidden sm:inline">{{ __('លុបប្រវត្តិ') }}</span>
            </button>
            <button onclick="toggleAIChat()" class="p-2 hover:bg-white/10 rounded-full transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Mode Tabs --}}
    <div class="flex border-b border-gray-50 bg-gray-50/50 p-2 space-x-2">
        <button type="button" onclick="setOption('info')" id="btn-info" class="flex-1 py-2.5 rounded-xl text-xs bg-white shadow-sm text-green-600 border border-green-200 font-bold transition-all">
            <i class="fas fa-info-circle mr-1"></i> {{ __('ព័ត៌មាន') }}
        </button>
        <button type="button" onclick="setOption('search')" id="btn-search" class="flex-1 py-2.5 rounded-xl text-xs text-gray-500 hover:bg-white font-bold transition-all">
            <i class="fas fa-search mr-1"></i> {{ __('ស្វែងរក') }}
        </button>
        <button type="button" onclick="setOption('process')" id="btn-process" class="flex-1 py-2.5 rounded-xl text-xs text-gray-500 hover:bg-white font-bold transition-all">
            <i class="fas fa-cog mr-1"></i> {{ __('របៀបប្រើ') }}
        </button>
    </div>

    {{-- Chat Box --}}
    <div id="chat-box" class="flex-grow overflow-y-auto p-6 space-y-6 bg-[#f8fafc] custom-scrollbar"></div>

    {{-- Thinking Indicator + Stop Button --}}
    <div id="thinking-indicator" class="hidden px-6 py-3 bg-gray-50 border-t border-gray-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2 text-gray-500 text-sm">
                <div class="flex space-x-1">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-bounce" style="animation-delay: -0.3s"></div>
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-bounce" style="animation-delay: -0.5s"></div>
                </div>
                <span class="italic">{{ __('កំពុងគិត...') }}</span>
            </div>
            <button id="stop-btn" onclick="stopGenerating()" class="px-4 py-1.5 bg-red-50 text-red-600 text-xs font-bold rounded-full hover:bg-red-100 transition-all flex items-center gap-1.5">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>
                {{ __('ឈប់') }}
            </button>
        </div>
    </div>

    {{-- Input --}}
    <div class="p-6 bg-white border-t border-gray-100">
        <form id="chat-form" class="relative flex items-center gap-3">
            @csrf
            <input type="hidden" id="chat-option" value="info">
            <input type="text" id="user-input" autocomplete="off"
                class="flex-grow bg-gray-50 border border-gray-200 rounded-2xl px-6 py-4 text-base outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all"
                placeholder="{{ __('សរសេរសំណួរនៅទីនេះ...') }}" required>
            <button type="submit" class="bg-green-600 text-white p-4 rounded-xl hover:bg-green-700 active:scale-95 shadow-lg transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </button>
        </form>
    </div>
</div>

{{-- Clear History Confirm Modal --}}
<div id="confirm-modal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-[200] flex items-center justify-center">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
        <div class="p-8 text-center">
            <div class="mx-auto w-20 h-20 bg-red-100 rounded-3xl flex items-center justify-center mb-6">
                <i class="fas fa-trash-alt text-red-600 text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ __('លុបប្រវត្តិសន្ទនា?') }}</h3>
            <p class="text-gray-600">
                {{ __('សកម្មភាពនេះនឹងលុបប្រវត្តិសន្ទនាទាំងអស់ជាអចិន្ត្រៃយ៍។') }}<br>
                {{ __('អ្នកពិតជាចង់បន្តទេ?') }}
            </p>
        </div>
        <div class="bg-gray-50 px-6 py-5 flex gap-3">
            <button onclick="hideConfirmModal()" class="flex-1 py-4 font-semibold text-gray-700 bg-white border border-gray-300 rounded-2xl hover:bg-gray-50">{{ __('បោះបង់') }}</button>
            <button onclick="confirmClearHistory()" class="flex-1 py-4 font-semibold text-white bg-red-600 rounded-2xl hover:bg-red-700">{{ __('បាទ/ចាស លុបចោល') }}</button>
        </div>
    </div>
</div>
