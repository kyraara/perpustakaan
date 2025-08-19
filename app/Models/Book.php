<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'author',
        'publisher',
        'published_year',
        'isbn',
        'stock',
        'cover_image',
    ];

    // Di dalam class Book
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
