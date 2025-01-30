<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $faceData = json_decode($request->input('face_data'), true);
        $users = User::all();

        foreach ($users as $user) {
            $storedFaceData = json_decode($user->face_data, true);
            $distance = $this->euclideanDistance($faceData, $storedFaceData);

            if ($distance < 0.6) { // Adjust the threshold as needed
                Auth::login($user);
                return response()->json(['success' => true, 'redirect' => route('home')]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Face not recognized.']);
    }

    private function euclideanDistance($arr1, $arr2)
    {
        return sqrt(array_sum(array_map(function ($a, $b) {
            return pow($a - $b, 2);
        }, $arr1, $arr2)));
    }
}
