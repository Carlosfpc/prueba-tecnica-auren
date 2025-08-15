<?php

namespace App\Filament\Widgets;

use App\Models\Country;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CountryStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCountries = Stat::make('Total Countries', Country::count())
            ->description('The total number of countries synced in the database.')
            ->descriptionIcon('heroicon-m-globe-alt')
            ->color('success');

        $totalPopulation = Stat::make('Total World Population', number_format(Country::sum('population')))
            ->description('Sum of all country populations.')
            ->descriptionIcon('heroicon-m-users')
            ->color('info');

        $mostPopulousRegionData = Country::query()
            ->selectRaw('region, count(*) as count')
            ->groupBy('region')
            ->orderByDesc('count')
            ->first();

        if ($mostPopulousRegionData) {
            $mostPopulousRegion = Stat::make('Top Region', $mostPopulousRegionData->region)
                ->description(sprintf('%d countries', $mostPopulousRegionData->count))
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('warning');
        }

        return array_filter([
            $totalCountries,
            $totalPopulation,
            $mostPopulousRegion ?? null,
        ]);
    }
}
