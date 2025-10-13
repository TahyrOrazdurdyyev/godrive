<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingScreen extends Model
{
    protected $connection = 'mysql_main';
    protected $table = 'onboarding_screens';
    
    protected $fillable = [
        'title',
        'description',
        'image',
        'app_type',
        'display_order',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];
}
