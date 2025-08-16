<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $primaryKey = 'cca3';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'cca3',
        'name_common',
        'name_official',
        'region',
        'subregion',
        'capital',
        'population',
        'area',
        'flag_emoji',
        'flag_png',
    ];

    /**
     * Scope a query to filter by name.
     * Allows searching in common and official names.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|null  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByName($query, $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where([
            ['name_common', 'like', "%{$search}%"],
            ['name_official', 'like', "%{$search}%", 'or'],
        ]);
    }

    /**
     * Scope a query to filter by region.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|null  $region
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByRegion($query, $region)
    {
        if (!$region) {
            return $query;
        }

        return $query->where('region', $region);
    }

    /**
     * Scope a query to sort by population.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByPopulation($query, $direction = 'desc')
    {
        return $query->orderBy('population', $direction);
    }

}
