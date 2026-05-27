<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferResource\Pages;
use App\Models\Company;
use App\Models\Offer;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Oferty';
    protected static ?string $navigationLabel = 'Oferty';
    protected static ?string $modelLabel = 'Oferta';
    protected static ?string $pluralModelLabel = 'Oferty';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Dane oferty')->schema([
                Forms\Components\TextInput::make('offer_number')->label('Numer oferty')->maxLength(50),
                Forms\Components\TextInput::make('offer_title')->label('Tytuł')->required()->maxLength(255),
                Forms\Components\DatePicker::make('offer_date')->label('Data oferty')->displayFormat('d.m.Y'),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft'    => 'Szkic',
                        'sent'     => 'Wysłana',
                        'accepted' => 'Zaakceptowana',
                        'rejected' => 'Odrzucona',
                        'archived' => 'Zarchiwizowana',
                    ]),
                Forms\Components\Select::make('company_id')
                    ->label('Firma')
                    ->options(fn () => Company::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                Forms\Components\TextInput::make('total_price')->label('Wartość (PLN)')->numeric(),
                Forms\Components\TextInput::make('profit_percent')->label('Marża (%)')->numeric(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('offer_number')->label('Nr oferty')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('offer_title')->label('Tytuł')->searchable()->sortable()->limit(40),
                Tables\Columns\TextColumn::make('company.name')->label('Firma')->searchable()->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'draft'    => 'Szkic',
                        'sent'     => 'Wysłana',
                        'accepted' => 'Zaakceptowana',
                        'rejected' => 'Odrzucona',
                        'archived' => 'Zarchiwizowana',
                        default    => $state,
                    })
                    ->colors([
                        'gray'    => 'draft',
                        'info'    => 'sent',
                        'success' => 'accepted',
                        'danger'  => 'rejected',
                        'warning' => 'archived',
                    ]),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Wartość')
                    ->money('PLN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('offer_date')->label('Data')->date('d.m.Y')->sortable(),
                Tables\Columns\TextColumn::make('creator.name')->label('Autor')->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Szkic', 'sent' => 'Wysłana',
                        'accepted' => 'Zaakceptowana', 'rejected' => 'Odrzucona',
                        'archived' => 'Zarchiwizowana',
                    ]),
                Tables\Filters\SelectFilter::make('company_id')
                    ->label('Firma')
                    ->options(fn () => Company::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('created_by')
                    ->label('Autor')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'sent')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOffers::route('/'),
            'edit'   => Pages\EditOffer::route('/{record}/edit'),
        ];
    }
}
