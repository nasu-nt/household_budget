{{-- resources/views/dashboard/partials/_sidebar.blade.php --}}
<aside
    class="dashboard__sidebar"
    data-dashboard-sidebar
    aria-label="{{ __('Expense tools') }}"
>
    <div
        id="dashboard-sidebar-content"
        class="dashboard__sidebar-scroll"
        data-dashboard-sidebar-scroll
    >
        <div class="dashboard__sidebar-inner">
            {{-- 支出登録フォーム --}}
            @include('dashboard.partials._expense-form')

            {{-- サブスクリプション設定への導線 --}}
            @include('dashboard.partials._recurring-expenses-cta')
        </div>
    </div>

    {{-- スクロール領域の外に置くことで、取っ手を固定する --}}
    @include('dashboard.partials._sidebar-handle')

</aside>