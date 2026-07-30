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
            ->whereNull('archived_at')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('settings.categories.index', compact('categories'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $userId = $request->user()->id;

        $archivedCategory = Category::query()
            ->where('user_id', $userId)
            ->where('name', $validated['name'])
            ->whereNotNull('archived_at')
            ->orderByDesc('archived_at')
            ->orderByDesc('id')
            ->first();

        if (
            $archivedCategory !== null
            && ($validated['intent'] ?? null) !== 'create_new'
        ) {
            return redirect()
                ->route('settings.categories.index')
                ->withInput([
                    'name' => $validated['name'],
                    'color_code' => $validated['color_code'],
                ])
                ->with('archived_category_conflict', [
                    'id' => $archivedCategory->id,
                    'name' => $archivedCategory->name,
                ]);
            }

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
            'enable' => $category->update([
                'is_active' => true,
            ]),
            'disable' => $category->update([
                'is_active' => false,
            ]),
            'archive' => $category->update([
                'is_active' => false,
                'archived_at' => now(),
            ]),
        };

        $message = match ($validated['intent']) {
            'save' => __('Category updated successfully.'),
            'enable' => __('Category enabled successfully.'),
            'disable' => __('Category disabled successfully.'),
            'archive' => __('Category archived successfully.'),
        };

        return redirect()
            ->route('settings.categories.index')
            ->with('success', $message);
    }

    public function restore(
        Request $request,
        Category $category
    ): RedirectResponse {
        abort_unless(
            $request->user() !== null
                && (int) $category->user_id === (int) $request->user()->id
                && $category->archived_at !== null,
            404
        );

        $request->merge([
            'color_code' => strtoupper(
                (string) $request->input('color_code')
            ),
        ]);

        $validated = $request->validateWithBag('storeCategory', [
            'color_code' => [
                'required',
                'string',
                'regex:/^#[0-9A-F]{6}$/',
            ],
        ], [
            'color_code.regex' => __('Select a valid category color.'),
        ]);

        $hasCurrentCategoryWithSameName = Category::query()
            ->where('user_id', $request->user()->id)
            ->where('name', $category->name)
            ->whereNull('archived_at')
            ->where('id', '!=', $category->id)
            ->exists();

        if ($hasCurrentCategoryWithSameName) {
            return redirect()
                ->route('settings.categories.index')
                ->withInput([
                    'name' => $category->name,
                    'color_code' => $validated['color_code'],
                ])
                ->withErrors([
                    'name' => __(
                        'A current category with this name already exists.'
                    ),
                ], 'storeCategory');
        }

        $category->update([
            'color_code' => $validated['color_code'],
            'is_active' => true,
            'archived_at' => null,
        ]);

        return redirect()
            ->route('settings.categories.index')
            ->with('success', __('Category restored successfully.'));
    }
}
