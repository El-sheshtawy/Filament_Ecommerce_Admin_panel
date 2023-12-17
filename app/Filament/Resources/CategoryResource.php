<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers\ProductsRelationManager;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Filament\GlobalSearch\Actions\Action;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 2;

  //  protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationGroup = 'Shopping';

    protected static ?string $activeNavigationIcon  = 'heroicon-o-check-badge';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'description'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Name' => $record->name,
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): ?string
    {
        return CategoryResource::getUrl('show', ['record' => $record]);
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
                   Forms\Components\Section::make([
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

                       Forms\Components\MarkdownEditor::make('description')
                           ->required()
                           ->columnSpanFull(),
                   ])->columns(2),
               ]),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Status')
                        ->schema([
                            Forms\Components\Toggle::make('is_visible')
                                ->label('Visibility')
                                ->helperText('Enable or disable category visibility')
                                ->default(true),

                            Forms\Components\Select::make('parent_id')
                                ->relationship('parent', 'name'),
                        ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent Category')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Visibility')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->date()
                    ->label('Updated at')
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Category info')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),

                        TextEntry::make('slug')
                            ->label('Slug'),

                        TextEntry::make('parent.name')
                            ->label('Parent Category'),

                        TextEntry::make('is_visible')
                            ->label('Visibility'),

                        TextEntry::make('description')
                            ->label('Description'),

                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
            'show' => Pages\ViewCategory::route('/{record}/show'),
        ];
    }
}
