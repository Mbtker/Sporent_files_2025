<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SupervisorsController extends Controller
{

    public function show()
    {
        GlobalVariables::$SideMenuSelected = "Supervisors";

        if (isset($_COOKIE['SortBy']))
        {
            if ($_COOKIE['SortBy'] == 'All')
            {
                $MyArray = DB::table('supervisors')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Active')
            {
                $MyArray = DB::table('supervisors')->select()->where('Status', '=', '1')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Inactive')
            {
                $MyArray = DB::table('supervisors')->select()->where('Status', '=', '0')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
            }

        } else
        {
            $MyArray = DB::table('supervisors')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
        }

        $TableView =  (string)view('reusable/supervisorsTable', ['MyArray' => $MyArray, 'iSFromSearch' => false]);

        return view('supervisors', ['TableView' => $TableView] );
    }

    public function editSupervisor(Request $request)
    {
        $CheckPhone = DB::table('supervisors')->where('Phone', '=', $request->Phone)->where('Id', '!=', $request->UserId)->first();

        if($CheckPhone)
        {
            $rules = [
                'Phone' => [Rule::unique('supervisors')->where('Phone', $request->Phone)],
            ];

            $messages = [
                'Phone.unique' => __('messages.PhoneAlreadyExists'),
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator -> fails())
            {
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_supervisor', 1);
            }
        }


        $CheckEmail = DB::table('supervisors')->where('Email', '=', $request->Email)->where('Id', '!=', $request->UserId)->first();

        if($CheckEmail)
        {
            $rules = [
                'Email' => [Rule::unique('supervisors')->where('Email', $request->Email)],
            ];

            $messages = [
                'Email.unique' => __('messages.EmailAlreadyExists'),
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator -> fails())
            {
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_supervisor', 1);
            }
        }

        $Data = [
            'Name' => $request->Name,
            'Phone' => $request->Phone,
            'Location' => $request->Location,
            'AreaRange' => $request->AreaRange,
            'Status' => $request->Status
        ];

        DB::table('supervisors')
            ->where('Id', $request->UserId)
            ->update($Data);

        return redirect()->back()->with('error_edit_supervisor', 3);
    }

    public function Searching()
    {
        if (isset($_GET['SearchText']))
        {
            $MyArray = DB::table('supervisors')->select()
                ->where('Id', '=', $_GET['SearchText'])
                ->orWhere('Name', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Phone', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Email', 'like', '%'.$_GET['SearchText'].'%')
                ->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            return (string)view('reusable/supervisorsTable', ['MyArray' => $MyArray, 'iSFromSearch' => true]);
        }
    }

    public function Details()
    {
        if (isset($_COOKIE['SupervisorId']))
        {
            $SupervisorId = $_COOKIE['SupervisorId'];

            $Supervisor = DB::table('supervisors')->find($SupervisorId);

            GlobalVariables::$SideMenuSelected = "Supervisors";

            return view('Details/supervisorDetail', ['User' => $Supervisor]);
        }

    }

    public function GetSupervisorsInfo()
    {
        if (isset($_GET['Id']))
        {
            $Id = $_GET['Id'];

            $Info = DB::table('supervisors')->where('Id', '=', $Id)->first();

            return $Info;
        }
    }
}
