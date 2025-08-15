<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Filament\Resources\CountryResource\RelationManagers;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('Identity & Location')
                ->description('Basic identification and geographical information.')
                ->schema([
                    Forms\Components\TextInput::make('cca3')
                        ->label('Country Code (CCA3)')
                        ->required()
                        ->length(3)
                        ->unique(ignoreRecord: true),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('name_common')
                                ->label('Common Name')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('name_official')
                                ->label('Official Name')
                                ->required()
                                ->maxLength(255),
                        ]),


                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('region')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('subregion')
                                ->maxLength(255),
                        ]),

                    Forms\Components\TextInput::make('capital')
                        ->maxLength(255),
                ])->columns(2),

            Forms\Components\Section::make('Demographics & Flags')
                ->schema([
                    Forms\Components\TextInput::make('population')
                        ->required()
                        ->numeric()
                        ->integer(),

                    Forms\Components\TextInput::make('area')
                        ->required()
                        ->numeric()
                        ->suffix(' km²'),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('flag_emoji')
                                ->label('Flag (Emoji)')
                                ->maxLength(16),
                            Forms\Components\TextInput::make('flag_png')
                                ->label('Flag (PNG URL)')
                                ->maxLength(255)
                                ->url(),
                        ]),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('flag_emoji')
                ->label('Flag'),
            Tables\Columns\TextColumn::make('cca3')
                ->label('Code')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('name_common')
                ->label('Common Name')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('region')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('population')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('name_official')
                ->label('Official Name')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('area')
                ->numeric()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            SelectFilter::make('region')
                ->options(
                    fn () => Country::query()->distinct()->pluck('region', 'region')->all()
                ),
            Filter::make('population')
                ->form([
                    Forms\Components\TextInput::make('population_from')
                        ->label('Population from')
                        ->numeric(),
                    Forms\Components\TextInput::make('population_to')
                        ->label('Population to')
                        ->numeric(),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['population_from'],
                            fn (Builder $query, $value): Builder => $query->where('population', '>=', $value)
                        )
                        ->when(
                            $data['population_to'],
                            fn (Builder $query, $value): Builder => $query->where('population', '<=', $value)
                        );
                })
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
