<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferTemplateResource\Pages;
use App\Models\OfferTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OfferTemplateResource extends Resource
{
    protected static ?string $model = OfferTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationGroup = 'Oferty';
    protected static ?string $navigationLabel = 'Szablony ofert';
    protected static ?string $modelLabel = 'Szablon oferty';
    protected static ?string $pluralModelLabel = 'Szablony ofert';
    protected static ?int $navigationSort = 21;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Podstawowe informacje')->schema([
                Forms\Components\TextInput::make('name')->label('Nazwa szablonu')->required()->maxLength(255),
                Forms\Components\TextInput::make('type_code')->label('Kod typu')->maxLength(50),
                Forms\Components\Toggle::make('is_active')->label('Aktywny')->default(true),
                Forms\Components\Textarea::make('description')->label('Opis')->rows(2)->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Domyślne stawki')->schema([
                Forms\Components\TextInput::make('default_km_rate')->label('Stawka km (PLN)')->numeric(),
                Forms\Components\TextInput::make('default_hour_rate')->label('Stawka godz. (PLN)')->numeric(),
                Forms\Components\TextInput::make('default_auditor_hours')->label('Domyślne godz. audytora')->numeric(),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nazwa')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type_code')->label('Kod typu')->badge()->toggleable(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktywny')->boolean(),
                Tables\Columns\TextColumn::make('default_km_rate')->label('Stawka km')->money('PLN')->toggleable(),
                Tables\Columns\TextColumn::make('default_hour_rate')->label('Stawka godz.')->money('PLN')->toggleable(),
                Tables\Columns\TextColumn::make('offers_count')->label('Ofert')->counts('offers')->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')->label('Dodany')->date('d.m.Y')->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Aktywny'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOfferTemplates::route('/'),
            'create' => Pages\CreateOfferTemplate::route('/create'),
            'edit'   => Pages\EditOfferTemplate::route('/{record}/edit'),
        ];
    }
}
