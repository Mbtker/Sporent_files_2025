<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExercisesController extends Controller
{
    public function show()
    {
        GlobalVariables::$SideMenuSelected = "Exercises";

        if (isset($_COOKIE['SortBy']))
        {
            if ($_COOKIE['SortBy'] == 'All')
            {
                $MyArray = DB::table('exercises')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Active')
            {
                $MyArray = DB::table('exercises')->select()->where('Status', '=', '1')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Inactive')
            {
                $MyArray = DB::table('exercises')->select()->where('Status', '=', '0')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
            }

        } else
        {
            $MyArray = DB::table('exercises')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
        }

        for($y = 0; $y<count($MyArray); $y++)
        {
            $MyArray[$y] = $this->GetReady($MyArray[$y]);
        }

        $TableView =  (string)view('reusable/exercisesTable', ['MyArray' => $MyArray, 'iSFromSearch' => false]);

        return view('exercises', ['TableView' => $TableView] );
    }

    public function GetReady($MyArray)
    {
        $StadiumId = $MyArray->StadiumId;

        if($StadiumId != null)
        {
            $Stadium = DB::table('stadiums')->where('Id', '=', $StadiumId)->first();
            $MyArray->StadiumName = $Stadium->Name;

        } else
        {
            $MyArray->StadiumName = '-';
        }

        $CreatById = $MyArray->CreatById;

        if($CreatById != null)
        {
            $player = DB::table('players')->where('Id', '=', $CreatById)->first();
            $MyArray->CreatByName = $player->Name;

        } else
        {
            $MyArray->CreatByName = '-';
        }

        return $MyArray;
    }

    public function edit(Request $request)
    {
        $CheckTopic = DB::table('exercises')->where('Topic', '=', $request->Topic)->where('Id', '!=', $request->Id)->first();

        if($CheckTopic)
        {
            $rules = [
                'Topic' => 'max:2',
            ];

            $messages = [
                'Topic.max' => __('messages.ExerciseTopicIsExists'),
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator -> fails())
            {
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_exercise', 1);
            }
        }

        $Data = [
            'Topic' => $request->Topic,
            'ExerciseType' => $request->ExerciseType,
            'Location' => $request->Location,
            'Fee' => $request->Fee,
            'ExerciseDate' => $request->ExerciseDate,
            'Status' => $request->Status,
        ];

        DB::table('exercises')
            ->where('Id', $request->Id)
            ->update($Data);

        return redirect()->back()->with('error_edit_exercise', 3);
    }


    public function Searching()
    {
        if (isset($_GET['SearchText']))
        {
            $MyArray = DB::table('exercises')->select()
                ->where('Id', '=', $_GET['SearchText'])
                ->orWhere('Topic', 'like', '%'.$_GET['SearchText'].'%')
                ->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            $GetReady = $this->GetReady($MyArray);

            return (string)view('reusable/exercisesTable', ['MyArray' => $GetReady, 'iSFromSearch' => true]);
        }
    }

    public function Details()
    {
        if (isset($_COOKIE['ExerciseId']))
        {
            $ExerciseId= $_COOKIE['ExerciseId'];

            $Exercise = DB::table('exercises')->find($ExerciseId);

            GlobalVariables::$SideMenuSelected = "Exercises";

            $Exercise = $this->GetReady($Exercise);

            return view('Details/exerciseDetail', ['MyArray' => $Exercise]);
        }
    }

    public function GetExerciseInfo()
    {
        if (isset($_GET['Id']))
        {
            $Id = $_GET['Id'];

            $Info = DB::table('exercises')->where('Id', '=', $Id)->first();

            return $Info;
        }
    }
}
