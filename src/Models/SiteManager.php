<?php

namespace GateGem\Crawler\Models;

use GateGem\Crawler\Facades\Crawler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiteManager extends Model
{
    use HasFactory;
    public $table = 'crawl_site_manager';
    protected $fillable = ['key', 'domain_site', 'link_site'];

    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $related
     * @param  string|null  $foreignKey
     * @param  string|null  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasManyTable($related, $foreignKey = null, $localKey = null, $table = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();
        if ($table) {
            $instance->setTable($table);
        }

        return $this->newHasMany(
            $instance->newQuery(),
            $this,
            $instance->getTable() . '.' . $foreignKey,
            $localKey
        );
    }
    public function DataRaw()
    {
        Crawler::checkKeyOrCreateTabale($this->key);
        return $this->hasManyTable(DataRawSite::class, 'domain_key', 'key', 'crawl_data_raw_site_manager_' . $this->key);
    }
}
