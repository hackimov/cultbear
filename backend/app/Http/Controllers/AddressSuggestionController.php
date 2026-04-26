<?php

namespace App\Http\Controllers;

use App\Services\DaDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressSuggestionController extends Controller
{
    public function __invoke(Request $request, DaDataService $daDataService): JsonResponse
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'min:3'],
        ]);

        return response()->json([
            'suggestions' => $daDataService->suggestAddress($validated['query']),
        ]);
    }
}
