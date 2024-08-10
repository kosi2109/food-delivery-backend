<?php

namespace App\Filament\Resources\OrderItemsResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orderItems';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $isSuperadmin = $user->isSuperadmin();

        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                // Add more columns as needed
            ])
            ->filters([
                // Add any custom filters if needed
            ])
            ->query(function (Builder $query) use ($isSuperadmin, $user) {
                if (!$isSuperadmin) {
                    // Filter records based on user role
                    $query->whereHas('item', function (Builder $q) use ($user) {
                        $q->where('created_by', $user->id);
                    });
                }
            })
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

