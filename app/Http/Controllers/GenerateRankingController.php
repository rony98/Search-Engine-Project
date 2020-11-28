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
		    $queryString = $queryString . "'description' like '%$value%' and ";
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
                    $matchDocs[$docID] +=
                        $posting['tf'] *
                        log($docCount + 1 / $entry['df'] + 1, 2);
                }
            }

		}

		foreach($matchDocs as $docID => $score) {
				$matchDocs[$docID] = $score/$index['docCount'][$docID];
		}

		$finalScore = array();
		foreach($matchDocs as $docID => $score) {
			$finalScore[$docID] = 0.5 * $this->cosineSim() + 0.5;
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

    function genHash($url) {
        $hash = "Mining PageRank is AGAINST GOOGLE'S TERMS OF SERVICE. Yes, I'm talking to you, scammer.";
        $c = 16909125;
        $length = strlen($url);
        $hashpieces = str_split($hash);
        $urlpieces = str_split($url);
        for ($d = 0; $d < $length; $d++) {
            $c = $c ^ (ord($hashpieces[$d]) ^ ord($urlpieces[$d]));
            $c = (($c >> 23) & 0x1ff) | $c << 9;
        }
        $c = -(~($c & 4294967295) + 1);
        return '8' . dechex($c);
    }

	function pagerank($url) {
		$googleurl = 'http://toolbarqueries.google.com/tbr?client=navclient-auto&ch=' . genHash($url) . '&features=Rank&q=info:' . urlencode($url);
		if(function_exists('curl_init')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $googleurl);
			$out = curl_exec($ch);
			curl_close($ch);
		} else {
			$out = file_get_contents($googleurl);
		}
		if(strlen($out) > 0) {
			return trim(substr(strrchr($out, ':'), 1));
		} else {
			return -1;
		}
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
