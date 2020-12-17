<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageRequest;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index(){
        $languages=Language::selection()->paginate(PAGINATION_COUNT);
        return view('admin.languages.index',compact('languages'));
    }

    public function create(){
        $languages=Language::selection()->paginate(PAGINATION_COUNT);
        return view('admin.languages.create');
    }

    public function insert(LanguageRequest $request)
    {
        try {
            Language::create($request->except(['_token']));
            return redirect()->route('admin.languages')->with(['success' => 'تم حفظ اللغة بنجاح']);
        } catch (\Exception $ex) {
            return redirect()->route('admin.languages')->with(['error' => 'هناك خطا ما يرجي المحاوله فيما بعد']);
        }
    }

    public function edit($id)
    {
        $language = Language::select()->find($id);
        if (!$language) {
            return redirect()->route('admin.languages')->with(['error' => 'هذه اللغة غير موجوده']);
        }
        return view('admin.languages.edit', compact('language'));
    }

    public function update(LanguageRequest $request,$id){
        $language = Language::find($id);
        if (!$language) {
            return redirect()->route('admin.languages')->with(['error' => 'هذه اللغة غير موجوده']);
        }
        if(!$request->has('active'))
            $request->request->add(['active'=>0]);
        $language->update($request->except('_token'));
        return redirect()->route('admin.languages')->with(['success' => 'تم تعديل اللغة بنجاح']);
    }

    public function delete($id){
        $language = Language::find($id);
        if (!$language) {
            return redirect()->route('admin.languages')->with(['error' => 'هذه اللغة غير موجوده']);
        }
        $language->delete();
        return redirect()->route('admin.languages')->with(['success' => 'تم حذف اللغة بنجاح']);
    }

}
