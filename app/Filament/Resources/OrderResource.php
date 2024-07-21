<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('total_price')->required()->numeric(),
                // Forms\Components\TextInput::make('delivery_note')->nullable(),
                // Forms\Components\TextInput::make('delivery_cost')->required()->numeric(),
                // Forms\Components\TextInput::make('sub_total')->required()->numeric(),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'ordered' => 'Ordered',
                        'delivering' => 'Delivering',
                        'delivered' => 'Delivered',
                        'cancel' => 'Canceled',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('customer.fullname')->label('Customer'),
                Tables\Columns\TextColumn::make('delivery_note'),
                Tables\Columns\TextColumn::make('delivery_cost'),
                Tables\Columns\TextColumn::make('sub_total'),
                Tables\Columns\TextColumn::make('total_price')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status'),
                Tables\Columns\TextColumn::make('created_at')->label('Ordered At')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                ->options([
                    'ordered' => 'Ordered',
                    'delivering' => 'Delivering',
                    'delivered' => 'Delivered',
                    'cancel' => 'Canceled',
                ]),
                Tables\Filters\Filter::make('customer')
                    ->form([
                        Forms\Components\TextInput::make('customer_name'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['customer_name'],
                                fn (Builder $query, $name): Builder => $query->whereHas('customer', function (Builder $query) use ($name) {
                                    $query->where('fullname', 'like', "%{$name}%");
                                })
                            );
                    })
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
            // RelationManagers\OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
