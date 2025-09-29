<?php

namespace App\Providers;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\AuthException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class FirebaseAuthService
{
    public function storeFirebaseService(Request $request){
        
		if(!empty($request->serviceJson)){

			Storage::disk('local')->put('firebase/credentials.json',file_get_contents(base64_decode($request->serviceJson)));
		}
    }

    public function getFirebaseToken()
    {
        if (request()->cookie('firebase_token')) {
            return request()->cookie('firebase_token');
        }

        //custom random user id
        $uid = "2cPMOVc73PFDsuPfWZ4R";

        $firebase = (new Factory)->withServiceAccount(storage_path('app/firebase/credentials.json'))->createAuth();
        
        $firebase_token = $firebase->createCustomToken($uid)->toString();

        return response()->json(['firebase_token' => $firebase_token]);
    }
}
