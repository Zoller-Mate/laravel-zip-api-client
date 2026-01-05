<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CountyController extends Controller
{
    /**
     * Display a listing of counties.
     */
    public function index(Request $request)
    {
        $needle = $request->get('needle');
        try {
            $url = $needle ? "counties?needle=" . urlencode($needle) : "counties";
            $response = Http::api()->get($url);

            if ($response->failed()) {
                return redirect()->route('counties.index')
                    ->with('error', "Hiba történt a lekérdezés során");
            }

            $counties = $this->getCounties($response);
            return view('counties.index', [
                'entities' => $counties,
                'isAuthenticated' => $this->isAuthenticated()
            ]);
        } catch (\Exception $e) {
            return redirect()->route('counties.index')
                ->with('error', "Nem sikerült betölteni a megyéket");
        }
    }

    /**
     * Show the form for creating a new county.
     */
    public function create()
    {
        return view('counties.create');
    }

    /**
     * Store a newly created county in storage.
     */
    public function store(CountyRequest $request)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('counties.index')
                ->with('error', "Csak bejelentkezett felhasználó hozhat létre megyét");
        }

        $name = $request->get('name');
        try {
            $response = Http::api()
                ->withToken($this->token)
                ->post('/counties', ['name' => $name]);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült létrehozni';
                return redirect()->route('counties.index')
                    ->with('error', "Hiba: $message");
            }

            return redirect()->route('counties.index')
                ->with('success', "$name megye sikeresen létrehozva!");
        } catch (\Exception $e) {
            return redirect()->route('counties.index')
                ->with('error', "Nem sikerült kommunikálni az API-val");
        }
    }

    /**
     * Display the specified county.
     */
    public function show($id)
    {
        try {
            $response = Http::api()->get("/counties/$id");

            if ($response->failed()) {
                return redirect()->route('counties.index')
                    ->with('error', "Megye nem található");
            }

            $county = $this->getCounty($response);
            return view('counties.show', ['entity' => $county]);
        } catch (\Exception $e) {
            return redirect()->route('counties.index')
                ->with('error', "Nem sikerült betölteni a megyét");
        }
    }

    /**
     * Show the form for editing the specified county.
     */
    public function edit($id)
    {
        try {
            $response = Http::api()->get("/counties/$id");

            if ($response->failed()) {
                return redirect()->route('counties.index')
                    ->with('error', "Megye nem található");
            }

            $county = $this->getCounty($response);
            return view('counties.edit', ['entity' => $county]);
        } catch (\Exception $e) {
            return redirect()->route('counties.index')
                ->with('error', "Nem sikerült betölteni a megyét");
        }
    }

    /**
     * Update the specified county in storage.
     */
    public function update(CountyRequest $request, $id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('counties.index')
                ->with('error', "Csak bejelentkezett felhasználó módosíthat");
        }

        $name = $request->get('name');
        try {
            $response = Http::api()
                ->withToken($this->token)
                ->put("/counties/$id", ['name' => $name]);

            if ($response->successful()) {
                return redirect()->route('counties.index')
                    ->with('success', "$name megye sikeresen frissítve!");
            }

            $errorMessage = $response->json('message') ?? 'Ismeretlen hiba';
            return redirect()->route('counties.index')
                ->with('error', "Hiba történt: $errorMessage");
        } catch (\Exception $e) {
            return redirect()->route('counties.index')
                ->with('error', "Nem sikerült frissíteni");
        }
    }

    /**
     * Remove the specified county from storage.
     */
    public function destroy($id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('counties.index')
                ->with('error', "Csak bejelentkezett felhasználó törölhet");
        }

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->delete("/counties/$id");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült törölni';
                return redirect()->route('counties.index')
                    ->with('error', "Hiba: $message");
            }

            return redirect()->route('counties.index')
                ->with('success', "Megye sikeresen törölve!");
        } catch (\Exception $e) {
            return redirect()->route('counties.index')
                ->with('error', "Nem sikerült kommunikálni az API-val");
        }
    }

    /**
     * Export counties to CSV.
     */
    public function exportCsv(Request $request)
    {
        $needle = $request->get('needle');
        $url = $needle ? "counties?needle=" . urlencode($needle) : "counties";

        try {
            $response = Http::api()->get($url);

            if ($response->failed()) {
                return redirect()->route('counties.index')
                    ->with('error', "Hiba történt a lekérdezés során");
            }

            $counties = $this->getCounties($response);

            $csv = "ID,Név\n";
            foreach ($counties as $county) {
                $csv .= "{$county->id},{$county->name}\n";
            }

            return response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="counties.csv"');
        } catch (\Exception $e) {
            return redirect()->route('counties.index')
                ->with('error', "CSV export sikertelen");
        }
    }

    /**
     * Export counties to PDF.
     */
    public function exportPdf(Request $request)
    {
        $needle = $request->get('needle');
        $url = $needle ? "counties?needle=" . urlencode($needle) : "counties";

        try {
            $response = Http::api()->get($url);

            if ($response->failed()) {
                return redirect()->route('counties.index')
                    ->with('error', "Hiba történt a lekérdezés során");
            }

            $counties = $this->getCounties($response);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.counties', ['entities' => $counties]);
            return $pdf->download('counties.pdf');
        } catch (\Exception $e) {
            return redirect()->route('counties.index')
                ->with('error', "PDF export sikertelen");
        }
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
     * Helper: Extract single county from API response.
     */
    private function getCounty($response)
    {
        $responseBody = json_decode($response->body(), false);
        
        // If response is an object directly (single county)
        if (isset($responseBody->id) && isset($responseBody->name)) {
            return $responseBody;
        }
        
        // If response has data.county structure
        $data = $responseBody->data ?? null;
        if (!empty($data) && isset($data->county)) {
            return $data->county;
        }
        
        // If response has county directly
        if (isset($responseBody->county)) {
            return $responseBody->county;
        }
        
        return (object)[];
    }
}
