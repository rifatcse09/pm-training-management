<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Helpers\HttpStatus;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::all();
        return response()->json([
            'success' => true,
            'data' => $countries,
        ], HttpStatus::OK);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:countries,code',
        ]);

        $country = Country::create($data);

        return response()->json([
            'success' => true,
            'data' => $country,
        ], HttpStatus::CREATED);
    }

    public function show($id)
    {
        $country = Country::find($id);

        if (!$country) {
            return response()->json([
                'success' => false,
                'error' => 'Country not found',
            ], HttpStatus::NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $country,
        ], HttpStatus::OK);
    }

    public function update(Request $request, $id)
    {
        $country = Country::find($id);

        if (!$country) {
            return response()->json([
                'success' => false,
                'error' => 'Country not found',
            ], HttpStatus::NOT_FOUND);
        }

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:10|unique:countries,code,' . $id,
        ]);

        $country->update($data);

        return response()->json([
            'success' => true,
            'data' => $country,
        ], HttpStatus::OK);
    }

    public function destroy($id)
    {
        $country = Country::find($id);

        if (!$country) {
            return response()->json([
                'success' => false,
                'error' => 'Country not found',
            ], HttpStatus::NOT_FOUND);
        }

        $country->delete();

        return response()->json([
            'success' => true,
            'data' => null,
        ], HttpStatus::NO_CONTENT);
    }
}
