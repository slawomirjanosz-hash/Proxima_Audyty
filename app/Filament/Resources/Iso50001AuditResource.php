<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Iso50001AuditResource\Pages;
use App\Models\Company;
use App\Models\Iso50001Audit;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class Iso50001AuditResource extends Resource
{
    protected static ?string $model = Iso50001Audit::class;
    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationGroup = 'Audyty';
    protected static ?string $navigationLabel = 'ISO 50001';
    protected static ?string $modelLabel = 'Audyt ISO 50001';
    protected static ?string $pluralModelLabel = 'Audyty ISO 50001';
    protected static ?int $navigationSort = 31;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Podstawowe dane')->schema([
                Forms\Components\TextInput::make('title')->label('Tytuł')->required()->maxLength(255),
                Forms\Components\Select::make('company_id')
                    ->label('Firma')
                    ->options(fn () => Company::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('reviewer_id')
                    ->label('Recenzent (audytor)')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(Iso50001Audit::statusLabels())
                    ->required(),
                Forms\Components\DatePicker::make('due_date')
                    ->label('Termin')
                    ->displayFormat('d.m.Y')
                    ->nullable(),
                Forms\Components\TextInput::make('current_step')
                    ->label('Krok')
                    ->numeric()
                    ->nullable(),
            ])->columns(2),

            Forms\Components\Section::make('Uwagi recenzenta')->schema([
                Forms\Components\Textarea::make('reviewer_notes')
                    ->label('Uwagi')
                    ->rows(4)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Tytuł')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('company.name')->label('Firma')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('creator.name')->label('Autor')->toggleable(),
                Tables\Columns\TextColumn::make('reviewer.name')->label('Recenzent')->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => Iso50001Audit::statusLabels()[$state] ?? $state)
                    ->colors([
                        'gray'    => 'draft',
                        'info'    => 'in_progress',
                        'warning' => ['submitted', 'in_review', 'changes_required'],
                        'success' => 'approved',
                    ]),
                Tables\Columns\TextColumn::make('current_step')->label('Krok')->alignCenter()->toggleable(),
                Tables\Columns\TextColumn::make('due_date')->label('Termin')->date('d.m.Y')->sortable(),
                Tables\Columns\TextColumn::make('submitted_at')->label('Przesłany')->date('d.m.Y')->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(Iso50001Audit::statusLabels()),
                Tables\Filters\SelectFilter::make('reviewer_id')
                    ->label('Recenzent')
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
        $count = static::getModel()::whereIn('status', ['submitted', 'in_review'])->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListIso50001Audits::route('/'),
            'create' => Pages\CreateIso50001Audit::route('/create'),
            'edit'   => Pages\EditIso50001Audit::route('/{record}/edit'),
        ];
    }
}
