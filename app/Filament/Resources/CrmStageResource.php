<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CrmStageResource\Pages;
use App\Models\CrmStage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CrmStageResource extends Resource
{
    protected static ?string $model = CrmStage::class;
    protected static ?string $navigationIcon = 'heroicon-o-funnel';
    protected static ?string $navigationGroup = 'CRM – Ustawienia';
    protected static ?string $navigationLabel = 'Etapy pipeline';
    protected static ?string $modelLabel = 'Etap';
    protected static ?string $pluralModelLabel = 'Etapy pipeline';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nazwa')
                ->required()
                ->maxLength(100),
            Forms\Components\TextInput::make('slug')
                ->label('Slug (identyfikator)')
                ->required()
                ->maxLength(50)
                ->unique(ignoreRecord: true),
            Forms\Components\ColorPicker::make('color')
                ->label('Kolor'),
            Forms\Components\TextInput::make('order')
                ->label('Kolejność')
                ->numeric()
                ->default(0),
            Forms\Components\Toggle::make('is_active')
                ->label('Aktywny')
                ->default(true),
            Forms\Components\Toggle::make('is_closed')
                ->label('Etap zamknięty (wygrana/przegrana)')
                ->default(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\ColorColumn::make('color')
                    ->label('Kolor'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nazwa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktywny')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_closed')
                    ->label('Zamknięty')
                    ->boolean(),
            ])
            ->reorderable('order')
            ->defaultSort('order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCrmStages::route('/'),
            'create' => Pages\CreateCrmStage::route('/create'),
            'edit'   => Pages\EditCrmStage::route('/{record}/edit'),
        ];
    }
}
