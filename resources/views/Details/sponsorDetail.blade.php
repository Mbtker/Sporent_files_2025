@extends('master')

@section('content')

    <head>
        <link rel="stylesheet" href="{{ URL::asset('css/detailStyle.css') }}" />

    </head>

    <!-- Send Message Modal -->
    @include('modals.sendMessageModal')

    <!-- edit Sponsors Modal -->
    @include('modals.editSponsorsModal')

    <h3 class="i-name">{{ __('messages.Details') }}</h3>

    <div class="content">
        <div class="top_card">
            <input type="text" class="form-control" id="Id" name="Id" value="{{ $MyArray->Id }}" hidden/>
            <input type="text" class="form-control" id="StadiumName" name="StadiumName" value="{{ $MyArray->Name }}" hidden/>
            <input type="text" class="form-control" id="StadiumTokenID" name="StadiumTokenID" value="{{ $MyArray->TokenId }}" hidden/>
            <input type="text" class="form-control" id="PhoneNumber" name="PhoneNumber" value="{{ $MyArray->Phone }}" hidden/>
            <div class="info_row_logo">
                <img src="@if($MyArray->Logo != null) {{ $MyArray->Logo }} @else {{ URL::asset('images/logo_icon.png') }} @endif"  alt="team_logo"/>
            </div>
            <div class="info_row">
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.Name') }}:</label> {{ $MyArray->Name }}</label>
                </div>
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.CommercialRegister') }}:</label> {{ $MyArray->CR }}</label>
                </div>
            </div>
            <div class="info_row">
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.Phone') }}:</label> {{ $MyArray->Phone }}</label>
                </div>
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.Email') }}:</label> {{ $MyArray->Email }}</label>
                </div>
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.CityName') }}:</label> {{ $MyArray->CityName }}</label>
                </div>
            </div>
            <div class="info_row">
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.LastActive') }}:</label> {{ (new \App\Http\Controllers\HelperFun)->CalculatingDaysHours($MyArray->{'LastActive'}) }}</label>
                </div>
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.Status') }}:</label> @if ($MyArray->{'Status'} == '1') {{ __('messages.Active') }} @else {{ __('messages.Inactive') }} @endif</label>
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
            document.getElementById("ToName").innerHTML = document.getElementById("StadiumName").value;

            $("#sendMessageModal").find('input[name="IsSMS"]').val(0);

            $("#sendMessageModal").find('input[name="TokenId"]').val(document.getElementById("StadiumTokenID").value);

            $("#sendMessageModal").modal('show');
        });

        $(document).on('click','#SendSMS', function() {

            document.getElementById("MessageModalTitle").innerHTML = document.getElementById("SendSMS").value;
            document.getElementById("ToName").innerHTML = document.getElementById("StadiumName").value;

            $("#sendMessageModal").find('input[name="IsSMS"]').val(1);

            $("#sendMessageModal").find('input[name="TokenId"]').val(document.getElementById("PhoneNumber").value);

            $("#sendMessageModal").modal('show');
        });

        $(document).on('click','.EditInfo', function(){

            var Id = document.getElementById("Id").value;

            jQuery.ajax({
                // Controller Name is Orders/getItems
                url: "{{ url('/GetSponsorInfo') }}",
                method: 'get',
                data: {
                    Id: Id
                },
                success: function(result){
                    console.log(result);

                    var Name = '';
                    var Phone = '';
                    var Email = '';
                    var Location = '';
                    var Status = '';

                    $.each(result, function (key, value) {

                        if (key === "Name") {
                            Name = value;
                        } else if (key === "Phone") {
                            Phone = value;
                        } else if (key === "Email") {
                            Email = value;
                        }  else if (key === "Location") {
                            Location = value;
                        } else if (key === "Status") {
                            Status = value;
                        }
                    });

                    $("#editSponsorsModal").find('input[name="UserId"]').val(Id);
                    $("#editSponsorsModal").find('input[name="Name"]').val(Name);
                    $("#editSponsorsModal").find('input[name="Phone"]').val(Phone);
                    $("#editSponsorsModal").find('input[name="Email"]').val(Email);

                    $("#Status").val(Status).attr("selected","selected");

                    // To remove validation
                    $("#editSponsorsModal").find('small').hide();

                    $("#editSponsorsModal").modal('show');

                }});

        });

    </script>

    @if(!empty(Session::get('error_sponsors')) && Session::get('error_sponsors') == 1)
        <script>
            $(function() {
                $('#editSponsorsModal').modal('show');
            });
        </script>
    @endif

    <div id="toast_message">Added successfully</div>

    @if(!empty(Session::get('error_edit_sponsors')) && Session::get('error_edit_sponsors') == 3)
        <script>
            var x = document.getElementById("toast_message");
            x.innerHTML = '{{ __('messages.UpdatedSuccessfully') }}';
            x.className = "show";
            setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);

        </script>
    @endif


@stop
