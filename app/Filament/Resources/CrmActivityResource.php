<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CrmActivityResource\Pages;
use App\Models\CrmActivity;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CrmActivityResource extends Resource
{
    protected static ?string $model = CrmActivity::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?string $navigationLabel = 'Aktywności';
    protected static ?string $modelLabel = 'Aktywność';
    protected static ?string $pluralModelLabel = 'Aktywności';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('type')
                ->label('Typ')
                ->options([
                    'call'    => 'Telefon',
                    'email'   => 'Email',
                    'meeting' => 'Spotkanie',
                    'note'    => 'Notatka',
                    'other'   => 'Inne',
                ])
                ->required(),
            Forms\Components\TextInput::make('subject')
                ->label('Temat')
                ->required()
                ->maxLength(255),
            Forms\Components\DateTimePicker::make('activity_date')
                ->label('Data')
                ->displayFormat('d.m.Y H:i')
                ->required(),
            Forms\Components\TextInput::make('duration')
                ->label('Czas trwania (min)')
                ->numeric(),
            Forms\Components\Select::make('company_id')
                ->label('Firma')
                ->relationship('company', 'name')
                ->searchable()
                ->preload(),
            Forms\Components\Select::make('deal_id')
                ->label('Szansa sprzedaży')
                ->relationship('deal', 'name')
                ->searchable()
                ->preload(),
            Forms\Components\Select::make('user_id')
                ->label('Użytkownik')
                ->options(fn () => User::pluck('name', 'id'))
                ->searchable(),
            Forms\Components\Textarea::make('description')
                ->label('Opis')
                ->rows(3)
                ->columnSpanFull(),
            Forms\Components\Textarea::make('outcome')
                ->label('Wynik / rezultat')
                ->rows(2)
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Typ')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'call' => 'Telefon', 'email' => 'Email',
                        'meeting' => 'Spotkanie', 'note' => 'Notatka',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Temat')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Firma')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deal.name')
                    ->label('Szansa')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Użytkownik')
                    ->sortable(),
                Tables\Columns\TextColumn::make('activity_date')
                    ->label('Data')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Czas (min)')
                    ->alignCenter()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Typ')
                    ->options([
                        'call' => 'Telefon', 'email' => 'Email',
                        'meeting' => 'Spotkanie', 'note' => 'Notatka',
                    ]),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Użytkownik')
                    ->options(fn () => User::pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('activity_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCrmActivities::route('/'),
            'create' => Pages\CreateCrmActivity::route('/create'),
            'edit'   => Pages\EditCrmActivity::route('/{record}/edit'),
        ];
    }
}
