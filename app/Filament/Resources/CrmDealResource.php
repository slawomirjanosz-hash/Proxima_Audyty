<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CrmDealResource\Pages;
use App\Models\CrmCompany;
use App\Models\CrmDeal;
use App\Models\CrmStage;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CrmDealResource extends Resource
{
    protected static ?string $model = CrmDeal::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?string $navigationLabel = 'Szanse sprzedaży';
    protected static ?string $modelLabel = 'Szansa sprzedaży';
    protected static ?string $pluralModelLabel = 'Szanse sprzedaży';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Podstawowe informacje')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nazwa szansy')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                Forms\Components\Select::make('company_id')
                    ->label('Firma')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('stage')
                    ->label('Etap')
                    ->options(fn () => CrmStage::orderBy('order')->pluck('name', 'slug'))
                    ->required(),
                Forms\Components\TextInput::make('value')
                    ->label('Wartość (PLN)')
                    ->numeric()
                    ->prefix('PLN'),
                Forms\Components\TextInput::make('probability')
                    ->label('Prawdopodobieństwo (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%')
                    ->default(50),
                Forms\Components\DatePicker::make('expected_close_date')
                    ->label('Planowane zamknięcie')
                    ->displayFormat('d.m.Y'),
                Forms\Components\DatePicker::make('actual_close_date')
                    ->label('Faktyczne zamknięcie')
                    ->displayFormat('d.m.Y'),
            ])->columns(2),

            Forms\Components\Section::make('Przypisanie')->schema([
                Forms\Components\Select::make('owner_id')
                    ->label('Właściciel')
                    ->options(fn () => User::whereIn('role', ['super_admin', 'admin', 'auditor'])->pluck('name', 'id'))
                    ->searchable(),
                Forms\Components\Select::make('user_id')
                    ->label('Handlowiec')
                    ->options(fn () => User::whereIn('role', ['super_admin', 'admin', 'auditor'])->pluck('name', 'id'))
                    ->searchable(),
            ])->columns(2),

            Forms\Components\Section::make('Szczegóły')->schema([
                Forms\Components\Textarea::make('description')
                    ->label('Opis')
                    ->rows(3)
                    ->columnSpan(2),
                Forms\Components\Textarea::make('lost_reason')
                    ->label('Powód przegranej')
                    ->rows(2)
                    ->columnSpan(2),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nazwa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Firma')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Wartość')
                    ->money('PLN')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('stage')
                    ->label('Etap')
                    ->formatStateUsing(fn ($state) => CrmStage::where('slug', $state)->value('name') ?? $state),
                Tables\Columns\TextColumn::make('probability')
                    ->label('%')
                    ->suffix('%')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('expected_close_date')
                    ->label('Planowane zamknięcie')
                    ->date('d.m.Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Właściciel')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dodano')
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('stage')
                    ->label('Etap')
                    ->options(fn () => CrmStage::orderBy('order')->pluck('name', 'slug')),
                Tables\Filters\SelectFilter::make('owner_id')
                    ->label('Właściciel')
                    ->options(fn () => User::whereIn('role', ['super_admin', 'admin', 'auditor'])->pluck('name', 'id')),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCrmDeals::route('/'),
            'create' => Pages\CreateCrmDeal::route('/create'),
            'edit'   => Pages\EditCrmDeal::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }
}
