<?php

namespace Tests\Unit\Services;

use App\Services\RestCountriesService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RestCountriesServiceTest extends TestCase
{
    /**
     * Test that the service can successfully fetch and return country data.
     *
     * @return void
     */
    public function test_it_fetches_countries_successfully(): void
    {
        // Arrange: Fake a successful API response
        Http::fake([
            'restcountries.com/*' => Http::response([
                [
                    'cca3' => 'ESP',
                    'name' => ['common' => 'Spain', 'official' => 'Kingdom of Spain'],
                ]
            ], 200)
        ]);

        $service = $this->app->make(RestCountriesService::class);
        $countries = $service->fetchAllCountries();

        $this->assertIsIterable($countries);
        $this->assertCount(1, $countries);
        $this->assertEquals('ESP', $countries->first()['cca3']);
    }

    /**
     * Test that the service throws an exception when the API call fails.
     *
     * @return void
     */
    public function test_it_throws_exception_on_api_failure(): void
    {
        // Arrange: Server error response.
        Http::fake(['restcountries.com/*' => Http::response(null, 500)]);

        $this->expectException(RequestException::class);

        $service = $this->app->make(RestCountriesService::class);
        $service->fetchAllCountries();
    }
}
