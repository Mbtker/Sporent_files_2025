@extends('master')

@section('content')

    <head>
        <link rel="stylesheet" href="{{ URL::asset('css/detailStyle.css') }}" />
    </head>

    <!-- Send Message Modal -->
    @include('modals.sendMessageModal')

    <!-- edit Match Modal -->
    @include('modals.editMatchModal')

    <h3 class="i-name">{{ __('messages.Details') }}</h3>

    <div class="content">
        <div class="top_card">
            <input type="text" class="form-control" id="MatchId" name="MatchId" value="{{ $MyArray->Id }}" hidden/>
            <input type="text" class="form-control" id="TheMatchDate" name="TheMatchDate" value="{{ Carbon\Carbon::parse($MyArray->{'MatchDate'})->format('Y-m-d') }}" hidden/>
            <div class="info_row">
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.MatchTopic') }}:</label> {{ $MyArray->Topic }}</label>
                </div>
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.MatchType') }}:</label> {{ $MyArray->MatchType }}</label>
                </div>
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.CityName') }}:</label> {{ $MyArray->CityName }}</label>
                </div>
            </div>
            <div class="info_row">
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.LeagueTopic') }}:</label> {{ $MyArray->LeagueTopic }}</label>
                </div>
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.StadiumName') }}:</label> {{ $MyArray->StadiumName }}</label>
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

            var Id = document.getElementById("MatchId").value;
            var MatchDate = document.getElementById("TheMatchDate").value;

            jQuery.ajax({
                // Controller Name is Orders/getItems
                url: "{{ url('/GetMatchInfo') }}",
                method: 'get',
                data: {
                    Id: Id
                },
                success: function(result){
                    console.log(result);

                    var Topic = '';
                    var Location = '';
                    var Status = '';

                    $.each(result, function (key, value) {

                        if (key === "Topic") {
                            Topic = value;
                        } else if (key === "Location") {
                            Location = value;
                        } else if (key === "Status") {
                            Status = value;
                        }
                    });

                    $("#editMatchModal").find('input[name="Id"]').val(Id);
                    $("#editMatchModal").find('input[name="Topic"]').val(Topic);
                    $("#editMatchModal").find('input[name="Location"]').val(Location);
                    $("#editMatchModal").find('input[name="MatchDate"]').val(MatchDate);

                    $("#Status").val(Status).attr("selected","selected");

                    // To remove validation
                    $("#editMatchModal").find('small').hide();

                    $("#editMatchModal").modal('show');

                }});

        });

    </script>

    @if(!empty(Session::get('error_match')) && Session::get('error_match') == 1)
        <script>
            $(function() {
                $('#editMatchModal').modal('show');
            });
        </script>
    @endif

    <div id="toast_message">Added successfully</div>

    @if(!empty(Session::get('error_edit_matches')) && Session::get('error_edit_matches') == 3)
        <script>
            var x = document.getElementById("toast_message");
            x.innerHTML = '{{ __('messages.UpdatedSuccessfully') }}';
            x.className = "show";
            setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);

        </script>
    @endif


@stop
