<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("backend.index");
    }
    public function countRegistrationsPerMonth(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $registrationType = $request->input('registrationType');

        // Example database query (adjust according to your schema)
        $counts = DB::table($registrationType) // Ensure this is a valid table name
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
        // Fill in missing months with zero counts
        for ($month = 1; $month <= 12; $month++) {
            if (!array_key_exists($month, $counts)) {
                $counts[$month] = 0; // Fill missing months with 0
            }
        }

        ksort($counts); // Sort counts by month

        return response()->json(array_values($counts)); // Return counts as JSON
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
