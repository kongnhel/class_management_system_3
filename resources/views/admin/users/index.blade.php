<x-app-layout>
    <x-slot name="header">
        <div class="px-4 md:px-6 lg:px-8">
            <h2 class="text-4xl font-extrabold text-gray-900 leading-tight flex items-center">
                {{ __('គ្រប់គ្រងអ្នកប្រើប្រាស់') }} <i class="fas fa-users-cog text-green-600 ml-4"></i>
            </h2>
            <p class="mt-2 text-lg text-gray-500">{{ __('បញ្ជីឈ្មោះអ្នកប្រើប្រាស់ទាំងអស់នៅក្នុងប្រព័ន្ធ') }}</p>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-8 lg:p-12 border border-gray-100">

                <div class="flex flex-col lg:flex-row justify-between items-center mb-10 gap-6">
                    <div class="text-center lg:text-left">
                        <h3 class="text-3xl font-bold text-gray-800 tracking-tight">
                            {{ __('បញ្ជីអ្នកប្រើប្រាស់') }}
                        </h3>
                        <p class="text-gray-500 text-sm mt-1">{{ __('គ្រប់គ្រង និងតាមដានព័ត៌មានសមាជិកទាំងអស់') }}</p>
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
                                    placeholder="{{ __('ស្វែងរកឈ្មោះ ឬអ៊ីម៉ែល...') }}"
                                    class="block w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-2xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 focus:bg-white transition-all duration-200 outline-none"
                                >
                            </div>
                        </form>
                        
                        <div class="hidden md:block h-8 w-px bg-gray-200"></div>

                        <a href="{{ route('admin.create-user') }}"
                           class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 bg-green-600 border border-transparent rounded-2xl font-bold text-sm text-white hover:bg-green-700 active:scale-95 focus:outline-none focus:ring-4 focus:ring-green-500/30 transition-all duration-200 shadow-lg shadow-green-200">
                            <i class="fas fa-plus-circle mr-2 text-lg"></i> 
                            {{ __('បន្ថែមសមាជិកថ្មី') }}
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
                    editDepartments: [],
                    editForm: {
                        id: '', name: '', email: '', role: 'admin', password: '', password_confirmation: '',
                        program_id: '', department_id: '', generation: '', faculty_id: '',
                        full_name_km: '', full_name_en: '', gender: '', phone_number: '', address: '', date_of_birth: '',
                        programs: [], departments: [], faculties: [], generations: []
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
                    <div class="flex justify-end mb-4">
                        <button @click="window.location.href = '{{ route('admin.users.export') }}?tab=' + activeTab + 
                            '&search={{ request('search') }}' + 
                            '&generation={{ request('generation') }}' + 
                            '&program_id={{ request('program_id') }}'"
                           class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 bg-emerald-600 border border-transparent rounded-2xl font-bold text-sm text-white hover:bg-emerald-700 active:scale-95 transition-all duration-200 shadow-lg shadow-emerald-200">
                            <i class="fas fa-file-excel mr-2 text-lg"></i> 
                            {{ __('ទាញយក Excel') }}
                        </button>
                    </div>

                    <div class="border-b-2 border-gray-200">
                        <nav class="-mb-0.5 flex space-x-6 overflow-x-auto" aria-label="Tabs">
                            <a href="{{ route('admin.manage-users', ['tab' => 'admins', 'search' => request('search')]) }}" @click.prevent="activeTab = 'admins'"
                               class="whitespace-nowrap py-4 px-1 border-b-2 text-lg transition-colors duration-200"
                               :class="{ 'border-green-500 text-green-600 font-semibold': activeTab === 'admins', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'admins' }">
                                <i class="fas fa-user-shield mr-2"></i>{{ __('អ្នកគ្រប់គ្រង') }}
                            </a>
                            <a href="{{ route('admin.manage-users', ['tab' => 'professors', 'search' => request('search')]) }}" @click.prevent="activeTab = 'professors'"
                               class="whitespace-nowrap py-4 px-1 border-b-2 text-lg transition-colors duration-200"
                               :class="{ 'border-green-500 text-green-600 font-semibold': activeTab === 'professors', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'professors' }">
                                <i class="fas fa-chalkboard-teacher mr-2"></i>{{ __('លោកគ្រូអ្នកគ្រូ') }}
                            </a>
                            <a href="{{ route('admin.manage-users', ['tab' => 'students', 'search' => request('search')]) }}" @click.prevent="activeTab = 'students'"
                               class="whitespace-nowrap py-4 px-1 border-b-2 text-lg transition-colors duration-200"
                               :class="{ 'border-green-500 text-green-600 font-semibold': activeTab === 'students', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'students' }">
                                <i class="fas fa-user-graduate mr-2"></i>{{ __('និស្សិត') }}
                            </a>
                        </nav>
                    </div>

                    <div class="mt-8">
                        <div x-show="activeTab === 'admins'" class="space-y-3">
                            @if ($admins->isEmpty())
                                <div class="bg-gray-100 p-6 rounded-xl text-center text-gray-500 shadow-inner">
                                    <p class="text-base font-medium">{{ __('មិនទាន់មានអ្នកគ្រប់គ្រងណាមួយនៅឡើយទេ។') }}</p>
                                </div>
                            @else
                                {{-- 1. DESKTOP VERSION --}}
                                <div id="screen-admins" class="hidden md:block overflow-x-auto rounded-2xl shadow-sm border border-gray-200">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('រូបភាព') }}</th>
                                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('ឈ្មោះអ្នកប្រើ') }}</th>
                                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('អ៊ីម៉ែល') }}</th>
                                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('ឈ្មោះពេញ') }}</th>
                                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('សកម្មភាព') }}</th>
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
                                                    <td class="px-6 py-3 text-right font-bold space-x-3">
                                                        <a href="{{ route('admin.show-user', $admin->id) }}" class="text-green-600 hover:underline">{{ __('មើល') }}</a>
                                                        <button type="button" @click.stop="openEditModal({{ $admin->id }})" class="text-emerald-600 hover:underline">{{ __('កែប្រែ') }}</button>
                                                        <button type="button" @click.stop="confirmDelete('delete-admin-{{ $admin->id }}', '{{ __('អ្នកគ្រប់គ្រង') }}')" class="text-red-500 hover:underline">{{ __('លុប') }}</button>
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
                                                <div class="flex space-x-4 text-xs font-bold">
                                                    <a href="{{ route('admin.show-user', $admin->id) }}" class="text-green-600 flex items-center">
                                                         <i class="fas fa-eye mr-1 text-[10px]"></i> {{ __('មើល') }}
                                                     </a>
                                                     <button type="button" @click.stop="openEditModal({{ $admin->id }})" class="text-emerald-600 flex items-center">
                                                         <i class="fas fa-edit mr-1 text-[10px]"></i> {{ __('កែ') }}
                                                     </button>
                                                    <button @click.stop="confirmDelete('del-adm-mob-{{ $admin->id }}', 'Admin')" class="text-red-500 flex items-center">
                                                        <i class="fas fa-trash mr-1 text-[10px]"></i> {{ __('លុប') }}
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
                            @if ($professorsGrouped->isEmpty())
                                <div class="bg-gray-100 p-8 rounded-2xl text-center text-gray-500 shadow-inner border-2 border-dashed border-gray-200">
                                    <i class="fas fa-user-tie text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-lg font-medium">{{ __('មិនទាន់មានលោកគ្រូអ្នកគ្រូណាមួយនៅឡើយទេ។') }}</p>
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
                                                    <p class="text-xs font-medium text-gray-500">{{ $professorList->count() }} {{ __('រូប') }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <span class="hidden sm:inline-block text-[10px] font-bold text-gray-400 uppercase tracking-widest" x-text="openDept ? '{{ __('បិទវិញ') }}' : '{{ __('មើលបញ្ជី') }}'"></span>
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
                                                                <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('រូបភាព') }}</th>
                                                                <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('ឈ្មោះអ្នកប្រើ / ពេញ') }}</th>
                                                                <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('អ៊ីម៉ែល') }}</th>
                                                                <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('សកម្មភាព') }}</th>
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
                                                                        <div class="flex items-center justify-end gap-3 text-xs font-bold">
                                                                            <a href="{{ route('admin.show-user', $professor->id) }}" class="text-green-600">{{ __('មើល') }}</a>
                                                                            <button type="button" @click.stop="openEditModal({{ $professor->id }})" class="text-emerald-600">{{ __('កែ') }}</button>
                                                                            <button type="button" @click.stop="confirmDelete('del-prof-{{ $professor->id }}', '{{ __('លោកគ្រូអ្នកគ្រូ') }}')" class="text-red-500">{{ __('លុប') }}</button>
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
                                                                    <a href="{{ route('admin.show-user', $professor->id) }}" class="text-green-600 text-xs font-bold uppercase tracking-widest">{{ __('មើល') }}</a>
                                                                    <button type="button" @click.stop="openEditModal({{ $professor->id }})" class="text-emerald-600 text-xs font-bold uppercase tracking-widest">{{ __('កែ') }}</button>
                                                                    <button @click.stop="confirmDelete('del-mob-prof-{{ $professor->id }}', 'Professor')" class="text-red-500 text-xs font-bold uppercase tracking-widest">{{ __('លុប') }}</button>
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
                                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">{{ __('ជំនាន់') }}</label>
                                        <select name="generation" onchange="this.form.submit()" class="w-full border-gray-200 rounded-xl text-sm focus:ring-green-500">
                                            <option value="">{{ __('គ្រប់ជំនាន់') }}</option>
                                            @foreach($generations as $gen)
                                                <option value="{{ $gen }}" {{ request('generation') == $gen ? 'selected' : '' }}>
                                                    {{ __('ជំនាន់ទី') }} {{ $gen }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="flex-1 min-w-[200px]">
                                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">{{ __('កម្មវិធីសិក្សា') }}</label>
                                        <select name="program_id" onchange="this.form.submit()" class="w-full border-gray-200 rounded-xl text-sm focus:ring-green-500">
                                            <option value="">{{ __('គ្រប់កម្មវិធីសិក្សា') }}</option>
                                            @foreach($programs as $prog)
                                                <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>
                                                    {{ $prog->name_km }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <a href="{{ route('admin.manage-users', ['tab' => 'students']) }}" class="px-6 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">
                                        {{ __('Reset') }}
                                    </a>
                                </form>
                            </div>

                            @if ($studentsGrouped->isEmpty())
                                <div class="bg-gray-100 p-8 rounded-2xl text-center text-gray-500 shadow-inner border-2 border-dashed border-gray-200">
                                    <i class="fas fa-user-slash text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-lg font-medium">{{ __('មិនទាន់មាននិស្សិតណាមួយនៅឡើយទេ។') }}</p>
                                </div>
                            @else
                                {{-- Loop តាមជំនាន់ (Generation) --}}
                                @foreach ($studentsGrouped as $generation => $programs)
                                    <div x-data="{ openGen: {{ $loop->first ? 'true' : 'false' }} }" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden transition-all duration-300">
                                        
                                        <button @click="openGen = !openGen" 
                                                class="w-full flex items-center justify-between px-6 py-4 bg-gray-50 hover:bg-gray-100 transition-colors border-b border-gray-100">
                                            <div class="flex items-center">
                                                <div class="h-11 w-11 bg-gradient-to-br from-green-500 to-green-700 text-white rounded-xl flex items-center justify-center shadow-lg shadow-green-100 mr-4 font-black text-sm uppercase">
                                                    G{{ $generation ?? '?' }}
                                                </div>
                                                <div class="text-left">
                                                    <h3 class="text-lg font-bold text-gray-800 tracking-tight">{{ __('ជំនាន់ទី') }} {{ $generation ?? 'មិនកំណត់' }}</h3>
                                                    <p class="text-xs font-medium text-gray-500">{{ $programs->flatten()->count() }} {{ __('និស្សិតសរុប') }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <span class="hidden sm:inline-block text-[10px] font-bold text-gray-400 uppercase tracking-widest" x-text="openGen ? '{{ __('បិទវិញ') }}' : '{{ __('មើលបញ្ជី') }}'"></span>
                                                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-500" :class="openGen ? 'rotate-180' : ''"></i>
                                            </div>
                                        </button>

                                        <div x-show="openGen" x-collapse>
                                            <div class="p-6 space-y-10">
                                                @foreach ($programs as $programName => $studentList)
                                                    <div class="relative">
                                                        <div class="flex items-center justify-between mb-4 border-b border-gray-50 pb-2">
                                                            <div class="flex items-center">
                                                                <div class="w-1.5 h-5 bg-green-500 rounded-full mr-3"></div>
                                                                <h4 class="text-sm font-extrabold text-gray-700 uppercase tracking-wider">{{ $programName }}</h4>
                                                            </div>
                                                            <span class="bg-green-50 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-md border border-green-100">
                                                                {{ $studentList->count() }} នាក់
                                                            </span>
                                                        </div>

                                                        {{-- 1. DESKTOP VERSION --}}
                                                        <div class="hidden md:block overflow-x-auto rounded-xl border border-gray-100 shadow-sm">
                                                            <table class="min-w-full divide-y divide-gray-200">
                                                                <thead class="bg-gray-50/50">
                                                                    <tr>
                                                                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('រូបភាព') }}</th>
                                                                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('អត្តសញ្ញាណ') }}</th>
                                                                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('ឈ្មោះអ្នកប្រើ / ពេញ') }}</th>
                                                                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('អ៊ីម៉ែល') }}</th>
                                                                        <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('ឆ្នាំសិក្សា') }}</th>
                                                                        <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('សកម្មភាព') }}</th>
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
                                                                            <td class="px-6 py-3 text-sm text-gray-600 font-medium edit-user-email">{{ $student->email ?? 'មិនទាន់បង្កើតគណនី' }}</td>
                                                                            <td class="px-6 py-3 text-center">
                                                                                @if($student->computed_year_level)
                                                                                    <span class="inline-flex items-center justify-center min-w-[2.5rem] px-2.5 py-1 rounded-lg text-xs font-bold
                                                                                        @if($student->computed_year_level >= $student->program->duration_years)
                                                                                            bg-purple-100 text-purple-700 border border-purple-200
                                                                                        @else
                                                                                            bg-emerald-50 text-emerald-700 border border-emerald-100
                                                                                        @endif">
                                                                                        {{ __('ឆ្នាំទី') }} {{ $student->computed_year_level }}
                                                                                    </span>
                                                                                @else
                                                                                    <span class="text-xs text-gray-400">—</span>
                                                                                @endif
                                                                            </td>
                                                                            <td class="px-6 py-3 text-right">
                                                                                <div class="flex items-center justify-end gap-3 text-xs font-bold">
                                                                                    <a href="{{ route('admin.show-user', $student->id) }}" class="text-green-600">{{ __('មើល') }}</a>
                                                                                    <button type="button" @click.stop="openEditModal({{ $student->id }})" class="text-emerald-600">{{ __('កែ') }}</button>
                                                                                    <button type="button" @click.stop="confirmDelete('del-std-{{ $student->id }}', '{{ __('និស្សិត') }}')" class="text-red-500">{{ __('លុប') }}</button>
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
                                                                                            @if($student->program && $student->computed_year_level >= $student->program->duration_years)
                                                                                                bg-purple-100 text-purple-700
                                                                                            @else
                                                                                                bg-emerald-50 text-emerald-700
                                                                                            @endif">
                                                                                            {{ __('ឆ្នាំទី') }} {{ $student->computed_year_level }}
                                                                                        </span>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                                                                        <span class="text-[10px] font-bold text-gray-400 italic">{{ $student->studentProfile->full_name_km ?? 'N/A' }}</span>
                                                                        <div class="flex space-x-4">
                                                                            <a href="{{ route('admin.show-user', $student->id) }}" class="text-green-600 text-xs font-bold uppercase tracking-widest">{{ __('មើល') }}</a>
                                                                            <button type="button" @click.stop="openEditModal({{ $student->id }})" class="text-emerald-600 text-xs font-bold uppercase tracking-widest">{{ __('កែ') }}</button>
                                                                            <button @click="confirmDelete('del-mob-{{ $student->id }}', 'Student')" class="text-red-500 text-xs font-bold uppercase tracking-widest">{{ __('លុប') }}</button>
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
                        
{{-- Global Delete Modal --}}
                        <div x-show="showDeleteModal" class="fixed inset-0 z-[9999] overflow-y-auto" x-cloak>
                            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                                <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showDeleteModal = false"></div>

                                <div x-show="showDeleteModal" @click.away="showDeleteModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                     class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl z-50">
                                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-center text-gray-900">{{ __('បញ្ជាក់ការលុប') }}</h3>
                                    <p class="mt-2 text-sm text-center text-gray-500">
                                        {{ __('តើអ្នកពិតជាចង់លុប') }} <span class="font-black text-red-600" x-text="deletingUserType"></span> {{ __('នេះមែនទេ? ទិន្នន័យនឹងបាត់បង់ជារៀងរហូត។') }}
                                    </p>
                                    <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                                        <button type="button" @click="showDeleteModal = false" class="px-5 py-2 text-sm font-bold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all">{{ __('បោះបង់') }}</button>
                                        <button type="button" @click="executeDeleteUser()" :disabled="isDeleting" class="px-5 py-2 text-sm font-bold text-white bg-red-600 rounded-xl hover:bg-red-700 shadow-lg shadow-red-200 transition-all disabled:opacity-50">
                                            <span x-show="!isDeleting">{{ __('លុបចេញ') }}</span>
                                            <span x-show="isDeleting"><i class="fas fa-spinner fa-spin mr-1"></i> កំពុងលុប...</span>
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
                                    <h3 class="text-lg font-bold text-gray-900">កែប្រែអ្នកប្រើប្រាស់</h3>
                                </div>
                                <button @click="showEditModal = false" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                    <i class="fas fa-times text-gray-400"></i>
                                </button>
                            </div>

                            {{-- Loading --}}
                            <div x-show="editLoading" class="p-12 text-center">
                                <i class="fas fa-spinner fa-spin text-2xl text-emerald-500"></i>
                                <p class="text-gray-400 mt-2 text-sm">កំពុងទាញយកទិន្នន័យ...</p>
                            </div>

                            {{-- Form --}}
                            <form x-show="!editLoading" @submit.prevent="submitEditForm()" class="p-6 space-y-5">
                                {{-- Name + Role --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1.5">ឈ្មោះអ្នកប្រើប្រាស់ <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="editForm.name" required placeholder="បញ្ចូលឈ្មោះអ្នកប្រើប្រាស់" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1.5">តួនាទី <span class="text-red-500">*</span></label>
                                        <select x-model="editForm.role" required class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                            <option value="admin">Admin</option>
                                            <option value="professor">Professor</option>
                                            <option value="student">Student</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Email (non-student) --}}
                                <div x-show="editForm.role !== 'student'">
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5">អ៊ីម៉ែល</label>
                                    <input type="email" x-model="editForm.email" placeholder="name@example.com" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                </div>

                                {{-- Password --}}
                                <div x-show="editForm.role !== 'student'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1.5">ពាក្យសម្ងាត់ថ្មី</label>
                                        <input type="password" x-model="editForm.password" placeholder="ទុកឱ្យនៅទទេប្រសិនបើមិនប្តូរ" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1.5">បញ្ជាក់ពាក្យសម្ងាត់</label>
                                        <input type="password" x-model="editForm.password_confirmation" placeholder="បញ្ជាក់ពាក្យសម្ងាត់ម្ដងទៀត" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                    </div>
                                </div>

                                {{-- Student fields --}}
                                <div x-show="editForm.role === 'student'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1.5">កម្មវិធីសិក្សា</label>
                                        <select x-model="editForm.program_id" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                            <option value="">ជ្រើសរើស</option>
                                            <template x-for="p in (editForm.programs || [])" :key="p.id">
                                                <option :value="p.id" x-text="p.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1.5">ជំនាន់</label>
                                        <select x-model="editForm.generation" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                            <option value="">ជ្រើសរើស</option>
                                            <template x-for="g in (editForm.generations || [])" :key="g.name">
                                                <option :value="g.name" x-text="'ជំនាន់ទី' + g.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                {{-- Professor fields --}}
                                <div x-show="editForm.role === 'professor'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1.5">មហាវិទ្យាល័យ</label>
                                        <select x-model="editForm.faculty_id" @change="filterEditDepartments($event.target.value)" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                            <option value="">ជ្រើសរើស</option>
                                            <template x-for="f in (editForm.faculties || [])" :key="f.id">
                                                <option :value="f.id" x-text="f.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1.5">ដេប៉ាតឺម៉ង់</label>
                                        <select x-model="editForm.department_id" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                            <option value="">ជ្រើសរើស</option>
                                            <template x-for="d in editDepartments" :key="d.id">
                                                <option :value="d.id" x-text="d.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                {{-- Profile Info --}}
                                <div class="border-t border-gray-100 pt-5">
                                    <h4 class="text-sm font-bold text-gray-700 mb-3"><i class="fas fa-id-card mr-1.5 text-orange-500"></i> ព័ត៌មានផ្ទាល់ខ្លួន</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1.5">ឈ្មោះពេញ (ខ្មែរ)</label>
                                            <input type="text" x-model="editForm.full_name_km" placeholder="បញ្ចូលឈ្មោះពេញជាភាសាខ្មែរ" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1.5">ឈ្មោះពេញ (អង់គ្លេស)</label>
                                            <input type="text" x-model="editForm.full_name_en" placeholder="Enter full name in English" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1.5">ភេទ</label>
                                            <select x-model="editForm.gender" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                                <option value="">ជ្រើសរើស</option>
                                                <option value="male">ប្រុស</option>
                                                <option value="female">ស្រី</option>
                                                <option value="other">ផ្សេងទៀត</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1.5">លេខទូរស័ព្ទ</label>
                                            <input type="text" x-model="editForm.phone_number" placeholder="012 345 678" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1.5">អាសយដ្ឋាន</label>
                                            <input type="text" x-model="editForm.address" placeholder="បញ្ចូលអាសយដ្ឋាន" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1.5">ថ្ងៃខែឆ្នាំកំណើត</label>
                                            <input type="date" x-model="editForm.date_of_birth" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 py-2.5 px-4">
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <label class="block text-xs font-bold text-gray-500 mb-2">រូបភាពប្រវត្តិរូប</label>
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
                                                <button type="button" onclick="document.getElementById('editProfilePicture').click()" class="px-4 py-2 text-xs font-bold text-emerald-700 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition-all">
                                                    <i class="fas fa-upload mr-1.5"></i>ជ្រើសរើសរូបភាព
                                                </button>
                                                <p class="text-[11px] text-gray-400 mt-1.5">JPEG, PNG ទំហំអតិបរមា 2MB</p>
                                                <button type="button" id="editRemovePicBtn" onclick="removeEditAvatar()" class="hidden mt-1.5 px-3 py-1 text-[11px] font-bold text-red-500 hover:text-red-700 transition-colors">
                                                    <i class="fas fa-times mr-1"></i>លុបរូបភាព
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                                    <button type="button" @click="showEditModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all">បោះបង់</button>
                                    <button type="submit" :disabled="editSaving" class="px-6 py-2.5 text-sm font-bold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition-all disabled:opacity-50">
                                        <span x-show="!editSaving"><i class="fas fa-save mr-1.5"></i> រក្សាទុក</span>
                                        <span x-show="editSaving"><i class="fas fa-spinner fa-spin mr-1.5"></i> កំពុងរក្សាទុក...</span>
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
            f.programs = data.programs || [];
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
                    removeBtn.classList.add('hidden');
                }
            }
            // Re-set select values after x-for options render
            var savedPid = data.program_id || '';
            var savedGen = data.generation || '';
            var savedDept = data.department_id || '';
            var savedFac = data.faculty_id || '';
            f.program_id = '';
            f.generation = '';
            f.department_id = '';
            f.faculty_id = '';
            setTimeout(function() {
                f.program_id = savedPid;
                f.generation = savedGen;
                f.department_id = savedDept;
                f.faculty_id = savedFac;
            }, 100);
        })
        .catch(function() {
            c.editLoading = false;
            c.showEditModal = false;
            window.showToast && window.showToast('មានបញ្ហាក្នុងការទាញយកទិន្នន័យ។', 'error');
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
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                removeBtn.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
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
            fd.append('program_id', f.program_id);
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
        if (fileInput && fileInput.files.length > 0) {
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
                    if (emailEl) emailEl.textContent = data.user.email || 'មិនទាន់បង្កើតគណនី';
                    if (fullnameEl) fullnameEl.textContent = data.user.full_name_km || data.user.full_name_en || 'N/A';
                    row.style.transition = 'all 0.3s ease';
                    row.style.backgroundColor = '#d1fae5';
                    setTimeout(function() { row.style.backgroundColor = ''; }, 1500);
                }
                window.showToast && window.showToast(data.message || 'បានធ្វើបច្ចុប្បន្នភាព។', 'success');
            } else {
                var msg = data.message || 'មានបញ្ហា។';
                if (data.errors) { msg += '\n' + Object.values(data.errors).flat().join('\n'); }
                window.showToast && window.showToast(msg, 'error');
            }
        })
        .catch(function() {
            c.editSaving = false;
            window.showToast && window.showToast('មានបញ្ហាក្នុងការរក្សាទុក។', 'error');
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
                window.showToast && window.showToast(data.message || 'អ្នកប្រើប្រាស់ត្រូវបានលុបដោយជោគជ័យ។', 'success');
            } else {
                window.showToast && window.showToast(data.message || 'មានបញ្ហា។', 'error');
            }
        })
        .catch(function() {
            form.submit();
        });
    }
    </script>

    <script>
    (function() {
        var searchInput = document.getElementById('live-search');
        var searchForm = document.getElementById('search-form');
        var timer = null;
        if (searchInput && searchForm) {
            searchInput.addEventListener('input', function() {
                clearTimeout(timer);
                timer = setTimeout(function() { searchForm.submit(); }, 400);
            });
        }
    })();
    </script>
</x-app-layout>