<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferResource\Pages;
use App\Models\Company;
use App\Models\Offer;
use App\Models\SystemSetting;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;

class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Oferty';
    protected static ?string $navigationLabel = 'Oferty';
    protected static ?string $modelLabel = 'Oferta';
    protected static ?string $pluralModelLabel = 'Oferty';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Dane oferty')->schema([
                Forms\Components\TextInput::make('offer_number')->label('Numer oferty')->maxLength(50),
                Forms\Components\TextInput::make('offer_title')->label('Tytuł')->required()->maxLength(255),
                Forms\Components\DatePicker::make('offer_date')->label('Data oferty')->displayFormat('d.m.Y'),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft'    => 'Szkic',
                        'sent'     => 'Wysłana',
                        'accepted' => 'Zaakceptowana',
                        'rejected' => 'Odrzucona',
                        'archived' => 'Zarchiwizowana',
                    ]),
                Forms\Components\Select::make('company_id')
                    ->label('Firma')
                    ->options(fn () => Company::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->live(),
                Forms\Components\TextInput::make('total_price')->label('Wartość (PLN)')->numeric(),
                Forms\Components\TextInput::make('profit_percent')->label('Marża (%)')->numeric(),
            ])->columns(2),

            Forms\Components\Section::make('Koszty przejazdu')
                ->description('Szacowanie trasy do klienta. Użyj przycisku AI, żeby automatycznie obliczyć dystans i koszty.')
                ->schema([
                    Forms\Components\TextInput::make('distance_km')
                        ->label('Dystans (km)')
                        ->numeric()
                        ->suffix('km'),
                    Forms\Components\TextInput::make('travel_hours')
                        ->label('Czas przejazdu (h)')
                        ->numeric()
                        ->suffix('h'),
                    Forms\Components\TextInput::make('km_rate')
                        ->label('Stawka km (PLN)')
                        ->numeric()
                        ->suffix('PLN/km')
                        ->default(fn () => (float) SystemSetting::get('default_km_rate', '0.89')),
                    Forms\Components\TextInput::make('hour_rate')
                        ->label('Stawka godz. (PLN)')
                        ->numeric()
                        ->suffix('PLN/h')
                        ->default(fn () => (float) SystemSetting::get('default_hour_rate', '150')),
                    Forms\Components\TextInput::make('travel_cost')
                        ->label('Koszt przejazdu (PLN)')
                        ->numeric()
                        ->suffix('PLN')
                        ->disabled()
                        ->dehydrated(),
                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('estimate_travel_ai')
                            ->label('Oszacuj trasę AI')
                            ->icon('heroicon-o-sparkles')
                            ->color('success')
                            ->modalHeading('Oszacowanie trasy — AI')
                            ->modalDescription('AI obliczy przybliżony dystans i czas przejazdu na podstawie miast.')
                            ->form([
                                Forms\Components\TextInput::make('base_city')
                                    ->label('Twoje miasto startowe')
                                    ->required()
                                    ->default(fn () => SystemSetting::get('company_base_city', 'Wrocław'))
                                    ->placeholder('np. Wrocław'),
                                Forms\Components\TextInput::make('client_city')
                                    ->label('Miasto klienta')
                                    ->required()
                                    ->default(function (Get $get) {
                                        $companyId = $get('../../company_id');
                                        if ($companyId) {
                                            $company = Company::find($companyId);
                                            return $company?->city ?? '';
                                        }
                                        return '';
                                    })
                                    ->placeholder('np. Warszawa'),
                            ])
                            ->action(function (array $data, Get $get, Set $set) {
                                $baseCity   = trim($data['base_city']);
                                $clientCity = trim($data['client_city']);

                                if (empty($baseCity) || empty($clientCity)) {
                                    Notification::make()
                                        ->title('Brakuje miast')
                                        ->body('Podaj oba miasta żeby oszacować trasę.')
                                        ->warning()
                                        ->send();
                                    return;
                                }

                                try {
                                    $response = Prism::text()
                                        ->using(Provider::OpenAI, 'gpt-4o-mini')
                                        ->withSystemPrompt('Jesteś kalkulatorem tras drogowych w Polsce. Odpowiadasz TYLKO w formacie JSON, bez żadnych dodatkowych komentarzy.')
                                        ->withPrompt(
                                            "Oszacuj trasę samochodem z miasta \"{$baseCity}\" do miasta \"{$clientCity}\" w Polsce.\n" .
                                            "Podaj wynik jako JSON:\n" .
                                            "{\"distance_km\": <liczba całkowita>, \"travel_hours\": <liczba dziesiętna z jednym miejscem po przecinku>, \"note\": \"<krótka uwaga np. autostrada/drogi krajowe>\"}\n" .
                                            "Uwzględnij typową trasę, bez korków. Odległość to kilometrów w obie strony × 2. Czas przejazdu w jedną stronę."
                                        )
                                        ->generate();

                                    $text = trim($response->text);
                                    // Extract JSON from response
                                    if (preg_match('/\{.*\}/s', $text, $matches)) {
                                        $decoded = json_decode($matches[0], true);
                                        if ($decoded && isset($decoded['distance_km'])) {
                                            $distKm     = (float) $decoded['distance_km'];
                                            $travelHrs  = (float) ($decoded['travel_hours'] ?? round($distKm / 2 / 100, 1));
                                            $kmRate     = (float) ($get('km_rate') ?: 0.89);
                                            $hourRate   = (float) ($get('hour_rate') ?: 150);
                                            $travelCost = round($distKm * $kmRate + ($travelHrs * 2) * $hourRate, 2);
                                            $note       = $decoded['note'] ?? '';

                                            // Save base city to SystemSetting for next time
                                            SystemSetting::set('company_base_city', $data['base_city']);

                                            $set('distance_km', $distKm);
                                            $set('travel_hours', $travelHrs);
                                            $set('travel_cost', $travelCost);

                                            Notification::make()
                                                ->title('Trasa oszacowana')
                                                ->body("Dystans: {$distKm} km · Czas: {$travelHrs} h · Koszt: {$travelCost} PLN" . ($note ? " · {$note}" : ''))
                                                ->success()
                                                ->send();
                                            return;
                                        }
                                    }

                                    Notification::make()
                                        ->title('Błąd parsowania odpowiedzi AI')
                                        ->body('AI zwróciło nieoczekiwany format. Spróbuj ponownie.')
                                        ->danger()
                                        ->send();

                                } catch (\Throwable $e) {
                                    Notification::make()
                                        ->title('Błąd AI')
                                        ->body('Nie udało się połączyć z AI: ' . $e->getMessage())
                                        ->danger()
                                        ->send();
                                }
                            }),
                    ])->columnSpanFull(),
                ])->columns(2)->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('offer_number')->label('Nr oferty')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('offer_title')->label('Tytuł')->searchable()->sortable()->limit(40),
                Tables\Columns\TextColumn::make('company.name')->label('Firma')->searchable()->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'draft'    => 'Szkic',
                        'sent'     => 'Wysłana',
                        'accepted' => 'Zaakceptowana',
                        'rejected' => 'Odrzucona',
                        'archived' => 'Zarchiwizowana',
                        default    => $state,
                    })
                    ->colors([
                        'gray'    => 'draft',
                        'info'    => 'sent',
                        'success' => 'accepted',
                        'danger'  => 'rejected',
                        'warning' => 'archived',
                    ]),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Wartość')
                    ->money('PLN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('offer_date')->label('Data')->date('d.m.Y')->sortable(),
                Tables\Columns\TextColumn::make('creator.name')->label('Autor')->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Szkic', 'sent' => 'Wysłana',
                        'accepted' => 'Zaakceptowana', 'rejected' => 'Odrzucona',
                        'archived' => 'Zarchiwizowana',
                    ]),
                Tables\Filters\SelectFilter::make('company_id')
                    ->label('Firma')
                    ->options(fn () => Company::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('created_by')
                    ->label('Autor')
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
        return (string) static::getModel()::where('status', 'sent')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOffers::route('/'),
            'edit'   => Pages\EditOffer::route('/{record}/edit'),
        ];
    }
}
