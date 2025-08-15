<?php

namespace Tests\Unit\Actions;

use App\Actions\SyncCountriesAction;
use App\Models\Country;
use App\Services\RestCountriesService;
use Illuminate\Support\Collection;
use Mockery; // Importante: Asegúrate de que esta línea está
use Tests\TestCase;

class SyncCountriesActionTest extends TestCase
{
    /**
     * Test that the action correctly fetches, formats, and upserts country data.
     *
     * @return void
     */
    public function test_it_correctly_synchronizes_countries(): void
    {
        // Fake API data.
        $fakeApiData = new Collection([
            [
                'cca3' => 'ESP',
                'name' => ['common' => 'Spain', 'official' => 'Kingdom of Spain'],
                'region' => 'Europe',
                'subregion' => 'Southern Europe',
                'capital' => ['Madrid'],
                'population' => 47351567,
                'area' => 505992,
                'flags' => ['emoji' => '🇪🇸', 'png' => 'https://...'],
            ]
        ]);


        $this->mock(RestCountriesService::class)
             ->shouldReceive('fetchAllCountries')
             ->once()
             ->andReturn($fakeApiData);

        $countryMock = Mockery::mock('alias:' . Country::class);
        $countryMock->shouldReceive('upsert')
                    ->once()
                    ->withArgs(function ($formattedData) {
                        $country = $formattedData[0];
                        return $country['cca3'] === 'ESP' && $country['capital'] === 'Madrid';
                    })
                    ->andReturn(1);


        $action = $this->app->make(SyncCountriesAction::class);
        $result = $action->execute();


        $this->assertEquals(1, $result['total_fetched']);
        $this->assertEquals(1, $result['upserted']);
        $this->assertEquals('Synchronization completed successfully.', $result['status']);
    }
}
