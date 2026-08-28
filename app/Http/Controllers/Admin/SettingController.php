<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequest;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function __construct(
        private readonly SettingService $settingService
    ) {}

    public function index(): Response
    {
        $settings = $this->settingService->getAllGrouped();
        $formConfig = $this->settingService->getFormConfig();

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
            'formConfig' => $formConfig,
        ]);
    }

    public function update(SettingRequest $request): RedirectResponse
    {
        $this->settingService->updateGroup(
            $request->input('group'),
            $request->input('settings')
        );

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}
