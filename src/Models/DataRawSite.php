<?php

namespace GateGem\Crawler\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DataRawSite extends Model
{
    use HasFactory;

    protected $fillable = ['domain_key', 'link_key', 'link', 'title', 'description', 'data_raw'];

    //protected static function newFactory()
    //{
    //    return \GateGem\Crawler\Database\Factories\DataRawSiteFactory::new();
    //}
    public static function NewDataRawSite($key)
    {
        $model = new self();
        $model->setTable('crawl_data_raw_site_manager_' . $key);
        return $model;
    }
}
