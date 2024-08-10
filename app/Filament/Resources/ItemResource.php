<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Item;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ItemResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ItemResource\RelationManagers;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()->role=='restaurant_owner';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('restaurant_id')
                    ->relationship('restaurant', 'name', function ($query) {
                        $query->where('is_approved', 1);

                        if (!auth()->user()->isSuperadmin()) {
                            $query->where('created_by', auth()->id());
                        }
                    })
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('rating')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(5),
                Forms\Components\FileUpload::make('cover_image')
                    ->directory('item_images')
                    ->image()
                    ->nullable(),
                Forms\Components\Toggle::make('is_offer_item')
                    ->default(false),
                Forms\Components\TextInput::make('offer_price')
                    ->numeric()
                    ->nullable(),
                Forms\Components\HasManyRepeater::make('portions')
                    ->relationship('portions')
                    ->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('price')->required(),
                        Forms\Components\Hidden::make('created_by')
                        ->default(Auth::id()),
                    ])
                    ->label('Portions')
                    ->collapsible(),
                    
                Forms\Components\Hidden::make('created_by')
                    ->default(Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('restaurant.name')->sortable()->label('Restaurant'),
                Tables\Columns\TextColumn::make('category.name')->sortable()->label('Category'),
                Tables\Columns\TextColumn::make('price')->sortable(),
                // Tables\Columns\TextColumn::make('rating')->sortable(),
                Tables\Columns\BooleanColumn::make('is_offer_item'),
                // Tables\Columns\TextColumn::make('offer_price')->sortable(),
                Tables\Columns\ImageColumn::make('cover_image'),
                // Tables\Columns\TextColumn::make('portions_count')
                // ->counts('portions')
                // ->label('Number of Portions'),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Posted By')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d-m-Y'),
                // Tables\Columns\TextColumn::make('updated_at')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('restaurant')
                ->relationship('restaurant', 'name'),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_offer_item')
                    ->label('Offer Item'),
                Tables\Filters\Filter::make('name')
                    ->query(fn (Builder $query, array $data): Builder => $query->where('name', 'like', '%' . $data['name'] . '%'))
                    ->form([
                        Forms\Components\TextInput::make('name')->label('Item Name'),
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
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
            'view' => Pages\ViewItem::route('/{record}'),
        ];
    }
}
