<x-app-layout>
    <div class="py-6 md:py-12 bg-gradient-to-br from-gray-50 to-green-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-2 md:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl p-4 md:p-8 lg:p-12 border border-gray-100">

                {{-- Header Section --}}
                <div class="flex flex-col md:flex-row items-center justify-between mb-6 md:mb-10 pb-6 border-b border-gray-100 gap-4">
                    <div class="text-center md:text-left">
                        <h2 class="font-extrabold text-2xl md:text-4xl text-gray-900 leading-tight">
                            <i class="fas fa-id-card text-green-600 mr-2 md:hidden"></i>{{ __('ប្រវត្តិរូប') }}
                        </h2>
                        <p class="mt-1 text-sm md:text-lg text-gray-500 italic">
                            {{ __('គ្រប់គ្រងព័ត៌មានផ្ទាល់ខ្លួនរបស់អ្នក') }}
                        </p>
                    </div>
                    <div class="w-full md:w-auto">
                        <a href="{{ route('professor.profile.edit') }}" 
                           class="flex items-center justify-center px-6 py-3 md:px-8 md:py-4 bg-green-600 text-sm md:text-base font-bold rounded-xl md:rounded-2xl text-white hover:bg-green-700 transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                            <i class="fas fa-user-edit mr-2"></i>
                            {{ __('កែប្រែប្រវត្តិរូប') }}
                        </a>
                    </div>
                </div>

                {{-- Success Message --}}
                @if (session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-3 md:p-5 rounded-xl mb-6 shadow-sm flex items-center animate-bounce" role="alert">
                        <i class="fas fa-check-circle mr-3 text-green-500 text-xl"></i>
                        <span class="font-bold text-sm md:text-lg">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-12">
                    
                    {{-- Left Column: Avatar & Basic Info --}}
                    <div class="lg:col-span-4 flex flex-col items-center">
                        <div class="relative group">
                            <div class="w-32 h-32 md:w-56 md:h-56 rounded-[2rem] md:rounded-[2.5rem] overflow-hidden border-4 md:border-8 border-white shadow-2xl transition-transform duration-500 group-hover:rotate-3">
@if ($userProfile->profile_picture_url)
    <img 
        src="{{ $userProfile->profile_picture_url }}?tr=w-600,h-600,fo-face tr=q-auto,f-auto" 
        class="object-cover w-full h-full"
        alt="{{ $user->name }}"
    >
@else
    <div class="w-full h-full bg-green-100 flex items-center justify-center text-green-600 text-4xl md:text-6xl font-black">
        {{ Str::upper(Str::substr($user->name, 0, 1)) }}
    </div>
@endif
                            </div>
                            <div class="absolute -bottom-3 left-1/2 transform -translate-x-1/2 bg-green-600 text-white px-4 py-1 rounded-full shadow-lg font-bold text-[10px] md:text-sm uppercase whitespace-nowrap">
                                <i class="fas fa-chalkboard-teacher mr-1"></i> {{ __('សាស្រ្តាចារ្យ') }}
                            </div>
                        </div>
                        
                        <div class="mt-6 md:mt-10 text-center">
                            <h3 class="text-xl md:text-3xl font-black text-gray-900">{{ $userProfile->full_name_km ?? $user->name }}</h3>
                            <p class="text-xs md:text-lg text-gray-500 font-medium">{{ $user->email }}</p>
                        </div>
                    </div>

                    {{-- Right Column: Information Details --}}
                    <div class="lg:col-span-8">
                        <div class="bg-gray-50 rounded-[1.5rem] md:rounded-[2rem] p-4 md:p-10 border border-gray-100 shadow-inner">
                            <h4 class="text-base md:text-xl font-bold text-gray-800 mb-5 md:mb-8 flex items-center">
                                <span class="w-1.5 h-6 bg-green-500 rounded-full mr-3"></span>
                                {{ __('ព័ត៌មានលម្អិត') }}
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-8">
                                
                                @php
                                    $details = [
                                        ['label' => __('ឈ្មោះខ្មែរ'), 'value' => $userProfile->full_name_km, 'icon' => 'fas fa-user-circle'],
                                        ['label' => __('ឈ្មោះអង់គ្លេស'), 'value' => $userProfile->full_name_en, 'icon' => 'fas fa-font'],
                                        ['label' => __('ភេទ'), 'value' => $userProfile->gender == 'male' ? __('ប្រុស') : ($userProfile->gender == 'female' ? __('ស្រី') : '---'), 'icon' => 'fas fa-venus-mars'],
                                        ['label' => __('ថ្ងៃកំណើត'), 'value' => $userProfile->date_of_birth ? \Carbon\Carbon::parse($userProfile->date_of_birth)->format('d-M-Y') : null, 'icon' => 'fas fa-calendar-alt'],
                                        ['label' => 'Telegram', 'value' => $userProfile->telegram_user ? '@' . $userProfile->telegram_user : null, 'icon' => 'fab fa-telegram-plane'],
                                        ['label' => __('ទូរស័ព្ទ'), 'value' => $userProfile->phone_number, 'icon' => 'fas fa-phone-alt'],
                                        ['label' => __('អាសយដ្ឋាន'), 'value' => $userProfile->address, 'icon' => 'fas fa-map-marker-alt', 'fullWidth' => true],
                                    ];
                                @endphp

                                @foreach ($details as $detail)
                                    <div class="{{ isset($detail['fullWidth']) ? 'md:col-span-2' : '' }}">
                                        <div class="flex items-center p-3 md:p-4 rounded-xl bg-white border border-gray-50 shadow-sm h-full hover:shadow-md transition-shadow">
                                            <div class="w-10 h-10 md:w-12 md:h-12 bg-green-50 rounded-lg flex items-center justify-center text-green-600 mr-3 md:mr-5 shrink-0">
                                                <i class="{{ $detail['icon'] }} text-sm md:text-lg"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-[10px] md:text-xs font-bold text-gray-400 uppercase tracking-widest">{{ __($detail['label']) }}</p>
                                                <p class="text-sm md:text-lg font-bold text-gray-800 truncate md:whitespace-normal">
                                                    {{ $detail['value'] ?? '---' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>