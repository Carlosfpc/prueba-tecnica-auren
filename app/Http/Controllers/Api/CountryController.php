<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\Controller;

// Models
use App\Models\Country;

// Validations
use App\Http\Requests\Api\GetCountriesRequest;
use App\Http\Requests\Api\ShowCountryRequest;

class CountryController extends Controller
{
    /**
     * Display a paginated list of countries with optional filters and sorting.
     */
    public function index(GetCountriesRequest $request): JsonResponse
    {
        // Retrieve the validated and sanitized parameters.
        $validatedData = $request->validated();
        $searchTerm = $validatedData['q'] ?? null;
        $regionFilter = $validatedData['region'] ?? null;
        $sortByPopulation = isset($validatedData['population_sort']);
        $sortDirection = $validatedData['sort_order'] ?? 'desc';
        $perPage = $validatedData['per_page'] ?? 15;

        // Start a base query.
        $query = Country::query();

        // Apply filters via model scopes.
        $query->filterByName($searchTerm);
        $query->filterByRegion($regionFilter);

        // Apply sorting conditionally.
        if ($sortByPopulation) {
            $query->sortByPopulation($sortDirection);
        }

        // Paginate and execute the final query.
        $countries = $query->paginate($perPage);

        return response()->json($countries);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display a single country by its CCA3 code.
     */
    public function show(ShowCountryRequest $request, string $cca3): JsonResponse
    {
        $country = Country::find($cca3);

        if (!$country) {
            return response()->json([
                'message' => 'Country not found.'
            ], 404);
        }

        return response()->json($country);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
