<?php

namespace App\Filament\Widgets;

use App\Models\CrmTask;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OverdueTasksWidget extends BaseWidget
{
    protected static ?string $heading = 'Zadania CRM po terminie';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return CrmTask::where('status', '!=', 'zakonczone')
            ->where('status', '!=', 'anulowane')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->exists();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CrmTask::query()
                    ->where('status', '!=', 'zakonczone')
                    ->where('status', '!=', 'anulowane')
                    ->whereNotNull('due_date')
                    ->where('due_date', '<', now())
                    ->with(['assignedTo', 'company'])
                    ->orderBy('due_date', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Zadanie')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Firma')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Przydzielone do')
                    ->placeholder('Brak'),
                Tables\Columns\BadgeColumn::make('priority')
                    ->label('Priorytet')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'wysoki', 'high'   => 'Wysoki',
                        'sredni', 'medium' => 'Średni',
                        'niski', 'low'     => 'Niski',
                        default            => ucfirst((string) $state),
                    })
                    ->colors([
                        'danger'  => fn ($state) => in_array($state, ['wysoki', 'high']),
                        'warning' => fn ($state) => in_array($state, ['sredni', 'medium']),
                        'gray'    => fn ($state) => in_array($state, ['niski', 'low']),
                    ]),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Termin')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->emptyStateHeading('Brak przeterminowanych zadań')
            ->emptyStateDescription('Wszystkie zadania są na bieżąco.')
            ->emptyStateIcon('heroicon-o-check-badge')
            ->paginated(false);
    }
}
