<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FamilyMember;
use App\Models\Citizenship;
use PHPUnit\Framework\MockObject\Stub\ReturnReference;

class MemberController extends Controller
{
    // list members and quick search
    public function index()
    {
        return view('members.index');
    }

    public function create()
    {
        $citizenships = Citizenship::orderBy('country')->limit(100)->get();
        return view('members.create', compact('citizenships'));
    }

    // simple page to show tree for a selected member
    public function showTree($id)
    {
        $member = FamilyMember::findOrFail($id);
        return view('members.tree', compact('member'));
    }

    public function store(Request $request)
    {
        // We let API handle create; keep this to redirect.
        return redirect()->route('members.index');
    }
    public function showTreenew($id)
    {
        // return "hi";

        try {
            

            // Validate ID
            if (!$id || !is_numeric($id)) {
                return back()->with('error', 'Invalid member ID.');
            }

            // Fetch user and related family
            $user = FamilyMember::with([
                'father',
                'mother',
                'spouse',
                'children',
                'father.father',
                'father.mother',
                'mother.father',
                'mother.mother'
            ])->find($id);

            if (!$user) {
                return back()->with('error', 'No family member found with this ID.');
            }

            return view('family.tree', [
                'user' => $user,
                'father' => $user->father,
                'mother' => $user->mother,
                'spouse' => $user->spouse,
                'children' => $user->children,
                'pgf' => $user->paternalGrandfather(),
                'pgm' => $user->paternalGrandmother(),
                'mgf' => $user->maternalGrandfather(),
                'mgm' => $user->maternalGrandmother(),
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

}
