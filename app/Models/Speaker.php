<?php

namespace App\Models;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Speaker extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'qualifications' => 'array',
    ];

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public static function getForm()
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            FileUpload::make('avatar')
//                ->avatar()
                ->image()
//                ->directory()
//                    ->preserveFilenames()
                ->imageEditor()
                ->maxSize(1024 * 1024 * 10),
            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            Textarea::make('bio')
                ->columnSpanFull(),
            TextInput::make('twitter_handle')
                ->maxLength(255),
            CheckboxList::make('qualifications')
                ->options([
                    'business-leader' => 'Business Leader',
                    'charisma' => 'Charismatic Speaker',
                    'first-time' => 'First Time Speaker',
                    'hometown-hero' => 'Hometown Hero',
                    'humanitarian' => 'Works in Humanitarian Field',
                    'laracasts-contributor' => 'Laracasts Contributor',
                    'twitter-influencer' => 'Large Twitter Following',
                    'youtube-influencer' => 'Large YouTube Following',
                    'open-source' => 'Open Source Creator / Maintainer',
                    'unique-perspective' => 'Unique Perspective'
                ])
                ->descriptions([
                    'business-leader' => 'Here is a nice long description',
                    'charisma' => 'This is even more information about why you should pick this one',
                ])
                ->bulkToggleable()
                ->searchable()
                ->columnSpanFull()
                ->columns(3)
        ];
    }
}
