<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Restaurant;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RestaurantResource\Pages;
use App\Filament\Resources\RestaurantResource\RelationManagers;

class RestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('shop_type')->required()->maxLength(255),
                Forms\Components\TextInput::make('address')->required()->maxLength(255),
                Forms\Components\TextInput::make('latitude')->required()->numeric(),
                Forms\Components\TextInput::make('longitude')->required()->numeric(),
                Forms\Components\TextInput::make('rating')->required()->numeric()->minValue(0)
                ->maxValue(5),
                Forms\Components\Textarea::make('description'),
                Forms\Components\FileUpload::make('logo')
                ->directory('restaurant_images')
                ->image()
                ->nullable(),
                Forms\Components\Toggle::make('is_popular')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                // Tables\Columns\TextColumn::make('shop_type')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('address')->sortable()->searchable()->limit(20),
                // Tables\Columns\TextColumn::make('latitude')->sortable(),
                // Tables\Columns\TextColumn::make('longitude')->sortable(),
                // Tables\Columns\TextColumn::make('rating')->sortable(),
                Tables\Columns\BooleanColumn::make('is_popular'),
                // Tables\Columns\TextColumn::make('description')->limit(50),
                Tables\Columns\ImageColumn::make('logo'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d-m-Y'),
                // Tables\Columns\TextColumn::make('updated_at')->dateTime(),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_popular')
                    ->query(fn (Builder $query): Builder => $query->where('is_popular', true)),
                Tables\Filters\Filter::make('shop_type')
                    ->query(fn (Builder $query, array $data): Builder => $query->where('shop_type', 'like', '%' . $data['shop_type'] . '%'))
                    ->form([
                        Forms\Components\TextInput::make('shop_type')->label('Food Type'),
                    ]),
                Tables\Filters\Filter::make('name')
                    ->query(fn (Builder $query, array $data): Builder => $query->where('name', 'like', '%' . $data['name'] . '%'))
                    ->form([
                        Forms\Components\TextInput::make('name')->label('Shop Name'),
                    ]),
                Tables\Filters\SelectFilter::make('rating')
                    ->options([
                        '1' => '1 Star',
                        '2' => '2 Stars',
                        '3' => '3 Stars',
                        '4' => '4 Stars',
                        '5' => '5 Stars',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRestaurants::route('/'),
            'create' => Pages\CreateRestaurant::route('/create'),
            'edit' => Pages\EditRestaurant::route('/{record}/edit'),
            'view' => Pages\ViewRestaurant::route('/{record}'),
        ];
    }
}
