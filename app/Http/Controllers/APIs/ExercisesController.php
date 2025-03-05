<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\APIs\HelperAPIsFun;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperFun;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ExercisesController extends Controller
{
    use GeneralTrait;

   public function GetExercises(Request $request)
    {
        $LoginId = $request->LoginId;
        $LoginAccountTypeId = $request->LoginAccountTypeId;

        $FinalResult = [];

/*        $currentDateTime = Carbon::now();
        $exerciseDate = '2023-12-09 3:36 PM';

        $diffInHours = $currentDateTime->diffInHours($exerciseDate);
        $diffInDays = $currentDateTime->diffInDays($exerciseDate);

        return [
            'CurrentDate' => $currentDateTime->format('Y-m-d g:i A'),
            'ExerciseDate' => $exerciseDate,
            'DiffrentHours' => $diffInHours,
            'ExerciseDays' => $diffInDays,
            'isPast' => Carbon::parse($exerciseDate)->isPast()
        ];*/


        $Result = DB::table('exercises')
            ->where('Status', '=', 1)
            ->orderByRaw('Id DESC')
            ->get();

        for($y = 0; $y<count($Result); $y++)
        {
            $GetExerciseType = $Result[$y]->ExerciseType;
            $ExerciseLocation = $Result[$y]->Location;
            $GetExerciseDate = $Result[$y]->ExerciseDate;

            if($ExerciseLocation != null)
            {
                $Location = explode(",", $ExerciseLocation);
                $ExerciseLatitude = $Location[0];
                $ExerciseLongitude = $Location[1];
                $currentDateTime = Carbon::now();

                $Distance =  HelperFun::distance(floatval($request->Latitude), floatval($request->Longitude), floatval($ExerciseLatitude), floatval($ExerciseLongitude), "K");

                if ($Distance <= MAX_DISTANCE_FOR_APP || "$Distance" == "NAN")
                {
                    $isPast = Carbon::parse($GetExerciseDate)->isPast();
                    $diffInHours = $currentDateTime->diffInHours($GetExerciseDate);

                     if ($GetExerciseType == 'Monthly' || !$isPast || ($isPast && $diffInHours <= 2)) { // To show only exercises that not passed 2 hours

                        $Result[$y]->Stadium = null;

                        $StadiumId = $Result[$y]->StadiumId;

                        if($StadiumId != null)
                        {
                            $Result[$y]->Stadium = DB::table('stadiums')
                                ->where('Id', '=', $StadiumId)
                                ->first();
                        }

                        $TableName = HelperAPIsFun::GetTableName($Result[$y]->CreateByUserTypeId);

                        $PlayerInfo = DB::table($TableName->TableName)
                            ->where('Id', '=', $Result[$y]->CreatById)
                            ->first();

                        $Result[$y]->CreateByName = $PlayerInfo->FirstName . " " . $PlayerInfo->LastName;

                        $GetJoinsCount = 0 ;

                        if ($Result[$y]->Fee > 0) {
                            $GetJoinsCount = DB::table('exerciseslist')
                                ->where('ExerciseId', '=', $Result[$y]->Id)
                                ->where('IsAccepted', '=', 1)
                                ->where('PaymentId', '!=', 0)
                                ->get()->count();
  
                            $CheckOwnerPayed = DB::table('exerciseslist')
                                ->where('ExerciseId', '=', $Result[$y]->Id)
                                ->where('PlayerId', '=', $Result[$y]->CreatById)
                                ->where('IsAccepted', '=', 1)
                                ->where('PaymentId', '!=', 0)
                                ->get()->count();

                            // The owner
                            if ($CheckOwnerPayed == 1) {
                            } else {
                                $GetJoinsCount += 1;
                            }
                            
                        } else {
                            $GetJoinsCount = DB::table('exerciseslist')
                                ->where('ExerciseId', '=', $Result[$y]->Id)
                                ->where('IsAccepted', '=', 1)
                                ->get()->count();
                        }


                        $Result[$y]->JoinsCount = $GetJoinsCount;
                        $Result[$y]->Distance = number_format((float)$Distance, 2, '.', '');

                        $FinalResult[] = $Result[$y];
                    }
                }
            }

        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function AddCreateBy($CreatById, $CreateByUserTypeId)
    {
        $TableName = HelperAPIsFun::GetTableName($CreateByUserTypeId);

        return DB::table($TableName->TableName)
            ->where('Id', '=', $CreatById)
            ->first();
    }

    public function GetExercisePlayers(Request $request)
    {
        $FinalResult = [];

        $Result = DB::table('exerciseslist')
            ->where('ExerciseId', '=', $request->ExerciseId)
            ->get();

        for($y = 0; $y<count($Result); $y++)
        {
            $GetId = $Result[$y]->Id;
            $PlayerId = $Result[$y]->PlayerId;

            $Player = DB::table('players')
                ->where('Id', '=', $PlayerId)
                ->first();

            $FinalResult[] = [
                'JoinId' => $GetId,
                'PlayerId' =>  $Player->Id,
                'AccountTypeId' =>  $Player->AccountTypeId,
                'FirstName' =>  $Player->FirstName,
                'LastName' =>  $Player->LastName,
                'Image' =>  $Player->Image,
                'IsAccepted' =>  $Result[$y]->IsAccepted,
                'PaymentId' =>  $Result[$y]->PaymentId,
                'IsInvite' =>  $Result[$y]->IsInvite
            ];
        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function SubscribeToExercise(Request $request)
    {
        $Result = DB::table('exerciseslist')
            ->where('ExerciseId', '=', $request->ExerciseId)
            ->where('PlayerId', '=', $request->UserId)
            ->first();

        if (!$Result) {
            $Data = [
                'ExerciseId' => $request->ExerciseId,
                'PlayerId' => $request->UserId,
            ];

            DB::table('exerciseslist')->insertGetId($Data);

            $ExerciseInf = DB::table('exercises')
                ->where('Id', '=', $request->ExerciseId)
                ->first();

            if ($ExerciseInf) {
                $JoinerInf = DB::table('players')
                    ->where('Id', '=', $request->UserId)
                    ->first();

                $OwnerInf = DB::table('players')
                    ->where('Id', '=', $ExerciseInf->CreatById)
                    ->first();

                if ($OwnerInf) {
                    $Message = "يوجد طلب انضمام للتمرين من قبل " . $JoinerInf->FirstName . " " . $JoinerInf->LastName;
                    if($OwnerInf->Lang == 'en') {
                        $Message = "There is a new request to join exercise by " . $JoinerInf->FirstName . " " . $JoinerInf->LastName;
                    }

                    if ($OwnerInf->TokenId != null && $OwnerInf->TokenId != "")
                        HelperController::SendNotifications([$OwnerInf->TokenId], $Message);
                }
            }
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function CreateNewExercise(Request $request)
    {
        $CreatById = $request->CreateById;
        $CreateByUserType = $request->CreateByUserType;
        $ExerciseType = $request->ExerciseType;
        $NumberOfPlayers = $request->NumberOfPlayers;
        $FromAge = $request->FromAge;
        $ToAge = $request->ToAge;
        $ExerciseName = $request->ExerciseName;
        $ExerciseDate = $request->ExerciseDate;
        $Location = $request->Location;
        $SendToNearly = $request->SendToNearly;
        $GetDays = $request->DaysMap;

        $Fee = 0;
        if ($request->Fee != null) {
            $Fee = $request->Fee;
        }

        $Data = [
            'Topic' => $ExerciseName,
            'ExerciseType' => $ExerciseType,
            'FromAge' => $FromAge,
            'ToAge' => $ToAge,
            'NumberOfPlayers' => $NumberOfPlayers,
            'ExerciseDate' => $ExerciseDate,
            'Location' => $Location,
            'Fee' => $Fee,
            'CreatById' => $CreatById,
            'CreateByUserTypeId' => $CreateByUserType,
            'SendToNearly' => $SendToNearly,
            'Days' => $GetDays,
        ];

        $ExerciseId = DB::table('exercises')->insertGetId($Data);

        $OwnerPlayerData = [
            'ExerciseId' => $ExerciseId,
            'PlayerId' => $CreatById,
            'IsAccepted' => 1,
        ];

      //  DB::table('exerciseslist')->insertGetId($OwnerPlayerData);

        $FinalTokenIds = [];

        if($request->PlayersMap != null && $request->PlayersMap != "")
        {
            $GetPlayers = explode(',', $request->PlayersMap);

            for($y = 0; $y<count($GetPlayers); $y++)
            {
                $GetPlayerId = (Integer)$GetPlayers[$y];

                if ($GetPlayerId == $CreatById) {

                    $PlayerData = [
                        'ExerciseId' => $ExerciseId,
                        'PlayerId' => (Integer)$GetPlayers[$y]
                    ];

                } else {
                    $PlayerData = [
                        'ExerciseId' => $ExerciseId,
                        'PlayerId' => (Integer)$GetPlayers[$y],
                        'IsInvite' => 1
                    ];
                }

                if((Integer)$GetPlayers[$y] != 0)
                    DB::table('exerciseslist')->insertGetId($PlayerData);

                if ($GetPlayerId != $CreatById) {
                    $PlayerInfo = DB::table('players')
                        ->where('Id', '=', $GetPlayerId)
                        ->first();

                    if ($PlayerInfo)
                    {
                        if ($PlayerInfo->TokenId != null && $PlayerInfo->TokenId != "")
                            $FinalTokenIds[] = $PlayerInfo->TokenId;
                    }
                }
            }

            $Message = "لديك دعوة للانظام لتمرين " . $ExerciseName;
            HelperController::SendNotifications($FinalTokenIds, $Message);
        }

        if($SendToNearly == 1)
        {
            $this->SendToNearly($Location);
        }

        return $this->returnDate('InsertStatus', true);
    }

    public function SendToNearly($PassLocation)
    {
        $FinalResult = [];
        $PassLocation = explode(",", $PassLocation);
        $PassLatitude = $PassLocation[0];
        $PassLongitude = $PassLocation[1];

        $Result = DB::table('players')
            ->where('Status', '=', 1)
            ->orderByRaw('Id DESC')
            ->get();

        for($y = 0; $y<count($Result); $y++)
        {
            $PlayerLocation = $Result[$y]->Location;

            if($PlayerLocation != null)
            {
                $Location = explode(",", $PlayerLocation);
                $PlayerLatitude = $Location[0];
                $PlayerLongitude = $Location[1];

                $Distance =  HelperFun::distance(floatval($PassLatitude), floatval($PassLongitude), floatval($PlayerLatitude), floatval($PlayerLongitude), "K");

                if ($Distance <= MAX_DISTANCE_FOR_NEARLY_PLAYERS || "$Distance" == "NAN")
                {
                    $FinalResult[] = $Result[$y]->TokenId;
                }
            }
        }

        HelperController::SendNotifications($FinalResult, "يوجد بالقرب منك تمرين بانتظار مشاركتك");
    }

/*    public function MyExercises(Request $request)
    {
        $Result = [];

        $MyExercises = DB::table('exercises')
            ->where('CreatById', '=', $request->UserId)
            ->where('CreateByUserTypeId', '=', $request->UserAccountTypeId)
            ->orderByRaw('Id DESC')
            ->get();

        for ($x = 0; $x < count($MyExercises); $x++) {

            $Result[] = $MyExercises[$x];
        }

        $MyJoinExercises = DB::table('exerciseslist')
            ->where('PlayerId', '=', $request->UserId)
            ->where('IsAccepted', '!=', 2)
            ->orderByRaw('Id DESC')
            ->get();

        $IsExist = false;

        for ($b = 0; $b < count($MyJoinExercises); $b++) {

            $ExerciseId = $MyJoinExercises[$b]->ExerciseId;

            for ($p = 0; $p < count($Result); $p++) {
                if ($ExerciseId == $Result[$p]->Id)
                    $IsExist = true;
            }

            if(!$IsExist) {
                $Result[] = DB::table('exercises')
                    ->where('Id', '=', $ExerciseId)
                    ->first();
            }
        }

        for ($y = 0; $y < count($Result); $y++) {

            $GetId = $Result[$y]->Id;
            $GetStadiumId = $Result[$y]->StadiumId;

            $Result[$y]->JoinsCount = 0 ;

            if ($Result[$y]->Fee > 0) {
                $Result[$y]->JoinsCount = DB::table('exerciseslist')
                    ->where('ExerciseId', '=', $Result[$y]->Id)
                    ->where('IsAccepted', '=', 1)
                    ->where('IsPayed', '=', 1)
                    ->get()->count();
            } else {
                $Result[$y]->JoinsCount = DB::table('exerciseslist')
                    ->where('ExerciseId', '=', $Result[$y]->Id)
                    ->where('IsAccepted', '=', 1)
                    ->get()->count();
            }

            if($GetStadiumId != 0)
            {
                $Result[$y]->Stadium = DB::table('stadiums')
                    ->where('Id', '=', $GetStadiumId)
                    ->first();
            } else
            {
                $Result[$y]->Stadium = null;
            }
        }

        return $this->returnDate('Data', $Result);
    }*/

    public function MyExercises(Request $request)
    {
        $UserId = $request->UserId;
        $UserAccountTypeId = $request->UserAccountTypeId;

        $Result = DB::select('SELECT DISTINCT Exr.*, COUNT(DISTINCT ExrList.Id) JoinsCount, CONCAT(player.FirstName, \' \', player.LastName) AS CreateByName FROM exercises Exr LEFT JOIN exerciseslist ExrList ON ExrList.ExerciseId = Exr.Id AND CASE WHEN Exr.Fee > 0 THEN ExrList.IsAccepted = 1 AND ExrList.PaymentId != 0 ELSE ExrList.IsAccepted = 1 END LEFT JOIN stadiums STD ON STD.Id = Exr.StadiumId LEFT JOIN players player ON player.Id = Exr.CreatById LEFT JOIN exerciseslist ExrList2 ON ExrList2.PlayerId = ? AND ExrList2.IsAccepted != 2 WHERE (Exr.CreatById = ? AND Exr.CreateByUserTypeId = ?) OR Exr.Id = ExrList2.ExerciseId GROUP BY Exr.Id, Exr.Topic, Exr.ExerciseType, Exr.FromAge, Exr.ToAge, Exr.Details, Exr.NumberOfPlayers, Exr.QRcode, Exr.ExerciseDate, Exr.Days, Exr.CityName, Exr.Location, Exr.ChatUrl, Exr.StadiumId, Exr.Fee, Exr.CreateDate, Exr.CreatById, Exr.CreateByUserTypeId, Exr.SendToNearly, Exr.CreateDate, Exr.Status, player.FirstName, player.LastName ORDER BY Exr.Id DESC', [$UserId, $UserId, $UserAccountTypeId]);

        return $this->returnDate('Data', $Result);
    }

    public function AcceptRejectRemovePlayerFromExercise(Request $request)
    {
        if($request->ExerciseId != null && $request->PlayerId != null)
        {
            $PlayerInfo = DB::table('players')
                ->where('Id', '=', $request->PlayerId)
                ->first();

            $Message = "";

            if ($request->Action == 1) { // Accept

                if ($PlayerInfo->Lang == "ar") {
                    $Message = "تم قبول انظمامك للتمرين";
                } else {
                    $Message = "You have been accepted to join the exercise";
                }

                DB::table('exerciseslist')
                    ->where('ExerciseId', '=', $request->ExerciseId)
                    ->where('PlayerId', '=', $request->PlayerId)
                    ->update(['IsAccepted' => 1]);

            } else if ($request->Action == 2) { // Reject

                if ($PlayerInfo->Lang == "ar") {
                    $Message = "نعتذر, تم رفض انضمامك للتمرين";
                } else {
                    $Message = "Sorry, you were not accepted to join the exercise";
                }

                DB::table('exerciseslist')
                    ->where('ExerciseId', '=', $request->ExerciseId)
                    ->where('PlayerId', '=', $request->PlayerId)
                    ->update(['IsAccepted' => 2]);

            } else if ($request->Action == 3) { // Remove

                if ($PlayerInfo->Lang == "ar") {
                    $Message = "نعتذر, تم حذفك من التمرين";
                } else {
                    $Message = "Sorry, you were removed from the exercise";
                }

                DB::table('exerciseslist')
                    ->where('ExerciseId', '=', $request->ExerciseId)
                    ->where('PlayerId', '=', $request->PlayerId)
                    ->delete();
            }

            if ($PlayerInfo->TokenId != null && $PlayerInfo->TokenId != "") {
                $FinalResult = [$PlayerInfo->TokenId];

                HelperController::SendNotifications($FinalResult, $Message);
            }

            return $this->returnDate('ExecuteStatus', true);
        }
    }

    public function PayForExercise(Request $request)
    {
        $JoinInfo = DB::table('exerciseslist')
            ->where('Id', '=', $request->JoinId)
            ->first();

        $PlayerInfo = DB::table('players')
            ->where('Id', '=', $JoinInfo->PlayerId)
            ->first();

        DB::table('exerciseslist')
            ->where('Id', '=', $request->JoinId)
            ->update(['PaymentId' => $request->PaymentId]);

        $ExerciseInfo = DB::table('exercises')
            ->where('Id', '=', $JoinInfo->ExerciseId)
            ->first();

        if ($ExerciseInfo) {
            $OwnerInfo = DB::table('players')
                ->where('Id', '=', $ExerciseInfo->CreatById)
                ->first();

            if ($OwnerInfo->TokenId != null && $OwnerInfo->TokenId != "") {
                $TokenId = [$OwnerInfo->TokenId];

                if ($OwnerInfo->Lang == "ar") {
                    $Message = "تم دفع رسوم التمرين من " . $PlayerInfo->FirstName . " " . $PlayerInfo->LastName;
                } else {
                    $Message = "The exercise fee paid by " . $PlayerInfo->FirstName . " " . $PlayerInfo->LastName;
                }

                HelperController::SendNotifications($TokenId, $Message);
            }
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function AddPlayerToExercise(Request $request)
    {
        $ExerciseId = $request->ExerciseId;
        $PlayerId = $request->PlayerId;

        $ExerciseInfo = DB::table('exercises')
            ->where('Id', '=', $request->ExerciseId)
            ->first();

        $PlayerInfo = DB::table('players')
            ->where('Id', '=', $PlayerId)
            ->first();

        $PlayerData = [
            'ExerciseId' => $ExerciseId,
            'PlayerId' => $PlayerId,
            'IsInvite' => 1
        ];

        DB::table('exerciseslist')->insertGetId($PlayerData);

        $Message = "";
        if ($PlayerInfo->Lang == "ar") {
            $Message = "لديك دعوة للانظام لتمرين " . $ExerciseInfo->Topic;
        } else {
            $Message = "You have invitation to join exercise " . $ExerciseInfo->Topic;
        }

        if ($PlayerInfo->TokenId != null && $PlayerInfo->TokenId != "") {
            $FinalResult = [$PlayerInfo->TokenId];

            HelperController::SendNotifications($FinalResult, $Message);
        }

        return $this->returnDate('ExecuteStatus', true);
    }

    public function UpdateExercise(Request $request)
    {
        $ExerciseId = $request->ExerciseId;
        $ExerciseName = $request->ExerciseName;
        $NumberOfPlayers = $request->NumberOfPlayers;
        $FromAge = $request->FromAge;
        $ToAge = $request->ToAge;
        $ExerciseDate = $request->ExerciseDate;
        $Location = $request->Location;
        $Fee = $request->Fee;
        $GetDays = $request->DaysMap;

        $data = [
            'Topic' => $ExerciseName,
            'NumberOfPlayers' => $NumberOfPlayers,
            'FromAge' => $FromAge,
            'ToAge' => $ToAge,
            'ExerciseDate' => $ExerciseDate,
            'Location' => $Location,
            'Fee' => $Fee,
            'Days' => $GetDays,
        ];

        DB::table('exercises')
            ->where('Id', $ExerciseId)
            ->update($data);

        $ExercisesList = DB::table('exerciseslist')
            ->where('ExerciseId', '=', $ExerciseId)
            ->where('IsAccepted', '=', 1)
            ->get();

        $TokenIds = [];

        for ($y = 0; $y < count($ExercisesList); $y++) {

            $GetPlayerId = $ExercisesList[$y]->PlayerId;

            $PlayerInfo = DB::table('players')
                ->where('Id', '=', $GetPlayerId)
                ->first();

            if ($PlayerInfo) {
                if ($PlayerInfo->TokenId != null && $PlayerInfo->TokenId != "") {
                    $TokenIds[] = $PlayerInfo->TokenId;
                }
            }
        }

        $Message = "تم اجراء تحديث على التمرين " . $ExerciseName;

        HelperController::SendNotifications($TokenIds, $Message);

        return $this->returnDate('ExecuteStatus', true);

    }
}
