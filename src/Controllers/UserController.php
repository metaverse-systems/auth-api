<?php

namespace MetaverseSystems\AuthApi\Controllers;

use Illuminate\Http\Request;
use MetaverseSystems\AuthApi\Models\User;
use Auth;
use Validator;
use Illuminate\Support\Facades\Log;

class UserController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->can("list users"))
        {
            return response()->json(["message"=>"No permission."], 403);
        }

        return User::withTrashed()->get();
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
        if(!Auth::user()->can("create users"))
        {   
            return response()->json(["message"=>"No permission."], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        event(new Registered($user));

        return $user;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!Auth::user()->can("show users") && Auth::id() != $id)
        {   
            return response()->json(["message"=>"No permission."], 403);
        }
        return User::withTrashed()->where("id", $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!Auth::user()->can("edit users") && Auth::id() != $id)
        {   
            return response()->json(["message"=>"No permission."], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        $user = User::where("id", $id)->first();

        $input = $request->all();
        $user->update($input);

        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!Auth::user()->can("delete users") && Auth::id() != $id)
        {   
            return response()->json(["message"=>"No permission."], 403);
        }
        User::where("id", $id)->delete();
        return response()->json(["message"=>"User deleted."], 204);
    }

    public function restore($id)
    {
        if(!Auth::user()->can("restore users"))
        {   
            return response()->json(["message"=>"No permission."], 403);
        }
        User::withTrashed()->where("id", $id)->restore();
        return response()->json(["message"=>"User restored."], 204);
    }
}
