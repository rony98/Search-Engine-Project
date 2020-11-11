<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GenerateRanking extends Controller
{
    public function __construct() 
    {

    }

    public function generateRanking(Request $request)
    {
        $query = $request->request->get('param');
    }
}
