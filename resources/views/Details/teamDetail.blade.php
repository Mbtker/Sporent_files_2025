@extends('master')

@section('content')

    <head>
        <link rel="stylesheet" href="{{ URL::asset('css/detailStyle.css') }}" />

    </head>

    <!-- Send Message Modal -->
    @include('modals.sendMessageModal')

    <h3 class="i-name">{{ __('messages.Details') }}</h3>

    <div class="content">
        <div class="top_card">
            <input type="text" class="form-control" id="Id" name="Id" value="{{ $Team->Id }}" hidden/>
            <input type="text" class="form-control" id="CaptainName" name="CaptainName" value="{{ $Team->CaptainName }}" hidden/>
            <input type="text" class="form-control" id="TokenID" name="TokenID" value="{{ $Team->TokenId }}" hidden/>
            <input type="text" class="form-control" id="PhoneNumber" name="PhoneNumber" value="{{ $Team->CaptainPhone }}" hidden/>
            <div class="info_row_logo">
                <img src="{{ $Team->{'Logo'} }}"  alt="team_logo"/>
            </div>
            <div class="info_row">
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.TeamName') }}:</label> @if(app()->getLocale() == 'en') {{ $Team->{'NameEn'} }} @else {{ $Team->{'NameAr'} }} @endif</label>
                </div>
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.CityName') }}:</label> {{ $Team->CityName }}</label>
                </div>
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.CaptainName') }}:</label> {{ $Team->CaptainName }}</label>
                </div>
            </div>
            <div class="info_row">
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.LastActive') }}:</label> {{ (new \App\Http\Controllers\HelperFun)->CalculatingDaysHours($Team->{'LastActive'}) }}</label>
                </div>
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.Status') }}:</label> @if ($Team->{'Status'} == '1') {{ __('messages.Active') }} @else {{ __('messages.Inactive') }} @endif</label>
                </div>
            </div>
            <div class="info_row button">
                <div class="mt-2">
                    <button id="SendNotification" value="{{ __('messages.SendNotification') }}">{{ __('messages.SendNotification') }}</button>
                </div>
                <div class="mt-3">
                    <button id="SendSMS" value="{{ __('messages.SendSMS') }}">{{ __('messages.SendSMS') }}</button>
                </div>
            </div>
            <a class="EditInfo" href="#" style="text-decoration: none; color: #7e7e7e;"><i class="fad fa-edit" ></i></a>
        </div>
        <div class="Right_card">
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>

        $(document).on('click','#SendNotification', function() {

            document.getElementById("MessageModalTitle").innerHTML = document.getElementById("SendNotification").value;
            document.getElementById("ToName").innerHTML = document.getElementById("CaptainName").value;

            $("#sendMessageModal").find('input[name="IsSMS"]').val(0);

            $("#sendMessageModal").find('input[name="TokenId"]').val(document.getElementById("TokenID").value);

            $("#sendMessageModal").modal('show');
        });

        $(document).on('click','#SendSMS', function() {

            document.getElementById("MessageModalTitle").innerHTML = document.getElementById("SendSMS").value;
            document.getElementById("ToName").innerHTML = document.getElementById("CaptainName").value;

            $("#sendMessageModal").find('input[name="IsSMS"]').val(1);

            $("#sendMessageModal").find('input[name="TokenId"]').val(document.getElementById("PhoneNumber").value);

            $("#sendMessageModal").modal('show');
        });
    </script>


@stop
