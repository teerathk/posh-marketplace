<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return 'silence is the gold';
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if (Auth::attempt($request->only('email', 'password'))){
            return response()->json(Auth::user(), 200);
        }
        throw ValidationException::withMessages([
            'email' =>['The provided credentials are incorect.']
        ]);
    }
    public function logout()
    {
        Auth::logout();
        return '0';
    }

    public function users($type = null, $perpage = 25, $order_by = 'id', $order = 'desc', $status = null)
    {
        $users = User::where('user_type', $type);

        if (!empty($status)) {
            $users = $users->where('status', $status);
        }

        $users = $users->orderBy($order_by, $order)
            ->paginate($perpage);

        return $users;
    }

    public function createUser(Request $request)
    {
        // Setup the validator
        $rules = array(
            'email'         => 'required|email|unique:users|max:255',
            'first_name'    => 'required',
            'last_name'     => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        // Validate the input and return correct response
        if ($validator->fails())
        {
            return Response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ), 400); // 400 being the HTTP code for an invalid request.
        }

        if(!empty($request->input('id'))) {
            User::where('id', $request->input('id'))
                ->update($request->all());
        } else {
            User::create($request->all());
        }
        return response()->json(['message' => 'user was updated successfully.']);
    }

    public function createVendor(Request $request)
    {
        // Setup the validator
        $rules = array(
            'email'             => 'required|email|unique:users|max:255',
            'first_name'        => 'required|min:3|max:50',
            'last_name'         => 'required|min:3|max:50',
            'address'           => 'required',
            'city'              => 'required',
            'state'             => 'required',
            'phone'             => 'required|max:17',
            'company'           => 'required',
            'subscription_fee'  => 'required',
            'company'           => 'required',
            'password'          => 'required|confirmed|max:6'
        );

        $validator = Validator::make($request->all(), $rules);

        // Validate the input and return correct response
        if ($validator->fails())
        {
            return Response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ), 400); // 400 being the HTTP code for an invalid request.
        }

        if(!empty($request->input('id'))) {
            User::where('id', $request->input('id'))
                ->update($request->all());
        } else {
            User::create($request->all());
        }
        return response()->json(['message' => 'user was updated successfully.']);
    }

    public function toggleActivation($user_id)
    {
        $status = User::where('id', $user_id)->pluck('status');

        if($status[0] == 'pending') {
            $status = 'approved';
        } else {
            $status = 'pending';
        }

        User::where('id', $user_id)
            ->update(['status' => $status]);

        return response()->json(['message' => 'user status was updated successfully.']);
    }

    public function changeUserStatus($user_id, $status)
    {
        User::where('id', $user_id)
            ->update(['status' => $status]);

        return response()->json(['message' => 'user status was updated successfully.']);
    }
}
