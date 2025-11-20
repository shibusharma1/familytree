<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FamilyMember;
use App\Models\Citizenship;

class FamilySeeder extends Seeder
{
    public function run(): void
    {
        $india = Citizenship::firstOrCreate(['country'=>'India']);
        $uk = Citizenship::firstOrCreate(['country'=>'United Kingdom']);

        // Grandparents
        $john = FamilyMember::create(['first_name'=>'John','last_name'=>'Smith','gender'=>'male','citizenship_id'=>$uk->id]);
        $mary  = FamilyMember::create(['first_name'=>'Mary','last_name'=>'Smith','gender'=>'female','citizenship_id'=>$uk->id,'spouse_id'=>$john->id]);
        $john->update(['spouse_id'=>$mary->id]);

        // Parents
        $mason = FamilyMember::create(['first_name'=>'Mason','last_name'=>'Johnson','gender'=>'male','father_id'=>$john->id,'mother_id'=>$mary->id,'citizenship_id'=>$uk->id]);
        $emily = FamilyMember::create(['first_name'=>'Emily','last_name'=>'Johnson','gender'=>'female','citizenship_id'=>$india->id]);

        // marriage
        $mason->update(['spouse_id'=>$emily->id]);
        $emily->update(['spouse_id'=>$mason->id]);

        // children (you + siblings)
        $lucas = FamilyMember::create(['first_name'=>'Lucas','last_name'=>'Johnson','gender'=>'male','father_id'=>$mason->id,'mother_id'=>$emily->id]);
        $chloe = FamilyMember::create(['first_name'=>'Chloe','last_name'=>'Johnson','gender'=>'female','father_id'=>$mason->id,'mother_id'=>$emily->id]);
    }
}
