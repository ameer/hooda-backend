<?php

namespace App\Http\Controllers;

use App\Models\deviceData;
use App\Http\Requests\StoredeviceDataRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UpdatedeviceDataRequest;
use App\Models\device;

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
    public function store(Request $request)
    {
        error_log(print_r($request->all(), true));
        $deviceId = device::where('imei', $request->ID)->firstOrFail()->id;
        $deviceData = new deviceData();
        $deviceData->device_id = $deviceId;
        $deviceData->sd = $request->SD;
        $deviceData->pir = $request->PIR;
        $deviceData->ss = $request->SS;
        $deviceData->rn = $request->RN;
        $deviceData->ps = $request->PS;
        $deviceData->bv = $request->BV;
        $deviceData->aq = $request->AQ;
        $deviceData->ntc = $request->NTC;
        $deviceData->psw = $request->PSW;
        $deviceData->cs = $request->CS;
        $deviceData->te = $request->TE;
        $deviceData->save();
        return response()->json(['message' => 'Device data added successfully']);
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
