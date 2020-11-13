<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DOMDocument;

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

    private function follow_links($url, $home){
        $doc = new DOMDocument();
        $doc->loadHTML(file_get_contents($url));

        $linklist = $doc->getElementsByTagName('a');

        foreach ($linklist as $link) {
            $l = $link->getAttribute("href");
            $full_link = $home.$l;

            if (!in_array($full_link, $this->already_crawled)) {
                $this->already_crawled[] = $full_link;
                $this->crawling[] = $full_link;
                echo $full_link.PHP_EOL;
                // Insert data in the DB

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
