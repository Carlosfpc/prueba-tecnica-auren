<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// OPENAPI
use OpenApi\Attributes as OA;

// Controllers
use App\Http\Controllers\Controller;

// Models
use App\Models\Country;

// Validations
use App\Http\Requests\Api\GetCountriesRequest;
use App\Http\Requests\Api\ShowCountryRequest;


/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Auren Countries API Documentation",
 *      description="API documentation for the technical test.",
 *      @OA\Contact(
 *          email="carlosfranciscopc@gmail.com"
 *      )
 * )
 *
 * @OA\Server(
 *      url="http://localhost:8000",
 *      description="Local Development Server"
 * )
 */
class CountryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/countries",
     *     operationId="getCountriesList",
     *     summary="Get a paginated list of countries",
     *     description="Returns a list of countries, supporting filtering, sorting, and pagination.",
     *     tags={"Countries"},
     *
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search term to filter by common or official name.",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="region",
     *         in="query",
     *         description="Filter results by a specific region (e.g., 'Europe', 'Americas').",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="population_sort",
     *         in="query",
     *         description="Flag to activate sorting by population. The value does not matter.",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Direction for population sort. Allowed values: 'asc', 'desc'.",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of results to return per page.",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="The page number to retrieve.",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation. Returns a paginated list of countries.",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error if parameters are invalid."
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/countries/{cca3}",
     *     summary="Get a single country by its CCA3 code",
     *     tags={"Countries"},
     *     @OA\Parameter(
     *         name="cca3",
     *         in="path",
     *         required=true,
     *         description="The 3-letter country code (e.g., ESP)",
     *         @OA\Schema(type="string", format="cca3")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Country not found"
     *     )
     * )
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
