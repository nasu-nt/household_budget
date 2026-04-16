{{-- resources\views\dashboard\partials\_expense-form.blade.php --}}
<section class="dashboard__record-expense-area">
    <h2 class="dashboard__record-expense-title">Log your spending</h2>
    <form class="dashboard__record-expense-form" method="POST" action="#">
        @csrf
        
        {{-- ここに input を足していく想定 --}}
        <div class="dashboard__record-expense-actions">
            <button type="button" class="dashboard__add-expense-row">+ Add another expense</button>
            <button type="submit" class="dashboard__save-expense">Save expense</button>
        </div>
    </form>
</section>