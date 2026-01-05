<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PlaceController extends Controller
{
    /**
     * Display a listing of places.
     */
    public function index(Request $request)
    {
        $countyId = $request->get('county_id');
        $letter = $request->get('letter');
        $lettersOnly = $request->get('letters_only', false);

        try {
            // If letters_only flag is set, return distinct letters
            if ($lettersOnly && $countyId) {
                $response = Http::api()->get("/places?county_id=$countyId&distinct_letters=true");
                if ($response->successful()) {
                    $body = json_decode($response->body(), false);

                    // Accept multiple response shapes
                    if (is_array($body)) {
                        return response()->json($body);
                    }

                    $letters = $body->letters
                        ?? ($body->data->letters ?? [])
                        ?? [];

                    return response()->json($letters);
                }
                return response()->json([]);
            }

            // If letter is specified, return places starting with that letter
            if ($countyId && $letter) {
                $response = Http::api()->get("/places?county_id=$countyId&letter=" . urlencode($letter));
                if ($response->successful()) {
                    $body = json_decode($response->body(), false);

                    // Accept multiple response shapes
                    if (is_array($body)) {
                        return response()->json($body);
                    }

                    $places = $body->places
                        ?? ($body->data->places ?? [])
                        ?? [];

                    return response()->json($places);
                }
                return response()->json([]);
            }

            // Standard index - list all places or filtered
            $needle = $request->get('needle');
            $url = $needle ? "places?needle=" . urlencode($needle) : "places";
            
            if ($countyId) {
                $url = "places?county_id=$countyId";
            }

            $response = Http::api()->get($url);

            if ($response->failed()) {
                return redirect()->route('places.index')
                    ->with('error', "Hiba történt a lekérdezés során");
            }

            $places = $this->getPlaces($response);
            
            // Get counties for select
            $countiesResponse = Http::api()->get('counties');
            $counties = $this->getCounties($countiesResponse);

            return view('places.index', [
                'entities' => $places,
                'counties' => $counties,
                'isAuthenticated' => $this->isAuthenticated()
            ]);
        } catch (\Exception $e) {
            return redirect()->route('places.index')
                ->with('error', "Nem sikerült betölteni a városokat");
        }
    }

    /**
     * Show the form for creating a new place.
     */
    public function create()
    {
        try {
            $countiesResponse = Http::api()->get('counties');
            $counties = $this->getCounties($countiesResponse);

            $postalCodesResponse = Http::api()->get('postal_codes');
            $postalCodes = $this->getPostalCodes($postalCodesResponse);

            return view('places.create', [
                'counties' => $counties,
                'postalCodes' => $postalCodes
            ]);
        } catch (\Exception $e) {
            return redirect()->route('places.index')
                ->with('error', "Nem sikerült betölteni az adatokat");
        }
    }

    /**
     * Store a newly created place in storage.
     */
    public function store(PlaceRequest $request)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('places.index')
                ->with('error', "Csak bejelentkezett felhasználó hozhat létre várost");
        }

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->post('/places', [
                    'name' => $request->get('name'),
                    'county_id' => $request->get('county_id'),
                    'postal_code_id' => $request->get('postal_code_id'),
                ]);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült létrehozni';
                return redirect()->route('places.index')
                    ->with('error', "Hiba: $message");
            }

            return redirect()->route('places.index')
                ->with('success', "Város sikeresen létrehozva!");
        } catch (\Exception $e) {
            return redirect()->route('places.index')
                ->with('error', "Nem sikerült kommunikálni az API-val");
        }
    }

    /**
     * Display the specified place.
     */
    public function show($id)
    {
        try {
            $response = Http::api()->get("/places/$id");

            if ($response->failed()) {
                return redirect()->route('places.index')
                    ->with('error', "Város nem található");
            }

            $place = $this->getPlace($response);
            return view('places.show', ['entity' => $place]);
        } catch (\Exception $e) {
            return redirect()->route('places.index')
                ->with('error', "Nem sikerült betölteni a várost");
        }
    }

    /**
     * Show the form for editing the specified place.
     */
    public function edit($id)
    {
        try {
            $response = Http::api()->get("/places/$id");

            if ($response->failed()) {
                return redirect()->route('places.index')
                    ->with('error', "Város nem található");
            }

            $place = $this->getPlace($response);

            $countiesResponse = Http::api()->get('counties');
            $counties = $this->getCounties($countiesResponse);

            $postalCodesResponse = Http::api()->get('postal_codes');
            $postalCodes = $this->getPostalCodes($postalCodesResponse);

            return view('places.edit', [
                'entity' => $place,
                'counties' => $counties,
                'postalCodes' => $postalCodes
            ]);
        } catch (\Exception $e) {
            return redirect()->route('places.index')
                ->with('error', "Nem sikerült betölteni a várost");
        }
    }

    /**
     * Update the specified place in storage.
     */
    public function update(PlaceRequest $request, $id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('places.index')
                ->with('error', "Csak bejelentkezett felhasználó módosíthat");
        }

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->put("/places/$id", [
                    'name' => $request->get('name'),
                    'county_id' => $request->get('county_id'),
                    'postal_code_id' => $request->get('postal_code_id'),
                ]);

            if ($response->successful()) {
                return redirect()->route('places.index')
                    ->with('success', "Város sikeresen frissítve!");
            }

            $errorMessage = $response->json('message') ?? 'Ismeretlen hiba';
            return redirect()->route('places.index')
                ->with('error', "Hiba történt: $errorMessage");
        } catch (\Exception $e) {
            return redirect()->route('places.index')
                ->with('error', "Nem sikerült frissíteni");
        }
    }

    /**
     * Remove the specified place from storage.
     */
    public function destroy($id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('places.index')
                ->with('error', "Csak bejelentkezett felhasználó törölhet");
        }

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->delete("/places/$id");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült törölni';
                return redirect()->route('places.index')
                    ->with('error', "Hiba: $message");
            }

            return redirect()->route('places.index')
                ->with('success', "Város sikeresen törölve!");
        } catch (\Exception $e) {
            return redirect()->route('places.index')
                ->with('error', "Nem sikerült kommunikálni az API-val");
        }
    }

    /**
     * Export places to CSV.
     */
    public function exportCsv(Request $request)
    {
        $needle = $request->get('needle');
        $url = $needle ? "places?needle=" . urlencode($needle) : "places";

        try {
            $response = Http::api()->get($url);

            if ($response->failed()) {
                return redirect()->route('places.index')
                    ->with('error', "Hiba történt a lekérdezés során");
            }

            $places = $this->getPlaces($response);

            $csv = "ID,Város,Megye,Irányítószám\n";
            foreach ($places as $place) {
                $county = $place->county->name ?? 'N/A';
                $postalCode = $place->postal_code->code ?? 'N/A';
                $csv .= "{$place->id},{$place->name},$county,$postalCode\n";
            }

            return response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="places.csv"');
        } catch (\Exception $e) {
            return redirect()->route('places.index')
                ->with('error', "CSV export sikertelen");
        }
    }

    /**
     * Export places to PDF.
     */
    public function exportPdf(Request $request)
    {
        $needle = $request->get('needle');
        $url = $needle ? "places?needle=" . urlencode($needle) : "places";

        try {
            $response = Http::api()->get($url);

            if ($response->failed()) {
                return redirect()->route('places.index')
                    ->with('error', "Hiba történt a lekérdezés során");
            }

            $places = $this->getPlaces($response);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.places', ['entities' => $places]);
            return $pdf->download('places.pdf');
        } catch (\Exception $e) {
            return redirect()->route('places.index')
                ->with('error', "PDF export sikertelen");
        }
    }

    /**
     * Helper: Extract places from API response.
     */
    private function getPlaces($response)
    {
        $responseBody = json_decode($response->body(), false);
        
        // If response is an array directly, return it
        if (is_array($responseBody)) {
            return $responseBody;
        }
        
        // If response has data.places structure
        $data = $responseBody->data ?? null;
        if (!empty($data) && isset($data->places)) {
            return $data->places;
        }
        
        // If response has places directly
        if (isset($responseBody->places)) {
            return $responseBody->places;
        }
        
        return [];
    }

    /**
     * Helper: Extract single place from API response.
     */
    private function getPlace($response)
    {
        $responseBody = json_decode($response->body(), false);
        
        // If response is an object directly (single place)
        if (isset($responseBody->id) && isset($responseBody->name)) {
            return $responseBody;
        }
        
        // If response has data.place structure
        $data = $responseBody->data ?? null;
        if (!empty($data) && isset($data->place)) {
            return $data->place;
        }
        
        // If response has place directly
        if (isset($responseBody->place)) {
            return $responseBody->place;
        }
        
        return (object)[];
    }

    /**
     * Helper: Extract counties from API response.
     */
    private function getCounties($response)
    {
        $responseBody = json_decode($response->body(), false);
        
        // If response is an array directly, return it
        if (is_array($responseBody)) {
            return $responseBody;
        }
        
        // If response has data.counties structure
        $data = $responseBody->data ?? null;
        if (!empty($data) && isset($data->counties)) {
            return $data->counties;
        }
        
        // If response has counties directly
        if (isset($responseBody->counties)) {
            return $responseBody->counties;
        }
        
        return [];
    }

    /**
     * Helper: Extract postal codes from API response.
     */
    private function getPostalCodes($response)
    {
        $responseBody = json_decode($response->body(), false);
        
        // If response is an array directly, return it
        if (is_array($responseBody)) {
            return $responseBody;
        }
        
        // If response has data.postal_codes structure
        $data = $responseBody->data ?? null;
        if (!empty($data) && isset($data->postal_codes)) {
            return $data->postal_codes;
        }
        
        // If response has postal_codes directly
        if (isset($responseBody->postal_codes)) {
            return $responseBody->postal_codes;
        }
        
        return [];
    }
}
