<?php

namespace App\Http\Controllers;

use App\Models\ReStockingChecklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReStockingChecklistControllers extends Controller
{
    public function getAll(Request $request)
  {
    $country = $request->query('country','EN');
    $packagesDetails = ReStockingChecklist::all();

      $translatedData = TranslationController::translateJson($packagesDetails->toArray(), $country);

    return response()->json($translatedData, 200);
  }
}
