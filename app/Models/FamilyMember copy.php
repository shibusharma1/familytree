<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FamilyMember extends Model
{
    protected $fillable = [
        'first_name','last_name','gender',
        'father_id','mother_id','spouse_id',
        'occupation','gotra','mul','dob','dod',
        'citizenship_id','photo'
    ];

    protected $appends = ['full_name','avatar'];

    public function father() { return $this->belongsTo(self::class,'father_id'); }
    public function mother() { return $this->belongsTo(self::class,'mother_id'); }
    public function spouse() { return $this->belongsTo(self::class,'spouse_id'); }

    // children: either father_id or mother_id equals this id
    public function children()
    {
        return $this->hasMany(self::class, 'father_id')->orWhere('mother_id', $this->id);
        // Note: for eager-loading across collections we build queries in controller
    }

    public function citizenship() { return $this->belongsTo(Citizenship::class,'citizenship_id'); }

    // convenience
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . ($this->last_name ?? ''));
    }

    // Avatar accessor: use uploaded file if exists, else generate SVG data URI initials
    public function getAvatarAttribute()
    {
        if ($this->photo && file_exists(public_path('uploads/'.$this->photo))) {
            return asset('uploads/'.$this->photo);
        }

        $initials = strtoupper(substr($this->first_name ?? '',0,1) . substr($this->last_name ?? '',0,1));
        if ($initials === '') $initials = '?';
        $bg = '#0EA5A4'; // teal-ish
        $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='200' height='200'>
            <rect width='100%' height='100%' fill='{$bg}' />
            <text x='50%' y='50%' fill='white' font-family='Inter, Arial' font-size='72' dominant-baseline='middle' text-anchor='middle'>{$initials}</text>
        </svg>";
        return 'data:image/svg+xml;utf8,'.rawurlencode($svg);
    }
}
