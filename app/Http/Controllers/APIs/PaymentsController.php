<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    public function IssueWithPayment(Request $request)
    {
        $GeUserAccountTypeId = $request->UserAccountTypeId;
        $GetUserId = $request->UserId;
        $GetDevice = $request->Device;
        $GetFeeId = $request->FeeId;
        $GetPageName = $request->PageName;
        $GetAmount = $request->Amount;
        $GetResult = $request->Result;
        $GetNote = $request->Note;

        $All = [
            'UserAccountTypeId' => $GeUserAccountTypeId,
            'UserId' => $GetUserId,
            'Device' => $GetDevice,
            'FeeId' => $GetFeeId,
            'PageName' => $GetPageName,
            'Amount' => $GetAmount,
            'Result' => json_encode($GetResult),
            'Note' => $GetNote
        ];

        $ResultAdded = DB::table('paymentissues')->insert($All);

        if($ResultAdded) {
            $message = "There is an issue with E-Payment";

            $AdminInfo = DB::table('supervisors')
                ->where('Id', '=', 1)
                ->get();

            if ($AdminInfo->TokenId != null && $AdminInfo->TokenId != "")
                HelperController::SendNotifications([$AdminInfo->TokenId], $message);
        }
    }
}
