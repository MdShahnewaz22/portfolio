<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedProject extends Model
{
    use HasFactory;

    protected $fillable = ['project_name','live_link','image', 'status','created_at','updated_at','deleted_at'];

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->created_at = now();
        });

        static::updating(function ($model) {
            $model->updated_at = now();
        });
    }

    public function getImageAttribute($value)
    {

        return (!is_null($value)) ? env('APP_URL') . '/public/storage/' . $value : null;
    }


}