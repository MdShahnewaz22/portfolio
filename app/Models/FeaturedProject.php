<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedProject extends Model
{
    use HasFactory;

    protected $fillable = ['project_name', 'live_link', 'image', 'status', 'created_at', 'updated_at', 'deleted_at'];

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

    // public function getImageAttribute($value)
    // {

    //     return (!is_null($value)) ? env('APP_URL') . '/public/storage/' . $value : null;
    // }


    public function getImageAttribute($value)
    {
        if (is_string($value)) {
            $images = json_decode($value, true);
        } elseif (is_array($value)) {
            $images = $value;
        } else {
            $images = [];
        }

        return array_map(function($image) {

            return (strpos($image, 'http://') === 0 || strpos($image, 'https://') === 0)
                ? $image
                : env('APP_URL') . '/public/storage/' . $image;
        }, $images);
    }

}
