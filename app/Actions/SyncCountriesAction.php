<?php

namespace App\Actions;

use App\Models\Country;
use App\Services\RestCountriesService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Encapsulates the business logic for synchronizing countries from the external API to our database.
 */
class SyncCountriesAction
{
    private RestCountriesService $restCountriesService;

    /**
     * Constructor.
     * @param RestCountriesService $restCountriesService The service responsible for API communication.
     */
    public function __construct(RestCountriesService $restCountriesService)
    {
        $this->restCountriesService = $restCountriesService;
    }

    /**
     * Execute the synchronization process.
     *
     * @return array A summary of the operation.
     */
    public function execute(): array
    {
        try {
            $countriesFromApi = $this->restCountriesService->fetchAllCountries();

            // Format the API data to match our database schema.
            $formattedCountries = $countriesFromApi->map(function ($country) {
                return [
                    'cca3' => $country['cca3'],
                    'name_common' => $country['name']['common'],
                    'name_official' => $country['name']['official'],
                    'region' => $country['region'] ?? 'N/A',
                    'subregion' => $country['subregion'] ?? null,
                    'capital' => $country['capital'][0] ?? null,
                    'population' => $country['population'],
                    'area' => $country['area'],
                    'flag_emoji' => $country['flags']['emoji'] ?? null,
                    'flag_png' => $country['flags']['png'] ?? null,
                ];
            })->toArray();

            if (empty($formattedCountries)) {
                return ['total_fetched' => 0, 'upserted' => 0, 'status' => 'No countries fetched.'];
            }

            $upsertedCount = Country::upsert(
                $formattedCountries,
                ['cca3'],
                [
                    'name_common', 'name_official', 'region', 'subregion', 'capital',
                    'population', 'area', 'flag_emoji', 'flag_png'
                ]
            );

            return [
                'total_fetched' => count($formattedCountries),
                'upserted' => $upsertedCount,
                'status' => 'Synchronization completed successfully.'
            ];

        } catch (\Exception $e) {

            Log::error('Country synchronization failed: ' . $e->getMessage());

            return [
                'total_fetched' => 0,
                'upserted' => 0,
                'status' => 'Synchronization failed. Check logs for details.'
            ];
        }
    }
}
