<?php

/**
 * Created by PhpStorm.
 * User: lechchut
 * Date: 7/23/19
 * Time: 12:17 PM.
 */

namespace App\Http\Controllers\Backend;

use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Category\CategorySortRequest;
use App\Http\Requests\Backend\Category\CategoryStoreRequest;
use App\Http\Requests\Backend\Category\CategoryUpdateRequest;
use App\Models\Category;
use App\Services\ArtworkService;
use Cache;
use Illuminate\Http\RedirectResponse;

class CategoryController
{
    private const DEFAULT_ROUTE = 'backend.categories.index';

    public function index()
    {
        if (!Cache::has('categories')) {
            Cache::forever('categories', Category::all());
        }

        return view('backend.categories.index')
            ->with([
                'nestable_categories' => $this->displayCategories(),
            ]);
    }

    public function create()
    {
        return view('backend.categories.create');
    }

    public function store(CategoryStoreRequest $request): RedirectResponse
    {
        $category = Category::create($request->all());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $category);
        }

        Cache::clear('categories');

        return MessageHelper::redirectMessage('Category successfully added!', self::DEFAULT_ROUTE);
    }

    public function edit(Category $category)
    {
        Cache::clear('categories');

        return view('backend.categories.edit')
            ->with([
                'category' => $category,
            ]);
    }

    public function update(Category $category, CategoryUpdateRequest $request): RedirectResponse
    {
        $category->update($request->all());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $category);
        }

        Cache::clear('categories');

        return MessageHelper::redirectMessage('Category successfully updated!', self::DEFAULT_ROUTE);
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        Cache::clear('categories');

        return MessageHelper::redirectMessage('Category successfully deleted!', self::DEFAULT_ROUTE);
    }

    public function sort(CategorySortRequest $request): RedirectResponse
    {
        $list = $request->input('list');
        $list = json_decode(stripslashes($list), true);

        if (!is_array($list)) {
            exit('error');
        }

        $list = parseJsonArray($list);

        foreach ($list as $value) {
            if ($id = intval($value['id'])) {
                Category::query()
                    ->where('id', $id)
                    ->update([
                        'parent_id' => intval($value['parent_id']),
                    ]);
            }
        }

        Cache::clear('categories');

        return MessageHelper::redirectMessage('Category successfully re-arranged!', self::DEFAULT_ROUTE);
    }

    private function displayCategories($parentId = 0, $subLevel = false)
    {
        if (!Cache::has('categories')) {
            abort('403', 'Can not get categories cache');
        }

        $catInfo = Cache::get('categories')->toArray();
        /** re-arrange array */
        $array = [];

        foreach ($catInfo as $row) {
            $array[$row['id']] = $row;
        }

        $catInfo = $array;
        $catItem = '';
        $rootCategory = [];

        if (count($catInfo)) {
            foreach ($catInfo as $cats) {
                if ($cats['parent_id'] == $parentId) {
                    $rootCategory[] = $cats['id'];
                }
            }

            if (count($rootCategory)) {
                foreach ($rootCategory as $id) {
                    $catItem .= "<li class=\"dd-item dd3-item\" data-id=\"{$catInfo[$id]['id']}\"><div class=\"dd-handle dd3-handle\"></div><div class=\"dd3-content\"><a href=\"" . route('backend.categories.edit', ['category' => $catInfo[$id]['id']]) . "\">{$catInfo[$id]['name']}</a></div><div class=\"dd3-action\"><a class=\"row-button upload\" href=\"" . route('backend.categories.edit', ['category' => $catInfo[$id]['id']]) . '"><i class="fa fa-fw fa-edit"></i></a><a class="row-button edit" href="' . route('backend.categories.destroy', ['category' => $catInfo[$id]['id']]) . '"><i class="fa fa-fw fa-trash"></i></a></div>';
                    $catItem .= $this->displayCategories($id, true);
                    $catItem .= '</li>';
                }

                return $subLevel
                    ? '<ol class="dd-list">' . $catItem . '</ol>'
                    : $catItem;
            }
        }
    }
}
