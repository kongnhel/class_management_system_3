<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
                {{ __('កិច្ចការរបស់ខ្ញុំ') }}
            </h2>
            <a href="#" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-300">
                {{ __('កិច្ចការដែលត្រូវធ្វើ') }}
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl p-8">
                <h3 class="text-3xl font-extrabold text-gray-800 mb-8">{{ __('បញ្ជីកិច្ចការ') }}</h3>

                @if (session('success'))
                    <div class="bg-green-50 border-l-4 border-green-400 text-green-700 p-4 mb-6 rounded-lg shadow-sm" role="alert">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm leading-5 font-medium">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="overflow-x-auto rounded-xl shadow-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-indigo-500 to-indigo-600">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider rounded-tl-xl">{{ __('ចំណងជើង') }}</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider">{{ __('មុខវិជ្ជា') }}</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider">{{ __('កាលបរិច្ឆេទដាក់ស្នើ') }}</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider">{{ __('ស្ថានភាព') }}</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider rounded-tr-xl">{{ __('ពិន្ទុ') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($assignments as $assignment)
                                <tr class="hover:bg-gray-50 transition duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $assignment->title_km ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $assignment->courseOffering->course->title_km ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $assignment->due_date->format('d-M-Y H:i') }}
                                        @if ($assignment->due_date->isPast() && !$assignment->isSubmitted)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ __('យឺត') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        @if ($assignment->isSubmitted)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold leading-5 bg-green-100 text-green-800">
                                                {{ __('បានដាក់ស្នើ') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold leading-5 bg-yellow-100 text-yellow-800">
                                                {{ __('មិនទាន់ដាក់ស្នើ') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{-- @if ($assignment->grade)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold leading-5 {{ $assignment->grade->score >= 80 ? 'bg-green-100 text-green-800' : ($assignment->grade->score >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $assignment->grade->score }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-500 font-semibold">N/A</span>
                                        @endif --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 font-medium">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                            </svg>
                                            <p>{{ __('មិនមានកិច្ចការថ្មីទេ។') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($assignments->hasPages())
                    <div class="mt-8 flex justify-end">
                        <nav role="navigation" aria-label="Pagination Navigation">
                            {{ $assignments->links() }}
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>