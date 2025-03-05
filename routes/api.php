<?php

use App\Http\Controllers\APIs\ClinicsController;
use App\Http\Controllers\APIs\ExercisesController;
use App\Http\Controllers\APIs\GeneralController;
use App\Http\Controllers\APIs\HelperController;
use App\Http\Controllers\APIs\LeaguePageAPIs\LeaguePageController;
use App\Http\Controllers\APIs\LeaguesController;
use App\Http\Controllers\APIs\MatchesController;
use App\Http\Controllers\APIs\MessagesController;
use App\Http\Controllers\APIs\OrganizerLeagueController;
use App\Http\Controllers\APIs\OrganizersController;
use App\Http\Controllers\APIs\PaymentsController;
use App\Http\Controllers\APIs\PlaceController;
use App\Http\Controllers\APIs\PlayerController;
use App\Http\Controllers\APIs\SponsorsController;
use App\Http\Controllers\APIs\StadiumsController;
use App\Http\Controllers\APIs\StoresController;
use App\Http\Controllers\APIs\TeamController;
use App\Http\Controllers\APIs\TransfersController;
use App\Http\Controllers\APIs\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/ShowCode',[HelperController::class, 'ShowCode'])->name('ShowCode');
// Route::get('/LeagueTopicAndTeams', [LeaguePageController::class, 'LeagueTopicAndTeams'])->name('LeagueTopicAndTeams');
  //  Route::get('/LeagueSponsors', [LeaguePageController::class, 'LeagueSponsors'])->name('LeagueSponsors');
   // Route::get('/LeagueChampionship', [LeaguePageController::class, 'LeagueChampionship'])->name('LeagueChampionship');
   // Route::get('/CurrentMatch', [LeaguePageController::class, 'CurrentMatch'])->name('CurrentMatch');
   // Route::get('/UpcomingMatch', [LeaguePageController::class, 'UpcomingMatch'])->name('UpcomingMatch');
   // Route::get('/OtherMatches', [LeaguePageController::class, 'OtherMatches'])->name('OtherMatches');
   // Route::get('/LeagueVideo', [LeaguePageController::class, 'LeagueVideo'])->name('LeagueVideo');
   // Route::get('/TheNews', [LeaguePageController::class, 'TheNews'])->name('TheNews');



Route::group(['middleware' => ['api', 'CheckPasswordLeaguePage']], function () {

   // Route::post('/LeagueTopicAndTeams', [LeaguePageController::class, 'LeagueTopicAndTeams'])->name('LeagueTopicAndTeams');
   // Route::post('/LeagueSponsors', [LeaguePageController::class, 'LeagueSponsors'])->name('LeagueSponsors');
   // Route::post('/LeagueChampionship', [LeaguePageController::class, 'LeagueChampionship'])->name('LeagueChampionship');
   // Route::get('/CurrentMatchTest', [LeaguePageController::class, 'CurrentMatchTest'])->name('CurrentMatchTest');
   // Route::post('/UpcomingMatch', [LeaguePageController::class, 'UpcomingMatch'])->name('UpcomingMatch');
   // Route::post('/OtherMatches', [LeaguePageController::class, 'OtherMatches'])->name('OtherMatches');
   // Route::post('/LeagueVideo', [LeaguePageController::class, 'LeagueVideo'])->name('LeagueVideo');
   // Route::post('/TheNews', [LeaguePageController::class, 'TheNews'])->name('TheNews');
});

Route::group(['middleware' => ['api', 'checkPassword']], function () {

    // Helper routs
    Route::post('/SendSMS', [HelperController::class, 'SendSMS'])->name('SendSMS');
    Route::post('/GetCountries', [HelperController::class, 'GetCountries'])->name('GetCountries');

    // General routs
    Route::post('/GetFees', [GeneralController::class, 'GetFees'])->name('GetFees');
    Route::post('/MakePaymentFee', [GeneralController::class, 'MakePaymentFee'])->name('MakePaymentFee');
    Route::post('/SavePayment', [GeneralController::class, 'SavePayment'])->name('SavePayment');
    Route::post('/IssueWithPayment', [PaymentsController::class, 'IssueWithPayment'])->name('IssueWithPayment');
    Route::post('/GetBankList', [GeneralController::class, 'GetBankList'])->name('GetBankList');
    Route::post('/SaveBankAccountInfo', [GeneralController::class, 'SaveBankAccountInfo'])->name('SaveBankAccountInfo');
    Route::post('/GetBankAccountInfo', [GeneralController::class, 'GetBankAccountInfo'])->name('GetBankAccountInfo');
    Route::post('/GetMyTotalBalance', [GeneralController::class, 'GetMyTotalBalance'])->name('GetMyTotalBalance');
    Route::post('/GetAvailableAmountToTransfer', [GeneralController::class, 'GetAvailableAmountToTransfer'])->name('GetAvailableAmountToTransfer');
    Route::post('/SendTransferRequest', [GeneralController::class, 'SendTransferRequest'])->name('SendTransferRequest');
    Route::post('/GetTransferRecords', [GeneralController::class, 'GetTransferRecords'])->name('GetTransferRecords');
    Route::post('/GetSliders', [GeneralController::class, 'GetSliders'])->name('GetSliders');
    Route::post('/GetAppsVersion', [GeneralController::class, 'GetAppsVersion'])->name('GetAppsVersion');
    Route::post('/CheckServiceAvailable', [GeneralController::class, 'CheckServiceAvailable'])->name('CheckServiceAvailable');


    // User routs
    Route::post('/LoginFunc',[UserController::class, 'Login'])->name('LoginFunc');
    Route::post('/AccountsType',[UserController::class, 'AccountsType'])->name('AccountsType');
    Route::post('/Register',[UserController::class, 'Register'])->name('Register');
    Route::post('/UpdateAfterUserLogin',[UserController::class, 'UpdateAfterUserLogin'])->name('UpdateAfterUserLogin');
    Route::post('/UploadImage',[UserController::class, 'UploadImage'])->name('UploadImage');
    Route::post('/GetUserMedia',[UserController::class, 'GetUserMedia'])->name('GetUserMedia');
    Route::post('/DeleteMedia',[UserController::class, 'DeleteMedia'])->name('DeleteMedia');
    Route::post('/UploadVideo',[UserController::class, 'UploadVideo'])->name('UploadVideo');
    Route::post('/UpdateVideoSize',[UserController::class, 'UpdateVideoSize'])->name('UpdateVideoSize');
    Route::post('/FetchUserData',[UserController::class, 'FetchUserData'])->name('FetchUserData');
    Route::post('/GetAllMedia',[UserController::class, 'GetAllMedia'])->name('GetAllMedia');
    Route::post('/GetAllMediaNew',[UserController::class, 'GetAllMediaNew'])->name('GetAllMediaNew');
    Route::post('/ShareFile',[UserController::class, 'ShareFile'])->name('ShareFile');
    Route::post('/LikeFile',[UserController::class, 'LikeFile'])->name('LikeFile');
    Route::post('/GetCommentsOfFile',[UserController::class, 'GetCommentsOfFile'])->name('GetCommentsOfFile');
    Route::post('/SendComment',[UserController::class, 'SendComment'])->name('SendComment');
    Route::post('/Follow',[UserController::class, 'Follow'])->name('Follow');
    Route::post('/GetMangeUsers',[UserController::class, 'GetMangeUsers'])->name('GetMangeUsers');
    Route::post('/GetMediaMange',[UserController::class, 'GetMediaMange'])->name('GetMediaMange');
    Route::post('/UpdateMediaInfo',[UserController::class, 'UpdateMediaInfo'])->name('UpdateMediaInfo');
    Route::post('/AddOrRemoveMediaToCommunity',[UserController::class, 'AddOrRemoveMediaToCommunity'])->name('AddOrRemoveMediaToCommunity');
    Route::post('/MangeAddOrRemoveMediaToCommunity',[UserController::class, 'MangeAddOrRemoveMediaToCommunity'])->name('MangeAddOrRemoveMediaToCommunity');
    Route::post('/GetFollowList',[UserController::class, 'GetFollowList'])->name('GetFollowList');
    Route::post('/ShareMediaWithTeam',[UserController::class, 'ShareMediaWithTeam'])->name('ShareMediaWithTeam');
    Route::post('/GetUserDocuments',[UserController::class, 'GetUserDocuments'])->name('GetUserDocuments');
    Route::post('/UserUploadDocument',[UserController::class, 'UserUploadDocument'])->name('UserUploadDocument');
    Route::post('/SendDeleteUserDataRequest',[UserController::class, 'SendDeleteUserDataRequest'])->name('SendDeleteUserDataRequest');

    // Leagues routs
    Route::post('/GetLeagues',[LeaguesController::class, 'GetLeagues'])->name('GetLeagues');
    Route::post('/GetAllLeagues',[LeaguesController::class, 'GetAllLeagues'])->name('GetAllLeagues');
    Route::post('/GetLeagueTeams',[LeaguesController::class, 'GetLeagueTeams'])->name('GetLeagueTeams');
    Route::post('/GetLeagueStandings',[LeaguesController::class, 'GetLeagueStandings'])->name('GetLeagueStandings');
    Route::post('/GetNews',[LeaguesController::class, 'GetNews'])->name('GetNews');
    Route::post('/AddNews',[LeaguesController::class, 'AddNews'])->name('AddNews');
    Route::post('/RemoveNews',[LeaguesController::class, 'RemoveNews'])->name('RemoveNews');
    Route::post('/GetLeagueReferees',[LeaguesController::class, 'GetLeagueReferees'])->name('GetLeagueReferees');
    Route::post('/GetLeagueCommentators',[LeaguesController::class, 'GetLeagueCommentators'])->name('GetLeagueCommentators');
    Route::post('/CreateNewLeague',[LeaguesController::class, 'CreateNewLeague'])->name('CreateNewLeague');
    Route::post('/CreateNewLeagueChampoinship',[LeaguesController::class, 'CreateNewLeagueChampoinship'])->name('CreateNewLeagueChampoinship');
    Route::post('/UpdateLeagueChampoinshipName',[LeaguesController::class, 'UpdateLeagueChampoinshipName'])->name('UpdateLeagueChampoinshipName');
    Route::post('/GetLeagueChampoinship',[LeaguesController::class, 'GetLeagueChampoinship'])->name('GetLeagueChampoinship');
    Route::post('/GetChampionshipTeams',[LeaguesController::class, 'GetChampionshipTeams'])->name('GetChampionshipTeams');
    Route::post('/AddRemoveTeamToChampionship',[LeaguesController::class, 'AddRemoveTeamToChampionship'])->name('AddRemoveTeamToChampionship');
    Route::post('/GetLeagueInfo',[LeaguesController::class, 'GetLeagueInfo'])->name('GetLeagueInfo');
    Route::post('/ChangeLeagueStatus',[LeaguesController::class, 'ChangeLeagueStatus'])->name('ChangeLeagueStatus');
    Route::post('/GetLeagueTeamsForAdmin',[LeaguesController::class, 'GetLeagueTeamsForAdmin'])->name('GetLeagueTeamsForAdmin');
    Route::post('/RemoveTeamFromLeague',[LeaguesController::class, 'RemoveTeamFromLeague'])->name('RemoveTeamFromLeague');
    Route::post('/AddTeamToLeague',[LeaguesController::class, 'AddTeamToLeague'])->name('AddTeamToLeague');
    Route::post('/UpdateLeague',[LeaguesController::class, 'UpdateLeague'])->name('UpdateLeague');
    Route::post('/GetLeagueMatchesForAdmin',[LeaguesController::class, 'GetLeagueMatchesForAdmin'])->name('GetLeagueMatchesForAdmin');
    Route::post('/AddMatchToLeague',[LeaguesController::class, 'AddMatchToLeague'])->name('AddMatchToLeague');
    Route::post('/GetMatchStaff',[LeaguesController::class, 'GetMatchStaff'])->name('GetMatchStaff');
    Route::post('/GetMatchStaffForLeague',[LeaguesController::class, 'GetMatchStaffForLeague'])->name('GetMatchStaffForLeague');
    Route::post('/RemoveMatchStaff',[LeaguesController::class, 'RemoveMatchStaff'])->name('RemoveMatchStaff');
    Route::post('/AddMatchStaff',[LeaguesController::class, 'AddMatchStaff'])->name('AddMatchStaff');
    Route::post('/GetStaffByLocation',[LeaguesController::class, 'GetStaffByLocation'])->name('GetStaffByLocation');
    Route::post('/GetLeagueAndMatchCommentators',[LeaguesController::class, 'GetLeagueAndMatchCommentators'])->name('GetLeagueAndMatchCommentators');
    Route::post('/RemoveCommentatorFromMatch',[LeaguesController::class, 'RemoveCommentatorFromMatch'])->name('RemoveCommentatorFromMatch');
    Route::post('/AddCommentatorToMatch',[LeaguesController::class, 'AddCommentatorToMatch'])->name('AddCommentatorToMatch');
    Route::post('/GetCommentatorsByLocation',[LeaguesController::class, 'GetCommentatorsByLocation'])->name('GetCommentatorsByLocation');
    Route::post('/GetLeagueAndMatchPhotographers',[LeaguesController::class, 'GetLeagueAndMatchPhotographers'])->name('GetLeagueAndMatchPhotographers');
    Route::post('/GetLeagueListTeams',[LeaguesController::class, 'GetLeagueListTeams'])->name('GetLeagueListTeams');
    Route::post('/GetLeagueTeamsListForUser',[LeaguesController::class, 'GetLeagueTeamsListForUser'])->name('GetLeagueTeamsListForUser');
    Route::post('/AddBestPlayersToVote',[LeaguesController::class, 'AddBestPlayersToVote'])->name('AddBestPlayersToVote');
    Route::post('/GetAdminBestPlayersToVote',[LeaguesController::class, 'GetAdminBestPlayersToVote'])->name('GetAdminBestPlayersToVote');
    Route::post('/GetBestPlayersToVote',[LeaguesController::class, 'GetBestPlayersToVote'])->name('GetBestPlayersToVote');
    Route::post('/VoteToPlayer',[LeaguesController::class, 'VoteToPlayer'])->name('VoteToPlayer');
    Route::post('/GetPlayersCard',[LeaguesController::class, 'GetPlayersCard'])->name('GetPlayersCard');
    Route::post('/GetLeagueScorers',[LeaguesController::class, 'GetLeagueScorers'])->name('GetLeagueScorers');
    Route::post('/GetSponsorsByLeagueId',[LeaguesController::class, 'GetSponsorsByLeagueId'])->name('GetSponsorsByLeagueId');

    // Teams routs
    Route::post('/GetTeamInfo',[TeamController::class, 'GetTeamInfo'])->name('GetTeamInfo');
    Route::post('/GetTeamPlayers',[TeamController::class, 'GetTeamPlayers'])->name('GetTeamPlayers');
    Route::post('/GetTeamPlayersWithInfo',[TeamController::class, 'GetTeamPlayersWithInfo'])->name('GetTeamPlayersWithInfo');
    Route::post('/GetTeamPlayersForMange',[TeamController::class, 'GetTeamPlayersForMange'])->name('GetTeamPlayersForMange');
    Route::post('/GetTeamMedia',[TeamController::class, 'GetTeamMedia'])->name('GetTeamMedia');
    Route::post('/GetAllTeamsByLocation',[TeamController::class, 'GetAllTeamsByLocation'])->name('GetAllTeamsByLocation');
    Route::post('/GetAllTeams',[TeamController::class, 'GetAllTeams'])->name('GetAllTeams');
    Route::post('/GetAllTeamsByStatus',[TeamController::class, 'GetAllTeamsByStatus'])->name('GetAllTeamsByStatus');
    Route::post('/ApproveTeam',[TeamController::class, 'ApproveTeam'])->name('ApproveTeam');
    Route::post('/GetJoinToTeamRequest',[TeamController::class, 'GetJoinToTeamRequest'])->name('GetJoinToTeamRequest');
    Route::post('/ResponseOfRequestJoinToTeam',[TeamController::class, 'ResponseOfRequestJoinToTeam'])->name('ResponseOfRequestJoinToTeam');
    Route::post('/CreateNewTeam',[TeamController::class, 'CreateNewTeam'])->name('CreateNewTeam');
    Route::post('/UpdateTeamLogo',[TeamController::class, 'UpdateTeamLogo'])->name('UpdateTeamLogo');
    Route::post('/RemovePlayerFromTeam',[TeamController::class, 'RemovePlayerFromTeam'])->name('RemovePlayerFromTeam');
    Route::post('/AddChatGroup',[TeamController::class, 'AddChatGroup'])->name('AddChatGroup');
    Route::post('/AddPlayerToTeam',[TeamController::class, 'AddPlayerToTeam'])->name('AddPlayerToTeam');
    Route::post('/GetTeamStatistics',[TeamController::class, 'GetTeamStatistics'])->name('GetTeamStatistics');
    Route::post('/GetTeamPlayersForAttendance',[TeamController::class, 'GetTeamPlayersForAttendance'])->name('GetTeamPlayersForAttendance');
    Route::post('/ActionOfTeamPlayerAttendance',[TeamController::class, 'ActionOfTeamPlayerAttendance'])->name('ActionOfTeamPlayerAttendance');


    // Players routs
    Route::post('/GetUserInfo',[PlayerController::class, 'GetUserInfo'])->name('GetUserInfo');
    Route::post('/GetStatistics',[PlayerController::class, 'GetStatistics'])->name('GetStatistics');
    Route::post('/FollowingUser',[PlayerController::class, 'FollowingUser'])->name('FollowingUser');
    Route::post('/PlayerSearch',[PlayerController::class, 'PlayerSearch'])->name('PlayerSearch');
    Route::post('/GetUserInfoToEdit',[PlayerController::class, 'GetUserInfoToEdit'])->name('GetUserInfoToEdit');
    Route::post('/UpdateUserInfo',[PlayerController::class, 'UpdateUserInfo'])->name('UpdateUserInfo');
    Route::post('/GetPlayersByStatus',[PlayerController::class, 'GetPlayersByStatus'])->name('GetPlayersByStatus');
    Route::post('/ChangePlayerStatus',[PlayerController::class, 'ChangePlayerStatus'])->name('ChangePlayerStatus');
    Route::post('/UpdateUserImage',[PlayerController::class, 'UpdateUserImage'])->name('UpdateUserImage');
    Route::post('/GetPlayerPositions',[PlayerController::class, 'GetPlayerPositions'])->name('GetPlayerPositions');
    Route::post('/ChangePlayerPosition',[PlayerController::class, 'ChangePlayerPosition'])->name('ChangePlayerPosition');
    Route::post('/GetRefereeActionCount',[PlayerController::class, 'GetRefereeActionCount'])->name('GetRefereeActionCount');
    Route::post('/GetPlayerCardsOrGoalList',[PlayerController::class, 'GetPlayerCardsOrGoalList'])->name('GetPlayerCardsOrGoalList');
    Route::post('/ChangePlayerNumber',[PlayerController::class, 'ChangePlayerNumber'])->name('ChangePlayerNumber');

    // Exercises routs
    Route::post('/GetExercises',[ExercisesController::class, 'GetExercises'])->name('GetExercises');
    Route::post('/GetExercisePlayers',[ExercisesController::class, 'GetExercisePlayers'])->name('GetExercisePlayers');
    Route::post('/SubscribeToExercise',[ExercisesController::class, 'SubscribeToExercise'])->name('SubscribeToExercise');
    Route::post('/CreateNewExercise',[ExercisesController::class, 'CreateNewExercise'])->name('CreateNewExercise');
    Route::post('/MyExercises',[ExercisesController::class, 'MyExercises'])->name('MyExercises');
    Route::post('/AcceptRejectRemovePlayerFromExercise',[ExercisesController::class, 'AcceptRejectRemovePlayerFromExercise'])->name('AcceptRejectRemovePlayerFromExercise');
    Route::post('/AddPlayerToExercise',[ExercisesController::class, 'AddPlayerToExercise'])->name('AddPlayerToExercise');
    Route::post('/UpdateExercise',[ExercisesController::class, 'UpdateExercise'])->name('UpdateExercise');
    Route::post('/PayForExercise',[ExercisesController::class, 'PayForExercise'])->name('PayForExercise');

    // Stadiums routs
    Route::post('/GetStadiums',[StadiumsController::class, 'GetStadiums'])->name('GetStadiums');
    Route::post('/GetStadiumDetails',[StadiumsController::class, 'GetStadiumDetails'])->name('GetStadiumDetails');
    Route::post('/BookingStadium',[StadiumsController::class, 'BookingStadium'])->name('BookingStadium');
    Route::post('/CancelBookingStadium',[StadiumsController::class, 'CancelBookingStadium'])->name('CancelBookingStadium');
    Route::post('/StadiumMyBooking',[StadiumsController::class, 'StadiumMyBooking'])->name('StadiumMyBooking');
    Route::post('/UpdateStadiumInfo',[StadiumsController::class, 'UpdateStadiumInfo'])->name('UpdateStadiumInfo');
    Route::post('/StadiumBookingList',[StadiumsController::class, 'StadiumBookingList'])->name('StadiumBookingList');
    Route::post('/AcceptOrRejectBooking',[StadiumsController::class, 'AcceptOrRejectBooking'])->name('AcceptOrRejectBooking');
    Route::post('/GetAllStadiumServices',[StadiumsController::class, 'GetAllStadiumServices'])->name('GetAllStadiumServices');
    Route::post('/GetStadiumServices',[StadiumsController::class, 'GetStadiumServices'])->name('GetStadiumServices');
    Route::post('/AddNewServiceToStadium',[StadiumsController::class, 'AddNewServiceToStadium'])->name('AddNewServiceToStadium');
    Route::post('/RemoveServiceFromStadium',[StadiumsController::class, 'RemoveServiceFromStadium'])->name('RemoveServiceFromStadium');
    Route::post('/GetMangeStadiums',[StadiumsController::class, 'GetMangeStadiums'])->name('GetMangeStadiums');
    Route::post('/ChangeStadiumStatus',[StadiumsController::class, 'ChangeStadiumStatus'])->name('ChangeStadiumStatus');
    Route::post('/GetBookingTimes',[StadiumsController::class, 'GetBookingTimes'])->name('GetBookingTimes');


    // Stores routs
    Route::post('/GetStoreCategories',[StoresController::class, 'GetStoreCategories'])->name('GetStoreCategories');
    Route::post('/GetStores',[StoresController::class, 'GetStores'])->name('GetStores');
    Route::post('/GetProductCategories',[StoresController::class, 'GetProductCategories'])->name('GetProductCategories');
    Route::post('/GetProducts',[StoresController::class, 'GetProducts'])->name('GetProducts');
    Route::post('/SendOrder',[StoresController::class, 'SendOrder'])->name('SendOrder');
    Route::post('/SendItems',[StoresController::class, 'SendItems'])->name('SendItems');
    Route::post('/GetStoreOrders',[StoresController::class, 'GetStoreOrders'])->name('GetStoreOrders');
    Route::post('/GetOrderProducts',[StoresController::class, 'GetOrderProducts'])->name('GetOrderProducts');
    Route::post('/CloseOrder',[StoresController::class, 'CloseOrder'])->name('CloseOrder');
    Route::post('/CreateNewCategory',[StoresController::class, 'CreateNewCategory'])->name('CreateNewCategory');
    Route::post('/DeleteCategory',[StoresController::class, 'DeleteCategory'])->name('DeleteCategory');
    Route::post('/UpdateCategory',[StoresController::class, 'UpdateCategory'])->name('UpdateCategory');
    Route::post('/CreateNewProduct',[StoresController::class, 'CreateNewProduct'])->name('CreateNewProduct');
    Route::post('/UpdateProduct',[StoresController::class, 'UpdateProduct'])->name('UpdateProduct');
    Route::post('/UpdateProductStatus',[StoresController::class, 'UpdateProductStatus'])->name('UpdateProductStatus');
    Route::post('/DeleteProduct',[StoresController::class, 'DeleteProduct'])->name('DeleteProduct');
    Route::post('/GetStoreInfo',[StoresController::class, 'GetStoreInfo'])->name('GetStoreInfo');
    Route::post('/UpdateStoreInfo',[StoresController::class, 'UpdateStoreInfo'])->name('UpdateStoreInfo');
    Route::post('/GetUserOrders',[StoresController::class, 'GetUserOrders'])->name('GetUserOrders');

    // Clinics routs
    Route::post('/GetClinics',[ClinicsController::class, 'GetClinics'])->name('GetClinics');
    Route::post('/GetClinicInfo',[ClinicsController::class, 'GetClinicInfo'])->name('GetClinicInfo');
    Route::post('/UpdateClinicInfo',[ClinicsController::class, 'UpdateClinicInfo'])->name('UpdateClinicInfo');

    // Messages routs
    Route::post('/GetMessagesSingle',[MessagesController::class, 'GetMessagesSingle'])->name('GetMessagesSingle');
    Route::post('/GetMessagesDetails',[MessagesController::class, 'GetMessagesDetails'])->name('GetMessagesDetails');
    Route::post('/SendMessage',[MessagesController::class, 'SendMessage'])->name('SendMessage');
    Route::post('/MessageSeen',[MessagesController::class, 'MessageSeen'])->name('MessageSeen');
    Route::post('/CheckNewMessages',[MessagesController::class, 'CheckNewMessages'])->name('CheckNewMessages');

    // Places routs
    Route::post('/GetPlaces',[PlaceController::class, 'GetPlaces'])->name('GetPlaces');

    // Transfer routs
    Route::post('/GetPlayersTransfer',[TransfersController::class, 'GetPlayersTransfer'])->name('GetPlayersTransfer');
    Route::post('/ApproveTransfer',[TransfersController::class, 'ApproveTransfer'])->name('ApproveTransfer');

    // Sponsors routs
    Route::post('/GetSponsors',[SponsorsController::class, 'GetSponsors'])->name('GetSponsors');
    Route::post('/GetSponsorDetails',[SponsorsController::class, 'GetSponsorDetails'])->name('GetSponsorDetails');
    Route::post('/CreateNewSponsor',[SponsorsController::class, 'CreateNewSponsor'])->name('CreateNewSponsor');
    Route::post('/ChangeSponsorStatus',[SponsorsController::class, 'ChangeSponsorStatus'])->name('ChangeSponsorStatus');

    // Matches routs
    Route::post('/GetMangeMatches',[MatchesController::class, 'GetMangeMatches'])->name('GetMangeMatches');
    Route::post('/GetMatchInfo',[MatchesController::class, 'GetMatchInfo'])->name('GetMatchInfo');
    Route::post('/AddMatchDetails',[MatchesController::class, 'AddMatchDetails'])->name('AddMatchDetails');
    Route::post('/GetStaffMatches',[MatchesController::class, 'GetStaffMatches'])->name('GetStaffMatches');
    Route::post('/GetUpcomingMatch',[MatchesController::class, 'GetUpcomingMatch'])->name('GetUpcomingMatch');
    Route::post('/EndTheMatch',[MatchesController::class, 'EndTheMatch'])->name('EndTheMatch');

    // City Chat Group Message
    Route::post('/GetCityChatGroup',[GeneralController::class, 'GetCityChatGroup'])->name('GetCityChatGroup');

    // Organizers of League
    Route::post('/GetOrganizerLeague',[OrganizerLeagueController::class, 'GetOrganizerLeague'])->name('GetOrganizerLeague');
    Route::post('/GetOrganizerLeagueMangeJoin',[OrganizerLeagueController::class, 'GetOrganizerLeagueMangeJoin'])->name('GetOrganizerLeagueMangeJoin');
    Route::post('/AcceptRejectRemoveMangeJoin',[OrganizerLeagueController::class, 'AcceptRejectRemoveMangeJoin'])->name('AcceptRejectRemoveMangeJoin');
    Route::post('/PayForManageJoin',[OrganizerLeagueController::class, 'PayForManageJoin'])->name('PayForManageJoin');
    Route::post('/GetStadiumsListOfLeague',[OrganizerLeagueController::class, 'GetStadiumsListOfLeague'])->name('GetStadiumsListOfLeague');
    Route::post('/GetStaffList',[OrganizerLeagueController::class, 'GetStaffList'])->name('GetStaffList');
    Route::post('/BookingStaff',[OrganizerLeagueController::class, 'BookingStaff'])->name('BookingStaff');
    Route::post('/CheckNewInvitations',[OrganizerLeagueController::class, 'CheckNewInvitations'])->name('CheckNewInvitations');
    Route::post('/GetInvitations',[OrganizerLeagueController::class, 'GetInvitations'])->name('GetInvitations');
    Route::post('/AcceptRejectInvitations',[OrganizerLeagueController::class, 'AcceptRejectInvitations'])->name('AcceptRejectInvitations');


});
