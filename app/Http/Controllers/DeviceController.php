<?php

namespace App\Http\Controllers;

use App\Models\device;
use Illuminate\Http\Request;

class DeviceController extends Controller
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'deviceType' => 'required',
            'simCardNumber' => 'required|unique:devices,sim_number|max:11',
            'serialNumber' => 'required|unique:devices,imei|max:16',
        ]);
        $device = new device();
        $device->owner_id = $user->id;
        $device->type = $request->deviceType;
        $device->imei = $request->serialNumber;
        $device->sim_number = $request->simCardNumber;
        $device->location = $request->location;
        $device->save();
        return response()->json(['message' => 'Device added successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\device  $device
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $user = $request->user();

        $deviceData = device::where('owner_id', $user->id)->get();
        return response()->json($deviceData);
    }

    public function getSingleDevice(Request $request, $id)
    {
        $user = $request->user();
        $deviceData = device::where(['owner_id' => $user->id, 'id' => $id])->firstOrFail();
        return $deviceData;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\device  $device
     * @return \Illuminate\Http\Response
     */
    public function edit(device $device)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\device  $device
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, device $device)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\device  $device
     * @return \Illuminate\Http\Response
     */
    public function destroy(device $device)
    {
        //
    }
}
