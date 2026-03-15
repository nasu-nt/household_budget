{{-- resources\views\dashboard\index.blade.php --}}
<x-app-layout>
    @if (session('success'))
        <div id="toast-success"
            class="toast toast--success"
            data-toast
            role="status"
            aria-live="polite"
        >
            <span>{{ session('success') }}</span>
            <button type="button" id="toast-close" data-toast-close aria-label="Close">×</button>
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    You're logged in!!
                </div>
            </div>
        </div>
    </div>
</x-app-layout>