<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnergyAuditResource\Pages;
use App\Models\AuditType;
use App\Models\Company;
use App\Models\EnergyAudit;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EnergyAuditResource extends Resource
{
    protected static ?string $model = EnergyAudit::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Audyty';
    protected static ?string $navigationLabel = 'Audyty energetyczne';
    protected static ?string $modelLabel = 'Audyt energetyczny';
    protected static ?string $pluralModelLabel = 'Audyty energetyczne';
    protected static ?int $navigationSort = 30;

    private static function statusColors(): array
    {
        return [
            'gray'    => ['new', 'oczekujący'],
            'info'    => ['in_progress', 'wysłany', 'rozpoczęty'],
            'warning' => ['do_analizy', 'zwrócony_do_poprawy'],
            'success' => ['zaakceptowany', 'zakończony', 'completed', 'zafakturowany', 'zapłacony'],
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Podstawowe dane')->schema([
                Forms\Components\TextInput::make('title')->label('Tytuł audytu')->required()->maxLength(255),
                Forms\Components\Select::make('company_id')
                    ->label('Firma')
                    ->options(fn () => Company::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('auditor_id')
                    ->label('Audytor')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Forms\Components\Select::make('audit_type_id')
                    ->label('Typ audytu')
                    ->options(fn () => AuditType::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(EnergyAudit::STATUSES)
                    ->required(),
                Forms\Components\DateTimePicker::make('completed_at')
                    ->label('Data zakończenia')
                    ->displayFormat('d.m.Y H:i')
                    ->nullable(),
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Firma')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('auditor.name')
                    ->label('Audytor')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => EnergyAudit::STATUSES[$state] ?? $state)
                    ->colors(self::statusColors()),
                Tables\Columns\IconColumn::make('questionnaire_completed')
                    ->label('Kwestionariusz')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Utworzony')
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Zakończony')
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(EnergyAudit::STATUSES),
                Tables\Filters\SelectFilter::make('auditor_id')
                    ->label('Audytor')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('company_id')
                    ->label('Firma')
                    ->options(fn () => Company::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::whereIn('status', EnergyAudit::ACTIVE_STATUSES)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEnergyAudits::route('/'),
            'create' => Pages\CreateEnergyAudit::route('/create'),
            'edit'   => Pages\EditEnergyAudit::route('/{record}/edit'),
        ];
    }
}
