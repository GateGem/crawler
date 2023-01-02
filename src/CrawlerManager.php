<?php

namespace GateGem\Crawler;

use GateGem\Crawler\Models\DataRawSite;
use GateGem\Crawler\Models\SiteManager;
use GateGem\Crawler\Supports\Browser\Client;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PhpParser\Node\Expr\FuncCall;
use Symfony\Component\DomCrawler\Crawler as DomCrawlerBase;

class CrawlerManager
{
    public function getDomFromHtml($html)
    {
        return new DomCrawlerBase($html);
    }
    public function getContentFromLink($link)
    {
        $client = new Client();
        return $client->request('GET', $link);
    }
    private function getInfoFromLink($link)
    {
        return  parse_url($link);
    }
    public function checkKeyOrCreateTabale($domain_key)
    {
        $table_name = 'crawl_data_raw_site_manager_' . $domain_key;
        if (!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                $table->id();
                $table->string('link_key');
                $table->string('domain_key');
                $table->string('domain_site')->default();
                $table->string('link');
                $table->string('title');
                $table->string('description');
                $table->longText('data_raw');
                $table->unique('link_key', 'link_key_unique');
                $table->timestamps();
            });
        }
    }
    public function checkLink($link)
    {

        $parts = $this->getInfoFromLink($link);
        $domain_key = md5($parts['host']);
        $table_name = 'crawl_data_raw_site_manager_' . $domain_key;
        if (!Schema::hasTable($table_name)) {
            return false;
        }
        $link_key = md5($link);
        return DataRawSite::NewDataRawSite($domain_key)->where('link_key', $link_key)->exists();
    }
    public function insertLink($link)
    {
        try{
            if ($this->checkLink($link)) return null;
            $parts = $this->getInfoFromLink($link);
            $domain_key = md5($parts['host']);
            $site =  SiteManager::where('key', $domain_key)->first();
            if (!$site) {
                $site = new SiteManager();
                $site->key = $domain_key;
                $site->domain_site = $parts['host'];
                $site->link_site = $parts['scheme'] . '://' . $parts['host'];
                $site->save();
            }
            $link_key = md5($link);
            $crawler = $this->getContentFromLink($link);
    
            $keywords = $crawler->filter('meta[name="keywords"]')->first();
            $description = $crawler->filter('meta[name="description"]')->first();
            $titles = $crawler->filter('title')->first();
            $keywords = $keywords->count() > 0 ? $keywords->attr('content') : "";
            $description = $description->count() > 0 ? $description->attr('content') : "";
            $links = $crawler->filter('a');
            $title = $titles->count() > 0 ? $titles->html() : "";
            $data_raw = $crawler->outerHtml();
            $site->DataRaw()->updateOrCreate([
                'link_key' => $link_key
            ], [
                'domain_key' => $domain_key, 'domain_site' => $site->link_site, 'link_key' => $link_key, 'link' => $link,
                'title' =>  $title, 'description' =>  $description, 'data_raw' =>  $data_raw,
            ]);
            return $links;
        }catch(\Exception $err){
            return null;
        }
        
    }
}
