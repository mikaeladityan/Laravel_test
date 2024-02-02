<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Courier;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreCourierRequest;
use App\Http\Requests\UpdateCourierRequest;

class CourierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check condition if Couriers Records is empty
        if (Courier::all()->isEmpty()) {
            // Return to Json 404 and send message
            return response()->json(['message' => 'Data Masih Kosong!'], 404);
        } else {
            // Get all couriers data from database and sort by name ASC
            $couriers = Courier::orderBy('name')->simplePaginate(10);

            // Check if user has request sort
            if ($request->has('sort')) {
                // Get data Courier filter by created at with DESC and make simple paginate
                $couriers = Courier::orderBy('created_at', 'desc')->simplePaginate(10);
            }

            // Check condition if user has search with request by name
            if ($request->has('search')) {
                // Search in database for name field using like operator
                $couriers = Courier::where('name', 'like', '%' . $request->get('search') . '%')->simplePaginate(10);
            }

            // Check if there is a level request, then filter results
            if ($request->has('level')) {
                $couriers = Courier::where('level', 'like', '%' . $request->get('level') . '%')->simplePaginate(10);
            }

            // Return json with status code 200 and data
            return response()->json($couriers, 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Before create new Courier, you must make the validation
        $rules = $request->validate([
            'name' => 'required|max:255',
            'driver_license' => 'alpha_num|min:13|max:15|unique:couriers,driver_license',
            'photo' => 'image|file|max:1024',
            'phone' => 'alpha_num|min:11|max:14|unique:couriers,phone',
            'address' => 'required|string|max:255'
        ]);

        // Check condition if user has upload the photo or not
        if ($request->file('photo')) {
            $rules['photo'] = Storage::putFile('/driver-photos', $request->file('photo'));
        }

        // after all rules and get the photo from request, this steps is to create in database
        Courier::create($rules);
        return back();
    }

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
