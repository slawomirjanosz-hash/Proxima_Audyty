<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrationResource\Pages;
use App\Models\ClientRegistration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RegistrationResource extends Resource
{
    protected static ?string $model = ClientRegistration::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'Administracja';
    protected static ?string $navigationLabel = 'Rejestracje';
    protected static ?string $modelLabel = 'Rejestracja';
    protected static ?string $pluralModelLabel = 'Rejestracje';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Firma')->schema([
                Forms\Components\TextInput::make('name')->label('Nazwa firmy')->required(),
                Forms\Components\TextInput::make('short_name')->label('Nazwa skrócona'),
                Forms\Components\TextInput::make('nip')->label('NIP'),
                Forms\Components\TextInput::make('city')->label('Miasto'),
                Forms\Components\TextInput::make('street')->label('Ulica'),
                Forms\Components\TextInput::make('postal_code')->label('Kod pocztowy'),
            ])->columns(2),
            Forms\Components\Section::make('Osoba kontaktowa')->schema([
                Forms\Components\TextInput::make('first_name')->label('Imię'),
                Forms\Components\TextInput::make('last_name')->label('Nazwisko'),
                Forms\Components\TextInput::make('email')->label('Email')->email(),
                Forms\Components\TextInput::make('phone')->label('Telefon'),
            ])->columns(2),
            Forms\Components\Section::make('Status')->schema([
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Oczekująca',
                        'accepted' => 'Zaakceptowana',
                        'rejected' => 'Odrzucona',
                    ])
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Firma')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nip')->label('NIP')->searchable(),
                Tables\Columns\TextColumn::make('city')->label('Miasto'),
                Tables\Columns\TextColumn::make('first_name')->label('Imię')->toggleable(),
                Tables\Columns\TextColumn::make('last_name')->label('Nazwisko')->toggleable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('Telefon')->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending'  => 'Oczekująca',
                        'accepted' => 'Zaakceptowana',
                        'rejected' => 'Odrzucona',
                        default    => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'accepted',
                        'danger'  => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->label('Data zgłoszenia')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Oczekująca',
                        'accepted' => 'Zaakceptowana',
                        'rejected' => 'Odrzucona',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('accept')
                    ->label('Akceptuj')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->update(['status' => 'accepted'])),
                Tables\Actions\Action::make('reject')
                    ->label('Odrzuć')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->update(['status' => 'rejected'])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRegistrations::route('/'),
            'edit'   => Pages\EditRegistration::route('/{record}/edit'),
        ];
    }
}
