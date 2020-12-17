<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MainCategoryRequest;
use App\Models\MainCategories;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MainCategoriesController extends Controller
{
    public function index()
    {
        $default_lang = get_default_lang();
        $cats = MainCategories::where('translation_lang', $default_lang)->selection()->get();
        return view('admin.maincategories.index', compact('cats'));
    }

    public function create()
    {
        return view('admin.maincategories.create');
    }

    public function insert(MainCategoryRequest $request)
    {
        try {
            $main_categories = collect($request->category);

            $filter = $main_categories->filter(function ($value, $key) {
                return $value['abbr'] == get_default_lang();
            });

            $default_category = array_values($filter->all())[0];


            $filePath = "";
            if ($request->has('photo')) {

                $filePath = uploadImage('maincategories', $request->photo);
            }

            DB::beginTransaction();

            $default_category_id = MainCategories::insertGetId([
                'translation_lang' => $default_category['abbr'],
                'translation_of' => 0,
                'name' => $default_category['name'],
                'slug' => $default_category['name'],
                'photo' => $filePath
            ]);

            $remain_categories = $main_categories->filter(function ($value, $key) {
                return $value['abbr'] != get_default_lang();
            });

            $categories_arr = [];
            if (isset($remain_categories) && $remain_categories->count() > 0) {
                foreach ($remain_categories as $cat) {
                    $categories_arr[] = [
                        'translation_lang' => $cat['abbr'],
                        'translation_of' => $default_category_id,
                        'name' => $cat['name'],
                        'slug' => $cat['name'],
                        'photo' => $filePath
                    ];
                };
            }
            MainCategories::insert($categories_arr);
            DB::commit();
            return redirect()->route('admin.maincategories')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['error' => 'حدث خطأ ما برجاء المحاولة لاحقا']);
        }
    }

    public function edit($id)
    {
        $mainCategory = MainCategories::selection()->with('categories')->find($id);

        if (!$mainCategory) {
            return redirect()->route('admin.maincategories')->with(['error' => 'عذرا هذا القسم غير موجود']);
        }
        return view('admin.maincategories.edit', compact('mainCategory'));
    }

    public function update(MainCategoryRequest $request, $id)
    {
        try {
            $cat = MainCategories::find($id);
            if (!$cat) {
                return redirect()->route('admin.maincategories')->with(['error' => 'عذرا هذا القسم غير موجود']);
            }
            $category = array_values($request->category)[0];
            if (!$request->has('category.0.active')) {
                $request->request->add(['active' => 0]);
            } else {
                $request->request->add(['active' => 1]);
            }


            MainCategories::where('id', $id)->update([
                'name' => $category['name'],
                'active' => $request['active'],
            ]);


            if ($request->has('photo')) {
                $filePath = uploadImage('maincategories', $request->photo);
                MainCategories::where('id', $id)->update([
                    'photo' => $filePath
                ]);
            }


            return redirect()->route('admin.maincategories')->with(['success' => 'تم تعديل القسم بنجاح']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'حدث خطأ ما برجاء المحاولة لاحقا']);
        }
    }

    public function delete($id)
    {
        try {
            $categories = MainCategories::with('categories')->find($id);
            if (!$categories) {
                return redirect()->route('admin.maincategories')->with(['error' => 'عذرا هذا القسم غير موجود']);
            }
            $vendors = $categories->vendors();
            if(isset($vendors)&&$vendors->count()>0){
            return redirect()->route('admin.maincategories')->with(['error'=>'لا يمكن حذف هذا القسم']);
        } 

            $image = Str::after($categories->photo, 'assets/');
            $image = base_path('assets/' . $image);
            unlink($image);
            $categories->categories()->delete();
            $categories->delete();
            return redirect()->route('admin.maincategories')->with(['success' => 'تم حذف القسم بنجاح']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'حدث خطأ ما برجاء المحاولة لاحقا']);
        }
    }

    public function changeStatus($id)
    {
        try {
            $cat = MainCategories::with('categories')->find($id);
            if (!$cat) {
                return redirect()->route('admin.maincategories')->with(['error' => 'عذرا هذا القسم غير موجود']);
            }
            $status = $cat->active == 0 ? 1 : 0;
            $cat->update(['active'=>$status]);
            return redirect()->route('admin.maincategories')->with(['success' => 'تم تفعيل القسم بنجاح']);
        } catch (\Exception $e) {
            return $e;
            return redirect()->back()->with(['error' => 'حدث خطأ ما برجاء المحاولة لاحقا']);
        }
    }
}
