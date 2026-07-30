{{-- resources\views\dashboard\partials\_expense-form.blade.php --}}
<section class="dashboard__record-expense-area">

    <form
        class="dashboard__record-expense-form"
        method="POST"
        action="{{ route('expenses.store') }}"
    >
        @csrf

        <div>
            <label for="expense_date">Date</label>

            <input
                id="expense_date"
                name="expense_date"
                type="date"
                value="{{ old('expense_date', now()->format('Y-m-d')) }}"
                required
            >

            @error('expense_date')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="category_id">Category</label>

            <select
                id="category_id"
                name="category_id"
                required
            >
                <option value="">Select category</option>

                @foreach ($categories as $category)
                    <option
                        value="{{ $category->id }}"
                        @selected(old('category_id') == $category->id)
                    >
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            @error('category_id')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="amount">Amount</label>

            <div class="money-input">
                <span
                    class="money-input__currency"
                    aria-hidden="true"
                >
                    ¥
                </span>

                <input
                    id="amount"
                    class="money-input__field"
                    data-money-input
                    name="amount"
                    type="text"
                    inputmode="numeric"
                    autocomplete="off"
                    value="{{ old('amount') ? number_format((int) old('amount')) : '' }}"
                    required
                >
            </div>

            @error('amount')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="memo">Memo</label>

            <input
                id="memo"
                name="memo"
                type="text"
                maxlength="255"
                value="{{ old('memo') }}"
            >

            @error('memo')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">
            Save expense
        </button>
    </form>
</section>