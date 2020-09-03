<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CurrencyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // 
    }

    public function index()
    {
        $currency = DB::table('tipocambio')
                    ->orderBy('Fec_Registro', 'DESC')
                    ->first();
        return response()->json($currency);
    }
}
