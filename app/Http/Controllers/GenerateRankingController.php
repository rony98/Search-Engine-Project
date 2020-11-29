<?php

namespace App\Http\Controllers;
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

		$queryString = "select * from results where ";
		foreach($queryArray as $value) {
		    $queryString = $queryString . "description like '%$value%' and ";
        }
		$queryString = substr($queryString, 0, -4);
        $queryString = $queryString . "limit 50";

        $results = DB::select($queryString);
        $results = json_decode(json_encode($results), true);

		$index = $this->getIndex($results);
		$matchDocs = array();
		$docCount = count($index['docCount']);

		foreach($queryArray as $qterm) {
            if (array_key_exists($qterm, $index['dictionary'])) {
                $entry = $index['dictionary'][$qterm];
                foreach($entry['postings'] as $docID => $posting) {
                    if (array_key_exists($docID, $matchDocs)) {
                        $matchDocs[$docID] +=
                            $posting['tf'] *
                            log($docCount + 1 / $entry['df'] + 1, 2);
                    } else {
                        $matchDocs[$docID] =
                            $posting['tf'] *
                            log($docCount + 1 / $entry['df'] + 1, 2);
                    }

                }
            }

		}

		foreach($matchDocs as $docID => $score) {
            $matchDocs[$docID] = $score / $index['docCount'][$docID];
        }
		$finalScore = array();
		foreach($matchDocs as $docID => $score) {
			$finalScore[$docID] = 0.5 * $matchDocs[$docID] + 0.5 * $this->pageRank($results[$docID]["website"]);
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

	function pageRank($url1) {
        $url = 'https://openpagerank.com/api/v1.0/getPageRank';
        $query = http_build_query(array(
            'domains' => array(
                $url1
            )
        ));
        $url = $url .'?'. $query;
        $ch = curl_init();
        $headers = ['API-OPR: sk0044gssg0kk448skcg48gscgww0oo8gscsg408'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec ($ch);
        curl_close ($ch);
        $output = json_decode($output,true);
        var_dump($output);
	}

	function getIndex($collection) {
        $dictionary = array();
        $docCount = array();
        for ($x = 0; $x < count($collection); $x++) {
            $terms = explode(' ', $collection[$x]["description"]);
            $docCount[$x] = count($terms);
            foreach($terms as $term) {
                if(!isset($dictionary[$term])) {
                    $dictionary[$term] = array('df' => 0, 'postings' => array());
                }
                if(!isset($dictionary[$term]['postings'][$x])) {
                    $dictionary[$term]['df']++;
                    $dictionary[$term]['postings'][$x] = array('tf' => 0);
                }
                $dictionary[$term]['postings'][$x]['tf']++;
            }
        }
        return array('docCount' => $docCount, 'dictionary' => $dictionary);
    }
}
