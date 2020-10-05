<?php

namespace App\Http\Controllers;

use App\ToDo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ToDoController extends Controller
{
    public function index(Request $request)
    {
        $list = ToDo::select();
        $list = $list->where('id_user', Auth::user()->id);
        /*if(isset($_GET['sortColumn']) && isset($_GET['sortDirection']))
            $list = $list->orderBy($_GET['sortColumn'], $_GET['sortDirection']==='ascending'?'asc':'desc');
        */
        if(isset($_GET['findText'])){
            $list = $list->where('title', 'like', '%'.$_GET['findText'].'%');
            $list = $list->orWhere('description', 'like', '%'.$_GET['findText'].'%');
        } 
        $list = $list->paginate(100);

        return response()->json(
            $list
            , 200);
    
    }

    public function item(Request $request, $id)
    {
        //$groups = isset($_GET['groups'])?json_decode($_GET['groups'], false):[0];
        $item = ToDo::find($id);

        return response()->json(
            $item
            , 200);
    
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:191',
            'description' => 'required|max:191'
        ], [
            'title.required' => 'Заголовок не может быть пустым.',
            'title.max' => 'Длина заголовка не более 191 символа.',
            'description.required' => 'Описание не может быть пустым.',
            'description.max' => 'Длина описания не более 191 символа.',
        ]);

        if ($validator->fails()) {
            return response()->json(
                ["list" => $validator->errors()->all()]
                , 422);
        }

        $item = new ToDo;
        $item->title = $request->all()['title'];
        $item->description = $request->all()['description'];
        $item->id_user = Auth::user()->id;
        $item->save();

        return response()->json(
            $item
            , 200);
        }
    

        public function update(Request $request, $id)
        {
            $item = ToDo::find($id);
    
            $validator = Validator::make($request->all(), [
                'title' => 'required|max:191',
                'description' => 'required|max:191'
            ], [
                'title.required' => 'Заголовок не может быть пустым.',
                'title.max' => 'Длина заголовка не более 191 символа.',
                'description.required' => 'Описание не может быть пустым.',
                'description.max' => 'Длина описания не более 191 символа.',
            ]);
    
            if ($validator->fails()) {
                return response()->json(
                    ["list" => $validator->errors()->all()]
                    , 422);
            }

            $item->title = $request->all()['title'];
            $item->description = $request->all()['description'];
            $item->done = $request->all()['done'];
            $item->save();
    
            return response()->json(
                $item
                , 200);
        }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ToDo  $toDo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = ToDo::find($id);
        if($item->id_user==Auth::user()->id){
            $item->delete();

            return response()->json(
                "success"
                , 200);
        }
        return response()->json(
            ['message'=>"Не достаточно прав"]
            , 403);
    }

}
