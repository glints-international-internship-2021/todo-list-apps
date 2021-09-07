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
        // validating POST parameter, title required 
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        //storing token ID
        $currentUser = JWTAuth::user()->id;

        //if validation fails, return failed message
        if ($validator->fails()) {

            $status = "failed";
            $message = "gagal melakukan input task";
            $error = $validator->errors();
            return response()->json(compact('status', 'message', 'error'), 400);
        }

        //if validation succeeds, return success message
        $task = Tasks::create([
            'title' => $request->get('title'),
            'image' => $request->get('image'), 
            'customer_id' => $currentUser,
        ]);
        $status = "success";
        $message = "Data berhasil disimpan";
        return response()->json(compact('status', 'message'), 201);
    }
}