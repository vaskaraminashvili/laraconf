<?php

namespace App\Models;

use App\Enums\Region;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Get;

class Conference extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'region' => Region::class,
        'venue_id' => 'integer',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public static function getForm()
    {
        return [
            Tabs::make()
            ->tabs([
                Tabs\Tab::make('Details')
                ->schema([
                    TextInput::make('name')
                        ->columnSpanFull()
                        ->required()
                        ->default('my conf name')
                        ->hint('here is hint')
                        ->helperText('Name of the inpit just')
                        ->maxLength(255),
                    RichEditor::make('description')
                        ->columnSpanFull()
                        ->required()
                        //                    ->disableToolbarButtons(['italic'])
                        //                        ->toolbarButtons(['h2', 'bold'])
                        ->maxLength(255),
                    DateTimePicker::make('start_date')
                        ->native(false)
                        ->required(),
                    DateTimePicker::make('end_date')
                        ->native(false)
                        ->required(),
                    Fieldset::make('Status')
                        ->columns(1)
                        ->schema([

                            Select::make('status')
                                ->options([
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                    'archived' => 'Archived',
                                ])
                                ->required(),
                            Toggle::make('is_published')
                                ->default(true),
                        ])
                ])
            ]),
//            Section::make('Conference Details')
//                ->collapsible()
////                ->aside()
//                ->description('test text')
////                ->columns(['md' => 2 , 'lg' => 3])
//                ->columns(2)
//                ->schema([
//

//                ]),
            Section::make('Location')
                ->columns(2)
                ->schema([
                    Select::make('region')
                        ->live()
                        ->enum(Region::class)
                        ->options(Region::class),
                    Select::make('venue_id')
                        ->searchable()
                        ->editOptionForm(Venue::getForm())
                        ->createOptionForm(Venue::getForm())
                        ->preload()
                        ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Get $get) {
                            return $query->where('region', $get('region'));
                        }),
                ]),


            CheckboxList::make('speakers')
                ->relationship('speakers', 'name')
                ->options(
                    Speaker::all()->pluck('name', 'id')
                )
        ];
    }
}
