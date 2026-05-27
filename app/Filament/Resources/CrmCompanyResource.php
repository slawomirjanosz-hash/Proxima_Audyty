<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CrmCompanyResource\Pages;
use App\Models\CrmCompany;
use App\Models\CrmCustomerType;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CrmCompanyResource extends Resource
{
    protected static ?string $model = CrmCompany::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?string $navigationLabel = 'Firmy';
    protected static ?string $modelLabel = 'Firma';
    protected static ?string $pluralModelLabel = 'Firmy';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Dane podstawowe')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nazwa firmy')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                Forms\Components\TextInput::make('short_name')
                    ->label('Nazwa skrócona')
                    ->maxLength(100),
                Forms\Components\TextInput::make('nip')
                    ->label('NIP')
                    ->maxLength(20),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Telefon')
                    ->tel()
                    ->maxLength(30),
                Forms\Components\TextInput::make('website')
                    ->label('Strona WWW')
                    ->url()
                    ->maxLength(255),
            ])->columns(2),

            Forms\Components\Section::make('Adres')->schema([
                Forms\Components\TextInput::make('address')
                    ->label('Ulica i nr')
                    ->maxLength(255)
                    ->columnSpan(2),
                Forms\Components\TextInput::make('city')
                    ->label('Miasto')
                    ->maxLength(100),
                Forms\Components\TextInput::make('postal_code')
                    ->label('Kod pocztowy')
                    ->maxLength(10),
                Forms\Components\TextInput::make('country')
                    ->label('Kraj')
                    ->default('PL')
                    ->maxLength(10),
            ])->columns(2),

            Forms\Components\Section::make('Klasyfikacja')->schema([
                Forms\Components\Select::make('customer_type_id')
                    ->label('Typ klienta')
                    ->relationship('customerType', 'name')
                    ->preload()
                    ->searchable(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'lead'     => 'Lead',
                        'prospect' => 'Prospekt',
                        'active'   => 'Aktywny',
                        'inactive' => 'Nieaktywny',
                    ])
                    ->default('lead'),
                Forms\Components\Select::make('source')
                    ->label('Źródło')
                    ->options([
                        'manual'    => 'Ręczne dodanie',
                        'website'   => 'Strona WWW',
                        'referral'  => 'Polecenie',
                        'cold_call' => 'Cold call',
                        'other'     => 'Inne',
                    ]),
                Forms\Components\Select::make('owner_id')
                    ->label('Opiekun')
                    ->options(fn () => User::whereIn('role', ['super_admin', 'admin', 'auditor'])->pluck('name', 'id'))
                    ->searchable(),
                Forms\Components\Textarea::make('notes')
                    ->label('Notatki')
                    ->rows(3)
                    ->columnSpan(2),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nazwa firmy')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Miasto')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'secondary' => 'lead',
                        'warning'   => 'prospect',
                        'success'   => 'active',
                        'danger'    => 'inactive',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'lead' => 'Lead', 'prospect' => 'Prospekt',
                        'active' => 'Aktywny', 'inactive' => 'Nieaktywny',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('customerType.name')
                    ->label('Typ klienta')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Opiekun')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('deals_count')
                    ->label('Deale')
                    ->counts('deals')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dodano')
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'lead' => 'Lead', 'prospect' => 'Prospekt',
                        'active' => 'Aktywny', 'inactive' => 'Nieaktywny',
                    ]),
                Tables\Filters\SelectFilter::make('customer_type_id')
                    ->label('Typ klienta')
                    ->relationship('customerType', 'name'),
                Tables\Filters\SelectFilter::make('owner_id')
                    ->label('Opiekun')
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
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCrmCompanies::route('/'),
            'create' => Pages\CreateCrmCompany::route('/create'),
            'edit'   => Pages\EditCrmCompany::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }
}
