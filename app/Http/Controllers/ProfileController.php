<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // dd($user);

        // Get the authenticated user
        // return view('profile.index', ['user' => $user]);
        return Inertia::render('Profile/index', ['user' => $user]);
    }

    public function update(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();

        try {
            $user = auth()->user();

            $request->validate([
                'phone' => 'required|digits:10',
                'secondary_phone' => 'nullable|digits:10',
                'slack_username' => 'required|string',
            ]);
            // 'profile_picture' => 'nullable|image|mimes:jpg',

            $user->phone = $request->phone;
            $user->secondary_phone = $request->secondary_phone;
            $user->slack_username = $request->slack_username;

            // Delete the existing profile picture if a new one is uploaded
            if ($request->hasFile('profile_picture') && $user->profile_picture) {
                Storage::delete('public/profiles/' . $user->profile_picture);
            }

            // Upload and save the new profile picture
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/profiles/', $fileName);
                $user->profile_picture = $fileName;
            }

            $user->save();

            DB::commit();

            return redirect()->route('profile.index')->with('success', 'Profile updated successfully.');
        } 
        
        catch (Exception $e) {
            // dd($e);
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }
}
