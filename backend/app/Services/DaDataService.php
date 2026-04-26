<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DaDataService
{
    public function suggestAddress(string $query): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Token '.config('services.dadata.token'),
            'X-Secret' => (string) config('services.dadata.secret'),
        ])->post(rtrim((string) config('services.dadata.base_url'), '/').'/suggestions/api/4_1/rs/suggest/address', [
            'query' => $query,
            'count' => 5,
        ]);

        if (! $response->successful()) {
            return [];
        }

        return $response->json('suggestions', []);
    }
}
