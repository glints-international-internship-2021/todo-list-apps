<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;

class TaskController extends Controller
{
    
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);
        $currentUser = JWTAuth::user()->id;
        if ($validator->fails()) {

            $status = "failed";
            $message = "gagal melakukan input task";
            $error = $validator->errors();
            return response()->json(compact('status', 'message', 'error'), 400);
        }
        $task = Tasks::create([
            'title' => $request->get('title'),
            'image' => 'temp',
            
        ]);
        
        $status = "success";
        $message = "Data berhasil disimpan";
        
        return response()->json(compact('status', 'message'), 200);
    }
}