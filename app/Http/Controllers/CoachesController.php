<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CoachesController extends Controller
{
    public function show()
    {
        GlobalVariables::$SideMenuSelected = "Coaches";

        if (isset($_COOKIE['SortBy']))
        {
            if ($_COOKIE['SortBy'] == 'All')
            {
                $MyArray = DB::table('coaches')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Active')
            {
                $MyArray = DB::table('coaches')->select()->where('Status', '=', '1')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Inactive')
            {
                $MyArray = DB::table('coaches')->select()->where('Status', '=', '0')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
            }

        } else
        {
            $MyArray = DB::table('coaches')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
        }

        $GetReady = $this->GetReady($MyArray);

        $TableView =  (string)view('reusable/coachesTable', ['MyArray' => $GetReady, 'iSFromSearch' => false]);

        return view('Coaches', ['TableView' => $TableView] );
    }

    public function GetReady($MyArray)
    {
        for($y = 0; $y<count($MyArray); $y++)
        {
            $TeamId = $MyArray[$y]->TeamId;

            if($TeamId != null)
            {
                $Team = DB::table('teams')->where('Id', '=', $TeamId)->first();

                if(app()->getLocale() == 'en')
                {
                    $MyArray[$y]->TeamName = $Team->NameEn;

                } else
                {
                    $MyArray[$y]->TeamName = $Team->NameAr;
                }

            } else
            {
                $MyArray[$y]->TeamName = '-';
            }

            if($MyArray[$y]->EPaymentInfoId != null)
            {
                $MyArray[$y]->EPaymentAvailable =  __('messages.Available');

            } else
            {
                $MyArray[$y]->EPaymentAvailable =  __('messages.Unavailable');
            }
        }

        return $MyArray;
    }

    public function edit(Request $request)
    {
        $CheckPhone = HelperFun::CheckIfPhoneAlreadyExists('coaches', $request->Phone);

        $iSExistsInThisTable = DB::table('coaches')->where('Phone', '=', $request->Phone)->where('Id', '!=', $request->UserId)->first();

        if($CheckPhone || $iSExistsInThisTable)
        {
            $rules = [
                'Phone' => 'max:2',
            ];

            $messages = [
                'Phone.max' => __('messages.PhoneAlreadyExists'),
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator -> fails())
            {
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_coaches', 1);
            }
        }

        $CheckEmail = HelperFun::CheckIfEmailAlreadyExists('coaches', $request->Email);

        $CheckEmailInThisTable = DB::table('coaches')->where('Email', '=', $request->Email)->where('Id', '!=', $request->UserId)->first();

        if($CheckEmail || $CheckEmailInThisTable)
        {
            $rules = [
                'Email' => 'max:2',
            ];

            $messages = [
                'Email.max' => __('messages.EmailAlreadyExists'),
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator -> fails())
            {
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_coaches', 1);
            }
        }

        $Data = [
            'Name' => $request->Name,
            'Phone' => $request->Phone,
            'Email' => $request->Email,
            'Status' => $request->Status
        ];

        DB::table('coaches')
            ->where('Id', $request->UserId)
            ->update($Data);

        return redirect()->back()->with('error_edit_coaches', 3);
    }

    public function Searching()
    {
        if (isset($_GET['SearchText']))
        {
            $MyArray = DB::table('coaches')->select()
                ->where('Id', '=', $_GET['SearchText'])
                ->orWhere('Name', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Phone', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Email', 'like', '%'.$_GET['SearchText'].'%')
                ->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            $GetReady = $this->GetReady($MyArray);

            return (string)view('reusable/coachesTable', ['MyArray' => $GetReady, 'iSFromSearch' => true]);
        }
    }

    public function Details()
    {
        if (isset($_COOKIE['CoachId']))
        {
            $CoachId = $_COOKIE['CoachId'];

            $Coach = DB::table('coaches')->find($CoachId);

            GlobalVariables::$SideMenuSelected = "Coaches";

            return view('Details/coachDetail', ['User' => $Coach]);
        }
    }

    public function GetCoachInfo()
    {
        if (isset($_GET['Id']))
        {
            $Id = $_GET['Id'];

            $Info = DB::table('coaches')->where('Id', '=', $Id)->first();

            return $Info;
        }
    }

}
