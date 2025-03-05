<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperFun;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClinicsController extends Controller
{
    use GeneralTrait;

    public function GetClinics(Request $request)
    {
        $Result = DB::table('physiotherapyclinics')
            ->where('Status', '=', 1)
            ->orderByRaw('Id DESC')
            ->get();

        $FinalResult = [];

        for ($y = 0; $y < count($Result); $y++) {
            $GetLocation = $Result[$y]->Location;

            if ($GetLocation != null && $GetLocation != "") {
                $Location = explode(",", $GetLocation);
                $ClinicLatitude = $Location[0];
                $ClinicLongitude = $Location[1];

                $Distance = HelperFun::distance(floatval($request->Latitude), floatval($request->Longitude), floatval($ClinicLatitude), floatval($ClinicLongitude), "K");

                if ($Distance <= MAX_DISTANCE_FOR_APP || "$Distance" == "NAN") {
                   // $Result[$y]->Distance = (string) number_format((float)$Distance, 2, '.', '');

                    $FinalResult[] = $Result[$y];
                }
            }
        }

        return $this->returnDate('Data', $FinalResult);
    }

    public function GetClinicInfo(Request $request)
    {
        $Result = DB::table('physiotherapyclinics')
            ->where('Id', '=', $request->Id)
            ->first();

        return $this->returnDate('Data', $Result);
    }

    public function UpdateClinicInfo(Request $request)
    {
        $ClinicId = $request->Id;
        $FirstName = $request->FirstName;
        $LastName = $request->LastName;
        $ClinicName = $request->ClinicName;
        $CR = $request->CR;
        $Location = $request->Location;

        $Data = [
            'FirstName' => $FirstName,
            'LastName' => $LastName,
            'Name' => $ClinicName,
            'CR' => $CR,
            'Location' => $Location,
        ];

        DB::table('physiotherapyclinics')
            ->where('Id', $ClinicId)
            ->update($Data);

        return $this->returnDate('ExecuteStatus', true);
    }

}
