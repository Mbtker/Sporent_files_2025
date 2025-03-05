<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommentatorsController extends Controller
{
    public function show()
    {
        GlobalVariables::$SideMenuSelected = "Commentators";

        if (isset($_COOKIE['SortBy']))
        {
            if ($_COOKIE['SortBy'] == 'All')
            {
                $MyArray = DB::table('commentators')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Active')
            {
                $MyArray = DB::table('commentators')->select()->where('Status', '=', '1')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Inactive')
            {
                $MyArray = DB::table('commentators')->select()->where('Status', '=', '0')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
            }

        } else
        {
            $MyArray = DB::table('commentators')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
        }


        $TableView =  (string)view('reusable/commentatorsTable', ['MyArray' => $MyArray, 'iSFromSearch' => false]);

        return view('commentators', ['TableView' => $TableView] );
    }

    public function edit(Request $request)
    {
        $CheckPhone = HelperFun::CheckIfPhoneAlreadyExists('commentators', $request->Phone);

        $iSExistsInThisTable = DB::table('commentators')->where('Phone', '=', $request->Phone)->where('Id', '!=', $request->UserId)->first();

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
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_commentators', 1);
            }
        }

        $CheckEmail = HelperFun::CheckIfEmailAlreadyExists('commentators', $request->Email);

        $CheckEmailInThisTable = DB::table('commentators')->where('Email', '=', $request->Email)->where('Id', '!=', $request->UserId)->first();

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
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_commentators', 1);
            }
        }

        $Data = [
            'Name' => $request->Name,
            'Phone' => $request->Phone,
            'Email' => $request->Email,
            'Status' => $request->Status
        ];

        DB::table('commentators')
            ->where('Id', $request->UserId)
            ->update($Data);

        return redirect()->back()->with('error_edit_commentators', 3);
    }

    public function Searching()
    {
        if (isset($_GET['SearchText']))
        {
            $MyArray = DB::table('commentators')->select()
                ->where('Id', '=', $_GET['SearchText'])
                ->orWhere('Name', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Phone', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Email', 'like', '%'.$_GET['SearchText'].'%')
                ->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            return (string)view('reusable/commentatorsTable', ['MyArray' => $MyArray, 'iSFromSearch' => true]);
        }
    }

    public function Details()
    {
        if (isset($_COOKIE['CommentatorId']))
        {
            $CommentatorId = $_COOKIE['CommentatorId'];

            $Commentator = DB::table('commentators')->find($CommentatorId);

            GlobalVariables::$SideMenuSelected = "Commentators";

            return view('Details/commentatorDetail', ['User' => $Commentator]);
        }
    }

    public function GetCommentatorInfo()
    {
        if (isset($_GET['Id']))
        {
            $Id = $_GET['Id'];

            $Info = DB::table('commentators')->where('Id', '=', $Id)->first();

            return $Info;
        }
    }

}
