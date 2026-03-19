{{-- _sidebar.blade.php --}}
<aside id="sidebar" class="dashboard__sidebar">
    {{-- 支出記録フォーム --}}
    @include('dashboard.partials._expense-form')

    {{-- サブスク管理への導線 --}}
    @include('dashboard.partials._recurring-expenses-cta')

    {{-- サイドバーを開け閉めする取っ手ボタン --}}
    @include('dashboard.partials._sidebar-handle')
</aside>