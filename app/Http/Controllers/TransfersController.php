<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Array_;

class TransfersController extends Controller
{
    public function show()
    {
        GlobalVariables::$SideMenuSelected = "Transfers";

        if (isset($_COOKIE['SortBy']))
        {
            if ($_COOKIE['SortBy'] == 'All')
            {
                $MyArray = DB::table('playertransfers')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'New')
            {
                $MyArray = DB::table('playertransfers')->select()->where('Closed', '=', '0')->Where('IsApproved', '=', '0')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            } else if ($_COOKIE['SortBy'] == 'Closed')
            {
                $MyArray = DB::table('playertransfers')->select()->where('Closed', '=', '1')->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
            }

        } else
        {
            $MyArray = DB::table('playertransfers')->select()->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);
        }

        $GetReady = $this->GetReady($MyArray);

        $TableView =  (string)view('reusable/transfersTable', ['MyArray' => $GetReady, 'iSFromSearch' => false]);

        return view('transfers', ['TableView' => $TableView] );
    }

    public function GetReady($MyArray)
    {
        for($y = 0; $y<count($MyArray); $y++)
        {
            $PlayerId = $MyArray[$y]->PlayerId;

            $Player = DB::table('players')->where('Id', '=', $PlayerId)->first();
            $MyArray[$y]->PlayerName = $Player->FirstName . ' ' . $Player->LastName;


            $FromTeamId = $MyArray[$y]->FromTeamId;

            $FromTeam = DB::table('teams')->where('Id', '=', $FromTeamId)->first();

            if(app()->getLocale() == 'en')
            {
                $MyArray[$y]->FromTeamName = $FromTeam->NameEn;

            } else
            {
                $MyArray[$y]->FromTeamName = $FromTeam->NameAr;
            }

            $ToTeamId = $MyArray[$y]->ToTeamId;

            $ToTeam = DB::table('teams')->where('Id', '=', $ToTeamId)->first();

            if(app()->getLocale() == 'en')
            {
                $MyArray[$y]->ToTeamName = $ToTeam->NameEn;

            } else
            {
                $MyArray[$y]->ToTeamName = $ToTeam->NameAr;
            }

            $PaymentId = $MyArray[$y]->PaymentId;

            if($PaymentId == null)
            {
                $MyArray[$y]->PaymentStatus = __('messages.NotPaid');

            } else
            {
                $PaymentInfo = DB::table('payments')->where('Id', '=', $PaymentId)->first();

                if($PaymentInfo->PaymentStatus == 'Done')
                {
                    $MyArray[$y]->PaymentStatus = __('messages.Paid');

                } else
                {
                    $MyArray[$y]->PaymentStatus = __('messages.PaymentPending');
                }
            }

            if($MyArray[$y]->IsApproved == '1')
            {
                $MyArray[$y]->ApprovedStatus = __('messages.Approved');

            } else
            {
                $MyArray[$y]->ApprovedStatus = __('messages.ApprovePending');
            }
        }

        return $MyArray;
    }

    public function Searching()
    {
        if (isset($_GET['SearchText']))
        {
            $MyArray = DB::table('playertransfers')->select()
                ->where('PlayerId', '=', $_GET['SearchText'])
                ->orderByRaw('Id DESC')->paginate(PAGINATION_COUNT);

            $GetReady = $this->GetReady($MyArray);

            return (string)view('reusable/transfersTable', ['MyArray' => $GetReady, 'iSFromSearch' => true]);
        }
    }
}
