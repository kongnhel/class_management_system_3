<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12 bg-slate-50 min-h-screen" x-data="attendanceBulk()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ __('ស្រង់វត្តមាននិស្សិត') }}
                    </h1>
                    <p class="text-slate-500 mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.993 7.993 0 002 12a7.993 7.993 0 007 7.196V4.804z"></path>
                        </svg>
                        {{ $courseOffering->course->name_km ?? 'មុខវិជ្ជា' }}
                    </p>
                </div>
                
                <div class="flex flex-col md:flex-row gap-3">
                    <button type="button" onclick="getLocation()" id="btn-location"
                        class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-xl font-bold text-white text-sm uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ __('ផ្ទៀងផ្ទាត់ទីតាំងគ្រូ') }}
                    </button>

                    <div class="bg-white p-2 rounded-xl shadow-sm border border-slate-200 inline-flex items-center">
                        <span class="px-3 text-sm font-semibold text-slate-600">{{ __('ថ្ងៃទី:') }}</span>
                        <input type="date" 
                               form="attendanceForm"
                               name="attendance_date" 
                               value="{{ $today ?? date('Y-m-d') }}" 
                               class="border-none focus:ring-0 text-slate-900 font-medium bg-transparent">
                    </div>
                </div>
            </div>

            {{-- Bulk Action Bar --}}
            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl p-4 mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" @change="toggleAll($event)" :checked="allSelected"
                            class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 cursor-pointer">
                        <span class="text-sm font-bold text-slate-700">{{ __('ជ្រើសរើសទាំងអស់') }}</span>
                    </label>
                    <span class="text-xs font-bold text-slate-400" x-show="selectedIds.length > 0" x-text="selectedIds.length + ' {{ __('រកឃើញ') }}'"></span>
                </div>
                <div class="flex items-center gap-2" x-show="selectedIds.length > 0" x-transition>
                    <button type="button" @click="bulkSet('present')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 border border-green-200 text-green-700 rounded-lg text-xs font-bold hover:bg-green-100 transition-all">
                        <i class="fas fa-check-circle"></i> {{ __('មក') }} (<span x-text="selectedIds.length"></span>)
                    </button>
                    <button type="button" @click="bulkSet('permission')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-xs font-bold hover:bg-blue-100 transition-all">
                        <i class="fas fa-file-alt"></i> {{ __('ច្បាប់') }} (<span x-text="selectedIds.length"></span>)
                    </button>
                    <button type="button" @click="bulkSet('absent')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 border border-red-200 text-red-700 rounded-lg text-xs font-bold hover:bg-red-100 transition-all">
                        <i class="fas fa-times-circle"></i> {{ __('អវត្តមាន') }} (<span x-text="selectedIds.length"></span>)
                    </button>
                    <button type="button" @click="clearSelection()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 border border-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-200 transition-all ml-1">
                        <i class="fas fa-times"></i> {{ __('បោះបង់') }}
                    </button>
                </div>
            </div>

            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl overflow-hidden">
                <form id="attendanceForm" action="{{ route('professor.attendance.store', $courseOffering->id) }}" method="POST">
                    @csrf
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="px-4 py-4 w-12"></th>
                                    <th class="px-8 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('ឈ្មោះនិស្សិត') }}</th>
                                    <th class="px-8 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('ស្ថានភាពវត្តមាន') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($students as $student)
                                    <tr class="hover:bg-slate-50 transition-colors group" :class="selectedIds.includes({{ $student->id }}) ? 'bg-emerald-50/50' : ''">
                                        <td class="px-4 py-5">
                                            <input type="checkbox" value="{{ $student->id }}"
                                                @change="toggleStudent({{ $student->id }})"
                                                :checked="selectedIds.includes({{ $student->id }})"
                                                class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 cursor-pointer">
                                        </td>
                                        <td class="px-8 py-5">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold group-hover:bg-green-100 group-hover:text-green-600 transition-colors">
                                                    {{ mb_substr($student->studentProfile->full_name_km ?? $student->name, 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-semibold text-slate-900">
                                                        {{ $student->studentProfile->full_name_km ?? $student->name }}
                                                    </div>
                                                    <div class="text-xs text-slate-400 font-mono uppercase">ID: {{ $student->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            <div class="flex justify-center items-center gap-2">
                                                @php $statuses = [
                                                    'present' => ['label' => __('មក'), 'color' => 'green'],
                                                    'permission' => ['label' => __('ច្បាប់'), 'color' => 'blue'],
                                                    'absent' => ['label' => __('អវត្តមាន'), 'color' => 'red']
                                                ]; @endphp

                                                @foreach($statuses as $value => $info)
                                                <label class="relative flex flex-col items-center cursor-pointer group/radio">
                                                    <input type="radio" name="attendance[{{ $student->id }}]" value="{{ $value }}" {{ $loop->first ? 'checked' : '' }} class="peer sr-only">
                                                    <span class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600 peer-checked:bg-{{ $info['color'] }}-50 peer-checked:border-{{ $info['color'] }}-500 peer-checked:text-{{ $info['color'] }}-700 transition-all hover:bg-slate-50">
                                                        {{ $info['label'] }}
                                                    </span>
                                                </label>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-slate-50 px-8 py-6 border-t border-slate-200 flex justify-end">
                        <button type="submit" class="inline-flex items-center justify-center px-8 py-3 bg-slate-900 border border-transparent rounded-xl font-bold text-white uppercase tracking-widest hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-all shadow-lg shadow-slate-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('រក្សាទុកវត្តមាន') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function attendanceBulk() {
            return {
                selectedIds: [],
                allSelected: false,
                totalStudents: {{ $students->count() }},
                toggleAll(event) {
                    if (event.target.checked) {
                        this.selectedIds = [@foreach($students as $s){{ $s->id }}, @endphp].map(id => id);
                        this.allSelected = true;
                    } else {
                        this.selectedIds = [];
                        this.allSelected = false;
                    }
                },
                toggleStudent(id) {
                    const idx = this.selectedIds.indexOf(id);
                    if (idx === -1) {
                        this.selectedIds.push(id);
                    } else {
                        this.selectedIds.splice(idx, 1);
                    }
                    this.allSelected = this.selectedIds.length === this.totalStudents;
                },
                bulkSet(status) {
                    this.selectedIds.forEach(id => {
                        const radio = document.querySelector('input[name="attendance[' + id + ']"][value="' + status + '"]');
                        if (radio) radio.checked = true;
                    });
                    this.clearSelection();
                },
                clearSelection() {
                    this.selectedIds = [];
                    this.allSelected = false;
                }
            }
        }

        function getLocation() {
            const btn = document.getElementById('btn-location');
            btn.disabled = true;
            btn.innerHTML = 'កំពុងឆែកទីតាំង...';

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                Swal.fire('កំហុស', 'Browser របស់អ្នកមិនគាំទ្រ GPS ទេ។', 'error');
                btn.disabled = false;
            }
        }

        function showPosition(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const sessionId = '{{ $courseOffering->id }}';

            fetch('{{ route("professor.verify-location") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    lat: lat,
                    lng: lng
                })
            })
            .then(response => response.json())
            .then(data => {
                const btn = document.getElementById('btn-location');
                btn.disabled = false;
                btn.innerHTML = 'ផ្ទៀងផ្ទាត់ទីតាំងគ្រូ';

                if (data.success) {
                    Swal.fire({
                        title: 'ជោគជ័យ!',
                        text: 'អ្នកបាន Check-in វត្តមានគ្រូបានជោគជ័យ។',
                        icon: 'success',
                        confirmButtonColor: '#1e293b'
                    });
                    btn.classList.replace('bg-emerald-600', 'bg-green-600');
                    btn.innerHTML = '✓ បានផ្ទៀងផ្ទាត់រួច';
                } else {
                    Swal.fire('បរាជ័យ', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('btn-location').disabled = false;
            });
        }

        function showError(error) {
            const btn = document.getElementById('btn-location');
            btn.disabled = false;
            btn.innerHTML = 'ផ្ទៀងផ្ទាត់ទីតាំងគ្រូ';
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    Swal.fire('សុំសិទ្ធិ', 'សូមអនុញ្ញាតឱ្យប្រើ GPS ជាមុនសិន។', 'warning');
                    break;
                default:
                    Swal.fire('កំហុស', 'មានបញ្ហាបច្ចេកទេសក្នុងការចាប់ទីតាំង។', 'error');
            }
        }
    </script>
</x-app-layout>