<?php

namespace App\Http\Controllers;
include "pr.php";

use Illuminate\Http\Request;
use DB;


class GenerateRankingController extends Controller
{
    public function __construct()
    {

    }

    public function generateRanking($query)
    {
	    // To use the query, just use the $query variable. E.g.
	    // dd($query);
	    // The above command will output query to the page
		$queryArray = explode(" ", $query);
        $results = DB::select('select * from results');
        $results = json_decode(json_encode($results), true);

		$index = $this->getIndex($results);
		$matchDocs = array();
		$docCount = count($index['docCount']);

		foreach($queryArray as $qterm) {
				$entry = $index['dictionary'][$qterm];
				foreach($entry['postings'] as $docID => $posting) {
						$matchDocs[$docID] +=
										$posting['tf'] *
										log($docCount + 1 / $entry['df'] + 1, 2);
				}
		}

		foreach($matchDocs as $docID => $score) {
				$matchDocs[$docID] = $score/$index['docCount'][$docID];
		}

		$finalScore = array();
		foreach($matchDocs as $docID => $score) {
			$finalScore[$docID] = 0.5 * $this->cosineSim() + 0.5 * getPagerank($results[$docID]["website"]);
		}

		arsort($finalScore); // high to low

		var_dump($finalScore);

		die();

	    // Results is gonna be a nested array. Each index in first array is an array of description/website. E.g.:
	    // array:1 [▼
	    //     0 => array:2 [▼
	    // 	     "description" => "random description"
	    // 	     "website" => "http://tst.com"
        //     ]
	    // ]

	    // Returns first description
	    // $results[0]["description"];

	    // Returns first website
	    // $results[0]["website"];

	    // Leave the return for now. Will update in future with a proper page
        //return redirect()->route('search');
	}

	function cosineSim($docA, $docB) {
        $result = 0;
        foreach($docA as $key => $weight) {
                $result += $weight * $docB[$key];
        }
        return $result;
	}

	function getIndex($collection) {

        $dictionary = array();
        $docCount = array();

        foreach($collection as $docID => $description) {
                $terms = explode(' ', $description);
                $docCount[$docID] = count($terms);

                foreach($terms as $term) {
                        if(!isset($dictionary[$term])) {
                                $dictionary[$term] = array('df' => 0, 'postings' => array());
                        }
                        if(!isset($dictionary[$term]['postings'][$docID])) {
                                $dictionary[$term]['df']++;
                                $dictionary[$term]['postings'][$docID] = array('tf' => 0);
                        }

                        $dictionary[$term]['postings'][$docID]['tf']++;
                }
        }

        return array('docCount' => $docCount, 'dictionary' => $dictionary);
    }
}
