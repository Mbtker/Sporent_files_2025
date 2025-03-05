<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Nette\Utils\DateTime;

class HelperFun
{
    function CalculatingDaysHours($LastDateTime)
    {
        $Different = array();
        date_default_timezone_set('Asia/Riyadh');

        $datde = strtotime($LastDateTime);

        $GetDateAndTime = date('Y-m-d h:i:s');
        try {
            $DateAndTimes = new DateTime($GetDateAndTime);
        } catch (\Exception $e) {
        }
        try {
            $datetime2 = new DateTime($LastDateTime);
        } catch (\Exception $e) {
        }


        $interval = $DateAndTimes->diff($datetime2);

        $Different["Year"] = $interval->format('%y');
        $Different["Month"] = $interval->format('%m');
        $Different["Day"] = $interval->format('%d');
        $Different["Hour"] = $interval->format('%h');
        $Different["Minute"] = $interval->format('%i');
        $Different["Second"] = $interval->format('%s');

        if ($Different["Year"] != 0)
        {
            return $Different["Year"]. __('messages.Year');

        } else if ($Different["Month"] != 0)
        {
            return $Different["Month"]. __('messages.Month');

        } else if ($Different["Day"] != 0)
        {
            return $Different["Day"]. __('messages.Day');

        } else if ($Different["Hour"] != 0)
        {
            return $Different["Hour"]. __('messages.Hour');

        } else if ($Different["Minute"] != 0)
        {
            return $Different["Minute"]. __('messages.Minute');

        } else if ($Different["Second"] != 0)
        {
            return $Different["Second"]. __('messages.Second');

        } else
        {
            return '--';
        }
    }

    static function CheckIfPhoneAlreadyExists($TableName, $Phone)
    {
        if($TableName != 'supervisors')
        {
            $iSExists = DB::table('supervisors')->where('Phone', '=', $Phone)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'organizers')
        {
            $iSExists = DB::table('organizers')->where('Phone', '=', $Phone)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'players')
        {
            $iSExists = DB::table('players')->where('Phone', '=', $Phone)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'coaches')
        {
            $iSExists = DB::table('coaches')->where('Phone', '=', $Phone)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'referees')
        {
            $iSExists = DB::table('referees')->where('Phone', '=', $Phone)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'stadiums')
        {
            $iSExists = DB::table('stadiums')->where('Phone', '=', $Phone)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'commentators')
        {
            $iSExists = DB::table('commentators')->where('Phone', '=', $Phone)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'photographers')
        {
            $iSExists = DB::table('photographers')->where('Phone', '=', $Phone)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'sponsors')
        {
            $iSExists = DB::table('sponsors')->where('Phone', '=', $Phone)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'advertisingagencies')
        {
            $iSExists = DB::table('advertisingagencies')->where('Phone', '=', $Phone)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'stores')
        {
            $iSExists = DB::table('stores')->where('Phone', '=', $Phone)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'scoutsofclubs')
        {
            $iSExists = DB::table('scoutsofclubs')->where('Phone', '=', $Phone)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'physiotherapyclinics')
        {
            $iSExists = DB::table('physiotherapyclinics')->where('Phone', '=', $Phone)->first();

            if($iSExists)
            {
                return true;
            }
        }

        return false;
    }

    static function CheckIfPhoneAlreadyExistsInAllTables($Phone)
    {
        $iSExists = DB::table('supervisors')->where('Phone', '=', $Phone)->first();

        if($iSExists)
        {
            return true;
        }

        $iSExists = DB::table('organizers')->where('Phone', '=', $Phone)->first();

        if($iSExists)
        {
            return true;
        }

        $iSExists = DB::table('players')->where('Phone', '=', $Phone)->first();

        if($iSExists)
        {
            return true;
        }

        $iSExists = DB::table('coaches')->where('Phone', '=', $Phone)->first();

        if($iSExists)
        {
            return true;
        }

        $iSExists = DB::table('referees')->where('Phone', '=', $Phone)->first();

        if($iSExists)
        {
            return true;
        }

        $iSExists = DB::table('stadiums')->where('Phone', '=', $Phone)->first();

        if($iSExists)
        {
            return true;
        }

        $iSExists = DB::table('commentators')->where('Phone', '=', $Phone)->first();

        if($iSExists)
        {
            return true;
        }

        $iSExists = DB::table('photographers')->where('Phone', '=', $Phone)->first();

        if($iSExists)
        {
            return true;
        }

        $iSExists = DB::table('sponsors')->where('Phone', '=', $Phone)->first();

        if($iSExists)
        {
            return true;
        }

        $iSExists = DB::table('advertisingagencies')->where('Phone', '=', $Phone)->first();

        if($iSExists)
        {
            return true;
        }

        $iSExists = DB::table('stores')->where('Phone', '=', $Phone)->first();

        if($iSExists)
        {
            return true;
        }

        $iSExists = DB::table('scoutsofclubs')->where('Phone', '=', $Phone)->first();

        if($iSExists)
        {
            return true;
        }

        $iSExists = DB::table('physiotherapyclinics')->where('Phone', '=', $Phone)->first();

        if($iSExists)
        {
            return true;
        }
        return false;
    }

    static function CheckIfEmailAlreadyExists($TableName, $Email)
    {

        if($TableName != 'supervisors')
        {
            $iSExists = DB::table('supervisors')->where('Email', '=', $Email)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'organizers')
        {
            $iSExists = DB::table('organizers')->where('Email', '=', $Email)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'players')
        {
            $iSExists = DB::table('players')->where('Email', '=', $Email)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'coaches')
        {
            $iSExists = DB::table('coaches')->where('Email', '=', $Email)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'referees')
        {
            $iSExists = DB::table('referees')->where('Email', '=', $Email)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'stadiums')
        {
            $iSExists = DB::table('stadiums')->where('Email', '=', $Email)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'commentators')
        {
            $iSExists = DB::table('commentators')->where('Email', '=', $Email)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'photographers')
        {
            $iSExists = DB::table('photographers')->where('Email', '=', $Email)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'sponsors')
        {
            $iSExists = DB::table('sponsors')->where('Email', '=', $Email)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'advertisingagencies')
        {
            $iSExists = DB::table('advertisingagencies')->where('Email', '=', $Email)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'stores')
        {
            $iSExists = DB::table('stores')->where('Email', '=', $Email)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'scoutsofclubs')
        {
            $iSExists = DB::table('scoutsofclubs')->where('Email', '=', $Email)->first();

            if($iSExists)
            {
                return true;
            }
        }

        if($TableName != 'physiotherapyclinics')
        {
            $iSExists = DB::table('physiotherapyclinics')->where('Email', '=', $Email)->first();

            if($iSExists)
            {
                return true;
            }
        }

        return false;
    }

    static function distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

}
