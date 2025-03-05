
<!-- Send Message Modal -->
@include('modals.sendMessageModal')

<div class="content">
    <div class="top_card">

        <input type="text" class="form-control" id="Id" name="Id" value="{{ $User->Id }}" hidden/>
        <input type="text" class="form-control" id="TokenID" name="TokenID" value="{{ $User->TokenId }}" hidden/>
        <input type="text" class="form-control" id="FullName" name="FullName" value="{{ $User->Name }}" hidden/>
        <input type="text" class="form-control" id="PhoneNumber" name="PhoneNumber" value="{{ $User->Phone }}" hidden/>
        <div class="info_row">
            <div class="mt-2">
                <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.Name') }}:</label> {{ $User->Name }}</label>
            </div>
            <div class="mt-2">
                <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.Nationality') }}:</label> {{ $User->Nationality }}</label>
            </div>
            <div class="mt-2">
                <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.NationalIdNumber') }}:</label> {{ $User->IdNumber }}</label>
            </div>
        </div>
        <div class="info_row">
            <div class="mt-2">
                <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.Phone') }}:</label> {{ $User->Phone }}</label>
            </div>
            <div class="mt-2">
                <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.Email') }}:</label> {{ $User->Email }}</label>
            </div>
            <div class="mt-2">
                <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.CityName') }}:</label> {{ $User->CityName }}</label>
            </div>
        </div>
        <div class="info_row">
            <div class="mt-2">
                <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.DeviceType') }}:</label> {{ $User->DeviceType }}</label>
            </div>
            <div class="mt-2">
                <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.AppLanguage') }}:</label> {{ $User->Lang }}</label>
            </div>
            <div class="mt-2">
                <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.joinDate') }}:</label> {{  date('Y-m-d', strtotime($User->CreateDate))  }}</label>
            </div>
        </div>
        <div class="info_row">
            <div class="mt-2">
                <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.LastActive') }}:</label> {{ (new \App\Http\Controllers\HelperFun)->CalculatingDaysHours($User->{'LastActive'}) }}</label>
            </div>
            <div class="mt-2">
                <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.Status') }}:</label> @if ($User->{'Status'} == '1') {{ __('messages.Active') }} @else {{ __('messages.Inactive') }} @endif</label>
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
        document.getElementById("ToName").innerHTML = document.getElementById("FullName").value;

        $("#sendMessageModal").find('input[name="IsSMS"]').val(0);

        $("#sendMessageModal").find('input[name="TokenId"]').val(document.getElementById("TokenID").value);

        $("#sendMessageModal").modal('show');
    });

    $(document).on('click','#SendSMS', function() {

        document.getElementById("MessageModalTitle").innerHTML = document.getElementById("SendSMS").value;
        document.getElementById("ToName").innerHTML = document.getElementById("FullName").value;

        $("#sendMessageModal").find('input[name="IsSMS"]').val(1);

        $("#sendMessageModal").find('input[name="TokenId"]').val(document.getElementById("PhoneNumber").value);

        $("#sendMessageModal").modal('show');
    });
</script>
