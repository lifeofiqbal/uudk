<?php

namespace App\Http\Controllers;

use App\Services\PenalCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PenalCodeController extends Controller
{
    public function __construct(private readonly PenalCodeService $service)
    {
    }

    public function index(): View
    {
        $settings = [
            'maxPasal' => max(1, (int) config('penal.max_pasal')),
            'dpoMultiplier' => max(1, (int) config('penal.dpo_multiplier')),
        ];

        $bootstrap = json_encode(
            ['data' => $this->service->payload(), 'settings' => $settings],
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
        );

        return view('penal-code', [
            'settings' => $settings,
            'bootstrap' => $bootstrap,
        ]);
    }

    public function data(): JsonResponse
    {
        return response()
            ->json($this->service->payload())
            ->setPublic()
            ->setMaxAge(300);
    }
}
