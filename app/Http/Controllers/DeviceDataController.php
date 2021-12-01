<?php

namespace App\Http\Controllers;

use App\Models\deviceData;
use App\Http\Requests\StoredeviceDataRequest;
use App\Http\Requests\UpdatedeviceDataRequest;

class DeviceDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoredeviceDataRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoredeviceDataRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\deviceData  $deviceData
     * @return \Illuminate\Http\Response
     */
    public function show(deviceData $deviceData)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\deviceData  $deviceData
     * @return \Illuminate\Http\Response
     */
    public function edit(deviceData $deviceData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatedeviceDataRequest  $request
     * @param  \App\Models\deviceData  $deviceData
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatedeviceDataRequest $request, deviceData $deviceData)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\deviceData  $deviceData
     * @return \Illuminate\Http\Response
     */
    public function destroy(deviceData $deviceData)
    {
        //
    }
}
