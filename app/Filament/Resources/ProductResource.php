<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $slug = 'sv23810310083-products';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    
    protected static ?string $navigationLabel = 'Sản phẩm 23810310083';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin cơ bản')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name') 
                            ->required()
                            ->label('Danh mục'),
                        
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->label('Tên sản phẩm')
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->label('Đường dẫn (Slug)'),
                    ])->columns(2),

                Forms\Components\Section::make('Chi tiết sản phẩm')
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->columnSpanFull()
                            ->label('Mô tả sản phẩm'),

                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->minValue(0) 
                            ->prefix('VNĐ')
                            ->label('Giá bán'),

                        Forms\Components\TextInput::make('stock_quantity')
                            ->required()
                            ->numeric()
                            ->integer() 
                            ->label('Số lượng tồn kho'),

                        Forms\Components\TextInput::make('warranty_months')
                            ->required()
                            ->numeric()
                            ->default(12)
                            ->suffix('tháng')
                            ->label('Thời gian bảo hành'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Bản nháp',
                                'published' => 'Đã xuất bản',
                                'out_of_stock' => 'Hết hàng',
                            ])
                            ->required()
                            ->label('Trạng thái'),
                    ])->columns(2),

                Forms\Components\Section::make('Hình ảnh')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->image()
                            ->directory('products')
                            ->label('Ảnh đại diện'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Ảnh'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable() 
                    ->label('Tên SP'),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->formatStateUsing(fn (string $state): string => number_format($state, 0, ',', '.') . ' ₫')
                    ->sortable()
                    ->label('Giá'),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->numeric()
                    ->sortable()
                    ->label('Kho'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'out_of_stock',
                        'warning' => 'draft',
                        'success' => 'published',
                    ])
                    ->label('Trạng thái'),

                Tables\Columns\TextColumn::make('warranty_months')
                    ->suffix(' tháng')
                    ->label('Bảo hành'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Lọc theo danh mục'),
            ])
            ->actions([
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}