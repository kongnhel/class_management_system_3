<x-app-layout>
    <x-slot name="header">
        <div class="px-4 md:px-6 lg:px-8">
            <h2 class="text-4xl font-extrabold text-gray-900 leading-tight flex items-center">
                {{ __('manage_users') }} <i class="fas fa-users-cog text-green-600 ml-4"></i>
            </h2>
            <p class="mt-2 text-lg text-gray-500">{{ __('list_of_all_users_in_the_system') }}</p>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-8 lg:p-12 border border-gray-100">

                <div class="flex flex-col lg:flex-row justify-between items-center mb-10 gap-6">
                    <div class="text-center lg:text-left">
                        <h3 class="text-3xl font-bold text-gray-800 tracking-tight">
                            {{ __('user_list') }}
                        </h3>
                        <p class="text-gray-500 text-sm mt-1">{{ __('manage_and_track_all_member_information') }}</p>
                    </div>

                    <div class="flex flex-col md:flex-row items-center gap-4 w-full lg:w-auto">
                        
                        <form id="search-form" action="{{ route('admin.manage-users') }}" method="GET" class="w-full md:w-80">
                            <input type="hidden" name="tab" value="{{ request('tab', 'admins') }}">
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400 group-focus-within:text-green-500 transition-colors"></i>
                                </div>
                                <input
                                    id="live-search"
                                    type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="{{ __('search_by_name_or_email') }}"
                                    autocomplete="off"
                                    aria-controls="user-results"
                                    class="block w-full pl-11 pr-20 py-3 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-2xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 focus:bg-white transition-all duration-200 outline-none"
                                >
                                <div id="live-search-loading" class="hidden absolute inset-y-0 right-11 items-center text-green-600" aria-hidden="true">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                                <button id="clear-live-search" type="button" class="hidden absolute inset-y-0 right-3 items-center text-gray-400 hover:text-gray-700 transition-colors" aria-label="{{ __('clear_search') }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <p id="live-search-status" class="sr-only" role="status" aria-live="polite"></p>
                        </form>
                        
                        <div class="hidden md:block h-8 w-px bg-gray-200"></div>

                        <a wire:navigate href="{{ route('admin.create-user') }}"
                           class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 bg-green-600 border border-transparent rounded-2xl font-bold text-sm text-white hover:bg-green-700 active:scale-95 focus:outline-none focus:ring-4 focus:ring-green-500/30 transition-all duration-200 shadow-lg shadow-green-200">
                            <i class="fas fa-plus-circle mr-2 text-lg"></i> 
                            {{ __('add_new_member') }}
                        </a>
                    </div>
                </div>

                <div id="user-manage-root" x-data="{ 
                    activeTab: $persist('admins').as('user_manage_tab'),
                    showDeleteModal: false,
                    deletingUserId: '',
                    deletingUserType: '',
                    deletingFormId: '',
                    isDeleting: false,
                    showEditModal: false,
                    editLoading: false,
                    editSaving: false,
                    showProfilePreview: false,
                    previewUser: {},
                    editDepartments: [],
                    editForm: {
                        id: '', name: '', email: '', role: 'admin', password: '', password_confirmation: '',
                        department_id: '', generation: '', faculty_id: '',
                        full_name_km: '', full_name_en: '', gender: '', phone_number: '', address: '', date_of_birth: '',
                        departments: [], faculties: [], generations: []
                    },

                    init() {
                        const urlParams = new URLSearchParams(window.location.search);
                        const tabParam = urlParams.get('tab');
                        if (tabParam) { this.activeTab = tabParam; }
                        window._editCtx = this;
                    },

                    confirmDelete(formId, userType) {
                        this.deletingFormId = formId;
                        this.deletingUserType = userType;
                        this.deletingUserId = formId;
                        this.showDeleteModal = true;
                    }
                }" class="mt-8">
                    
                    {{-- 🔥 ដាក់ប៊ូតុង Excel នៅទីនេះ (ក្នុង x-data) ដើម្បីឱ្យវាស្គាល់ activeTab និង Filter --}}
                    <div class="flex justify-end mb-4 gap-2">
                        <a href="javascript:void(0)" onclick="printStudentsPdf()"
                           class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 bg-blue-600 border border-transparent rounded-2xl font-bold text-sm text-white hover:bg-blue-700 active:scale-95 transition-all duration-200 shadow-lg shadow-blue-200">
                            <i class="fas fa-print mr-2 text-lg"></i> 
                            {{ __('print_pdf') }}
                        </a>
                        <button @click="window.location.href = '{{ route('admin.users.export') }}?tab=' + activeTab + 
                            '&search={{ request('search') }}' + 
                            '&generation={{ request('generation') }}' + 
                            '&department_id={{ request('department_id') }}'"
                           class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 bg-emerald-600 border border-transparent rounded-2xl font-bold text-sm text-white hover:bg-emerald-700 active:scale-95 transition-all duration-200 shadow-lg shadow-emerald-200">
                            <i class="fas fa-file-excel mr-2 text-lg"></i> 
                            {{ __('download_excel') }}
                        </button>
                    </div>

                    <div class="border-b-2 border-gray-200">
                        <nav class="-mb-0.5 flex space-x-6 overflow-x-auto" aria-label="Tabs">
                            <a wire:navigate href="{{ route('admin.manage-users', ['tab' => 'admins', 'search' => request('search')]) }}" @click="activeTab = 'admins'"
                               class="whitespace-nowrap py-4 px-1 border-b-2 text-lg transition-colors duration-200"
                               :class="{ 'border-green-500 text-green-600 font-semibold': activeTab === 'admins', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'admins' }">
                                <i class="fas fa-user-shield mr-2"></i>{{ __('administrator') }}
                            </a>
                            <a wire:navigate href="{{ route('admin.manage-users', ['tab' => 'professors', 'search' => request('search')]) }}" @click="activeTab = 'professors'"
                               class="whitespace-nowrap py-4 px-1 border-b-2 text-lg transition-colors duration-200"
                               :class="{ 'border-green-500 text-green-600 font-semibold': activeTab === 'professors', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'professors' }">
                                <i class="fas fa-chalkboard-teacher mr-2"></i>{{ __('lecturers') }}
                            </a>
                            <a wire:navigate href="{{ route('admin.manage-users', ['tab' => 'students', 'search' => request('search')]) }}" @click="activeTab = 'students'"
                               class="whitespace-nowrap py-4 px-1 border-b-2 text-lg transition-colors duration-200"
                               :class="{ 'border-green-500 text-green-600 font-semibold': activeTab === 'students', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'students' }">
                                <i class="fas fa-user-graduate mr-2"></i>{{ __('students_3') }}
                            </a>
                        </nav>
                    </div>

                    <div id="user-results" class="mt-8" aria-live="polite">
                        <div x-show="activeTab === 'admins'" class="space-y-3">
                            @if ($admins->isEmpty())
                                <div class="bg-gray-100 p-6 rounded-xl text-center text-gray-500 shadow-inner">
                                    <p class="text-base font-medium">{{ __('no_administrators_yet') }}</p>
                                </div>
                            @else
                                {{-- 1. DESKTOP VERSION --}}
                                <div id="screen-admins" class="hidden md:block overflow-x-auto rounded-2xl shadow-sm border border-gray-200">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('image') }}</th>
                                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('username') }}</th>
                                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('email_2') }}</th>
                                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('full_name_2') }}</th>
                                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('actions_2') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-100 text-sm">
                                            @foreach ($admins as $admin)
                                                <tr class="hover:bg-gray-50 transition-colors" data-user-id="{{ $admin->id }}">
                                                    <td class="px-6 py-3">
                                                        @if ($admin->profile && $admin->profile->profile_picture_url)
                                                            <img src="{{ $admin->profile->profile_picture_url }}" 
                                                                 class="h-12 w-12 rounded-full object-cover border border-gray-100"
                                                                 alt="{{ $admin->name }}">
                                                        @else
                                                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white font-black text-xl shadow-md flex-shrink-0">
                                                                {{ mb_strtoupper(mb_substr($admin->name, 0, 1, 'UTF-8'), 'UTF-8') }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-3 font-semibold text-gray-900 edit-user-name">{{ $admin->name }}</td>
                                                    <td class="px-6 py-3 text-gray-600 edit-user-email">{{ $admin->email }}</td>
                                                    <td class="px-6 py-3 text-gray-600 edit-user-fullname">{{ $admin->profile->full_name_km ?? 'N/A' }}</td>
                                                    <td class="px-6 py-3 text-right font-bold space-x-4">
                                                        <a wire:navigate href="{{ route('admin.show-user', $admin->id) }}" class="text-green-600 hover:text-green-700 text-sm px-3 py-1.5 rounded-lg hover:bg-green-50 transition-all">{{ __('view') }}</a>
                                                        <button type="button" @click.stop="openEditModal({{ $admin->id }})" class="text-emerald-600 hover:text-emerald-700 text-sm px-3 py-1.5 rounded-lg hover:bg-emerald-50 transition-all">{{ __('edit_2') }}</button>
                                                        <button type="button" @click.stop="confirmDelete('delete-admin-{{ $admin->id }}', '{{ __('administrator') }}')" class="text-red-500 hover:text-red-600 text-sm px-3 py-1.5 rounded-lg hover:bg-red-50 transition-all">{{ __('delete_2') }}</button>
                                                        <form id="delete-admin-{{ $admin->id }}" action="{{ route('admin.delete-user', $admin->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- 2. MOBILE VERSION --}}
                                <div id="mobile-admins" class="md:hidden space-y-3">
                                    @foreach ($admins as $admin)
                                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm" data-user-id="{{ $admin->id }}">
                                            <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-50">
                                                <div class="flex items-center space-x-3 min-w-0">
                                                    @if ($admin->profile && $admin->profile->profile_picture_url)
                                                        <img src="{{ $admin->profile->profile_picture_url }}?tr=w-100,h-100,fo-face" 
                                                             class="h-12 w-12 rounded-full object-cover border border-gray-100"
                                                             alt="{{ $admin->name }}">
                                                    @else
                                                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white font-black text-xl shadow-md flex-shrink-0">
                                                            {{ mb_strtoupper(mb_substr($admin->name, 0, 1, 'UTF-8'), 'UTF-8') }}
                                                        </div>
                                                    @endif
                                                    <div class="min-w-0">
                                                        <h4 class="text-base font-black text-gray-900 truncate tracking-tight uppercase">{{ $admin->name }}</h4>
                                                        <p class="text-xs text-gray-500 truncate">{{ $admin->email }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <span class="bg-red-50 text-red-700 text-[10px] font-bold px-2 py-1 rounded border border-red-100 uppercase tracking-widest">Admin</span>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center justify-between">
                                                <div class="min-w-0">
                                                     <p class="text-xs text-gray-600 font-medium truncate">{{ $admin->profile->full_name_km ?? 'No Name' }}</p>
                                                </div>
                                                <div class="flex space-x-4 text-sm font-bold">
                                                    <a wire:navigate href="{{ route('admin.show-user', $admin->id) }}" class="text-green-600 flex items-center px-3 py-1.5 rounded-lg hover:bg-green-50 transition-all">
                                                         <i class="fas fa-eye mr-1.5"></i> {{ __('view') }}
                                                     </a>
                                                     <button type="button" @click.stop="openEditModal({{ $admin->id }})" class="text-emerald-600 flex items-center px-3 py-1.5 rounded-lg hover:bg-emerald-50 transition-all">
                                                         <i class="fas fa-edit mr-1.5"></i> {{ __('edit_3') }}
                                                     </button>
                                                    <button @click.stop="confirmDelete('del-adm-mob-{{ $admin->id }}', 'Admin')" class="text-red-500 flex items-center px-3 py-1.5 rounded-lg hover:bg-red-50 transition-all">
                                                        <i class="fas fa-trash mr-1.5"></i> {{ __('delete_2') }}
                                                    </button>
                                                </div>
                                                <form id="del-adm-mob-{{ $admin->id }}" action="{{ route('admin.delete-user', $admin->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-5">
                                    {{ $admins->links('pagination::tailwind', ['pageName' => 'adminsPage']) }}
                                </div>
                            @endif
                        </div>

                        <div x-show="activeTab === 'professors'" class="space-y-4">
                            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
                                <form id="professor-filter-form" action="{{ route('admin.manage-users') }}" method="GET" class="flex flex-wrap items-end gap-4">
                                    <input type="hidden" name="tab" value="professors">
                                    <input type="hidden" name="search" value="{{ request('search') }}">

                                    <div class="flex-1 min-w-[200px]">
                                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">{{ __('faculty_2') }}</label>
                                        <select name="faculty_id" onchange="this.form.requestSubmit()" class="w-full border-gray-200 rounded-xl text-sm focus:ring-green-500">
                                            <option value="">{{ __('all_faculties') }}</option>
                                            @foreach($faculties as $fac)
                                                <option value="{{ $fac->id }}" {{ request('faculty_id') == $fac->id ? 'selected' : '' }}>{{ $fac->name_km }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="flex-1 min-w-[200px]">
                                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">{{ __('department_3') }}</label>
                                        <select name="department_id" onchange="this.form.requestSubmit()" class="w-full border-gray-200 rounded-xl text-sm focus:ring-green-500">
                                            <option value="">{{ __('all_departments') }}</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name_km }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <a wire:navigate href="{{ route('admin.manage-users', ['tab' => 'professors']) }}" class="px-6 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">
                                        {{ __('Reset') }}
                                    </a>
                                </form>
                            </div>

                            @if ($professorsGrouped->isEmpty())
                                <div class="bg-gray-100 p-8 rounded-2xl text-center text-gray-500 shadow-inner border-2 border-dashed border-gray-200">
                                    <i class="fas fa-user-tie text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-lg font-medium">{{ __('no_lecturers_yet') }}</p>
                                </div>
                            @else
                                @foreach ($professorsGrouped as $deptName => $professorList)
                                    <div x-data="{ openDept: {{ $loop->first ? 'true' : 'false' }} }" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden transition-all duration-300">
                                        
                                        <button @click="openDept = !openDept" 
                                                class="w-full flex items-center justify-between px-6 py-4 bg-emerald-50/30 hover:bg-emerald-50 transition-colors border-b border-gray-100">
                                            <div class="flex items-center">
                                                <div class="h-11 w-11 bg-gradient-to-br from-emerald-500 to-emerald-700 text-white rounded-xl flex items-center justify-center shadow-lg shadow-emerald-100 mr-4 font-bold">
                                                    <i class="fas fa-university text-sm"></i>
                                                </div>
                                                <div class="text-left">
                                                    <h3 class="text-lg font-bold text-gray-800 tracking-tight">{{ $deptName }}</h3>
                                                    <p class="text-xs font-medium text-gray-500">{{ $professorList->count() }} {{ __('photo') }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <span class="hidden sm:inline-block text-[10px] font-bold text-gray-400 uppercase tracking-widest" x-text="openDept ? '{{ __('close_2') }}' : '{{ __('view_list') }}'"></span>
                                                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-500" :class="openDept ? 'rotate-180' : ''"></i>
                                            </div>
                                        </button>

                                        <div x-show="openDept" x-collapse>
                                            <div class="p-6">
                                                {{-- 1. DESKTOP VERSION --}}
                                                <div class="hidden md:block overflow-x-auto rounded-xl border border-gray-100 shadow-sm">
                                                    <table class="min-w-full divide-y divide-gray-200">
                                                        <thead class="bg-gray-50/50">
                                                            <tr>
                                                                <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('image') }}</th>
                                                                <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('username_full_name') }}</th>
                                                                <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('email_2') }}</th>
                                                                <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('actions_2') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white divide-y divide-gray-100">
                                                            @foreach ($professorList as $professor)
                                                                <tr class="hover:bg-gray-50 transition-colors" data-user-id="{{ $professor->id }}">
                                                                    <td class="px-6 py-3 whitespace-nowrap">
                                                                        @if ($professor->profile && $professor->profile->profile_picture_url)
                                                                            <img src="{{ $professor->profile->profile_picture_url }}?tr=w-100,h-100,fo-face" 
                                                                                 class="h-10 w-10 rounded-full object-cover border border-gray-100 shadow-sm"
                                                                                 alt="{{ $professor->name }}">
                                                                        @else
                                                                            <div class="h-10 w-10 rounded-full bg-emerald-600 flex items-center justify-center text-white font-bold text-sm">
                                                                                {{ mb_substr($professor->name, 0, 1, 'UTF-8') }}
                                                                            </div>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-6 py-3">
                                                                        <div class="text-sm font-bold text-gray-900 uppercase tracking-tighter edit-user-name">{{ $professor->name }}</div>
                                                                        <div class="text-[11px] text-gray-500 font-medium edit-user-fullname">{{ $professor->profile->full_name_km ?? 'N/A' }}</div>
                                                                    </td>
                                                                    <td class="px-6 py-3 text-sm text-gray-600 font-medium edit-user-email">{{ $professor->email }}</td>
                                                                    <td class="px-6 py-3 text-right">
                                                                        <div class="flex items-center justify-end gap-4 text-sm font-bold">
                                                                            <a wire:navigate href="{{ route('admin.show-user', $professor->id) }}" class="text-green-600 hover:text-green-700 px-3 py-1.5 rounded-lg hover:bg-green-50 transition-all">{{ __('view') }}</a>
                                                                            <button type="button" @click.stop="openEditModal({{ $professor->id }})" class="text-emerald-600 hover:text-emerald-700 px-3 py-1.5 rounded-lg hover:bg-emerald-50 transition-all">{{ __('edit_3') }}</button>
                                                                            <button type="button" @click.stop="confirmDelete('del-prof-{{ $professor->id }}', '{{ __('lecturers') }}')" class="text-red-500 hover:text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-50 transition-all">{{ __('delete_2') }}</button>
                                                                        </div>
                                                                        <form id="del-prof-{{ $professor->id }}" action="{{ route('admin.delete-user', $professor->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                {{-- 2. MOBILE VERSION --}}
                                                <div class="md:hidden space-y-3">
                                                    @foreach ($professorList as $professor)
                                                        <div class="bg-gray-50/50 border border-gray-100 rounded-xl p-4 shadow-sm hover:border-emerald-200 transition-colors" data-user-id="{{ $professor->id }}">
                                                            <div class="flex items-center justify-between mb-3">
                                                                <div class="flex items-center space-x-3">
                                                                    @if ($professor->profile && $professor->profile->profile_picture_url)
                                                                        <img src="{{ $professor->profile->profile_picture_url }}?tr=w-150,h-150,fo-face" 
                                                                             class="h-12 w-12 rounded-full object-cover border-2 border-white shadow-sm"
                                                                             alt="{{ $professor->name }}">
                                                                    @else
                                                                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white font-black text-lg shadow-sm">
                                                                            {{ mb_strtoupper(mb_substr($professor->name, 0, 1, 'UTF-8'), 'UTF-8') }}
                                                                        </div>
                                                                    @endif
                                                                    <div class="min-w-0">
                                                                        <h5 class="text-sm font-black text-gray-900 uppercase truncate">{{ $professor->name }}</h5>
                                                                        <p class="text-[10px] text-gray-500 truncate font-medium">{{ $professor->email }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                                <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                                                                    <span class="text-[10px] font-bold text-gray-400 italic">{{ $professor->profile->full_name_km ?? 'N/A' }}</span>
                                                                    <div class="flex space-x-4">
                                                                        <a wire:navigate href="{{ route('admin.show-user', $professor->id) }}" class="text-green-600 text-sm font-bold px-3 py-1.5 rounded-lg hover:bg-green-50 transition-all">{{ __('view') }}</a>
                                                                        <button type="button" @click.stop="openEditModal({{ $professor->id }})" class="text-emerald-600 text-sm font-bold px-3 py-1.5 rounded-lg hover:bg-emerald-50 transition-all">{{ __('edit_3') }}</button>
                                                                        <button @click.stop="confirmDelete('del-mob-prof-{{ $professor->id }}', 'Professor')" class="text-red-500 text-sm font-bold px-3 py-1.5 rounded-lg hover:bg-red-50 transition-all">{{ __('delete_2') }}</button>
                                                                    </div>
                                                                </div>
                                                            <form id="del-mob-prof-{{ $professor->id }}" action="{{ route('admin.delete-user', $professor->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div x-show="activeTab === 'students'" class="space-y-4">
                            
                            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
                                <form id="student-filter-form" action="{{ route('admin.manage-users') }}" method="GET" class="flex flex-wrap items-end gap-4">
                                    <input type="hidden" name="tab" value="students">
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                    
                                    <div class="flex-1 min-w-[200px]">
                                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">{{ __('generation') }}</label>
                                        <select name="generation" onchange="this.form.requestSubmit()" class="w-full border-gray-200 rounded-xl text-sm focus:ring-green-500">
                                            <option value="">{{ __('all_generations_2') }}</option>
                                            @foreach($generations as $gen)
                                                <option value="{{ $gen }}" {{ request('generation') == $gen ? 'selected' : '' }}>
                                                    {{ __('generation_2') }} {{ $gen }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="flex-1 min-w-[200px]">
                                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">{{ __('department_3') }}</label>
                                        <select name="department_id" onchange="this.form.requestSubmit()" class="w-full border-gray-200 rounded-xl text-sm focus:ring-green-500">
                                            <option value="">{{ __('all_departments') }}</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                                    {{ $dept->name_km }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <a wire:navigate href="{{ route('admin.manage-users', ['tab' => 'students']) }}" class="px-6 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">
                                        {{ __('Reset') }}
                                    </a>
                                </form>
                            </div>

                            @if ($studentsGrouped->isEmpty())
                                <div class="bg-gray-100 p-8 rounded-2xl text-center text-gray-500 shadow-inner border-2 border-dashed border-gray-200">
                                    <i class="fas fa-user-slash text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-lg font-medium">{{ __('no_students_yet') }}</p>
                                </div>
                            @else
                                {{-- Loop តាមជំនាន់ (Generation) --}}
                                @foreach ($studentsGrouped as $generation => $departments)
                                    <div x-data="{ openGen: {{ $loop->first ? 'true' : 'false' }} }" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden transition-all duration-300">
                                        
                                        <button @click="openGen = !openGen" 
                                                class="w-full flex items-center justify-between px-6 py-4 bg-gray-50 hover:bg-gray-100 transition-colors border-b border-gray-100">
                                            <div class="flex items-center">
                                                <div class="h-11 w-11 bg-gradient-to-br from-green-500 to-green-700 text-white rounded-xl flex items-center justify-center shadow-lg shadow-green-100 mr-4 font-black text-sm uppercase">
                                                    G{{ $generation ?? '?' }}
                                                </div>
                                                <div class="text-left">
                                                    <h3 class="text-lg font-bold text-gray-800 tracking-tight">{{ __('generation_2') }} {{ $generation ?? 'មិនកំណត់' }}</h3>
                                                    <p class="text-xs font-medium text-gray-500">{{ $departments->flatten()->count() }} {{ __('total_students_2') }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <span class="hidden sm:inline-block text-[10px] font-bold text-gray-400 uppercase tracking-widest" x-text="openGen ? '{{ __('close_2') }}' : '{{ __('view_list') }}'"></span>
                                                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-500" :class="openGen ? 'rotate-180' : ''"></i>
                                            </div>
                                        </button>

                                        <div x-show="openGen" x-collapse>
                                            <div class="p-6 space-y-10">
                                                @foreach ($departments as $departmentName => $studentList)
                                                    <div class="relative">
                                                        <div class="flex items-center justify-between mb-4 border-b border-gray-50 pb-2">
                                                            <div class="flex items-center">
                                                                <div class="w-1.5 h-5 bg-green-500 rounded-full mr-3"></div>
                                                                <h4 class="text-sm font-extrabold text-gray-700 uppercase tracking-wider">{{ $departmentName }}</h4>
                                                            </div>
                                                            <span class="bg-green-50 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-md border border-green-100">
                                                                {{ $studentList->count() }} {{ __('students_2') }}
                                                            </span>
                                                        </div>

                                                        {{-- 1. DESKTOP VERSION --}}
                                                        <div class="hidden md:block overflow-x-auto rounded-xl border border-gray-100 shadow-sm">
                                                            <table class="min-w-full divide-y divide-gray-200">
                                                                <thead class="bg-gray-50/50">
                                                                    <tr>
                                                                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('image') }}</th>
                                                                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('key_id') }}</th>
                                                                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('username_full_name') }}</th>
                                                                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('email_2') }}</th>
                                                                        <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('academic_year') }}</th>
                                                                        <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('actions_2') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="bg-white divide-y divide-gray-100">
                                                                    @foreach ($studentList as $student)
                                                                        <tr class="hover:bg-gray-50 transition-colors" data-user-id="{{ $student->id }}">
                                                                            <td class="px-6 py-3 whitespace-nowrap">
                                                                                @if ($student->studentProfile && $student->studentProfile->profile_picture_url)
                                                                                    <img src="{{ $student->studentProfile->profile_picture_url }}?tr=w-100,h-100,fo-face" 
                                                                                         class="h-10 w-10 rounded-full object-cover border border-gray-100"
                                                                                         alt="{{ $student->name }}">
                                                                                @else
                                                                                    <div class="h-10 w-10 rounded-full bg-green-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                                                                        {{ mb_strtoupper(mb_substr($student->name, 0, 1, 'UTF-8'), 'UTF-8') }}
                                                                                    </div>
                                                                                @endif
                                                                            </td>
                                                                            <td class="px-6 py-3">
                                                                                <span class="font-mono text-xs font-bold text-green-700 bg-green-50 px-2 py-1 rounded-md border border-green-100">{{ $student->student_id_code ?? 'N/A' }}</span>
                                                                            </td>
                                                                            <td class="px-6 py-3">
                                                                                <div class="text-sm font-bold text-gray-900 uppercase tracking-tighter edit-user-name">{{ $student->name }}</div>
                                                                                <div class="text-[11px] text-gray-500 font-medium edit-user-fullname">{{ $student->studentProfile->full_name_km ?? 'N/A' }}</div>
                                                                            </td>
                                                                            <td class="px-6 py-3 text-sm text-gray-600 font-medium edit-user-email">{{ $student->email ?? __('no_account_created_yet') }}</td>
                                                                            <td class="px-6 py-3 text-center">
                                                                                @if($student->computed_year_level)
                                                                                    <span class="inline-flex items-center justify-center min-w-[2.5rem] px-2.5 py-1 rounded-lg text-xs font-bold
                                                                                        @if($student->computed_year_level >= $student->department->duration_years)
                                                                                            bg-purple-100 text-purple-700 border border-purple-200
                                                                                        @else
                                                                                            bg-emerald-50 text-emerald-700 border border-emerald-100
                                                                                        @endif">
                                                                                        {{ __('year') }} {{ $student->computed_year_level }}
                                                                                    </span>
                                                                                @else
                                                                                    <span class="text-xs text-gray-400">—</span>
                                                                                @endif
                                                                            </td>
                                                                            <td class="px-6 py-3 text-right">
                                                                                <div class="flex items-center justify-end gap-4 text-sm font-bold">
                                                                                    <a wire:navigate href="{{ route('admin.show-user', $student->id) }}" class="text-green-600 hover:text-green-700 px-3 py-1.5 rounded-lg hover:bg-green-50 transition-all">{{ __('view') }}</a>
                                                                                    <button type="button" @click.stop="openEditModal({{ $student->id }})" class="text-emerald-600 hover:text-emerald-700 px-3 py-1.5 rounded-lg hover:bg-emerald-50 transition-all">{{ __('edit_3') }}</button>
                                                                                    <button type="button" @click.stop="confirmDelete('del-std-{{ $student->id }}', '{{ __('students_3') }}')" class="text-red-500 hover:text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-50 transition-all">{{ __('delete_2') }}</button>
                                                                                </div>
                                                                                <form id="del-std-{{ $student->id }}" action="{{ route('admin.delete-user', $student->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        {{-- 2. MOBILE VERSION --}}
                                                        <div class="md:hidden space-y-3">
                                                            @foreach ($studentList as $student)
                                                                <div class="bg-gray-50/50 border border-gray-100 rounded-xl p-4 shadow-sm hover:border-green-200 transition-colors" data-user-id="{{ $student->id }}">
                                                                    <div class="flex items-center justify-between mb-3">
                                                                        <div class="flex items-center space-x-3">
                                                                            @if ($student->studentProfile && $student->studentProfile->profile_picture_url)
                                                                                <img src="{{ $student->studentProfile->profile_picture_url }}?tr=w-150,h-150,fo-face" 
                                                                                     class="h-12 w-12 rounded-full object-cover border-2 border-white shadow-sm"
                                                                                     alt="{{ $student->name }}">
                                                                            @else
                                                                                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-green-500 to-green-700 flex items-center justify-center text-white font-black text-lg shadow-sm">
                                                                                    {{ mb_strtoupper(mb_substr($student->name, 0, 1, 'UTF-8'), 'UTF-8') }}
                                                                                </div>
                                                                            @endif
                                                                            <div class="min-w-0">
                                                                                <h5 class="text-sm font-black text-gray-900 uppercase truncate">{{ $student->name }}</h5>
                                                                                <p class="text-[10px] text-gray-500 truncate font-medium">{{ $student->email }}</p>
                                                                                <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                                                                    <span class="inline-block font-mono text-[10px] font-bold text-green-700 bg-green-50 px-1.5 py-0.5 rounded border border-green-100">{{ $student->student_id_code ?? 'N/A' }}</span>
                                                                                    @if($student->computed_year_level)
                                                                                        <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded text-[10px] font-bold
                                                                                            @if($student->department && $student->computed_year_level >= $student->department->duration_years)
                                                                                                bg-purple-100 text-purple-700
                                                                                            @else
                                                                                                bg-emerald-50 text-emerald-700
                                                                                            @endif">
                                                                                            {{ __('year') }} {{ $student->computed_year_level }}
                                                                                        </span>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                                                                        <span class="text-[10px] font-bold text-gray-400 italic">{{ $student->studentProfile->full_name_km ?? 'N/A' }}</span>
                                                                        <div class="flex space-x-4">
                                                                            <a wire:navigate href="{{ route('admin.show-user', $student->id) }}" class="text-green-600 text-sm font-bold px-3 py-1.5 rounded-lg hover:bg-green-50 transition-all">{{ __('view') }}</a>
                                                                            <button type="button" @click.stop="openEditModal({{ $student->id }})" class="text-emerald-600 text-sm font-bold px-3 py-1.5 rounded-lg hover:bg-emerald-50 transition-all">{{ __('edit_3') }}</button>
                                                                            <button @click="confirmDelete('del-mob-{{ $student->id }}', 'Student')" class="text-red-500 text-sm font-bold px-3 py-1.5 rounded-lg hover:bg-red-50 transition-all">{{ __('delete_2') }}</button>
                                                                        </div>
                                                                    </div>
                                                                    <form id="del-mob-{{ $student->id }}" action="{{ route('admin.delete-user', $student->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        </div>
                        
{{-- Global Delete Modal --}}
                        <div x-show="showDeleteModal" class="fixed inset-0 z-[9999] overflow-y-auto" x-cloak>
                            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                                <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showDeleteModal = false"></div>

                                <div x-show="showDeleteModal" @click.away="showDeleteModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                     class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl z-50">
                                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-center text-gray-900">{{ __('confirm_deletion') }}</h3>
                                    <p class="mt-2 text-sm text-center text-gray-500">
                                        {{ __('are_you_sure_you_want_to_delete') }} <span class="font-black text-red-600" x-text="deletingUserType"></span> {{ __('data_will_be_permanently_lost') }}
                                    </p>
                                    <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                                        <button type="button" @click="showDeleteModal = false" class="px-5 py-2 text-sm font-bold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all">{{ __('cancel_2') }}</button>
                                        <button type="button" @click="executeDeleteUser()" :disabled="isDeleting" class="px-5 py-2 text-sm font-bold text-white bg-red-600 rounded-xl hover:bg-red-700 shadow-lg shadow-red-200 transition-all disabled:opacity-50">
                                            <span x-show="!isDeleting">{{ __('delete_3') }}</span>
                                            <span x-show="isDeleting"><i class="fas fa-spinner fa-spin mr-1"></i> {{ __('deleting') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                {{-- Edit User Modal --}}
                <div x-show="showEditModal" class="fixed inset-0 z-[9999] overflow-y-auto" x-cloak>
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showEditModal = false"></div>
                        <div x-show="showEditModal" @click.away="showEditModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl z-50 max-h-[90vh] overflow-y-auto" id="edit-modal-content">

                            {{-- Header --}}
                            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10 rounded-t-2xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                                        <i class="fas fa-user-edit"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ __('edit_user') }}</h3>
                                </div>
                                <button @click="showEditModal = false" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                    <i class="fas fa-times text-gray-400"></i>
                                </button>
                            </div>

                            {{-- Loading --}}
                            <div x-show="editLoading" class="p-12 text-center">
                                <i class="fas fa-spinner fa-spin text-2xl text-emerald-500"></i>
                                <p class="text-gray-400 mt-2 text-sm">{{ __('loading_data') }}</p>
                            </div>

                            {{-- Form --}}
                            <form x-show="!editLoading" @submit.prevent="submitEditForm()" class="p-6 space-y-5">
                                {{-- Name + Role --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('user_name') }} <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="editForm.name" required placeholder="{{ __('enter_username') }}" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('role') }} <span class="text-red-500">*</span></label>
                                        <select x-model="editForm.role" required class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                            <option value="admin">Admin</option>
                                            <option value="professor">Professor</option>
                                            <option value="student">Student</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Email (non-student) --}}
                                <div x-show="editForm.role !== 'student'">
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('email_2') }}</label>
                                    <input type="email" x-model="editForm.email" placeholder="name@example.com" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                </div>

                                {{-- Password --}}
                                <div x-show="editForm.role !== 'student'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('new_password_2') }}</label>
                                        <input type="password" x-model="editForm.password" placeholder="{{ __('leave_empty_if_not_changing') }}" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('confirm_password') }}</label>
                                        <input type="password" x-model="editForm.password_confirmation" placeholder="{{ __('confirm_password_again') }}" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                    </div>
                                </div>

                                {{-- Student fields --}}
                                <div x-show="editForm.role === 'student'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('department_3') }}</label>
                                        <select x-model="editForm.department_id" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                            <option value="">{{ __('select_2') }}</option>
                                            <template x-for="d in (editForm.departments || [])" :key="d.id">
                                                <option :value="d.id" x-text="d.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('generation') }}</label>
                                        <select x-model="editForm.generation" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                            <option value="">{{ __('select_2') }}</option>
                                            <template x-for="g in (editForm.generations || [])" :key="g.name">
                                                <option :value="g.name" x-text="'{{ __("generation_2") }}' + g.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                {{-- Professor fields --}}
                                <div x-show="editForm.role === 'professor'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('faculty') }}</label>
                                        <select x-model="editForm.faculty_id" @change="filterEditDepartments($event.target.value)" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                            <option value="">{{ __('select_2') }}</option>
                                            <template x-for="f in (editForm.faculties || [])" :key="f.id">
                                                <option :value="f.id" x-text="f.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('department_3') }}</label>
                                        <select x-model="editForm.department_id" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                            <option value="">{{ __('select_2') }}</option>
                                            <template x-for="d in editDepartments" :key="d.id">
                                                <option :value="d.id" x-text="d.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                {{-- Profile Info --}}
                                <div class="border-t border-gray-100 pt-5">
                                    <h4 class="text-sm font-bold text-gray-700 mb-3"><i class="fas fa-id-card mr-1.5 text-orange-500"></i> {{ __('personal_information') }}</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('full_name_khmer') }}</label>
                                            <input type="text" x-model="editForm.full_name_km" placeholder="{{ __('enter_full_name_in_khmer') }}" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('full_name_english') }}</label>
                                            <input type="text" x-model="editForm.full_name_en" placeholder="Enter full name in English" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('gender') }}</label>
                                            <select x-model="editForm.gender" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                                <option value="">{{ __('select_2') }}</option>
                                                <option value="male">{{ __('male') }}</option>
                                                <option value="female">{{ __('female') }}</option>
                                                <option value="other">{{ __('other') }}</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('phone_number') }}</label>
                                            <input type="text" x-model="editForm.phone_number" placeholder="012 345 678" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('address') }}</label>
                                            <input type="text" x-model="editForm.address" placeholder="{{ __('enter_address') }}" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('date_of_birth') }}</label>
                                            <input type="date" x-model="editForm.date_of_birth" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <label class="block text-xs font-bold text-gray-500 mb-2">{{ __('profile_picture') }}</label>
                                        <div class="flex items-center gap-5">
                                            <div class="relative group">
                                                <div id="editAvatarPreview" class="w-20 h-20 rounded-2xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden">
                                                    <i class="fas fa-camera text-gray-400 text-xl"></i>
                                                </div>
                                                <label for="editProfilePicture" class="absolute inset-0 rounded-2xl bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center cursor-pointer transition-opacity">
                                                    <i class="fas fa-pen text-white text-sm"></i>
                                                </label>
                                            </div>
                                            <div class="flex-1">
                                                <input type="file" id="editProfilePicture" accept="image/jpeg,image/png,image/jpg" class="hidden" onchange="previewEditAvatar(this)">
                                                <input type="hidden" id="editProfilePictureBase64" value="">
                                                <button type="button" onclick="document.getElementById('editProfilePicture').click()" class="px-4 py-2 text-xs font-bold text-emerald-700 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition-all">
                                                    <i class="fas fa-upload mr-1.5"></i>{{ __('choose_image') }}
                                                </button>
                                                <p class="text-[11px] text-gray-400 mt-1.5">{{ __('supported_image_formats') }}</p>
                                                <button type="button" id="editRemovePicBtn" onclick="removeEditAvatar()" class="hidden mt-1.5 px-3 py-1 text-[11px] font-bold text-red-500 hover:text-red-700 transition-colors">
                                                    <i class="fas fa-times mr-1"></i>{{ __('remove_image') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                                    <button type="button" @click="showEditModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all">{{ __('cancel_2') }}</button>
                                    <button type="submit" :disabled="editSaving" class="px-6 py-2.5 text-sm font-bold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition-all disabled:opacity-50">
                                        <span x-show="!editSaving"><i class="fas fa-save mr-1.5"></i> {{ __('save_2') }}</span>
                                        <span x-show="editSaving"><i class="fas fa-spinner fa-spin mr-1.5"></i> {{ __('saving') }}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                </div>
            </div>
        </div>
    </div>
</div>

    <style>
        /* Enhanced hover effects for action links */
        .text-green-600:hover, .text-emerald-600:hover, .text-red-500:hover {
            text-decoration: underline !important;
            transform: scale(1.05);
            transition: all 0.2s ease;
        }
        
        /* Button hover effects */
        button[type="button"]:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Tab navigation hover */
        nav a:hover {
            border-bottom-width: 2px;
        }
        
        /* Action links in tables */
        td a:hover, td button:hover {
            opacity: 0.8;
        }
    </style>

    <script>
    var ADMIN_BASE = '{{ url("/admin/users") }}';
    var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function _ctx() { return window._editCtx || Alpine.$data(document.getElementById('user-manage-root')); }

    function openEditModal(userId) {
        var c = _ctx();
        c.editLoading = true;
        c.showEditModal = true;
        fetch(ADMIN_BASE + '/' + userId + '/ajax-edit', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var f = c.editForm;
            f.id = data.id;
            f.name = data.name || '';
            f.email = data.email || '';
            f.role = data.role || 'admin';
            f.department_id = data.department_id || '';
            f.faculty_id = data.faculty_id || '';
            f.full_name_km = data.full_name_km || '';
            f.full_name_en = data.full_name_en || '';
            f.gender = data.gender || '';
            f.phone_number = data.phone_number || '';
            f.address = data.address || '';
            f.date_of_birth = data.date_of_birth || '';
            f.departments = data.departments || [];
            f.faculties = data.faculties || [];
            f.generations = data.generations || [];
            f.password = '';
            f.password_confirmation = '';
            c.editDepartments = data.departments || [];
            if (data.faculty_id) {
                c.editDepartments = c.editDepartments.filter(function(d) { return d.faculty_id == data.faculty_id; });
            }
            c.editLoading = false;
            // Clear file input
            var fileInput = document.getElementById('editProfilePicture');
            if (fileInput) fileInput.value = '';
            // Set profile picture preview
            var preview = document.getElementById('editAvatarPreview');
            var removeBtn = document.getElementById('editRemovePicBtn');
            c._removePicture = false;
            if (preview) {
                if (data.profile_picture_url) {
                    preview.innerHTML = '<img src="' + data.profile_picture_url + '" class="w-full h-full object-cover">';
                    removeBtn.classList.remove('hidden');
                } else {
                    preview.innerHTML = '<i class="fas fa-camera text-gray-400 text-xl"></i>';
        var base64Input = document.getElementById('editProfilePictureBase64');
        if (base64Input) base64Input.value = '';
        removeBtn.classList.add('hidden');
                }
            }
            // Re-set select values after x-for options render
            var savedGen = data.generation || '';
            var savedDept = data.department_id || '';
            var savedFac = data.faculty_id || '';
            f.generation = '';
            f.department_id = '';
            f.faculty_id = '';
            setTimeout(function() {
                f.generation = savedGen;
                f.department_id = savedDept;
                f.faculty_id = savedFac;
            }, 100);
        })
        .catch(function() {
            c.editLoading = false;
            c.showEditModal = false;
            window.showToast && window.showToast('{{ __("problem_fetching_data") }}', 'error');
        });
    }

    function filterEditDepartments(facultyId) {
        var c = _ctx();
        c.editDepartments = c.editForm.departments.filter(function(d) { return d.faculty_id == facultyId; });
        c.editForm.department_id = '';
    }

    function previewEditAvatar(input) {
        var preview = document.getElementById('editAvatarPreview');
        var removeBtn = document.getElementById('editRemovePicBtn');
        var base64Input = document.getElementById('editProfilePictureBase64');
        if (input.files && input.files[0]) {
            var file = input.files[0];
            if (file.size > 5 * 1024 * 1024) {
                showToast('{{ __("validation_file_max_size") }}', 'error');
                input.value = '';
                return;
            }
            window._compressToBase64(file).then(function(dataUrl) {
                base64Input.value = dataUrl;
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                    removeBtn.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            });
        }
    }

    function removeEditAvatar() {
        var preview = document.getElementById('editAvatarPreview');
        var input = document.getElementById('editProfilePicture');
        var removeBtn = document.getElementById('editRemovePicBtn');
        var c = _ctx();
        preview.innerHTML = '<i class="fas fa-camera text-gray-400 text-xl"></i>';
        input.value = '';
        removeBtn.classList.add('hidden');
        c._removePicture = true;
    }

    function submitEditForm() {
        var c = _ctx();
        c.editSaving = true;
        var f = c.editForm;
        var fd = new FormData();
        fd.append('name', f.name);
        fd.append('email', f.email);
        fd.append('role', f.role);
        if (f.password) {
            fd.append('password', f.password);
            fd.append('password_confirmation', f.password_confirmation);
        }
        if (f.role === 'student') {
            fd.append('department_id', f.department_id);
            fd.append('generation', f.generation);
        } else if (f.role === 'professor') {
            fd.append('department_id', f.department_id);
        }
        fd.append('full_name_km', f.full_name_km);
        fd.append('full_name_en', f.full_name_en);
        fd.append('gender', f.gender);
        fd.append('phone_number', f.phone_number);
        fd.append('address', f.address);
        fd.append('date_of_birth', f.date_of_birth);
        var fileInput = document.getElementById('editProfilePicture');
        var base64Input = document.getElementById('editProfilePictureBase64');
        if (base64Input && base64Input.value) {
            fd.append('profile_picture_base64', base64Input.value);
        } else if (fileInput && fileInput.files.length > 0) {
            fd.append('profile_picture', fileInput.files[0]);
        }
        if (c._removePicture) {
            fd.append('remove_picture', '1');
        }
        fd.append('_method', 'PUT');

        fetch(ADMIN_BASE + '/' + f.id, {
            method: 'POST',
            body: fd,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            c.editSaving = false;
            if (data.success) {
                c.showEditModal = false;
                var row = document.querySelector('[data-user-id="' + data.user.id + '"]');
                if (row) {
                    var nameEl = row.querySelector('.edit-user-name');
                    var emailEl = row.querySelector('.edit-user-email');
                    var fullnameEl = row.querySelector('.edit-user-fullname');
                    if (nameEl) nameEl.textContent = data.user.name;
                    if (emailEl) emailEl.textContent = data.user.email || '{{ __("no_account_created_yet") }}';
                    if (fullnameEl) fullnameEl.textContent = data.user.full_name_km || data.user.full_name_en || 'N/A';
                    row.style.transition = 'all 0.3s ease';
                    row.style.backgroundColor = '#d1fae5';
                    setTimeout(function() { row.style.backgroundColor = ''; }, 1500);
                }
                window.showToast && window.showToast(data.message || '{{ __("updated_successfully") }}', 'success');
            } else {
                var msg = data.message || '{{ __("there_is_a_problem_2") }}';
                if (data.errors) { msg += '\n' + Object.values(data.errors).flat().join('\n'); }
                window.showToast && window.showToast(msg, 'error');
            }
        })
        .catch(function() {
            c.editSaving = false;
            window.showToast && window.showToast('{{ __("problem_saving") }}', 'error');
        });
    }
    </script>

    <script>
    function executeDeleteUser() {
        var el = document.getElementById('user-manage-root');
        var scope = Alpine.$data(el);
        if (!scope || scope.isDeleting) return;
        scope.isDeleting = true;

        var form = document.getElementById(scope.deletingFormId);
        if (!form) { scope.isDeleting = false; return; }

        var url = form.getAttribute('action');
        var userId = url.split('/').pop();

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            scope.showDeleteModal = false;
            scope.isDeleting = false;

            if (data.success) {
                document.querySelectorAll('[data-user-id="' + userId + '"]').forEach(function(el) {
                    el.style.transition = 'all 0.4s ease';
                    el.style.opacity = '0';
                    el.style.transform = 'translateX(40px)';
                    setTimeout(function() { el.remove(); }, 400);
                });
                window.showToast && window.showToast(data.message || '{{ __("user_deleted_successfully") }}', 'success');
            } else {
                window.showToast && window.showToast(data.message || '{{ __("there_is_a_problem_2") }}', 'error');
            }
        })
        .catch(function() {
            form.submit();
        });
    }
    </script>

    <script>
    (function() {
        if (window.__adminUsersLiveSearchInitialized) return;
        window.__adminUsersLiveSearchInitialized = true;

        var timer = null;
        var activeRequest = null;
        var isComposing = false;
        var pendingSearchForm = null;

        function getActiveTab() {
            var root = document.getElementById('user-manage-root');
            if (root && window.Alpine) {
                var scope = Alpine.$data(root);
                if (scope && scope.activeTab) return scope.activeTab;
            }

            var tabInput = document.querySelector('#search-form input[name="tab"]');
            return tabInput ? tabInput.value : 'admins';
        }

        function buildResultsUrl(form) {
            var url = new URL(form.action, window.location.href);
            var currentParams = new URLSearchParams(window.location.search);

            currentParams.forEach(function(value, key) {
                url.searchParams.set(key, value);
            });

            new FormData(form).forEach(function(value, key) {
                url.searchParams.set(key, value);
            });

            var activeTab = getActiveTab();
            url.searchParams.set('tab', activeTab);
            url.searchParams.delete('adminsPage');

            if (activeTab === 'admins') {
                ['generation', 'faculty_id', 'department_id'].forEach(function(key) {
                    url.searchParams.delete(key);
                });
            } else if (activeTab === 'professors') {
                ['generation'].forEach(function(key) {
                    url.searchParams.delete(key);
                });
            } else if (activeTab === 'students') {
                ['faculty_id', 'department_id'].forEach(function(key) {
                    url.searchParams.delete(key);
                });
            }

            return url;
        }

        function setResultsLoading(isLoading) {
                    var results = document.getElementById('user-results');
                    if (!results) return;
                    results.setAttribute('aria-busy', isLoading ? 'true' : 'false');
                    // Do NOT gray out or disable the results while searching — the input
                    // and list must stay fully interactive.

                    var searchInput = document.getElementById('live-search');
                    var loadingIcon = document.getElementById('live-search-loading');
                    var clearButton = document.getElementById('clear-live-search');
                    var status = document.getElementById('live-search-status');

            if (searchInput) searchInput.setAttribute('aria-busy', isLoading ? 'true' : 'false');
            if (loadingIcon) loadingIcon.classList.toggle('hidden', !isLoading);
            if (loadingIcon) loadingIcon.classList.toggle('flex', isLoading);
            if (clearButton) clearButton.classList.toggle('hidden', isLoading || !(searchInput && searchInput.value));
            if (clearButton) clearButton.classList.toggle('flex', !isLoading && !!(searchInput && searchInput.value));
            if (status) status.textContent = isLoading ? '{{ __('searching') }}' : '';
        }

        function fetchUserResults(url) {
            var results = document.getElementById('user-results');
            if (!results) return;

            if (activeRequest) activeRequest.abort();
            var requestController = new AbortController();
            activeRequest = requestController;
            setResultsLoading(true);

            fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: requestController.signal
            })
            .then(function(response) {
                if (!response.ok) throw new Error('Unable to fetch users');
                return response.text();
            })
            .then(function(html) {
                var documentParser = new DOMParser();
                var nextDocument = documentParser.parseFromString(html, 'text/html');
                var nextResults = nextDocument.querySelector('#user-results');

                if (!nextResults) throw new Error('User results were not returned');

                if (window.Alpine && Alpine.destroyTree) Alpine.destroyTree(results);
                                results.innerHTML = nextResults.innerHTML;
                                if (window.Alpine && Alpine.initTree) Alpine.initTree(results);

                                // Do NOT overwrite the search input value. The input is the
                                // user's live typing — resetting it from the URL would yank
                                // characters they typed while this request was in flight.
                                var searchInput = document.getElementById('live-search');
                                var clearButton = document.getElementById('clear-live-search');
                                if (searchInput) {
                                    var typed = searchInput.value;
                                    if (clearButton) clearButton.classList.toggle('hidden', !typed);
                                    if (clearButton) clearButton.classList.toggle('flex', !!typed);
                                }
                                window.history.replaceState({}, '', url.toString());
            })
            .catch(function(error) {
                if (error.name !== 'AbortError') {
                    // Keep filtering usable if JavaScript fetch is unavailable.
                    window.location.assign(url.toString());
                }
            })
            .finally(function() {
                if (activeRequest === requestController) {
                    activeRequest = null;
                    setResultsLoading(false);
                }
            });
        }

        function queueSearch(form) {
                    pendingSearchForm = form;
                    clearTimeout(timer);
                    timer = setTimeout(function() {
                        if (!isComposing && pendingSearchForm) {
                            fetchUserResults(buildResultsUrl(pendingSearchForm));
                        }
                    }, 350);
                }

        document.addEventListener('compositionstart', function(event) {
            if (event.target && event.target.id === 'live-search') {
                isComposing = true;
                clearTimeout(timer);
            }
        });

        document.addEventListener('compositionend', function(event) {
            if (event.target && event.target.id === 'live-search') {
                isComposing = false;
                queueSearch(event.target.form);
            }
        });

        document.addEventListener('input', function(event) {
            if (event.target && event.target.id === 'live-search') {
                if (event.isComposing || isComposing) return;
                queueSearch(event.target.form);
            }
        });

        document.addEventListener('click', function(event) {
            if (!event.target.closest('#clear-live-search')) return;

            var searchInput = document.getElementById('live-search');
            if (!searchInput) return;

            clearTimeout(timer);
            searchInput.value = '';
            searchInput.focus();
            fetchUserResults(buildResultsUrl(searchInput.form));
        });

        document.addEventListener('submit', function(event) {
            var form = event.target;
            if (!form || !form.matches('#search-form, #professor-filter-form, #student-filter-form')) return;

            event.preventDefault();
            fetchUserResults(buildResultsUrl(form));
        });

        document.addEventListener('click', function(event) {
            var link = event.target.closest('#user-results a');
            if (!link) return;

            var url = new URL(link.href, window.location.href);
            if (!url.searchParams.has('adminsPage')) return;

            event.preventDefault();
            fetchUserResults(url);
        });

        setResultsLoading(false);
    })();

    function printStudentsPdf() {
        var root = document.getElementById('user-manage-root');
        var scope = Alpine.$data(root);
        var activeTab = scope.activeTab;

        if (activeTab === 'students') {
            var genEl = document.querySelector('select[name=generation]');
            var progEl = document.querySelector('select[name=department_id]');
            var gen = genEl ? genEl.value : '';
            var prog = progEl ? progEl.value : '';
            if (!gen || !prog) {
                window.showToast && window.showToast('{{ __("please_select_a_generation_and_program_before_printing") }}', 'warning');
                return;
            }
            window.open('{{ route('admin.users.print-students') }}?generation=' + gen + '&department_id=' + prog, '_blank');
        } else if (activeTab === 'professors') {
            var facEl = document.querySelector('#professor-filter-form select[name=faculty_id]');
            var deptEl = document.querySelector('#professor-filter-form select[name=department_id]');
            var fac = facEl ? facEl.value : '';
            var dept = deptEl ? deptEl.value : '';
            if (!fac && !dept) {
                window.showToast && window.showToast('{{ __("please_select_a_faculty_or_department_before_printing") }}', 'warning');
                return;
            }
            window.open('{{ route('admin.users.print-professors') }}?faculty_id=' + fac + '&department_id=' + dept, '_blank');
        } else {
            window.showToast && window.showToast('{{ __("this_feature_only_supports_students_and_lecturers") }}', 'warning');
        }
    }

    window._compressToBase64 = function(file) {
        return new Promise(function(resolve, reject) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                var img = new Image();
                img.onload = function() {
                    var canvas = document.createElement('canvas');
                    var ctx = canvas.getContext('2d');
                    var w = img.width, h = img.height;
                    if (w > 1920 || h > 1920) {
                        var r = Math.min(1920 / w, 1920 / h);
                        w = Math.round(w * r);
                        h = Math.round(h * r);
                    }
                    canvas.width = w;
                    canvas.height = h;
                    ctx.drawImage(img, 0, 0, w, h);
                    var quality = 0.8;
                    function tryCompress() {
                        canvas.toBlob(function(blob) {
                            if (!blob) return reject('Canvas failed');
                            if (blob.size <= 1024 * 1024 || quality <= 0.3) {
                                var fr = new FileReader();
                                fr.onload = function(e) { resolve(e.target.result); };
                                fr.onerror = reject;
                                fr.readAsDataURL(blob);
                                return;
                            }
                            quality -= 0.05;
                            tryCompress();
                        }, 'image/jpeg', quality);
                    }
                    tryCompress();
                };
                img.onerror = reject;
                img.src = ev.target.result;
            };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    };
    </script>
</x-app-layout>
