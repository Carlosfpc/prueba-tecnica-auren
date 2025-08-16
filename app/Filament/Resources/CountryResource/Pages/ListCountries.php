<?php

namespace App\Filament\Resources\CountryResource\Pages;

use App\Actions\SyncCountriesAction;
use App\Filament\Resources\CountryResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use App\Jobs\SyncCountriesJob;

class ListCountries extends ListRecords
{
    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('sync')
                ->label('Sync from API')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->action(function () {
                    SyncCountriesJob::dispatch();
                    Notification::make()
                        ->title('Synchronization Started')
                        ->body('The country data is being synchronized in the background. The table will update automatically upon completion.')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getTablePollingInterval(): ?string
    {
        return '5s';
    }
}
