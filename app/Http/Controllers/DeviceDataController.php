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
        $array = $request->all();
        $keys = array_keys($array);
        $json = json_decode($keys[0]);
        error_log(print_r($json, true));
        $deviceId = device::where('imei', $json->ID)->firstOrFail()->id;
        $deviceData = new deviceData();
        $deviceData->device_id = $deviceId;
        $deviceData->sd = $json->SD;
        $deviceData->pir = $json->PIR;
        $deviceData->ss = $json->SS;
        $deviceData->rn = $json->RN;
        $deviceData->ps = $json->PS;
        $deviceData->bv = $json->BV;
        $deviceData->aq = $json->AQ;
        $deviceData->ntc = $json->NTC;
        $deviceData->psw = $json->PSW;
        $deviceData->cs = $json->CS;
        $deviceData->te = $json->TE;
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
