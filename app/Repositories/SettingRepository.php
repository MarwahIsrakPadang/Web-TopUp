<?php

namespace App\Repositories;

use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SettingRepository
{
    public function getAllGrouped(): Collection
    {
        return Cache::remember('settings_all_grouped', 21600, fn() =>
            Setting::all()->groupBy('group')
        );
    }

    public function getByGroup(string $group): Collection
    {
        return Setting::where('group', $group)->get();
    }

    public function upsert(string $group, string $key, ?string $value): Setting
    {
        $setting = Setting::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value]
        );

        Cache::forget('settings_all_grouped');

        return $setting;
    }

    public function upsertBatch(string $group, array $data): void
    {
        foreach ($data as $key => $value) {
            $this->upsert($group, $key, $value);
        }

        Cache::forget('settings_all_grouped');
    }
}