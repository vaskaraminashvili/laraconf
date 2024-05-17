<?php

namespace App\Models;

use App\Enums\Region;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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
                Section::make('Conference Details')
                    ->columns(2)
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

                    ]),
                Toggle::make('is_published')
                    ->default(true),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])
                    ->required(),
                Select::make('region')
                    ->live()
                    ->enum(Region::class)
                    ->options(Region::class),
                Select::make('venue_id')
                    ->searchable()
                    ->editOptionForm(Venue::getForm())
                    ->createOptionForm(Venue::getForm())
                    ->preload()
                    ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query , Get $get){
                        return $query->where('region', $get('region'));
                    }),
                CheckboxList::make('speakers')
                ->relationship('speakers', 'name')
                ->options(
                    Speaker::all()->pluck('name', 'id')
                )
            ];
    }
}
