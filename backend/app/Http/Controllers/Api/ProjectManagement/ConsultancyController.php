<?php

namespace App\Http\Controllers\Api\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\Consultancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConsultancyController extends Controller
{
    /**
     * Display a listing of the consultancies.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $consultancies = Consultancy::all();
        return response()->json($consultancies);
    }

    /**
     * Store a newly created consultancy in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'consultancy_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email_address' => 'required|email|max:255',
            'company_address' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $consultancy = Consultancy::create($request->all());

        return response()->json($consultancy, 201); // 201 Created
    }

    /**
     * Display the specified consultancy.
     *
     * @param  \App\Models\Consultancy  $consultancy
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Consultancy $consultancy)
    {
        return response()->json($consultancy);
    }

    /**
     * Update the specified consultancy in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Consultancy  $consultancy
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Consultancy $consultancy)
    {
        $validator = Validator::make($request->all(), [
            'consultancy_name' => 'string|max:255',
            'phone_number' => 'string|max:20',
            'email_address' => 'email|max:255',
            'company_address' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $consultancy->update($request->all());

        return response()->json($consultancy, 200); // 200 OK
    }

    /**
     * Remove the specified consultancy from storage.
     *
     * @param  \App\Models\Consultancy  $consultancy
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Consultancy $consultancy)
    {
        $consultancy->delete();

        return response()->json(null, 204); // 204 No Content
    }
}