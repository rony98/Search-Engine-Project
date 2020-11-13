<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;
use Spatie\Crawler\Crawler;

class runCrawler extends Command
{
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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $websites = ["https://wwww.bbc.com/news"];

        foreach ($websites as $website) {
            SitemapGenerator::create($website)
                ->configureCrawler(function (Crawler $crawler) {
                    $crawler->ignoreRobots();
                })
                ->hasCrawled(function (Url $url) {
                    $this->info($url);
                });
        }
    }
}
