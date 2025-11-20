<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberApiController;

Route::get('/tree',function(){
    return view('tree');
});

Route::get('/family/tree/{id}', [MemberController::class, 'showTreenew'])->name('family.tree');


Route::get('/', [MemberController::class, 'index'])->name('members.index');
Route::get('/members/create', [MemberController::class,'create'])->name('members.create');
Route::post('/members', [MemberController::class,'store'])->name('members.store');
Route::get('/members/{id}/tree', [MemberController::class,'showTree'])->name('members.tree');


Route::get('/members/{id}/four-gen', [MemberController::class, 'fourGen'])->name('members.fourgen');

// API endpoints used by the front-end JS
Route::prefix('api')->group(function () {
    Route::get('members', [MemberApiController::class,'index']);           // list / search
    Route::get('members/{id}', [MemberApiController::class,'show']);      // single (for autofill)
    Route::post('members', [MemberApiController::class,'store']);         // create
    Route::get('nodes/{id}', [MemberApiController::class,'nodesForTree']); // nodes for family tree (4 generations)
});
