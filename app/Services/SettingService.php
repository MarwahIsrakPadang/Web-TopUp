<?php

namespace App\Services;

use App\Repositories\SettingRepository;
use Illuminate\Support\Collection;

class SettingService
{
    public function __construct(
        private readonly SettingRepository $repository
    ) {}

    public function getAllGrouped(): Collection
    {
        return $this->repository->getAllGrouped();
    }

    public function getByGroup(string $group): Collection
    {
        return $this->repository->getByGroup($group);
    }

    public function updateGroup(string $group, array $data): void
    {
        $this->repository->upsertBatch($group, $data);
    }

    public function getFormConfig(): array
    {
        return [
            'general' => [
                'label' => 'Umum',
                'fields' => [
                    'site_name' => ['label' => 'Nama Website', 'type' => 'text'],
                    'site_description' => ['label' => 'Deskripsi', 'type' => 'textarea'],
                    'contact_email' => ['label' => 'Email Kontak', 'type' => 'email'],
                    'contact_phone' => ['label' => 'No. Telepon', 'type' => 'text'],
                    'whatsapp_number' => ['label' => 'No. WhatsApp', 'type' => 'text'],
                ],
            ],
            'theme' => [
                'label' => 'Tema',
                'fields' => [
                    'primary_color' => ['label' => 'Warna Utama', 'type' => 'color'],
                    'dark_mode_default' => [
                        'label' => 'Mode Gelap Default',
                        'type' => 'select',
                        'options' => ['system' => 'Ikuti Sistem', 'light' => 'Terang', 'dark' => 'Gelap'],
                    ],
                ],
            ],
            'payment' => [
                'label' => 'Pembayaran',
                'fields' => [
                    'currency' => ['label' => 'Mata Uang', 'type' => 'text'],
                    'tax_percentage' => ['label' => 'Pajak (%)', 'type' => 'number'],
                ],
            ],
        ];
    }
}
