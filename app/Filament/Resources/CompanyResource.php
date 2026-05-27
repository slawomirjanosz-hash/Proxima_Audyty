<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Administracja';
    protected static ?string $navigationLabel = 'Firmy klientów';
    protected static ?string $modelLabel = 'Firma';
    protected static ?string $pluralModelLabel = 'Firmy klientów';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Dane firmy')->schema([
                Forms\Components\TextInput::make('name')->label('Pełna nazwa')->required()->maxLength(255),
                Forms\Components\TextInput::make('short_name')->label('Nazwa skrócona')->maxLength(100),
                Forms\Components\TextInput::make('nip')->label('NIP')->maxLength(20),
                Forms\Components\TextInput::make('regon')->label('REGON')->maxLength(20),
                Forms\Components\TextInput::make('krs')->label('KRS')->maxLength(20),
            ])->columns(2),

            Forms\Components\Section::make('Adres i kontakt')->schema([
                Forms\Components\TextInput::make('city')->label('Miasto')->maxLength(100),
                Forms\Components\TextInput::make('street')->label('Ulica')->maxLength(150),
                Forms\Components\TextInput::make('postal_code')->label('Kod pocztowy')->maxLength(10),
                Forms\Components\TextInput::make('phone')->label('Telefon')->maxLength(20),
                Forms\Components\TextInput::make('email')->label('Email')->email()->maxLength(150),
            ])->columns(2),

            Forms\Components\Section::make('Przypisania')->schema([
                Forms\Components\Select::make('client_id')
                    ->label('Użytkownik-klient')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Forms\Components\Select::make('auditor_id')
                    ->label('Audytor prowadzący')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Forms\Components\Textarea::make('description')->label('Opis')->rows(2)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Firma')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nip')->label('NIP')->searchable(),
                Tables\Columns\TextColumn::make('city')->label('Miasto')->sortable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->toggleable(),
                Tables\Columns\TextColumn::make('auditor.name')->label('Audytor')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('client.name')->label('Klient (użytkownik)')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('energy_audits_count')
                    ->label('Audytów')
                    ->counts('energyAudits')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('offers_count')
                    ->label('Ofert')
                    ->counts('offers')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')->label('Dodana')->date('d.m.Y')->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('auditor_id')
                    ->label('Audytor')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit'   => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
