{{-- resources\views\dashboard\index.blade.php --}}
<x-app-layout>

    {{-- 登録画面から遷移してきた場合 --}}
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

    <div class="dashboard-layout">
        {{-- サイドバー --}}
        @include('dashboard.partials._sidebar')

        {{-- メイン --}}
        <section class="dashboard-main">
<<<<<<< Updated upstream
            <h1>Dashboard</h1>
=======
            @include('dashboard.partials._calendar')
>>>>>>> Stashed changes
        </section>
    </div>

</x-app-layout>