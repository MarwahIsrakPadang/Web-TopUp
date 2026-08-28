<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ApiConfig extends Model
{
    protected $fillable = [
        'provider',
        'config_key',
        'config_value',
    ];

    public static function getConfig(string $provider, string $key, mixed $default = null): mixed
    {
        return Cache::remember("api_config_{$provider}_{$key}", 3600, function () use ($provider, $key, $default) {
            $config = static::where('provider', $provider)->where('config_key', $key)->first();
            return $config ? $config->config_value : $default;
        });
    }

    public static function setConfig(string $provider, string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['provider' => $provider, 'config_key' => $key],
            ['config_value' => $value]
        );

        Cache::forget("api_config_{$provider}_{$key}");
    }
}
