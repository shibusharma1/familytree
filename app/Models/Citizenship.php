<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Citizenship extends Model
{
    protected $fillable = ['country', 'iso_code'];

    public function members()
    {
        return $this->hasMany(FamilyMember::class);
    }
}
