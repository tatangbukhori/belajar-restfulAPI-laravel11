<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Post extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */

    protected $fillable = [
        'image',
        'title',
        'content',
    ];

    /**
     * image
     *
     * @return Attribute
     */

    // protected function image(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn(string $value) => ucfirst($value),
    //     );
    // }
    public function getImageAttribute($value)
    {
        // $value adalah isi dari kolom image di DB (nama file)
        if (!$value) {
            return null;
        }

        return asset('storage/posts/' . $value);
    }
}
