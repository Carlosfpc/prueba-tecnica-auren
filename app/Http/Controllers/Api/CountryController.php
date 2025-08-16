<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Models\Country;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a paginated list of countries with optional filters and sorting.
     */
    public function index(Request $request): JsonResponse
    {
        // Start a base query.
        $query = Country::query();

        // Retrieve validated parameters from the request.
        $searchTerm = $request->input('q', null);
        $regionFilter = $request->input('region', null);
        $sortByPopulation = $request->has('population_sort');
        $sortDirection = $request->input('sort_order', 'desc');
        $perPage = $request->input('per_page', 15);

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
    public function show(string $cca3): JsonResponse
    {
        // Find the country by its primary key (cca3).
        // findOrFail will automatically throw a 404 Not Found exception if not found.
        $country = Country::findOrFail($cca3);

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
