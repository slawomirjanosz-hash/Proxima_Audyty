<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditTypeResource\Pages;
use App\Models\AuditType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditTypeResource extends Resource
{
    protected static ?string $model = AuditType::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Audyty';
    protected static ?string $navigationLabel = 'Typy audytów';
    protected static ?string $modelLabel = 'Typ audytu';
    protected static ?string $pluralModelLabel = 'Typy audytów';
    protected static ?int $navigationSort = 32;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nazwa')->required()->maxLength(255),
            Forms\Components\TextInput::make('category')->label('Kategoria')->maxLength(100),
            Forms\Components\TextInput::make('agent_type')->label('Typ agenta AI')->maxLength(100),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nazwa')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category')->label('Kategoria')->badge()->toggleable(),
                Tables\Columns\TextColumn::make('agent_type')->label('Typ agenta')->toggleable(),
                Tables\Columns\TextColumn::make('sections_count')->label('Sekcji')->counts('sections')->alignCenter(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAuditTypes::route('/'),
            'create' => Pages\CreateAuditType::route('/create'),
            'edit'   => Pages\EditAuditType::route('/{record}/edit'),
        ];
    }
}
