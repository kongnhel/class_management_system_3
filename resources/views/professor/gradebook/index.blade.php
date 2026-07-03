<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
                <th class="px-6 py-4 text-sm font-semibold text-slate-700">{{ __('ចំណាត់ថ្នាក់') }}</th>
                <th class="px-6 py-4 text-sm font-semibold text-slate-700">{{ __('អត្តលេខ') }}</th>
                <th class="px-6 py-4 text-sm font-semibold text-slate-700">{{ __('ឈ្មោះនិស្សិត') }}</th>
                <th class="px-6 py-4 text-sm font-semibold text-slate-700 text-center">{{ __('ពិន្ទុសរុប (100%)') }}</th>
                <th class="px-6 py-4 text-sm font-semibold text-slate-700 text-center">{{ __('និទ្ទេស') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($rankedStudents as $student)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4">
                    @if($student->rank == 1)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            🏆 លេខ ១
                        </span>
                    @elseif($student->rank == 2)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-200 text-slate-800">
                            🥈 លេខ ២
                        </span>
                    @elseif($student->rank == 3)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            🥉 លេខ ៣
                        </span>
                    @else
                        <span class="text-slate-600 font-medium ml-4">{{ $student->rank }}</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ $student->student_id_code }}</td>
                <td class="px-6 py-4 text-sm font-medium text-slate-900">
                    {{ $student->profile->full_name_km ?? $student->name }}
                </td>
                <td class="px-6 py-4 text-sm text-center font-bold text-emerald-600">
                    {{ number_format($student->total_score, 2) }}
                </td>
                <td class="px-6 py-4 text-center">
                    @php
                        $grade = '';
                        if($student->total_score >= 85) $grade = 'A';
                        elseif($student->total_score >= 80) $grade = 'B+';
                        elseif($student->total_score >= 70) $grade = 'B';
                        elseif($student->total_score >= 65) $grade = 'C+';
                        elseif($student->total_score >= 50) $grade = 'C';
                        else $grade = 'F';
                    @endphp
                    <span class="font-bold {{ $grade == 'F' ? 'text-red-500' : 'text-emerald-600' }}">
                        {{ $grade }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>