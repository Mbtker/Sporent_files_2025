<div class="boardView_details">
    <div class="boardView">
    <!-- Table -->
    <table style="width: 100%">
        <thead>
        <tr>
            <td style="text-align: center; width: 90px" width="25">#</td>
            <td style="min-width: 200px">{{ __('messages.Name') }}</td>
            <td style="text-align: center">{{ __('messages.Phone') }}</td>
            <td style="text-align: center">{{ __('messages.Email') }}</td>
            <td style="text-align: center">{{ __('messages.CityName') }}</td>
            <td style="text-align: center">{{ __('messages.Location') }}</td>
            <td style="text-align: center; display: none">{{ __('messages.Location') }}</td>
            <td style="text-align: center; display: none">{{ __('messages.Location') }}</td>
            <td style="text-align: center">{{ __('messages.LastActive') }}</td>
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
                    <td style="min-width: 250px">{{ $Item->{'Name'} }}</td>
                    <td style="text-align: center">{{ $Item->{'Phone'} }}</td>
                    <td style="text-align: center">{{ $Item->{'Email'} }}</td>
                    <td style="text-align: center">{{ $Item->{'CityName'} }}</td>
                    <td style="text-align: center">
                        <a href="https://www.google.com/maps/place/{{ $Item->{'Location'} }}" target="_blank" style="text-decoration: none; color: black; font-size: 18px"><i class="fad fa-map-marked map_icon"></i></a>
                    </td>
                    <td style="text-align: center; display: none">{{ $Item->{'Location'} }}</td>
                    <td style="text-align: center; display: none">{{ $Item->{'AreaRange'} }}</td>
                    <td style="text-align: center">{{ (new \App\Http\Controllers\HelperFun)->CalculatingDaysHours($Item->{'LastActive'}) }}</td>
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
