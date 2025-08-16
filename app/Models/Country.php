<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
    public function scopeFilterByName(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function (Builder $q, string $searchTerm) {
            $q->where(function (Builder $subQuery) use ($searchTerm) {
                $subQuery->where('name_common', 'like', "%{$searchTerm}%")
                    ->orWhere('name_official', 'like', "%{$searchTerm}%");
            });
        });
    }

    /**
     * Scope a query to filter by region.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|null  $region
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByRegion(Builder $query, ?string $region): Builder
    {
        return $query->when($region, function (Builder $q, string $regionName) {
            $q->where('region', $regionName);
        });
    }

    /**
     * Scope a query to sort by population.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByPopulation(Builder $query, string $direction = 'desc'): Builder
    {
        return $query->orderBy('population', $direction);
    }

}
