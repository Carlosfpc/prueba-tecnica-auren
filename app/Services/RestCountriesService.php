<?php

namespace App\Services;

use Illuminate\Http\Client\Factory as Http;
use Illuminate\Support\Collection;

/**
 * Service class responsible for all communication with the restcountries.com API.
 */
class RestCountriesService
{
    private const API_BASE_URL = 'https://restcountries.com/v3.1';

    private array $apiFields = [
        'cca3',
        'name',
        'region',
        'subregion',
        'capital',
        'population',
        'area',
        'flag',
        'flags',
    ];

    private Http $http;

    public function __construct(Http $http)
    {
        $this->http = $http;
    }

    /**
     * Fetches all countries from the API with a predefined set of fields.
     *
     * @return Collection A collection of country data arrays.
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function fetchAllCountries(): Collection
    {
        $response = $this->http
            ->baseUrl(self::API_BASE_URL)
            ->get('all', ['fields' => implode(',', $this->apiFields)]);


        $response->throw();

        return $response->collect();
    }
}
