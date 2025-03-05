<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SponsorsController extends Controller
{
    use GeneralTrait;

    public function GetSponsors(Request $request)
    {
        $GetLatitude = $request->Latitude;
        $GetLongitude = $request->Longitude;

        $MyArray = DB::table('sponsors')->orderByRaw('Id DESC')->get();

        for($y = 0; $y<count($MyArray); $y++)
        {
            $LeagueId = $MyArray[$y]->LeagueId;

            if($LeagueId != 0)
            {
                $LeagueInfo = DB::table('leagues')->where('Id', '=', $LeagueId)->first();
                $MyArray[$y]->League = $LeagueInfo;
            } else
            {
                $MyArray[$y]->League = null;
            }

            $TeamId = $MyArray[$y]->TeamId;

            if($TeamId != 0)
            {
                $TeamInfo = DB::table('teams')->where('Id', '=', $TeamId)->first();
                $MyArray[$y]->Team = $TeamInfo;
            } else
            {
                $MyArray[$y]->Team = null;
            }
        }

        return $this->returnDate('Data', $MyArray);
    }

    public function GetSponsorDetails(Request $request)
    {
        $GetId = $request->Id;

        $MyArray = DB::table('sponsors')->where('Id', '=', $GetId)->first();

        $LeagueId = $MyArray->LeagueId;

        if($LeagueId != 0)
        {
            $LeagueInfo = DB::table('leagues')->where('Id', '=', $LeagueId)->first();
            $MyArray->League = $LeagueInfo;
        } else
        {
            $MyArray->League = null;
        }

        $TeamId = $MyArray->TeamId;

        if($TeamId != 0)
        {
            $TeamInfo = DB::table('teams')->where('Id', '=', $TeamId)->first();
            $MyArray->Team = $TeamInfo;
        } else
        {
            $MyArray->Team = null;
        }

        return $this->returnDate('Data', $MyArray);
    }

    public function CreateNewSponsor(Request $request)
    {
        $GetLogo = $request->Logo;
        $GetWidth = $request->Width;
        $GetHeight = $request->Height;
        $GetNameAr = $request->NameAr;
        $GetNameEn = $request->NameEn;
        $GetDetails = $request->Details;
        $GetPhone = $request->Phone;
        $GetEmail = $request->Email;
        $GetLocation = $request->Location;
        $GetLeagueId = $request->LeagueId;
        $GetTeamId = $request->TeamId;

        $Check = DB::select('SELECT * FROM sponsors spon WHERE (spon.NameAr = ? OR spon.NameEn = ?) AND (spon.LeagueId = ? AND spon.TeamId = ?)  AND spon.Status = 1', [$GetNameAr, $GetNameEn, $GetLeagueId, $GetTeamId]);

        if(!$Check)
        {
            $Data = [
                'Logo' => $GetLogo,
                'Width' => $GetWidth,
                'Height' => $GetHeight,
                'NameAr' => $GetNameAr,
                'NameEn' => $GetNameEn,
                'Details' => $GetDetails,
                'Phone' => $GetPhone,
                'Email' => $GetEmail,
                'Location' => $GetLocation,
                'LeagueId' => $GetLeagueId,
                'TeamId' => $GetTeamId,
            ];

            DB::table('sponsors')->insert($Data);

            return $this->returnMessage(true,1, '');
        } else {
            return $this->returnMessage(false,2, '');
        }
    }

    public function ChangeSponsorStatus(Request $request)
    {
        $GetSponsorId = $request->SponsorId;
        $GetStatus = $request->Status;

        $Data = [
            'Status' => $GetStatus,
        ];

        DB::table('sponsors')
            ->where('Id', $GetSponsorId)
            ->update($Data);

        return $this->returnDate('ExecuteStatus', true);
    }
}
