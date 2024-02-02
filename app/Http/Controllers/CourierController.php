<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourierRequest;
use App\Http\Requests\UpdateCourierRequest;
use App\Models\Courier;

class CourierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check condition if Couriers Records is empty
        if (Courier::all()->isEmpty()) {
            // Return to Json 404 and send message
            return response()->json(['message' => 'Data Masih Kosong!'], 404);
        } else {
            // Get all couriers data from database and sort by name ASC
            $couriers = Courier::orderBy('name')->simplePaginate(10);
            // Return json with status code 200 and data
            return response()->json($couriers, 200);
        }
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
    public function store(StoreCourierRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Courier $courier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Courier $courier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourierRequest $request, Courier $courier)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Courier $courier)
    {
        //
    }
}
