<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperFun;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlaceController extends Controller
{
    use GeneralTrait;

    public function GetPlaces(Request $request)
    {
        $ResultFinal = [];

        $Clinics = DB::table('physiotherapyclinics')
            ->where('Status', 1)
            ->orderByRaw('Id ASC')
            ->get();

        for ($n = 0; $n < count($Clinics); $n++)
        {
            $GetLocation = $Clinics[$n]->Location;

            if($GetLocation != null && $GetLocation != "")
            {
                $Location = explode(",", $GetLocation);
                $GetLatitude = $Location[0];
                $GetLongitude = $Location[1];

                $Distance = HelperFun::distance(floatval($request->Latitude), floatval($request->Longitude), floatval($GetLatitude), floatval($GetLongitude), "K");

                if ($Distance <= MAX_DISTANCE_FOR_APP || "$Distance" == "NAN") {
                    $Clinics[$n]->Distance = number_format((float)$Distance
                        , 2, '.', '');

                    $GetId = $Clinics[$n]->Id;
                    $GetAccountTypeId = $Clinics[$n]->AccountTypeId;
                    $GetLogo = $Clinics[$n]->Logo;
                    $GetName = $Clinics[$n]->Name;
                    $GetPhone = $Clinics[$n]->Phone;
                    $GetEmail = $Clinics[$n]->Email;
                    $GetCityName = $Clinics[$n]->CityName;
                    $GetFee = 0.0;

                    $ResultFinal[] = ['Id' => $GetId, 'AccountTypeId' => $GetAccountTypeId, 'Logo' => $GetLogo, 'Name' => $GetName, 'Phone' => $GetPhone, 'Email' => $GetEmail, 'CityName' => $GetCityName, 'Location' => $GetLocation, 'Fee' => $GetFee];
                }
            }
        }

        $Stadiums = DB::table('stadiums')
            ->where('Status', 1)
            ->orderByRaw('Id ASC')
            ->get();

        for($y = 0; $y<count($Stadiums); $y++)
        {
            $GetStadiumLocation = $Stadiums[$y]->Location;

            if($GetStadiumLocation != null && $GetStadiumLocation != "")
            {
                $StadiumLocation = explode(",", $GetStadiumLocation);
                $GetStadiumLatitude = $StadiumLocation[0];
                $GetStadiumLongitude = $StadiumLocation[1];

                $Distance = HelperFun::distance(floatval($request->Latitude), floatval($request->Longitude), floatval($GetStadiumLatitude), floatval($GetStadiumLongitude), "K");

                if ($Distance <= MAX_DISTANCE_FOR_APP || "$Distance" == "NAN") {
                    $Stadiums[$y]->Distance = number_format((float)$Distance
                        , 2, '.', '');

                    $GetId = $Stadiums[$y]->Id;
                    $GetAccountTypeId = $Stadiums[$y]->AccountTypeId;
                    $GetLogo = $Stadiums[$y]->Logo;
                    $GetName = $Stadiums[$y]->Name;
                    $GetPhone = $Stadiums[$y]->Phone;
                    $GetEmail = $Stadiums[$y]->Email;
                    $GetCityName = $Stadiums[$y]->CityName;
                    $GetFee = $Stadiums[$y]->Fee;

                    $ResultFinal[] = ['Id' => $GetId, 'AccountTypeId' => $GetAccountTypeId, 'Logo' => $GetLogo, 'Name' => $GetName, 'Phone' => $GetPhone, 'Email' => $GetEmail, 'CityName' => $GetCityName, 'Location' => $GetStadiumLocation, 'Fee' => $GetFee];
                }
            }
        }

        /*$Advertising = DB::table('advertisingagencies')
            ->where('Status', 1)
            ->orderByRaw('Id ASC')
            ->get();

        for($b = 0; $b<count($Advertising); $b++)
        {
            $GetAdvertisingLocation = $Advertising[$b]->Location;

            if($GetAdvertisingLocation != null && $GetAdvertisingLocation != "")
            {
                $AdvertisingLocation = explode(",", $GetAdvertisingLocation);
                $GetAdvertisingLatitude = $AdvertisingLocation[0];
                $GetAdvertisingLongitude = $AdvertisingLocation[1];

                $Distance = HelperFun::distance(floatval($request->Latitude), floatval($request->Longitude), floatval($GetAdvertisingLatitude), floatval($GetAdvertisingLongitude), "K");

                if ($Distance <= MAX_DISTANCE_FOR_APP || "$Distance" == "NAN") {
                    $Advertising[$b]->Distance = number_format((float)$Distance
                        , 2, '.', '');

                    $GetId = $Advertising[$b]->Id;
                    $GetAccountTypeId = $Advertising[$b]->AccountTypeId;
                    $GetLogo = $Advertising[$b]->Image;
                    $GetName = $Advertising[$b]->Name;
                    $GetPhone = $Advertising[$b]->Phone;
                    $GetEmail = $Advertising[$b]->Email;
                    $GetCityName = $Advertising[$b]->CityName;
                    $GetFee = 0.0;

                    $ResultFinal[] = ['Id' => $GetId, 'AccountTypeId' => $GetAccountTypeId, 'Logo' => $GetLogo, 'Name' => $GetName, 'Phone' => $GetPhone, 'Email' => $GetEmail, 'CityName' => $GetCityName, 'Location' => $GetAdvertisingLocation, 'Fee' => $GetFee];
                }
            }
        }*/

        return $this->returnDate('Data', $ResultFinal);
    }
}
