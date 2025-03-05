<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Traits\GeneralTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransfersController extends Controller
{
    use GeneralTrait;

    public function GetPlayersTransfer(Request $request)
    {
        $GetLatitude = $request->Latitude;
        $GetLongitude = $request->Longitude;

        $MyArray = DB::table('playertransfers')->where('Closed', '=', 0)->orderByRaw('Id DESC')->get();

        for($y = 0; $y<count($MyArray); $y++)
        {
            $PlayerId = $MyArray[$y]->PlayerId;

            $Player = DB::table('players')->where('Id', '=', $PlayerId)->first();
            $MyArray[$y]->PlayerName = $Player->FirstName . ' ' . $Player->LastName;


            $FromTeamId = $MyArray[$y]->FromTeamId;

            $FromTeam = DB::table('teams')->where('Id', '=', $FromTeamId)->first();

            $MyArray[$y]->FromTeamNameEn = $FromTeam->NameEn;
            $MyArray[$y]->FromTeamNameAr = $FromTeam->NameAr;

            $ToTeamId = $MyArray[$y]->ToTeamId;

            $ToTeam = DB::table('teams')->where('Id', '=', $ToTeamId)->first();
            $MyArray[$y]->ToTeamNameEn = $ToTeam->NameEn;
            $MyArray[$y]->ToTeamNameAr = $ToTeam->NameAr;

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

        return $this->returnDate('Data', $MyArray);
    }

    public function ApproveTransfer(Request $request)
    {
        $GetTransferId = $request->TransferId;
        $GetPlayerId = $request->PlayerId;
        $GetToTeamId = $request->ToTeamId;
        $GetApprovedBy = $request->ApproveById;
        $GetApprovedByAccountTypeId = $request->ApproveByAccountTypeId;

        $GetDate = Carbon::now();

        $TransferResult = DB::table('players')
            ->where([
                'Id' => $GetPlayerId,
            ])
            ->update(['TeamId'=> $GetToTeamId]);

        if($TransferResult)
        {
            $ApprovedResult = DB::table('playertransfers')
                ->where([
                    'Id' => $GetTransferId,
                ])
                ->update(['IsApproved'=> 1
                    ,
                    'ApprovedBy'=> $GetApprovedBy,
                    'ApprovedByAccountTypeId'=> $GetApprovedByAccountTypeId,
                    'ApprovedDate'=> $GetDate,
                    'Closed'=> 1]);

            return $this->returnDate('ExecuteStatus', true);

        } else
        {
            return $this->returnDate('ExecuteStatus', false);
        }

    }
}
