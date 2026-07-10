<?php

namespace App\Filament\Resources\PickupCalls\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PickupCallForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('school_id')
                    ->required()
                    ->numeric(),
                TextInput::make('school_class_id')
                    ->required()
                    ->numeric(),
                TextInput::make('student_id')
                    ->required()
                    ->numeric(),
                TextInput::make('parent_id')
                    ->numeric(),
                TextInput::make('kiosk_id')
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('waiting'),
                DateTimePicker::make('called_at'),
                DateTimePicker::make('completed_at'),
            ]);
    }
}
