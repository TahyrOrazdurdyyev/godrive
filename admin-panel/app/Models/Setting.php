<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $connection = 'mysql_main';
    protected $table = 'settings';

    protected $fillable = [
        'setting_key',
        'setting_value'
    ];

    protected $casts = [
        'setting_value' => 'array',
    ];

    public static function getValue($key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }
}
