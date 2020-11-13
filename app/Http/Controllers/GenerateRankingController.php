<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class GenerateRankingController extends Controller
{
    public function __construct()
    {

    }

    public function generateRanking(Request $request)
    {
        $query = $request->request->get('param');

        $results = DB::select('select * from results');
        $results = json_decode(json_encode($results), true);

	// Results is gonna be a nested array. Each index in first array is an array of description/website. E.g.:
	// array:1 [▼
	//     0 => array:2 [▼
	// 	 "description" => "random description"
	// 	 "website" => "http://tst.com"
	//     ]
	// ]

	// Returns first description
	// $results[0]["description"];
	
	// Returns first website
	// $results[0]["website"];

	// Leave the return for now. Will update in future with a proper page
        return redirect()->route('search');
    }
}
