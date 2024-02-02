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

    public function show($id)
    {
        // This method will be used for more detail of one courier
        $courier = Courier::findOrFail($id);
        return view('courier', compact('courier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Courier $courier)
    {
        return view('edit', [
            'courier' => $courier,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Courier $courier)
    {
        // Make the rules for validation
        $rules = ([
            'name' => 'required|max:255',
            'photo' => 'image|file|max:1024',
            'address' => 'required|string|max:255'
        ]);

        // Check Condition if user change the driver license
        if ($request->driver_license != $courier->driver_license) {
            $rules['driver_license'] = 'alpha_num|min:14|max:14|unique:couriers,driver_license';
        }
        // Check Condition if user change the phone
        if ($request->phone != $courier->phone) {
            $rules['phone'] = 'alpha_num|min:11|max:14|unique:couriers,phone';
        }

        //  Run The Validator
        $data = $request->validate($rules);

        // If User Upload a new photo
        if ($request->file('photo')) {
            // Check and search the Old Photo in storage
            if ($request->oldPhoto) {
                // if the old photo has match with the request, delete it from storage
                Storage::delete($request->oldPhoto);
            }
            // save the photo in storage
            $data['photo'] = $request->file('photo')->store('public/driver-photos');
        }

        // Run the method update with id of courier
        Courier::where('id', $courier->id)->update($data);
        // Redirect
        return redirect('/couriers')->with('success', 'Kurir telah berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Courier $courier)
    {
        //
    }
}
