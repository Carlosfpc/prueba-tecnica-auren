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

}
