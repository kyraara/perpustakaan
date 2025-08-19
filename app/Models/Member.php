<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nis',
        'name',
        'class',
        'address',
        'phone_number',
        'email',
        'photo',
    ];

    // Di dalam class Member
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }
}
