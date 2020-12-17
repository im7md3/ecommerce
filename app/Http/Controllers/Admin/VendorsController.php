<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorsRequest;
use App\Models\MainCategories;
use App\Models\Vendor;
use App\Notifications\VendorCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VendorsController extends Controller
{
    public function index()
    {
        $vendors = Vendor::with('categories')->selection()->paginate(PAGINATION_COUNT);
        return view('admin.vendors.index', compact('vendors'));
    }

    public function create()
    {
        $categories=MainCategories::where('translation_lang',get_default_lang())->active()->get();
        return view('admin.vendors.create',compact('categories'));
    }

    public function insert(VendorsRequest $request)
    {
        try{
            if(!$request->has('active')){
                $request->request->add(['active'=>0]);
            }else{
                $request->request->add(['active'=>1]);
            }
            if($request->has('logo')){
                $filePath=uploadImage('vendors',$request->logo);
            }
            $vendor=Vendor::create([
                'name'              =>$request->name,
                'mobile'            =>$request->mobile,
                'email'             =>$request->email,
                'active'            =>$request->active,
                'address'           =>$request->address,
                'logo'              =>$filePath,
                'category_id'       =>$request->category_id,
                'password'          =>$request->password,
            ]);
            Notification::send($vendor, new VendorCreated($vendor));
            return redirect()->route('admin.vendors')->with(['success' => 'تم الحفظ بنجاح']);


        }catch(\Exception $e){
            return $e;
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function edit($id)
    {
        try{
            $categories=MainCategories::where('translation_lang',get_default_lang())->active()->get();
            $vendor=Vendor::with('categories')->selection()->find($id);
        if(!$vendor){
            return redirect()->route('admin.vendors')->with(['error' => 'عذرا هذا المتجر غير موجود']);
        }
        return view('admin.vendors.edit',compact('vendor','categories'));
    }catch(\Exception $e){
        return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
    }
}

    public function update($id,VendorsRequest $request)
    {
        try{
            $vendor=Vendor::find($id);
        if(!$vendor){
            return redirect()->route('admin.vendors')->with(['error' => 'عذرا هذا المتجر غير موجود']);
        }
        if(!$request->has('active')){
            $request->request->add(['active'=>0]);
        }else{
            $request->request->add(['active'=>1]);
        }

        DB::beginTransaction();
        
        if($request->has('logo')){
            $filePath=uploadImage('vendors',$request->logo);
            Vendor::where('id',$id)->update([
                'logo'=>$filePath
            ]);
        }

        $data = $request->except('_token', 'id', 'logo', 'password');


            if ($request->has('password') && !is_null($request->  password)) {
                $data['password'] = $request->password;
            }

            Vendor::where('id', $id)
                ->update(
                    $data
                );

        DB::commit();
        return redirect()->route('admin.vendors')->with(['success' => 'تم تعديل بيانات المتجر بنجاح']);

    }catch(\Exception $e){
        return $e;
        DB::rollback();
        return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
    }
    }

    public function delete($id)
    {
        try{
            $vendor=Vendor::find($id);
            if(!$vendor){
                return redirect()->route('admin.vendors')->with(['error' => 'عذرا هذا المتجر غير موجود']);
            }
            $image=Str::after($vendor->logo, 'assets/');
            $image=base_path('assets/'.$image);
            unlink($image);
            $vendor->delete();
            return redirect()->route('admin.vendors')->with(['success' => 'تم حذف المتجر بنجاح']);

        }catch(\Exception $e){
            return $e;
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function changeStatus($id)
    {
    }
}
