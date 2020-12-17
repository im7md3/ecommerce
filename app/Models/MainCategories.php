<?php

namespace App\Models;

use App\Observers\MainCategoriesObserver;
use Illuminate\Database\Eloquent\Model;

class MainCategories extends Model
{
    protected $table = 'main_categories';
    protected $fillable = ['translation_lang', 'translation_of', 'name', 'slug', 'photo', 'active', 'updated_at', 'created_at'];
    protected $hidden = [];

    protected static function boot()
    {
        parent::boot();
        MainCategories::observe(MainCategoriesObserver::class);
    }


    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeSelection($query)
    {
        return $query->select('id', 'translation_lang', 'name', 'slug', 'photo', 'active');
    }

    public function getActive()
    {
        return $this->active == '1' ? 'مفعل' : 'غير مفعل';
    }

    public function getPhotoAttribute($val)
    {
        return ($val !== null) ? asset('/assets/'.$val) : '';
    }

    public function categories(){
        return $this->hasMany(self::class,'translation_of');
    }

    public function vendors(){
        return $this->hasMany('App\Models\Vendor','category_id');
    }
}
