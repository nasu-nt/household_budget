<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->where('user_id', $request->user()->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('settings.categories.index', compact('categories'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $userId = $request->user()->id;

        $maxSortOrder = Category::query()
            ->where('user_id', $userId)
            ->max('sort_order');

        Category::create([
            'user_id' => $userId,
            'name' => $validated['name'],
            'color_code' => $validated['color_code'],
            'sort_order' => $maxSortOrder === null
                ? 0
                : $maxSortOrder + 1,
            'is_active' => true,
        ]);

        return redirect()
            ->route('settings.categories.index')
            ->with('success', __('Category added successfully.'));
    }

    public function update(
        UpdateCategoryRequest $request,
        Category $category
    ): RedirectResponse {
        $validated = $request->validated();

        match ($validated['intent']) {
            'save' => $category->update([
                'name' => $validated['name'],
                'color_code' => $validated['color_code'],
            ]),
            'enable' => $category->update(['is_active' => true]),
            'disable' => $category->update(['is_active' => false]),
        };

        $message = match ($validated['intent']) {
            'save' => __('Category updated successfully.'),
            'enable' => __('Category enabled successfully.'),
            'disable' => __('Category disabled successfully.'),
        };

        return redirect()
            ->route('settings.categories.index')
            ->with('success', $message);
    }
}
