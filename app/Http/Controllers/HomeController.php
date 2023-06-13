<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        return inertia('Modules/Home/Index');
    }

    public function update(Request $request){

        $validatedData = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:9|confirmed',
            'password_confirmation' => 'same:password',
        ]);

        $id = ($request->id) ? $request->id : \Auth::user()->id;

        User::find($id)->update(['password'=> \Hash::make($request->input('password'))]);

        return back()->with([
            'message' => 'Password Changed',
            'data' => 'wew',
            'type' => 'bxs-check-circle'
        ]); 
        
    }
}
