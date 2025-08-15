<?php

namespace App\Filament\Resources\CountryResource\Pages;

use App\Actions\SyncCountriesAction;
use App\Filament\Resources\CountryResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListCountries extends ListRecords
{
    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // New Country button.
            Actions\CreateAction::make(),

            // Custom "Sync from API" action.
            Actions\Action::make('sync')
                ->label('Sync from API')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    $summary = app(SyncCountriesAction::class)->execute();

                    Notification::make()
                        ->title('Synchronization Complete')
                        ->body(sprintf(
                            'Fetched: %d countries. Upserted: %d records. Status: %s',
                            $summary['total_fetched'],
                            $summary['upserted'],
                            $summary['status']
                        ))
                        ->success()
                        ->send();
                }),
        ];
    }
}
