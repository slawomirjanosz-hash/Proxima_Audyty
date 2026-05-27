<?php

namespace App\Filament\Widgets;

use App\Models\Company;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentCompaniesWidget extends BaseWidget
{
    protected static ?string $heading = 'Ostatnie firmy klientów';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Company::query()
                    ->withCount([
                        'energyAudits',
                        'energyAudits as active_audits_count' => fn ($q) => $q->whereIn('status', \App\Models\EnergyAudit::ACTIVE_STATUSES),
                    ])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Firma')
                    ->searchable()
                    ->weight('bold')
                    ->url(fn (Company $record) => route('firma.show', $record))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Miasto')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('auditor.name')
                    ->label('Audytor')
                    ->placeholder('Brak'),
                Tables\Columns\TextColumn::make('active_audits_count')
                    ->label('Aktywne audyty')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('energy_audits_count')
                    ->label('Wszystkie audyty')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dodana')
                    ->date('d.m.Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Otwórz')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Company $record) => route('firma.show', $record))
                    ->openUrlInNewTab(),
            ])
            ->paginated(false);
    }
}
