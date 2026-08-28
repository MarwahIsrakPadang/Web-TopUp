<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApiConfigRequest;
use App\Models\ApiConfig;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ApiConfigController extends Controller
{
    public function index(Request $request): Response
    {
        $activeProvider = $request->get('provider', 'tripay');

        $tripayConfigs = ApiConfig::where('provider', 'tripay')
            ->get()
            ->keyBy('config_key')
            ->map(fn($item) => $item->config_value);

        $gameConfigs = ApiConfig::where('provider', 'game_api')
            ->get()
            ->keyBy('config_key')
            ->map(fn($item) => $item->config_value);

        return Inertia::render('Admin/ApiConfigs/Index', [
            'activeProvider' => $activeProvider,
            'providers' => [
                'tripay' => $tripayConfigs,
                'game_api' => $gameConfigs,
            ],
        ]);
    }

    public function update(ApiConfigRequest $request): RedirectResponse
    {
        $provider = $request->input('provider');

        foreach ($request->input('configs') as $key => $value) {
            ApiConfig::setConfig($provider, $key, $value);
        }

        return redirect()
            ->route('admin.api-configs.index', ['provider' => $provider])
            ->with('success', 'Konfigurasi ' . $provider . ' berhasil disimpan.');
    }
}
