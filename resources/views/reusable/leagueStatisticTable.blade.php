<table class="table table-striped table-hover">
    <thead>
    <tr>
        <td style="text-align: center; font-weight: bold; width: 160px; font-size: 13px;">{{ $CountTitle }}</td>
        <td style="text-align: center; font-weight: bold; width: 300px; font-size: 13px;">الاسم</td>
        <td style="text-align: center; font-weight: bold; width: 100px; font-size: 13px;">الرقم</td>
        <td style="text-align: center; font-weight: bold; width: 300px; font-size: 13px;">الفريق</td>
    </tr>
    </thead>
    <tbody id="table_data">
    @if (isset($MyArray))
        @foreach($MyArray as $Item)
            <tr>
                <td style="text-align: center; color: white; width: 160px; font-size: 13px; padding: 10px">{{ $Item->{'Count'} }}</td>
                <td style="text-align: center; color: white; width: 300px; font-size: 13px; padding: 10px">{{ $Item->{'PlayerName'} }}</td>
                <td style="text-align: center; color: white; width: 100px; font-size: 13px; padding: 10px">
                    @if($Item->{'PlayerNumber'} == '')
                        {{ '-' }}
                    @else
                        {{ $Item->{'PlayerNumber'} }}
                    @endif
                </td>
                <td style="text-align: center; color: white; width: 300px">
                    <div class="Team_logo">
                        <label style="color: white; font-size: 13px">{{ $Item->{'Team'} }}</label>
                        <img src='https://sporent.net/public/images/{{ $Item->{'Logo'} }}'  alt="logo"/>
                    </div>
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>


@if (!isset($MyArray) || count($MyArray) == 0)
    <div class="NoData">
        <p>No date!</p>
    </div>
@endif
