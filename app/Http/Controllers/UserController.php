<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
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

    public function create_substant_device_admin(Request $request, $id)
    {
        $request->validate([
            'phone' => 'required|string|max:11',
        ]);
        $user = $request->user();
        if ($user->phone === $request->phone) {
            return response()->json(['message' => 'شما نمی‌توانید شماره خودتان را به عنوان مدیر دیگری اضافه کنید.'], 400);
        }
        $device = $user->devices()->where('device_uuid', $id)->firstOrFail();
        if ($device->users()->where('phone', $request->phone)->exists()) {
            return response()->json(['message' => 'این شماره تلفن قبلا در این دستگاه ثبت شده است.'], 400);
        }
        if ($device->pivot->role !== 1) {
            return response()->json([
                'message' => 'شما مجاز به افزودن کاربر جدید نیستید.'
            ], 403);
        }
        $countOfDeviceUsers = count($device->users()->get());
        if ($countOfDeviceUsers <= 2) {
            $newUser = User::where(['phone' => $request->phone])->first();
            $message = "کاربر موردنظر با شماره تلفن $request->phone به مدیران دستگاه اضافه شد.";
            if ($newUser) {
                $device->users()->save($newUser, ['role' => $countOfDeviceUsers + 1]);
                $device['countOfDeviceUsers'] = $countOfDeviceUsers + 1;
                return response()->json(['message' => $message, 'device' => $device]);
            } else {
                $newUser = new User();
                $newUser->phone = $request->phone;
                $newUser->is_active = false;
                $newUser->save();
                $device->users()->save($newUser, ['role' => $countOfDeviceUsers + 1]);
                $device['countOfDeviceUsers'] = $countOfDeviceUsers + 1;
                return response()->json(['message' => $message, 'device' => $device]);
            }
        } else {
            return response()->json(['message' => 'تعداد مدیران ثبت شده برای این دستگاه به حداکثر رسیده است.'], 400);
        }
    }

    public function remove_substant_device_admin(Request $request, $id)
    {
        $request->validate([
            'phone' => 'required|string|max:11',
        ]);
        $user = $request->user();
        if ($user->phone === $request->phone) {
            return response()->json(['message' => 'شما نمی‌توانید شماره خودتان را حذف کنید. از گزینه بازگشت به تنظیمات کارخانه استفاده کنید.'], 400);
        }
        $device = $user->devices()->where('device_uuid', $id)->firstOrFail();
        if ($device->pivot->role !== 1) {
            return response()->json([
                'message' => 'شما مجاز به حذف مدیر نیستید.'
            ], 403);
        }
        if ($device->users()->where('phone', $request->phone)->exists()) {
            $user_to_removed = $device->users()->where('phone', $request->phone)->first();
            $device->users()->detach($user_to_removed);
            $device['countOfDeviceUsers'] = count($device->users()->get());
            return response()->json(['message' => 'کاربر موردنظر با موفقیت از مدیران دستگاه حذف شد.', 'device' => $device]);
        } else {
            return response()->json(['message' => 'کاربر موردنظر در این دستگاه ثبت نشده است.'], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'fullname' => 'required|regex:/^[\x{0600}-\x{06ee}\s]+$/u|string|max:255',
            'city' => 'required|regex:/^[\x{0600}-\x{06ee}\s]+$/u|string|max:255',
            'email' => [
                'nullable',
                'email',
                Rule::unique('users')->ignore($user),
            ],
        ]);
        $user->update(
            $request->only(['fullname', 'city', 'email'])
        );
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'message' => __('auth.updated')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
