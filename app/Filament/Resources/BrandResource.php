<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Filament\Resources\BrandResource\RelationManagers;
use App\Filament\Resources\BrandResource\RelationManagers\ProductsRelationManager;
use App\Models\Brand;
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

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationGroup = 'Shopping';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return ['Name' => $record->name];
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
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make()->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->live(true)
                            ->unique()
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if (! in_array($operation, ['create', 'edit'])) {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(Brand::class, 'slug')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('url')
                            ->label('Website URL')
                            ->unique()
                            ->required()
                            ->columnSpan('full'),

                        Forms\Components\MarkdownEditor::make('description')
                            ->required()
                            ->columnSpanFull()
                    ])->columns(2)
                ]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status')
                            ->schema([
                                Forms\Components\Toggle::make('is_visible')
                                    ->label('Visibility')
                                    ->helperText('Enable or disable brand visibility')
                                    ->default(true),
                            ]),

                        Forms\Components\Section::make('Color')->schema([
                            Forms\Components\ColorPicker::make('primary_hex')
                                ->label('Primary color')
                                ->required(),
                        ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('url')
                    ->label('Website URL')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\ColorColumn::make('primary_hex')
                    ->label('Primary Color'),

                Tables\Columns\IconColumn::make('is_visible')
                    ->boolean()
                    ->label('Visibility')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
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
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
            'show' => Pages\ViewBrand::route('/{record}/show'),
        ];
    }
}
