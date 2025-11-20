<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FamilyMember;
use App\Models\Citizenship;
use Illuminate\Validation\Rule;

class MemberApiController extends Controller
{
    // GET /api/members?search=...
    public function index(Request $request)
    {
        $q = $request->query('search');
        $query = FamilyMember::query();

        if ($q) {
            $query->where(function($q2) use ($q) {
                $q2->where('first_name','like',"%$q%")
                   ->orWhere('last_name','like',"%$q%")
                   ->orWhere('occupation','like',"%$q%")
                   ->orWhere('gotra','like',"%$q%")
                   ->orWhere('mul','like',"%$q%");
            })->with('citizenship');
        } else {
            $query->with('citizenship')->limit(200);
        }

        $members = $query->take(200)->get();

        return response()->json($members);
    }

    // GET /api/members/{id}
    public function show($id)
    {
        $member = FamilyMember::with('citizenship')->findOrFail($id);
        return response()->json($member);
    }

    // POST /api/members
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'=>'required|string|max:100',
            'last_name'=>'nullable|string|max:100',
            'gender'=>'nullable|in:male,female,other',
            'father_id'=>'nullable|exists:family_members,id',
            'mother_id'=>'nullable|exists:family_members,id',
            'spouse_id'=>'nullable|exists:family_members,id',
            'occupation'=>'nullable|string|max:150',
            'gotra'=>'nullable|string|max:150',
            'mul'=>'nullable|string|max:150',
            'dob'=>'nullable|date',
            'dod'=>'nullable|date',
            'citizenship_id'=>'nullable|exists:citizenships,id',
            'citizenship_country'=>'nullable|string|max:150',
            'photo'=>'nullable|string', // if file upload implement separately
        ]);

        // If user provided a citizenship_country but not id, create or fetch
        if (empty($data['citizenship_id']) && !empty($request->input('citizenship_country'))) {
            $country = trim($request->input('citizenship_country'));
            $cit = Citizenship::firstOrCreate(['country' => $country], ['iso_code'=>'']);
            $data['citizenship_id'] = $cit->id;
        }

        // now create
        $member = FamilyMember::create($data);

        return response()->json(['success'=>true,'member'=>$member], 201);
    }

    /**
     * Return nodes for family-tree display centered around $id:
     * - include up to 4 generations (ancestors and descendants)
     * - include spouse relationships (pids)
     */
    public function nodesForTree($id)
    {
        $center = FamilyMember::findOrFail($id);

        // BFS traversal collecting nodes up to depth
        $maxGen = 4; // depth
        $collected = collect();
        $queue = collect([ ['id' => $center->id, 'gen' => 0] ]);
        $seen = [];

        while ($queue->isNotEmpty()) {
            $item = $queue->shift();
            $mid = $item['id']; $gen = $item['gen'];

            if (isset($seen[$mid])) continue;
            $seen[$mid] = true;

            $m = FamilyMember::with('citizenship')->find($mid);
            if (!$m) continue;
            $collected->push($m);

            // traverse parents (upwards) until maxGen
            if ($gen < $maxGen) {
                if ($m->father_id) $queue->push(['id'=>$m->father_id, 'gen'=>$gen+1]);
                if ($m->mother_id) $queue->push(['id'=>$m->mother_id, 'gen'=>$gen+1]);
            }

            // traverse children (downwards)
            if ($gen < $maxGen) {
                // find children: where father_id == mid OR mother_id == mid
                $children = FamilyMember::where(function($q) use ($mid){
                    $q->where('father_id', $mid)->orWhere('mother_id', $mid);
                })->pluck('id');

                foreach ($children as $cid) { $queue->push(['id'=>$cid, 'gen'=>$gen+1]); }
            }
        }

        // Ensure we include spouses of collected nodes
        $spouseIds = [];
        foreach ($collected as $m) {
            if ($m->spouse_id) $spouseIds[] = $m->spouse_id;
        }
        $spouseIds = array_unique($spouseIds);
        if (!empty($spouseIds)) {
            $extra = FamilyMember::whereIn('id', $spouseIds)->get();
            foreach ($extra as $e) $collected->push($e);
        }

        // Map to FamilyTreeJS node shape
        $unique = $collected->unique('id')->values();
        $nodes = $unique->map(function($m) {
            $pids = [];
            if ($m->spouse_id) $pids[] = (int)$m->spouse_id;
            return [
                'id' => (int)$m->id,
                'fid' => $m->father_id ? (int)$m->father_id : null,
                'mid' => $m->mother_id ? (int)$m->mother_id : null,
                'pids' => $pids,
                'name' => $m->full_name,
                'title' => $m->occupation ?? '',
                'img' => $m->avatar,
                'gotra' => $m->gotra,
                'mul' => $m->mul,
                'citizenship' => $m->citizenship ? $m->citizenship->country : null,
            ];
        })->values();

        return response()->json($nodes);
    }
}
