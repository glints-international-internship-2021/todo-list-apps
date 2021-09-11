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
        //if validation fails, return failed message
        if ($validator->fails()) {

            $status = "failed";
            $message = "gagal melakukan input task";
            $error = $validator->errors();
            return response()->json(compact('status', 'message', 'error'), 400);
        }
        // saving image: declaring image path as null and change it to its path when image exists
        $img_path = null;
        if($request->image != null){
            $img_response = $this->img_save($request);
            // echo $img_response->getData()->status;
            if($img_response->getData()->status =="failed")
            {
                return $img_response;
            }
            $img_path = $img_response->getData()->path;
        }
        //storing token ID
        $currentUser = JWTAuth::user()->id;
        //Saving input and give success response
        $task = Tasks::create([
            'title' => $request->get('title'),
            'image' => $img_path,
            'customer_id' => $currentUser,
        ]);
        $status = "success";
        $message = "Data berhasil disimpan";
        return response()->json(compact('status', 'message'), 201);
    }
    public function view()
    {
        $currentUser = JWTAuth::user()->id;
        // Validating whether $currentUser is null
        if (is_null($currentUser)){
            $status = "failed";
            $message = "Customer tidak ditemukan";
            return response()->json(compact('status', 'message'), 404);
        }
        // Selecting the customer's tasks'id and title] where 'is_deleted' is false
        $data = Tasks::where([['customer_id', $currentUser],['is_deleted', 0]])->select('id', 'title')->paginate()->items();
        // Response Message
        $status = "success";
        $message = "Data berhasil didapatkan";
        return response()->json(compact('status', 'message','data'), 200);
    }
    public function delete($id_todolist, Request $request)
    {
        $currentUser = JWTAuth::user()->id;
        // Validating whether $currentUser is null
        if (is_null($currentUser)){
            $status = "failed";
            $message = "Customer tidak ditemukan";
            return response()->json(compact('status', 'message'), 404);
        }
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
    public function edit($id_todolist, Request $request)
    {
        $currentUser = JWTAuth::user()->id;
        // Validating whether $currentUser is null
        if (is_null($currentUser)){
            $status = "failed";
            $message = "Customer tidak ditemukan";
            return response()->json(compact('status', 'message'), 404);
        }
        if (Tasks::where('id', $id_todolist)->exists()) {
            $task = Tasks::find($id_todolist);
            // echo $task->customer_id
            
            if ($task->is_deleted == 1){
                $status = "failed";
                $message = "Task sudah dihapus";
                return response()->json(compact('status', 'message'), 404);
            }
            // Updating image when $request->image not null 
            $img_path = $task->image;
            if($request->image != null){
                $img_response = $this->img_save($request);
                // echo $img_response->getData()->status;
                if($img_response->getData()->status =="failed")
                {
                    return $img_response;
                }
                $img_path = $img_response->getData()->path;
            }
            if($task->customer_id == $currentUser){
                $task->title = $request->title;
                $sekarang = Carbon::now();
                $task->image = $img_path;
                $task->updated_at = $sekarang;
                
                $task->save();
                
                // Success Response
                $status = "success";
                $message = "Data berhasil diupdate";
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
    public function img_save(Request $request)
    {  
        // If image is not input, return to previous function
        $validator = Validator::make($request->all(), [
            'image' => 'image|mimes:png,jpeg,jpg,gif,svg',
        ]);
        // if validation failed, return failed status and corresponded errors
        if ($validator->fails()) {
            $status = "failed";
            $message = "failed to upload image";
            $error = $validator->errors();
            return response()->json(compact('status', 'message', 'error'),400);
        }
        $path = $request->file('image')->store('public/images');
        $status = "success";
        $message = "Image Uploaded";
        return response()->json(compact('status', 'path'),201);
    }
}
