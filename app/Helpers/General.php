<?php

use App\Models\Language;
use Illuminate\Support\Facades\Config;

function get_languages(){
    return Language::active()->selection()->get();
}

function get_default_lang(){
    return Config::get('app.locale');
}

function uploadImage($folder,$image){
    $image->store('/',$folder);
    $fileName=$image->hashName();
    $path='images/'.$folder.'/'.$fileName;
    return $path;
}