@extends('master')

@section('content')

    <head>
        <link rel="stylesheet" href="{{ URL::asset('css/detailStyle.css') }}" />
    </head>

    <!-- Send Message Modal -->
    @include('modals.sendMessageModal')

    <!-- edit Leagues Modal -->
    @include('modals.editLeaguesModal')

    <h3 class="i-name">{{ __('messages.Details') }}</h3>

    <div class="content">
        <div class="top_card">
            <input type="text" class="form-control" id="LeagueId" name="LeagueId" value="{{ $MyArray->Id }}" hidden/>
            <div class="info_row">
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.LeaguesTopic') }}:</label> {{ $MyArray->Topic }}</label>
                </div>
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.StadiumName') }}:</label> {{ $MyArray->StadiumName }}</label>
                </div>
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.CityName') }}:</label> {{ $MyArray->CityName }}</label>
                </div>
            </div>
            <div class="info_row">
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.Fee') }}:</label> {{ number_format((float)$MyArray->{'Fee'}, 2, '.', '')}} <label style="font-size: 12px; color: #51585e">SAR</label></label>
                </div>
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.Status') }}:</label> @if ($MyArray->{'Status'} == '1') {{ __('messages.Active') }} @else {{ __('messages.Inactive') }} @endif</label>
                </div>
            </div>
            <a class="EditInfo" href="#" style="text-decoration: none; color: #7e7e7e;"><i class="fad fa-edit" ></i></a>
        </div>
        <div class="Right_card">
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>

        $(document).on('click','.EditInfo', function(){

            var Id = document.getElementById("LeagueId").value;

            jQuery.ajax({
                // Controller Name is Orders/getItems
                url: "{{ url('/GetLeaguesInfo') }}",
                method: 'get',
                data: {
                    Id: Id
                },
                success: function(result){
                    console.log(result);

                    var Topic = '';
                    var Location = '';
                    var Fee = '';
                    var Status = '';

                    $.each(result, function (key, value) {

                        if (key === "Topic") {
                            Topic = value;
                        } else if (key === "Location") {
                            Location = value;
                        } else if (key === "Fee") {
                            Fee = value;
                        } else if (key === "Status") {
                            Status = value;
                        }
                    });

                    $("#editLeaguesModal").find('input[name="Id"]').val(Id);
                    $("#editLeaguesModal").find('input[name="Topic"]').val(Topic);
                    $("#editLeaguesModal").find('input[name="Location"]').val(Location);
                    $("#editLeaguesModal").find('input[name="Fee"]').val(Fee);

                    $("#Status").val(Status).attr("selected","selected");

                    // To remove validation
                    $("#editLeaguesModal").find('small').hide();

                    $("#editLeaguesModal").modal('show');

                }});

        });

    </script>

    @if(!empty(Session::get('error_leagues')) && Session::get('error_leagues') == 1)
        <script>
            $(function() {
                $('#editLeaguesModal').modal('show');
            });
        </script>
    @endif

    <div id="toast_message">Added successfully</div>

    @if(!empty(Session::get('error_edit_leagues')) && Session::get('error_edit_leagues') == 3)
        <script>
            var x = document.getElementById("toast_message");
            x.innerHTML = '{{ __('messages.UpdatedSuccessfully') }}';
            x.className = "show";
            setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);

        </script>
    @endif


@stop
