<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StoresController extends Controller
{
    public function show()
    {
        GlobalVariables::$SideMenuSelected = "Stores";

        if (isset($_COOKIE['SortBy']))
        {
            if ($_COOKIE['SortBy'] == 'All')
            {
                $MyArray = DB::table('stores')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Active')
            {
                $MyArray = DB::table('stores')->select()->where('Status', '=', '1')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Inactive')
            {
                $MyArray = DB::table('stores')->select()->where('Status', '=', '0')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
            }

        } else
        {
            $MyArray = DB::table('stores')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
        }

        for($y = 0; $y<count($MyArray); $y++)
        {
            $MyArray[$y] = $this->GetReady($MyArray[$y]);
        }

        $TableView =  (string)view('reusable/storesTable', ['MyArray' => $MyArray, 'iSFromSearch' => false]);

        return view('stores', ['TableView' => $TableView] );
    }

    public function GetReady($MyArray)
    {
        $StoreCategoryId = $MyArray->StoreCategoryId;

        if($StoreCategoryId != null)
        {
            $StoreCategory = DB::table('storecategories')->where('Id', '=', $StoreCategoryId)->first();

            if(app()->getLocale() == 'en')
            {
                $MyArray->StoreType = $StoreCategory->NameEn;

            } else
            {
                $MyArray->StoreType = $StoreCategory->NameAr;
            }

        } else
        {
            $MyArray->StoreType = '-';
        }

        return $MyArray;
    }

    public function edit(Request $request)
    {
        $CheckPhone = HelperFun::CheckIfPhoneAlreadyExists('stores', $request->Phone);

        $iSExistsInThisTable = DB::table('stores')->where('Phone', '=', $request->Phone)->where('Id', '!=', $request->Id)->first();

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
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_store', 1);
            }
        }

        $CheckEmail = HelperFun::CheckIfEmailAlreadyExists('stores', $request->Email);

        $CheckEmailInThisTable = DB::table('stores')->where('Email', '=', $request->Email)->where('Id', '!=', $request->Id)->first();

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
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error_store', 1);
            }
        }

        $Data = [
            'Name' => $request->Name,
            'Phone' => $request->Phone,
            'Email' => $request->Email,
            'OwnerName' => $request->OwnerName,
            'OwnerPhone' => $request->OwnerPhone,
            'Location' => $request->Location,
            'Status' => $request->Status
        ];

        DB::table('stores')
            ->where('Id', $request->Id)
            ->update($Data);

        return redirect()->back()->with('error_edit_store', 3);
    }


    public function Searching()
    {
        if (isset($_GET['SearchText']))
        {
            $MyArray = DB::table('stores')->select()
                ->where('Id', '=', $_GET['SearchText'])
                ->orWhere('Name', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Phone', 'like', '%'.$_GET['SearchText'].'%')
                ->orWhere('Email', 'like', '%'.$_GET['SearchText'].'%')
                ->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            $GetReady = $this->GetReady($MyArray);

            return (string)view('reusable/storesTable', ['MyArray' => $GetReady, 'iSFromSearch' => true]);
        }
    }

    public function Details()
    {
        if (isset($_COOKIE['StoreId']))
        {
            $StoreId= $_COOKIE['StoreId'];

            $Store = DB::table('stores')->find($StoreId);

            GlobalVariables::$SideMenuSelected = "Stores";

            $Store = $this->GetReady($Store);

            return view('Details/storeDetail', ['MyArray' => $Store]);
        }
    }

    public function GetStoreInfo()
    {
        if (isset($_GET['Id']))
        {
            $Id = $_GET['Id'];

            $Info = DB::table('stores')->where('Id', '=', $Id)->first();

            return $Info;
        }
    }
}
