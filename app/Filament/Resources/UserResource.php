<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Company;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Administracja';
    protected static ?string $navigationLabel = 'Użytkownicy';
    protected static ?string $modelLabel = 'Użytkownik';
    protected static ?string $pluralModelLabel = 'Użytkownicy';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Dane osobowe')->schema([
                Forms\Components\TextInput::make('first_name')->label('Imię')->maxLength(100),
                Forms\Components\TextInput::make('last_name')->label('Nazwisko')->maxLength(100),
                Forms\Components\TextInput::make('name')->label('Nazwa wyświetlana')->required()->maxLength(150),
                Forms\Components\TextInput::make('short_name')->label('Skrót')->maxLength(20),
            ])->columns(2),

            Forms\Components\Section::make('Kontakt i rola')->schema([
                Forms\Components\TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('phone')->label('Telefon')->maxLength(20),
                Forms\Components\TextInput::make('position')->label('Stanowisko')->maxLength(100),
                Forms\Components\Select::make('role')
                    ->label('Rola')
                    ->options(collect(UserRole::cases())->mapWithKeys(fn ($r) => [$r->value => $r->label()]))
                    ->required(),
                Forms\Components\Toggle::make('also_auditor')->label('Również audytor'),
                Forms\Components\Select::make('company_id')
                    ->label('Firma klienta')
                    ->options(fn () => Company::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
            ])->columns(2),

            Forms\Components\Section::make('Hasło')->schema([
                Forms\Components\TextInput::make('password')
                    ->label('Nowe hasło')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->nullable()
                    ->helperText('Zostaw puste, jeśli nie chcesz zmieniać hasła.'),
            ])->columns(1)->visibleOn('edit'),

            Forms\Components\Section::make('Hasło')->schema([
                Forms\Components\TextInput::make('password')
                    ->label('Hasło')
                    ->password()
                    ->required()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
            ])->columns(1)->visibleOn('create'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nazwa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('role')
                    ->label('Rola')
                    ->formatStateUsing(fn ($state) => $state instanceof UserRole ? $state->label() : $state)
                    ->colors([
                        'danger'  => fn ($state) => ($state instanceof UserRole ? $state->value : $state) === 'super_admin',
                        'warning' => fn ($state) => ($state instanceof UserRole ? $state->value : $state) === 'admin',
                        'success' => fn ($state) => ($state instanceof UserRole ? $state->value : $state) === 'auditor',
                        'gray'    => fn ($state) => ($state instanceof UserRole ? $state->value : $state) === 'client',
                    ]),
                Tables\Columns\TextColumn::make('phone')->label('Telefon')->toggleable(),
                Tables\Columns\TextColumn::make('position')->label('Stanowisko')->toggleable(),
                Tables\Columns\TextColumn::make('company.name')->label('Firma')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->label('Data rejestracji')->date('d.m.Y')->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rola')
                    ->options(collect(UserRole::cases())->mapWithKeys(fn ($r) => [$r->value => $r->label()])),
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
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
