<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Récupère une valeur de paramètre global avec valeur par défaut.
     */
    public static function get(string $key, $default = null)
    {
        $settings = Cache::remember('app_settings', 300, function () {
            return self::pluck('value', 'key')->all();
        });

        return array_key_exists($key, $settings) && $settings[$key] !== null
            ? $settings[$key]
            : $default;
    }

    public static function set(string $key, $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('app_settings');
    }
}
