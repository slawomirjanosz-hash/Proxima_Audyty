<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CrmTaskResource\Pages;
use App\Models\CrmCompany;
use App\Models\CrmDeal;
use App\Models\CrmTask;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CrmTaskResource extends Resource
{
    protected static ?string $model = CrmTask::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?string $navigationLabel = 'Zadania';
    protected static ?string $modelLabel = 'Zadanie';
    protected static ?string $pluralModelLabel = 'Zadania';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Zadanie')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Tytuł')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                Forms\Components\Select::make('type')
                    ->label('Typ')
                    ->options([
                        'call'    => 'Telefon',
                        'email'   => 'Email',
                        'meeting' => 'Spotkanie',
                        'task'    => 'Zadanie',
                        'other'   => 'Inne',
                    ])
                    ->default('task'),
                Forms\Components\Select::make('priority')
                    ->label('Priorytet')
                    ->options([
                        'low'    => 'Niski',
                        'medium' => 'Średni',
                        'high'   => 'Wysoki',
                    ])
                    ->default('medium'),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'nowe'        => 'Nowe',
                        'w_toku'      => 'W toku',
                        'oczekuje'    => 'Oczekuje',
                        'zakonczone'  => 'Zakończone',
                        'anulowane'   => 'Anulowane',
                    ])
                    ->default('nowe'),
                Forms\Components\DateTimePicker::make('due_date')
                    ->label('Termin')
                    ->displayFormat('d.m.Y H:i'),
                Forms\Components\Textarea::make('description')
                    ->label('Opis')
                    ->rows(3)
                    ->columnSpan(2),
            ])->columns(2),

            Forms\Components\Section::make('Przypisanie')->schema([
                Forms\Components\Select::make('assigned_to')
                    ->label('Przypisane do')
                    ->options(fn () => User::whereIn('role', ['super_admin', 'admin', 'auditor'])->pluck('name', 'id'))
                    ->searchable(),
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
                Forms\Components\Toggle::make('notify_on_complete')
                    ->label('Powiadom po zakończeniu')
                    ->default(false),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Tytuł')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Typ')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'call' => 'Telefon', 'email' => 'Email',
                        'meeting' => 'Spotkanie', 'task' => 'Zadanie',
                        default => $state,
                    }),
                Tables\Columns\BadgeColumn::make('priority')
                    ->label('Priorytet')
                    ->colors([
                        'secondary' => 'low',
                        'warning'   => 'medium',
                        'danger'    => 'high',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'low' => 'Niski', 'medium' => 'Średni', 'high' => 'Wysoki',
                        default => $state,
                    }),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'secondary' => 'nowe',
                        'warning'   => 'w_toku',
                        'primary'   => 'oczekuje',
                        'success'   => 'zakonczone',
                        'danger'    => 'anulowane',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'nowe' => 'Nowe', 'w_toku' => 'W toku',
                        'oczekuje' => 'Oczekuje', 'zakonczone' => 'Zakończone',
                        'anulowane' => 'Anulowane', default => $state,
                    }),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Termin')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->color(fn ($record) => $record->due_date && $record->due_date->isPast() && $record->status !== 'zakonczone' ? 'danger' : null),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Firma')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Przypisane do')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'nowe' => 'Nowe', 'w_toku' => 'W toku',
                        'oczekuje' => 'Oczekuje', 'zakonczone' => 'Zakończone',
                        'anulowane' => 'Anulowane',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Priorytet')
                    ->options(['low' => 'Niski', 'medium' => 'Średni', 'high' => 'Wysoki']),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('Przypisane do')
                    ->options(fn () => User::whereIn('role', ['super_admin', 'admin', 'auditor'])->pluck('name', 'id')),
                Tables\Filters\Filter::make('overdue')
                    ->label('Przeterminowane')
                    ->query(fn ($query) => $query->where('due_date', '<', now())->where('status', '!=', 'zakonczone')),
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
            ->defaultSort('due_date', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCrmTasks::route('/'),
            'create' => Pages\CreateCrmTask::route('/create'),
            'edit'   => Pages\EditCrmTask::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $overdue = static::getModel()::where('due_date', '<', now())
            ->where('status', '!=', 'zakonczone')
            ->count();
        return $overdue > 0 ? (string) $overdue : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
