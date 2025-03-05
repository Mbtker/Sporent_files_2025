<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Nette\Schema\Context;

class OrganizersController extends Controller
{
    public function show()
    {
        GlobalVariables::$SideMenuSelected = "Organizers";

        if (isset($_COOKIE['SortBy']))
        {
            if ($_COOKIE['SortBy'] == 'All')
            {
                $MyArray = DB::table('organizers')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Active')
            {
                $MyArray = DB::table('organizers')->select()->where('Status', '=', '1')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Inactive')
            {
                $MyArray = DB::table('organizers')->select()->where('Status', '=', '0')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
            }

        } else
        {
            $MyArray = DB::table('organizers')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
        }

        $TableView =  (string)view('reusable/organizersTable', ['MyArray' => $MyArray, 'iSFromSearch' => false]);

        return view('organizers', ['TableView' => $TableView]);
    }

    public function editOrganizers(Request $request)
    {
        $CheckPhone = HelperFun::CheckIfPhoneAlreadyExists('organizers', $request->Phone);

        $iSExistsInThisTable = DB::table('organizers')->where('Phone', '=', $request->Phone)->where('Id', '!=', $request->UserId)->first();

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
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_organizers', 1);
            }
        }

        $CheckEmail = HelperFun::CheckIfEmailAlreadyExists('organizers', $request->Email);

        $CheckEmailInThisTable = DB::table('organizers')->where('Email', '=', $request->Email)->where('Id', '!=', $request->UserId)->first();

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
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_organizers', 1);
            }
        }

        $Data = [
            'Name' => $request->Name,
            'OrganizeCategory' => $request->Category,
            'Phone' => $request->Phone,
            'Email' => $request->Email,
            'Location' => $request->Location,
            'Status' => $request->Status
        ];

        DB::table('organizers')
            ->where('Id', $request->UserId)
            ->update($Data);

        return redirect()->back()->with('error_edit_organizers', 3);
    }

    public function Searching()
    {
        if (isset($_GET['SearchText']))
        {
            $MyArray = DB::table('organizers')->select()
                ->where('Id', '=', $_GET['SearchText'])
                ->orWhere('Name', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Phone', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Email', 'like', '%'.$_GET['SearchText'].'%')
                ->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            return (string)view('reusable/organizersTable', ['MyArray' => $MyArray, 'iSFromSearch' => true]);
        }
    }

    public function Details()
    {
        if (isset($_COOKIE['OrganizerId']))
        {
            $OrganizerId = $_COOKIE['OrganizerId'];

            $Organizer = DB::table('organizers')->find($OrganizerId);

            GlobalVariables::$SideMenuSelected = "Organizers";

            return view('Details/organizersDetail', ['User' => $Organizer]);
        }
    }

    public function GetOrganizersInfo()
    {
        if (isset($_GET['Id']))
        {
            $Id = $_GET['Id'];

            $Info = DB::table('organizers')->where('Id', '=', $Id)->first();

            return $Info;
        }
    }
}
