<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'gender', 
        'father_id', 'mother_id', 'spouse_id',
        'occupation', 'gotra', 'mul',
        'dob', 'dod', 'citizenship_id', 'photo'
    ];

    // Father
    public function father()
    {
        return $this->belongsTo(FamilyMember::class, 'father_id');
    }

    // Mother
    public function mother()
    {
        return $this->belongsTo(FamilyMember::class, 'mother_id');
    }

    // Spouse (bi-directional)
    public function spouse()
    {
        return $this->belongsTo(FamilyMember::class, 'spouse_id');
    }

    // Children (reverse relation)
    public function children()
    {
        return $this->hasMany(FamilyMember::class, 'father_id')
                    ->orWhere('mother_id', $this->id);
    }

    // Grandparents
    public function paternalGrandfather()
    {
        return $this->father ? $this->father->father : null;
    }

    public function paternalGrandmother()
    {
        return $this->father ? $this->father->mother : null;
    }

    public function maternalGrandfather()
    {
        return $this->mother ? $this->mother->father : null;
    }

    public function maternalGrandmother()
    {
        return $this->mother ? $this->mother->mother : null;
    }
}
