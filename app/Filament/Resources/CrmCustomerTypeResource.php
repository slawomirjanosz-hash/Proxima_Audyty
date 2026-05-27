<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CrmCustomerTypeResource\Pages;
use App\Models\CrmCustomerType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CrmCustomerTypeResource extends Resource
{
    protected static ?string $model = CrmCustomerType::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'CRM – Ustawienia';
    protected static ?string $navigationLabel = 'Typy klientów';
    protected static ?string $modelLabel = 'Typ klienta';
    protected static ?string $pluralModelLabel = 'Typy klientów';
    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nazwa')
                ->required()
                ->maxLength(100),
            Forms\Components\TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(50)
                ->unique(ignoreRecord: true),
            Forms\Components\ColorPicker::make('color')
                ->label('Kolor'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('color')
                    ->label('Kolor'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nazwa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug'),
                Tables\Columns\TextColumn::make('companies_count')
                    ->label('Firm')
                    ->counts('companies')
                    ->alignCenter(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCrmCustomerTypes::route('/'),
            'create' => Pages\CreateCrmCustomerType::route('/create'),
            'edit'   => Pages\EditCrmCustomerType::route('/{record}/edit'),
        ];
    }
}
