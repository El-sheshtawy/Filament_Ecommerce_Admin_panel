<?php

namespace App\Filament\Resources;

use App\Enums\ProductTypeEnum;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\GlobalSearch\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Shopping';

    protected static ?string $recordTitleAttribute = 'name';

    protected static int $globalSearchResultsLimit = 10;

    protected static ?string $activeNavigationIcon  = 'heroicon-o-check-badge';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'brand.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return ['Product brand' => $record->brand->name];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with('brand:id,name');
    }

    public static function getGlobalSearchResultUrl(Model $record): ?string
    {
        return ProductResource::getUrl('show', ['record' => $record]);
    }

    public static function getGlobalSearchResultActions(Model $record): array
    {
        return [
            Action::make('show details')
                ->iconButton()
                ->icon('heroicon-o-eye')
                ->url(static::getUrl('show', ['record' => $record])),
        ];
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Main info')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->live(true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if (! in_array($operation, ['create', 'edit'])) {
                                            return;
                                        }
                                        $set('slug', Str::slug($state));
                                    }),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->unique(Product::class, 'slug')
                                    ->disabled()
                                    ->dehydrated(),
                                Forms\Components\MarkdownEditor::make('description')->columnSpan('full'),
                                ])->columns(2),

                         Forms\Components\Section::make('Pricing & Inventory')
                             ->schema([
                                 Forms\Components\TextInput::make('sku')
                                     ->label('SKU (Stock Keeping Unit)')
                                     ->unique()
                                     ->required(),
                                 Forms\Components\TextInput::make('price')
                                     ->required()
                                     ->numeric()
                                     ->rules('regex:/^[0-9]+$/'),
                                 Forms\Components\TextInput::make('quantity')
                                     ->required()
                                     ->numeric()
                                     ->minValue(1)
                                     ->maxValue(100),
                                 Forms\Components\Select::make('type')
                                     ->options([
                                         'downloadable' => ProductTypeEnum::DOWNLOADABLE->value,
                                         'deliverable' => ProductTypeEnum::DELIVERABLE->value,
                                     ])->required()
                             ])->columns(2)
                    ]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status')
                            ->schema([
                                Forms\Components\Toggle::make('is_visible')
                                    ->label('Visibility')
                                    ->helperText('Enable or disable product visibility')
                                    ->default(true),
                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Featured')
                                    ->helperText('Enable or disable product featured status'),
                                Forms\Components\DatePicker::make('published_at')
                                    ->label('Availability')
                                    ->default(now()),
                                ]),

                        Forms\Components\Section::make('Image')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->directory('products-images')
                                    ->preserveFilenames()
                                    ->image()
                                    ->imageEditor(),
                            ])->collapsible(),

                        Forms\Components\Section::make('Associations')
                            ->schema([
                                Forms\Components\Select::make('brand_id')
                                    ->relationship('brand', 'name')
                                    ->required(),

                                Forms\Components\Select::make('categories')
                                    ->relationship('categories', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->multiple()
                                    ->required()
                            ])->collapsible(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Visibility')
                    ->sortable()
                    ->toggleable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('price'),
                Tables\Columns\TextColumn::make('quantity')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type'),
            ])
            ->filters([
               Tables\Filters\TernaryFilter::make('is_visible')
                   ->label('Visibility')
                   ->boolean()
                   ->trueLabel('Only visible products')
                   ->falseLabel('Only hidden products')
                   ->native(false),

                Tables\Filters\SelectFilter::make('brand')
                    ->relationship('brand', 'name')
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'show' => Pages\ViewProduct::route('/{record}/show'),
        ];
    }
}
