<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use JWTAuth;

class TaskController extends Controller
{
    function __construct()
    {
        //set [Customers] as the model that will be used for this class
        \Config::set('auth.providers.users.model', \App\Models\Customers::class);
    }
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
    public function delete($id_todolist, Request $request)
    {
        $currentUser = JWTAuth::user()->id;
        if (Tasks::where('id', $id_todolist)->exists()) {
            $task = Tasks::find($id_todolist);
            if ($task->is_deleted == 1){
                $status = "failed";
                $message = "Task sudah dihapus";
                return response()->json(compact('status', 'message'), 404);
            }
            if($task->customer_id == $currentUser){
                $sekarang = Carbon::now();
                $task->deleted_at = $sekarang;
                $task->is_deleted = 1;
                $task->save();
                
                // Success Response
                $status = "success";
                $message = "Data berhasil dihapus";
                return response()->json(compact('status', 'message'),201);
                
                // Customer does not own the task
                } else{
                    $status = "failed";
                    $message = "Anda tidak memiliki task tersebut";
                    return response()->json(compact('status', 'message'), 403);
                }
            // Task with that that id  does not exist
          } else {
              $status = "failed";
              $message = "Data tidak ditemukan";
              return response()->json(compact('status', 'message'), 404);
          }
    }
}