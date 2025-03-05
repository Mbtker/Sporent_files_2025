<?php

namespace App\Http\Controllers\APIs\Enums;

use Illuminate\Validation\Rules\Enum;

class FirebaseKeys extends Enum
{
    const CurrentMatch = 'CurrentMatch';
    const LeagueChampionship = 'LeagueChampionship';
    const LeagueSponsors = 'LeagueSponsors';
    const LeagueTopicAndTeams = 'LeagueTopicAndTeams';
    const LeagueVideo = 'LeagueVideo';
    const OtherMatches = 'OtherMatches';
    const TheNews = 'TheNews';
    const UpcomingMatch = 'UpcomingMatch';

    //  {
    //      "CurrentMatch": "2024-03-13 22:39:44",
    //      "LeagueChampionship": "2024-03-13 22:39:44",
    //      "LeagueSponsors": "2024-03-13 22:39:44",
    //      "LeagueTopicAndTeams": "2024-03-13 22:39:44",
    //      "LeagueVideo": "2024-03-12 22:39:44",
    //      "OtherMatches": "2024-03-13 22:39:44",
    //      "TheNews": "2024-03-12 22:39:44",
    //      "UpcomingMatch": "2024-03-15 22:39:44"
    //  }
}
