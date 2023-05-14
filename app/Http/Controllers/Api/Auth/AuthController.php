<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|unique:users',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password',
            'name' => 'required|string',
            'nip' => 'required|string',
            'phone' => 'required|max:13',
            'place_of_birth' => 'required',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:MALE,FEMALE',
            'religion' => 'required|in:ISLAM,KRISTEN,KHATOLIK,BUDHA,HINDU,KONG_WU_CHU',
            'address' => 'required',
            'date_joined' => 'required|date',
            'role' => 'required|in:bod,ga,staff'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $role = Role::where('name', $request->role)->first();

            $user = User::create([
                'username' => $request->email,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $user->roles()->attach($role);
            $user->save();

            $staff = Staff::create([
                'user_uuid' => $user->uuid,
                'name' => $request->name,
                'nip' => $request->nip,
                'phone' => $request->phone,
                'place_of_birth' => $request->place_of_birth,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'religion' => $request->religion,
                'address' => $request->address,
                'is_active' => true,
                'date_joined' => $request->date_joined,
            ]);

            $tokenResult = $user->createToken('MyApp');
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            DB::commit();

            return response()->json([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString()
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['msg' => $th->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = $request->user();
            $tokenResult = $user->createToken('MyApp');
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            return response()->json([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString()
            ]);
        }

        dd($credentials);
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
