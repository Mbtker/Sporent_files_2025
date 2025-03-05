<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperFun;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneralController extends Controller
{
    use GeneralTrait;

    public function GetFees(Request $request)
    {
        $Result = DB::table('fees')
            ->where('Id', '=', $request->FeeId)
            ->first();

        return $this->returnDate('Data', $Result);
    }

    public function GetBankList(Request $request)
    {
        $Result = DB::table('bank')
            ->where('Status', '=', 1)
            ->get();

        return $this->returnDate('Data', $Result);
    }

    public function GetBankAccountInfo(Request $request)
    {
        $GetUserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;

        $GetEPaymentInfo = DB::table('epaymentinfo')
            ->where('UserId', '=', $GetUserId)
            ->where('UserAccountTypeId', '=', $GetUserAccountTypeId)
            ->first();

        if ($GetEPaymentInfo) {
            $GetBankInfo = DB::table('bank')
                ->where('Id', '=', $GetEPaymentInfo->BankId)
                ->first();
            $GetEPaymentInfo->BankNameAr = $GetBankInfo->BankNameAr;
            $GetEPaymentInfo->BankNameEn = $GetBankInfo->BankNameEn;
        }
        return $this->returnDate('Data', $GetEPaymentInfo);
    }

    public function SaveBankAccountInfo(Request $request)
    {
        $GetUserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;
        $GetBankId = $request->BankId;
        $GetBeneficiaryName = $request->BeneficiaryName;
        $GetBankAccountNo = $request->BankAccountNo;

        $GetEPaymentInfo = DB::table('epaymentinfo')
            ->where('UserId', '=', $GetUserId)
            ->where('UserAccountTypeId', '=', $GetUserAccountTypeId)
            ->first();

        if ($GetEPaymentInfo) {
            $Data = [
                'BankId' => $GetBankId,
                'BankAccount' => $GetBankAccountNo,
                'BeneficiaryName' => $GetBeneficiaryName,
            ];
            DB::table('epaymentinfo')
                ->where('Id', $GetEPaymentInfo->Id)
                ->update($Data);
        } else {
            $Data = [
                'UserId' => $GetUserId,
                'UserAccountTypeId' => $GetUserAccountTypeId,
                'BankId' => $GetBankId,
                'BankAccount' => $GetBankAccountNo,
                'BeneficiaryName' => $GetBeneficiaryName,
            ];
            DB::table('epaymentinfo')->insert($Data);
        }
        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetMyTotalBalance(Request $request)
    {
        $GetUserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;

        $Result = [];
        $Balance = 0.0;

        if ($GetUserAccountTypeId == 14) { // For League organizers
            $Result = DB::table('managejoin')
                ->where('CreateByUserId', '=', $GetUserId)
                ->where('CreateByAccountTypeId', '=', $GetUserAccountTypeId)
                ->where('AccountTypeId', '=', 13) // Team Account Type Id Only
                ->where('IsAccepted', '=', 1)
                ->where('PaymentId', '>', 0)
                ->where('Fee', '>', 0)
                ->where('LeagueId', '>', 0)
                ->where('IsDeleted', '=', 0)
                ->where('Status', '=', 1)
                ->get();
        } else {
            $Result = DB::table('managejoin')
                ->where('UserId', '=', $GetUserId)
                ->where('AccountTypeId', '=', $GetUserAccountTypeId)
                ->where('IsAccepted', '=', 1)
                ->where('PaymentId', '>', 0)
                ->where('Fee', '>', 0)
                ->where('IsDeleted', '=', 0)
                ->where('Status', '=', 1)
                ->get();
        }

        for($y = 0; $y<count($Result); $y++) {

            $GetManageJoinId = $Result[$y]->Id;
            $GetFee = $Result[$y]->Fee;

            $TransferRequests = DB::table('transferamountrequests')
                ->where('UserId', '=', $GetUserId)
                ->where('UserAccountTypeId', '=', $GetUserAccountTypeId)
                ->where('ActionDate', '!=', null)
                ->where('Status', '=', 1)
                ->get();

            if (count($TransferRequests) > 0) {
                for($n = 0; $n<count($TransferRequests); $n++) {
                    $GetIds = $TransferRequests[$n]->ManageJoinIds;
                    if ($GetIds == null || $GetIds == "" || !str_contains($GetIds, $GetManageJoinId)) {
                        $Balance += $GetFee;
                    }
                }
            } else {
                $Balance += $GetFee;
            }
        }

        $response = ['Balance' => (string)number_format((float)$Balance, 2, '.', '')];
        return $this->returnDate('Data', $response);
    }

    public function GetAvailableAmountToTransfer(Request $request)
    {
        $GetUserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;

        $Result = [];
        $Balance = 0.0;
        $ManageJoinIds = "";

        $IsThereOpenRequest = false;

        $GetTransferRequests = DB::table('transferamountrequests')
            ->where('UserId', '=', $GetUserId)
            ->where('UserAccountTypeId', '=', $GetUserAccountTypeId)
            ->where('ActionDate', '=', null)
            ->where('Status', '=', 1)
            ->first();

        if ($GetTransferRequests) {
            $IsThereOpenRequest = true;
        } else {
            if ($GetUserAccountTypeId == 14) { // For League organizers
                $Result = DB::table('managejoin')
                    ->where('CreateByUserId', '=', $GetUserId)
                    ->where('CreateByAccountTypeId', '=', $GetUserAccountTypeId)
                    ->where('AccountTypeId', '=', 13) // Team Account Type Id Only
                    ->where('IsAccepted', '=', 1)
                    ->where('PaymentId', '>', 0)
                    ->where('Fee', '>', 0)
                    ->where('LeagueId', '>', 0)
                    ->where('IsDeleted', '=', 0)
                    ->where('Status', '=', 1)
                    ->get();
            } else {
                $Result = DB::table('managejoin')
                    ->where('UserId', '=', $GetUserId)
                    ->where('AccountTypeId', '=', $GetUserAccountTypeId)
                    ->where('IsAccepted', '=', 1)
                    ->where('PaymentId', '>', 0)
                    ->where('Fee', '>', 0)
                    ->where('IsDeleted', '=', 0)
                    ->where('Status', '=', 1)
                    ->get();
            }

            for($y = 0; $y<count($Result); $y++) {

                $GetManageJoinId = $Result[$y]->Id;
                $GetFee = $Result[$y]->Fee;
                $GetLeagueId = $Result[$y]->LeagueId;

                $TransferRequests = DB::table('transferamountrequests')
                    ->where('UserId', '=', $GetUserId)
                    ->where('UserAccountTypeId', '=', $GetUserAccountTypeId)
                    ->where('ActionDate', '!=', null)
                    ->where('Status', '=', 1)
                    ->get();

                if ($GetLeagueId != null && $GetLeagueId != 0) {
                    $GetLeagueInfo = DB::table('leagues')
                        ->where('Id', '=', $GetLeagueId)
                        ->first();
                }

                if (count($TransferRequests) > 0) {
                    for($n = 0; $n<count($TransferRequests); $n++) {
                        $GetIds = $TransferRequests[$n]->ManageJoinIds;
                        if ($GetIds == null || $GetIds == "" || !str_contains($GetIds, $GetManageJoinId)) {
                            if ($GetLeagueId != null && $GetLeagueId != 0) {
                                if ($GetUserAccountTypeId == 14) { // For League organizers
                                    $Balance += $GetFee;
                                    $ManageJoinIds .= "," .$GetManageJoinId;
                                } else {
                                    if ($GetLeagueInfo->Status == 2) {
                                        $Balance += $GetFee;
                                        $ManageJoinIds .= "," .$GetManageJoinId;
                                    }
                                }
                            } else {
                                $Balance += $GetFee;
                                $ManageJoinIds .= "," .$GetManageJoinId;
                            }
                        }
                    }
                } else {
                    if ($GetLeagueId != null && $GetLeagueId != 0) {
                        if ($GetUserAccountTypeId == 14) { // For League organizers
                            $Balance += $GetFee;
                            $ManageJoinIds .= "," .$GetManageJoinId;
                        } else {
                            if ($GetLeagueInfo->Status == 2) {
                                $Balance += $GetFee;
                                $ManageJoinIds .= "," .$GetManageJoinId;
                            }
                        }
                    } else {
                        $Balance += $GetFee;
                        $ManageJoinIds .= "," .$GetManageJoinId;
                    }
                }
            }
        }

        $response = ['IsThereOpenRequest' => $IsThereOpenRequest, 'Amount' => (string)number_format((float)$Balance, 2, '.', ''), 'ManageJoinIds' => $ManageJoinIds];
        return $this->returnDate('Data', $response);
    }

    public function SendTransferRequest(Request $request)
    {
        $GetUserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;
        $GeManageJoinIds = $request->ManageJoinIds;

        $Data = [
            'UserId' => $GetUserId,
            'UserAccountTypeId' => $GetUserAccountTypeId,
            'ManageJoinIds' => $GeManageJoinIds,
        ];

        DB::table('transferamountrequests')->insert($Data);

        return $this->returnDate('ExecuteStatus', true);
    }

    public function GetTransferRecords(Request $request)
    {
        $GetUserId = $request->UserId;
        $GetUserAccountTypeId = $request->UserAccountTypeId;


        $Result = DB::table('transferamountrequests')
            ->where('UserId', '=', $GetUserId)
            ->where('UserAccountTypeId', '=', $GetUserAccountTypeId)
            ->orderBy('Id', 'desc')
            ->get();

        for($y = 0; $y<count($Result); $y++) {

            $Balance = 0.0;

            $GetManageJoinIds = $Result[$y]->ManageJoinIds;

            if ($GetManageJoinIds != null && $GetManageJoinIds != "") {
                $GetJoinIds = explode(",", $GetManageJoinIds);

                for($n = 0; $n<count($GetJoinIds); $n++) {
                    $GetId = $GetJoinIds[$n];

                    if ($GetId != "") {
                        $GetJoinInfo = DB::table('managejoin')
                            ->where('Id', '=', $GetId)
                            ->first();
                        if ($GetJoinInfo != null) {
                            if ($GetJoinInfo->Fee > 0)
                                $Balance += $GetJoinInfo->Fee;
                        }
                    }
                }
            }

            $Result[$y]->Amount = (string)number_format((float)$Balance, 2, '.', '');

        }

        return $this->returnDate('Data', $Result);
    }

    public function MakePaymentFee(Request $request)
    {
        $GetFeeInfo = DB::table('fees')
            ->where('Id', '=', $request->FeeId)
            ->first();

        $Data = [
            'FeeId' => 1,
            'PayForId' => $request->TeamId,
            'Amount' => $GetFeeInfo->Fee,
            'PaymentMethod' => "",
            'PaymentStatus' => 1,
        ];

        DB::table('payments')->insert($Data);

        $SupervisorsArray = DB::table('supervisors')
            ->where('Status', '=', 1)
            ->get();

        $AllTokenIds = [];

        for($y = 0; $y<count($SupervisorsArray); $y++) {
            $AllTokenIds[] = $SupervisorsArray[$y]->TokenId;
        }

        $GetTeamInfo = DB::table('teams')
            ->where('Id', '=', $request->TeamId)
            ->first();

        $message = "تم دفع رسوم انشاء فريق, بانتظار التفعيل";

        if ($GetTeamInfo) {
            $message = "تم دفع رسوم انشاء فريق من قبل " . $GetTeamInfo->NameAr . ", بانتظار التفعيل";
        }

        if (count($AllTokenIds) > 0) {
            (new GeneralController)->SendManyNotifications($message, $AllTokenIds);
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function SavePayment(Request $request)
    {
        $Data = [
            'FeeId' => $request->FeeId,
            'PayForId' => $request->PayForId,
            'Amount' => $request->Amount,
            'PaymentMethod' => $request->PaymentMethod,
            'PaymentStatus' => 1,
            'ById' => $request->ById,
            'ByAccountTypeId' => $request->ByAccountTypeId,
        ];

        $PaymentId = DB::table('payments')->insertGetId($Data);

        $response = ['PaymentId' => $PaymentId];

        return $this->returnDate('Data', $response);
    }

    function SendManyNotifications($message, $AllTokenIDs)
    {
        return $this->SendNotifications($message, $AllTokenIDs);
    }

    function SendSingleNotifications($message, $TokenId)
    {
        return $this->SendNotifications($message, [$TokenId]);
    }

    function SendNotifications($message, $AllTokenIDs)
    {
        $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';

        $server_key = "AAAA8Fhneok:APA91bFlIuj1WymKJbWU9jV-cPwILQONgghp3_0Y53yJ45k9ff6Gbwly4EcnkWaL7D-iSv_-zg_FKlBaV_1hTMT-1g4d22UBiqpoJOPyUhAD_Mt_V9UPQITI5GLwRT4ETVGH_ie00fKu";

        $fields = array
        (
            'registration_ids' => $AllTokenIDs,
            'notification'=>array('body'=>$message, 'sound'=> 'notfi.wav')
        );

        $headers = array
        (
            'Authorization: key=' .$server_key,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path_to_fcm);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

    public function GetCityChatGroup(Request $request)
    {
        $Result = DB::table('citieschatgroup')
            ->where('Status', '=', 1)
            ->get();

        $FinalResult = null;

        for($y = 0; $y<count($Result); $y++)
        {
            $ChatLocation = $Result[$y]->Location;

            if($ChatLocation != null)
            {
                $Location = explode(",", $ChatLocation);
                $ChatLatitude = $Location[0];
                $ChatLongitude = $Location[1];

                $Distance =  HelperFun::distance(floatval($request->Latitude), floatval($request->Longitude), floatval($ChatLatitude), floatval($ChatLongitude), "K");

                if ($Distance <= MAX_DISTANCE_FOR_CITY_CHAT_GROUP || "$Distance" == "NAN")
                {
                    $FinalResult = $Result[$y];
                }
            }

        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function GetSliders(Request $request)
    {
        $GetLocation = $request->Location;

        $Result = DB::table('sliders')
            ->where('Status', '=', 1)
            ->get();

        return $this->returnDate('Data', $Result);
    }

    public function GetAppsVersion(Request $request)
    {

        $Result = DB::table('AppsVersion')
            ->where('Id', '=', 1)
            ->first();

        return $this->returnDate('Data', $Result);
    }

    public function CheckServiceAvailable(Request $request)
    {
        $UserLatitude = $request->Latitude;
        $UserLongitude = $request->Longitude;

        $Leagues = DB::select('select * from leagues where Status = 1 || Status = 2');

        for($y = 0; $y<count($Leagues); $y++)
        {
            $GetLocation = $Leagues[$y]->Location;

            if($GetLocation != null)
            {
                $Location = explode(",", $GetLocation);
                $GetLatitude = $Location[0];
                $GetLongitude = $Location[1];

                $Distance =  HelperFun::distance(floatval($UserLatitude), floatval($UserLongitude), floatval($GetLatitude), floatval($GetLongitude), "K");

                if ($Distance <= MAX_DISTANCE_FOR_APP || "$Distance" == "NAN")
                {
                    return $this->returnDate('Data', ['IsThereService' => true]);
                }
            }
        }

        $Exercises = DB::select('select * from exercises where Status = 1');

        for($n = 0; $n<count($Exercises); $n++)
        {
            $GetLocation = $Exercises[$n]->Location;

            if($GetLocation != null)
            {
                $Location = explode(",", $GetLocation);
                $GetLatitude = $Location[0];
                $GetLongitude = $Location[1];

                $Distance =  HelperFun::distance(floatval($UserLatitude), floatval($UserLongitude), floatval($GetLatitude), floatval($GetLongitude), "K");

                if ($Distance <= MAX_DISTANCE_FOR_APP || "$Distance" == "NAN")
                {
                    return $this->returnDate('Data', ['IsThereService' => true]);
                }
            }
        }

        $Stadiums = DB::select('select * from stadiums where Status = 1');

        for($r = 0; $r<count($Stadiums); $r++)
        {
            $GetLocation = $Stadiums[$r]->Location;

            if($GetLocation != null)
            {
                $Location = explode(",", $GetLocation);
                $GetLatitude = $Location[0];
                $GetLongitude = $Location[1];

                $Distance =  HelperFun::distance(floatval($UserLatitude), floatval($UserLongitude), floatval($GetLatitude), floatval($GetLongitude), "K");

                if ($Distance <= MAX_DISTANCE_FOR_APP || "$Distance" == "NAN")
                {
                    return $this->returnDate('Data', ['IsThereService' => true]);
                }
            }
        }

        $Clinics = DB::select('select * from physiotherapyclinics where Status = 1');

        for($c = 0; $c<count($Clinics); $c++)
        {
            $GetLocation = $Clinics[$c]->Location;

            if($GetLocation != null)
            {
                $Location = explode(",", $GetLocation);
                $GetLatitude = $Location[0];
                $GetLongitude = $Location[1];

                $Distance =  HelperFun::distance(floatval($UserLatitude), floatval($UserLongitude), floatval($GetLatitude), floatval($GetLongitude), "K");

                if ($Distance <= MAX_DISTANCE_FOR_APP || "$Distance" == "NAN")
                {
                    return $this->returnDate('Data', ['IsThereService' => true]);
                }
            }
        }

        return $this->returnDate('Data', ['IsThereService' => false]);
    }
}
