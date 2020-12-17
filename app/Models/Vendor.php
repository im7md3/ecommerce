<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Vendor extends Model
{
    use Notifiable;

    protected $table = 'vendors';
    protected $fillable = ['name', 'logo', 'mobile', 'address', 'email','password', 'category_id', 'active', 'created_at', 'updated_at'];
    protected $hidden = [];

    public function scopeSelection($query)
    {
        return $query->select('id', 'name', 'logo', 'mobile', 'address', 'email', 'active','category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function getLogoAttribute($val)
    {
        return ($val !== null) ? asset('/assets/' . $val) : "";
    }

    public function getActive()
    {
        return $this->active == '1' ? 'مفعل' : 'غير مفعل';
    }

    public function setPasswordAttribute($password){
        if(!empty($password)){
            $this->attributes['password']=bcrypt($password);
        }
    }

    public function categories()
    {
        return $this->belongsTo('App\Models\MainCategories', 'category_id');
    }
}
