<?php

namespace App\Filament\Widgets;

use App\Models\EnergyAudit;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingAuditsWidget extends BaseWidget
{
    protected static ?string $heading = 'Audyty oczekujące na zatwierdzenie';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return EnergyAudit::where('status', 'oczekujący')->exists();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                EnergyAudit::query()
                    ->where('status', 'oczekujący')
                    ->with(['company', 'auditor'])
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Nazwa audytu')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Firma')
                    ->placeholder('—')
                    ->url(fn (EnergyAudit $record) => $record->company
                        ? route('firma.show', $record->company)
                        : null)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('audit_type')
                    ->label('Typ')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('auditor.name')
                    ->label('Audytor')
                    ->placeholder('Nie przydzielono')
                    ->color(fn ($state) => $state ? null : 'warning'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => EnergyAudit::STATUSES[$state] ?? $state)
                    ->colors([
                        'warning' => 'oczekujący',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Utworzony')
                    ->date('d.m.Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('go_to_company')
                    ->label('Firma')
                    ->icon('heroicon-o-building-office')
                    ->url(fn (EnergyAudit $record) => $record->company
                        ? route('firma.show', $record->company)
                        : null)
                    ->openUrlInNewTab()
                    ->visible(fn (EnergyAudit $record) => (bool) $record->company_id),
            ])
            ->emptyStateHeading('Brak oczekujących audytów')
            ->emptyStateDescription('Wszystkie audyty zostały zatwierdzone.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->paginated(false);
    }
}
