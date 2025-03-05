<div class="boardView_details">
    <div class="boardView" style="width: 95%">
    <!-- Table -->
    <table style="width: 100%">
        <thead>
        <tr>
            <td style="text-align: center; width: 90px" width="25">#</td>
            <td style="min-width: 120px">{{ __('messages.MatchTopic') }}</td>
            <td style="min-width: 95px">{{ __('messages.MatchType') }}</td>
            <td style="min-width: 95px">{{ __('messages.LeagueTopic') }}</td>
            <td style="display: none">LeagueId</td>
            <td style="text-align: center">{{ __('messages.StadiumName') }}</td>
            <td style="display: none">StadiumId</td>
            <td style="text-align: center">{{ __('messages.CommentatorName') }}</td>
            <td style="display: none">CommentatorId</td>
            <td style="text-align: center">{{ __('messages.FirstTeamName') }}</td>
            <td style="display: none">FirstTeamId</td>
            <td style="text-align: center">{{ __('messages.SecondTeamName') }}</td>
            <td style="display: none">SecondTeamId</td>
            <td style="text-align: center">{{ __('messages.CityName') }}</td>
            <td style="text-align: center">{{ __('messages.Location') }}</td>
            <td style="text-align: center; display: none">{{ __('messages.Location') }}</td>
            <td style="text-align: center; width: 110px">{{ __('messages.MatchDate') }}</td>
            <td style="text-align: center; display: none">{{ __('messages.MatchDate') }}</td>
            <td style="text-align: center; width: 95px">{{ __('messages.Status') }}</td>
            <td style="text-align: center; width: 95px; display: none">{{ __('messages.Status') }}</td>
            <td style="text-align: center; width: 30px">{{ __('messages.Action') }}</td>
        </tr>
        </thead>
        <tbody id="table_data">
        @if (isset($MyArray))
            @foreach($MyArray as $Item)
                <tr>
                    <td style="text-align: center">{{ $Item->{'Id'} }}</td>
                    <td style="min-width: 250px">{{ $Item->{'Topic'} }}</td>
                    <td style="min-width: 250px">{{ $Item->{'MatchType'} }}</td>
                    <td style="text-align: center">@if($Item->{'LeagueId'} == null) {{ '-' }} @else <a id="LeagueDetails" href="#" style="text-decoration: none; color: black">{{ $Item->{'LeagueTopic'} }}</a> @endif</td>
                    <td style="display: none">{{ $Item->{'LeagueId'} }}</td>
                    <td style="text-align: center">@if($Item->{'StadiumId'} == null) {{ '-' }} @else <a id="StadiumDetails" href="#" style="text-decoration: none; color: black">{{ $Item->{'StadiumName'} }}</a> @endif</td>
                    <td style="display: none">{{ $Item->{'StadiumId'} }}</td>
                    <td style="text-align: center">@if($Item->{'CommentatorId'} == null) {{ '-' }} @else <a id="CommentatorDetails" href="#" style="text-decoration: none; color: black">{{ $Item->{'CommentatorName'} }}</a> @endif</td>
                    <td style="display: none">{{ $Item->{'CommentatorId'} }}</td>
                    <td style="text-align: center">@if($Item->{'FirstTeamId'} == null) {{ '-' }} @else <a id="FirstTeamDetails" href="#" style="text-decoration: none; color: black">{{ $Item->{'FirstTeamName'} }}</a> @endif</td>
                    <td style="display: none">{{ $Item->{'FirstTeamId'} }}</td>
                    <td style="text-align: center">@if($Item->{'SecondTeamId'} == null) {{ '-' }} @else <a id="SecondTeamDetails" href="#" style="text-decoration: none; color: black">{{ $Item->{'SecondTeamName'} }}</a> @endif</td>
                    <td style="display: none">{{ $Item->{'SecondTeamId'} }}</td>
                    <td style="text-align: center">@if($Item->{'CityName'} == null) {{ '-' }} @else {{ $Item->{'CityName'} }} @endif</td>
                    <td style="text-align: center">
                        <a href="https://www.google.com/maps/place/{{ $Item->{'Location'} }}" target="_blank" style="text-decoration: none; color: black; font-size: 18px"><i class="fad fa-map-marked map_icon"></i></a>
                    </td>
                    <td style="text-align: center; display: none">{{ $Item->{'Location'} }}</td>
                    <td style="text-align: center">{{ Carbon\Carbon::parse($Item->{'MatchDate'})->format('Y-m-d') }}</td>
                    <td style="text-align: center; display: none">{{ $Item->{'MatchDate'} }}</td>
                    <td style="text-align: center">@if ($Item->{'Status'} == '1') {{ __('messages.Active') }} @else {{ __('messages.Inactive') }} @endif</td>
                    <td style="text-align: center; display: none">{{ $Item->{'Status'} }}</td>
                    <td class="text-align: center">
                        <a class="Details" href="#" style="text-decoration: none; color: #7e7e7e; margin-inline-end: 10px"><i class="fal fa-file-alt"></i></a>
                        <a class="Edit" href="#" style="text-decoration: none; color: #7e7e7e;"><i class="fad fa-edit" ></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>

    @if (!isset($MyArray) || count($MyArray) == 0)
        <div class="NoData">
            <p>{{ __('messages.NoDate') }}</p>
        </div>
    @endif
<!-- To show no data if array count is 0 -->

        @if(!$iSFromSearch)
            <div style="margin: 8px; font-size: 11px">
                @if (isset($MyArray))
                    {{ $MyArray->onEachSide(1)->links() }}
                @endif
            </div>
        @endif

</div>

</div>
