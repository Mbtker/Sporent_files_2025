@extends('master')

@section('content')

    <head>
        <link rel="stylesheet" href="{{ URL::asset('css/detailStyle.css') }}" />

    </head>

    <!-- Send Message Modal -->
    @include('modals.sendMessageModal')

    <!-- edit Store Modal -->
    @include('modals.editStoreModal')

    <h3 class="i-name">{{ __('messages.Details') }}</h3>

    <div class="content">
        <div class="top_card">
            <input type="text" class="form-control" id="StoreId" name="StoreId" value="{{ $MyArray->Id }}" hidden/>
            <input type="text" class="form-control" id="StoreName" name="StoreName" value="{{ $MyArray->Name }}" hidden/>
            <input type="text" class="form-control" id="StoreTokenID" name="StoreTokenID" value="{{ $MyArray->TokenId }}" hidden/>
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
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.CityName') }}:</label> {{ $MyArray->CityName }}</label>
                </div>
            </div>
            <div class="info_row">
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.Phone') }}:</label> {{ $MyArray->Phone }}</label>
                </div>
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.Email') }}:</label> {{ $MyArray->Email }}</label>
                </div>
            </div>
            <div class="info_row">
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.OwnerName') }}:</label> {{ $MyArray->OwnerName }}</label>
                </div>
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.OwnerPhone') }}:</label> {{ $MyArray->OwnerPhone }}</label>
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
            document.getElementById("ToName").innerHTML = document.getElementById("StoreName").value;

            $("#sendMessageModal").find('input[name="IsSMS"]').val(0);

            $("#sendMessageModal").find('input[name="TokenId"]').val(document.getElementById("StoreTokenID").value);

            $("#sendMessageModal").modal('show');
        });

        $(document).on('click','#SendSMS', function() {

            document.getElementById("MessageModalTitle").innerHTML = document.getElementById("SendSMS").value;
            document.getElementById("ToName").innerHTML = document.getElementById("StoreName").value;

            $("#sendMessageModal").find('input[name="IsSMS"]').val(1);

            $("#sendMessageModal").find('input[name="TokenId"]').val(document.getElementById("PhoneNumber").value);

            $("#sendMessageModal").modal('show');
        });

        $(document).on('click','.EditInfo', function(){

            var Id = document.getElementById("StoreId").value;

            jQuery.ajax({
                // Controller Name is Orders/getItems
                url: "{{ url('/GetStoreInfo') }}",
                method: 'get',
                data: {
                    Id: Id
                },
                success: function(result){
                    console.log(result);

                    var Name = '';
                    var Phone = '';
                    var Email = '';
                    var OwnerName = '';
                    var OwnerPhone = '';
                    var Location = '';
                    var Status = '';

                    $.each(result, function (key, value) {

                        if (key === "Name") {
                            Name = value;
                        } else if (key === "Phone") {
                            Phone = value;
                        } else if (key === "Email") {
                            Email = value;
                        } else if (key === "OwnerName") {
                            OwnerName = value;
                        } else if (key === "OwnerPhone") {
                            OwnerPhone = value;
                        } else if (key === "Location") {
                            Location = value;
                        } else if (key === "Status") {
                            Status = value;
                        }
                    });


                    $("#editStoreModal").find('input[name="Id"]').val(Id);
                    $("#editStoreModal").find('input[name="Name"]').val(Name);
                    $("#editStoreModal").find('input[name="Phone"]').val(Phone);
                    $("#editStoreModal").find('input[name="Email"]').val(Email);
                    $("#editStoreModal").find('input[name="OwnerName"]').val(OwnerName);
                    $("#editStoreModal").find('input[name="OwnerPhone"]').val(OwnerPhone);
                    $("#editStoreModal").find('input[name="Location"]').val(Location);

                    $("#Status").val(Status).attr("selected","selected");

                    // To remove validation
                    $("#editStoreModal").find('small').hide();

                    $("#editStoreModal").modal('show');

                }});

        });

    </script>

    @if(!empty(Session::get('error_store')) && Session::get('error_store') == 1)
        <script>
            $(function() {
                $('#editStoreModal').modal('show');
            });
        </script>
    @endif

    <div id="toast_message">Added successfully</div>

    @if(!empty(Session::get('error_edit_store')) && Session::get('error_edit_store') == 3)
        <script>
            var x = document.getElementById("toast_message");
            x.innerHTML = '{{ __('messages.UpdatedSuccessfully') }}';
            x.className = "show";
            setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);

        </script>
    @endif


@stop
