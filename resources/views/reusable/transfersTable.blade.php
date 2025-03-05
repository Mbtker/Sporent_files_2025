<div class="boardView_details">
    <div class="boardView">
    <!-- Table -->
    <table style="width: 100%">
        <thead>
        <tr>
            <td style="text-align: center; width: 40px">#</td>
            <td style="text-align: center; width: 95px">{{ __('messages.Date') }}</td>
            <td style="min-width: 150px">{{ __('messages.Name') }}</td>
            <td style="display: none">PlayerId</td>
            <td style="min-width: 100px">{{ __('messages.FromTeam') }}</td>
            <td style="display: none">FromTeamId</td>
            <td style="min-width: 100px">{{ __('messages.ToTeam') }}</td>
            <td style="display: none">ToTeamId</td>
            <td style="text-align: center">{{ __('messages.Amount') }}</td>
            <td style="text-align: center; width: 140px">{{ __('messages.PaymentStatus') }}</td>
            <td style="text-align: center; width: 140px">{{ __('messages.ApprovedStatus') }}</td>
            <td style="text-align: center; width: 95px">{{ __('messages.Status') }}</td>
            <td style="text-align: center; width: 30px">{{ __('messages.Action') }}</td>
        </tr>
        </thead>
        <tbody id="table_data">
        @if (isset($MyArray))
            @foreach($MyArray as $Item)
                <tr>
                    <td style="text-align: center">{{ $Item->{'Id'} }}</td>
                    <td style="text-align: center">{{ Carbon\Carbon::parse($Item->{'CreateDate'})->format('Y-m-d') }}</td>
                    <td style="min-width: 150px"><a id="PlayerDetails" style="text-decoration: none; color: black" href="#">{{ $Item->{'PlayerName'} }}</a></td>
                    <td style="display: none">{{ $Item->{'PlayerId'} }}</td>
                    <td style="min-width: 100px"><a id="FromTeamDetails" style="text-decoration: none; color: black" href="#">{{ $Item->{'FromTeamName'} }}</a></td>
                    <td style="display: none">{{ $Item->{'FromTeamId'} }}</td>
                    <td style="min-width: 100px"><a id="ToTeamDetails" style="text-decoration: none; color: black" href="#">{{ $Item->{'ToTeamName'} }}</td>
                    <td style="display: none">{{ $Item->{'ToTeamId'} }}</td>
                    <td style="text-align: center">{{ number_format((float)$Item->{'Amount'}, 2, '.', '')}} <label style="font-size: 12px; color: #51585e">SAR</label></td>
                    <td style="text-align: center">{{ $Item->{'PaymentStatus'} }}</td>
                    <td style="text-align: center">{{ $Item->{'ApprovedStatus'} }}</td>
                    <td style="text-align: center">@if ($Item->{'Closed'} == '1') {{ __('messages.Closed') }} @else {{ __('messages.New') }} @endif</td>
                    <td class="text-align: center"><a class="Edit" href="#" style="text-decoration: none; color: #7e7e7e;"><i class="fad fa-edit" ></i></a></td>
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
