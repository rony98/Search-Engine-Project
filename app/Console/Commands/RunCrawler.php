<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DOMDocument;
use DB;

class runCrawler extends Command
{
    var $already_crawled = [];
    var $crawling = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the crawler for hardcoded list of websites';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

private function remove_invalid($Str) {  
  $StrArr = str_split($Str); $NewStr = '';
  foreach ($StrArr as $Char) {    
    $CharNo = ord($Char);
    if ($CharNo == 163) { $NewStr .= $Char; continue; } // keep £ 
    if ($CharNo > 31 && $CharNo < 127) {
      $NewStr .= $Char;    
    }
  }  
  return mb_convert_encoding(iconv("UTF-8", "UTF-8//IGNORE", $NewStr), 'UTF-8', 'UTF-8');
}

    private function follow_links($url, $home){
        $doc = new DOMDocument();
        @$doc->loadHTML(file_get_contents($url));

        $linklist = $doc->getElementsByTagName('a');

        foreach ($linklist as $link) {
            $l = $link->getAttribute("href");

	    if (strpos($l, "https://") !== false || strpos($l, "http://") !== false || strpos($l, "www.") !== false) {
                $full_link = $l;
            } else {
                $full_link = $home.$l;
            }

            if (!in_array($full_link, $this->already_crawled) && substr($full_link, 0, 4) == "http") {
                $this->already_crawled[] = $full_link;
                $this->crawling[] = $full_link;
		echo $full_link.PHP_EOL;
                // Insert data in the DB
                
		$dom = new DOMDocument;
		@$dom->loadHTMLFile($full_link);

		$html = new \Html2Text\Html2Text($dom->saveHTML($dom->getElementsByTagName('body')->item(0)));

                DB::table('results')->insert([
                    ['description' => $this->remove_invalid($html->getText()), 'website' => $this->remove_invalid($full_link)]
                ]);
            }
        }

        array_shift($this->crawling);
        foreach ($this->crawling as $link) {
            $this->follow_links($link, $home);
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $websites = ["https://bbc.com/news"];

        foreach ($websites as $website) {
            $this->follow_links($website, $website);
        }
    }
}
