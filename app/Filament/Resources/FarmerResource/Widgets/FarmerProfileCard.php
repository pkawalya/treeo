<?php

namespace App\Filament\Resources\FarmerResource\Widgets;

use App\Models\Farmer;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class FarmerProfileCard extends Widget
{
    protected static string $view = 'filament.resources.farmer-resource.widgets.farmer-profile-card';
    
    protected int|string|array $columnSpan = 'full';
    
    public ?Model $record = null;
}
