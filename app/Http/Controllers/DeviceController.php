<?php

namespace App\Http\Controllers;

use App\Models\device;
use App\Models\Relation;
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
            // 'powerState' => 'required',
            'devicePassword' => 'required|max:4|min:4',
            'simCardNumber' => 'required|unique:devices,sim_number|max:11',
            'serialNumber' => 'required|unique:devices,imei|max:16',
        ]);
        $device = new device();

        $device->type = $request->deviceType;
        $device->imei = $request->serialNumber;
        // $device->ss = $request->powerState;
        $device->psw = $request->devicePassword;
        $device->sim_number = $request->simCardNumber;
        $device->nickname = $request->nickname;
        $device->location = $request->location;
        $device->save();
        $user->devices()->save($device);
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
        $devices = $user->devices()->get();
        $devicesObjectByUUIDasKey = [];
        for ($i = 0; $i < count($devices); $i++) {
            $devicesObjectByUUIDasKey[$devices[$i]->uuid] = $devices[$i];
        }
        return response()->json($devicesObjectByUUIDasKey);
    }
    public function checkDevice(Request $request)
    {
        $current_user = $request->user();
        $device = device::where('imei', $request->serialNumber)->first();
        if ($device) {
            foreach ($device->users as $user) {
                if ($user->pivot->owner_id == $current_user->id) {
                    if ($user->pivot->role == 1) {
                        return response()->json(['message' => 'شما قبلا این دستگاه را ثبت کرده‌اید.'], 409);
                        break;
                    } else if ($user->pivot->role == 2 || $user->pivot->role == 3) {
                        $role = $user->pivot->role;
                        return response()->json(['message' => "شما توسط مدیر اصلی به عنوان مدیر $role این دستگاه ثبت شده‌اید.", 'device' => $device], 201);
                        break;
                    } else {
                        continue;
                    }
                }
            }
            return response()->json(['message' => 'این دستگاه توسط شخص دیگری ثبت شده است.'], 400);
        } else {
            return response()->json(['message' => 'سریال وارد شده صحیح است.'], 200);
        }
    }
    public function getSingleDevice(Request $request, $id)
    {
        $user = $request->user();
        $deviceData = $user->devices()->where('device_uuid', $id)->firstOrFail();
        return $deviceData;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\device  $device
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\device  $device
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $result = $user->devices()->where('device_uuid', $id)->update([
            'nickname' => $request->nickname,
            'location' => $request->location,
        ]);
        if ($result) {
            return response()->json(['message' => 'اطلاعات دستگاه با موفقیت بروزرسانی شد.', 'device' => $user->devices()->where('device_uuid', $id)->first()], 200);
        } else {
            return response()->json(['message' => 'اطلاعات دستگاه بروز نشد.'], 404);
        }
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
