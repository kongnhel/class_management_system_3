<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('កិច្ចការផ្ទះទាំងអស់') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-8">
                <h3 class="text-3xl font-bold text-gray-800 mb-8 flex items-center">
                    <i class="fas fa-file-alt mr-3 text-red-600"></i>{{ __('បញ្ជីកិច្ចការផ្ទះទាំងអស់ដែលខ្ញុំគ្រប់គ្រង') }}
                </h3>

                <div class="overflow-x-auto bg-gray-50 rounded-lg shadow-inner mb-6">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr class="bg-gray-200 text-gray-700 uppercase text-sm font-semibold">
                                <th class="py-3 px-4 text-left rounded-tl-lg">{{ __('ចំណងជើង') }}</th>
                                <th class="py-3 px-4 text-left">{{ __('មុខវិជ្ជា') }}</th>
                                <th class="py-3 px-4 text-left">{{ __('ថ្ងៃផុតកំណត់') }}</th>
                                <th class="py-3 px-4 text-left">{{ __('ពិន្ទុអតិបរមា') }}</th>
                                <th class="py-3 px-4 text-left">{{ __('ស្ថានភាព') }}</th>
                                <th class="py-3 px-4 text-center rounded-tr-lg">{{ __('សកម្មភាព') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assignments as $assignment)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-4 text-gray-800">{{ $assignment->title_km ?? $assignment->title_en ?? 'N/A' }}</td>
                                    <td class="py-3 px-4 text-gray-600">{{ $assignment->courseOffering->course->title_km ?? 'N/A' }}</td>
                                    <td class="py-3 px-4 text-gray-600">{{ \Carbon\Carbon::parse($assignment->due_date)->format('Y-m-d H:i') }}</td>
                                    <td class="py-3 px-4 text-gray-600">{{ $assignment->max_score }}</td>
                                    <td class="py-3 px-4 text-gray-600">
                                        @if (\Carbon\Carbon::parse($assignment->due_date)->isPast())
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ __('ផុតកំណត់') }}</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ __('នៅសល់') }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center space-x-2">
                                        {{-- Link to view submissions for this assignment (requires a specific route) --}}
                                        <a href="{{ route('professor.manage-assignments', ['offering_id' => $assignment->course_offering_id]) }}" class="text-emerald-600 hover:text-emerald-800 font-semibold py-1 px-3 rounded-full text-sm transition-colors duration-200 hover:bg-emerald-100">
                                            {{ __('មើលការដាក់ស្នើ') }} (0)
                                        </a>
                                        {{-- Edit Button (placeholder for now) --}}
                                        <button class="text-purple-600 hover:text-purple-800 font-semibold py-1 px-3 rounded-full text-sm transition-colors duration-200 hover:bg-purple-100">
                                            {{ __('កែសម្រួល') }}
                                        </button>
                                        {{-- Delete Form (placeholder for now) --}}
                                        <form action="#" method="POST" class="inline-block" onsubmit="return confirm('{{ __('តើអ្នកពិតជាចង់លុបកិច្ចការផ្ទះនេះមែនទេ?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold py-1 px-3 rounded-full text-sm transition-colors duration-200 hover:bg-red-100">
                                                {{ __('លុប') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 px-6 text-center text-gray-500">
                                        {{ __('មិនទាន់មានកិច្ចការផ្ទះណាមួយត្រូវបានកំណត់នៅឡើយទេ។') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="mt-4">
                    {{ $assignments->links('pagination::tailwind', ['pageName' => 'assignmentsPage']) }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
