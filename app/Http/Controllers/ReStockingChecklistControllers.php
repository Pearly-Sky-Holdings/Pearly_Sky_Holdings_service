<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReStockingChecklistControllers extends Controller
{
    public function getAll()
  {
 
    $packagesDetails = DB::table('re_stocking_checklists')
      ->get();
    return response()->json($packagesDetails, 200);
  }
}
