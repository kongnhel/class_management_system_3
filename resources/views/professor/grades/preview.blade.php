<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('មើលឯកសារ PDF') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-xl">
                <div class="p-6 bg-white">
                    <!-- Action buttons -->
                    <div class="flex justify-between items-center mb-6">
                        <!-- Go back button -->
                        <a href="{{ route('professor.manage-grades', ['offering_id' => $courseOffering->id]) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-300">
                            <i class="fas fa-arrow-left mr-2"></i> ត្រឡប់ក្រោយ
                        </a>
                        
                        <!-- Download button -->
                        <a href="{{ route('professor.grades.download-pdf', ['offering_id' => $courseOffering->id]) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-300">
                            <i class="fas fa-download mr-2"></i> ទាញយក
                        </a>
                    </div>
                    
                    <!-- This section directly renders the report data from the PDF template -->
                    <div class="report-container p-6 bg-white border border-gray-200 rounded-lg shadow-inner">
                        <style>
                            /*
                                We will use Tailwind classes for most styling, but keep this for Khmer font.
                                Note: This font will need to be configured for your project.
                            */
                            .report-container {
                                font-family: 'khmerosmoul', sans-serif;
                                font-size: 14px;
                            }
                        </style>

                        <div class="text-center mb-8">
                            <h1 class="text-2xl font-bold mb-1">តារាងពិន្ទុ</h1>
                            <p class="text-sm text-gray-600 mb-1"><strong>{{ __('មុខវិជ្ជា:') }}</strong> {{ $courseOffering->course->title_km }} ({{ $courseOffering->course->code }})</p>
                            <p class="text-sm text-gray-600 mb-1"><strong>ឆ្នាំសិក្សា:</strong> {{ $courseOffering->academic_year }} | <strong>ឆមាស:</strong> {{ $courseOffering->semester }}</p>
                            <p class="text-sm text-gray-600"><strong>សាស្រ្តាចារ្យ:</strong> {{ $courseOffering->lecturer->name }}</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden border border-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">
                                            ឈ្មោះនិស្សិត
                                        </th>
                                        @foreach($assessments as $assessment)
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-r last:border-r-0 border-gray-200">
                                                {{ $assessment->title_km }} ({{ $assessment->max_score }})
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($students as $student)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-200">
                                                {{ $student->profile->full_name_km ?? $student->name }}
                                            </td>
                                            @foreach ($assessments as $assessment)
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center border-r last:border-r-0 border-gray-200">
                                                    {{ $gradebook[$student->id][$assessment->id] ?? '-' }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $assessments->count() + 1 }}" class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-500">
                                                មិនទាន់មាននិស្សិតចុះឈ្មោះក្នុងមុខវិជ្ជានេះទេ។
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
