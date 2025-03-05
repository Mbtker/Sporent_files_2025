@extends('master')

@section('content')

    <head>
        <link rel="stylesheet" href="{{ URL::asset('css/detailStyle.css') }}" />
    </head>

    <!-- Send Message Modal -->
    @include('modals.sendMessageModal')

    <!-- edit Exercises Modal -->
    @include('modals.editExerciseModal')

    <h3 class="i-name">{{ __('messages.Details') }}</h3>

    <div class="content">
        <div class="top_card">
            <input type="text" class="form-control" id="MatchId" name="MatchId" value="{{ $MyArray->Id }}" hidden/>
            <input type="text" class="form-control" id="TheExerciseDate" name="TheExerciseDate" value="{{ Carbon\Carbon::parse($MyArray->{'ExerciseDate'})->format('Y-m-d') }}" hidden/>
            <div class="info_row">
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.ExerciseTopic') }}:</label> {{ $MyArray->Topic }}</label>
                </div>
                <div class="mt-2">
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.ExerciseType') }}:</label> {{ $MyArray->ExerciseType }}</label>
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
                    <label class="Mytxt"><label style="font-weight: bold">{{ __('messages.ExerciseDate') }}:</label> {{ Carbon\Carbon::parse($MyArray->{'ExerciseDate'})->format('Y-m-d') }}</label>
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

            let Id = document.getElementById("MatchId").value;
            let ExerciseDate = document.getElementById("TheExerciseDate").value;

            jQuery.ajax({
                // Controller Name is Orders/getItems
                url: "{{ url('/GetExerciseInfo') }}",
                method: 'get',
                data: {
                    Id: Id
                },
                success: function(result){
                    console.log(result);

                    let Topic = '';
                    let ExerciseType = '';
                    let Location = '';
                    let Fee = '';
                    let Status = '';

                    $.each(result, function (key, value) {

                        if (key === "Topic") {
                            Topic = value;
                        } else if (key === "ExerciseType") {
                            ExerciseType = value;
                        } else if (key === "Location") {
                            Location = value;
                        } else if (key === "Fee") {
                            Fee = value;
                        } else if (key === "Status") {
                            Status = value;
                        }
                    });

                    $("#editExerciseModal").find('input[name="Id"]').val(Id);
                    $("#editExerciseModal").find('input[name="Topic"]').val(Topic);
                    $("#editExerciseModal").find('input[name="ExerciseType"]').val(ExerciseType);
                    $("#editExerciseModal").find('input[name="Fee"]').val(Fee);
                    $("#editExerciseModal").find('input[name="Location"]').val(Location);
                    $("#editExerciseModal").find('input[name="ExerciseDate"]').val(ExerciseDate);

                    $("#Status").val(Status).attr("selected","selected");

                    // To remove validation
                    $("#editExerciseModal").find('small').hide();

                    $("#editExerciseModal").modal('show');

                }});

        });

    </script>

    @if(!empty(Session::get('error_exercise')) && Session::get('error_exercise') == 1)
        <script>
            $(function() {
                $('#editExerciseModal').modal('show');
            });
        </script>
    @endif

    <div id="toast_message">Added successfully</div>

    @if(!empty(Session::get('error_edit_exercise')) && Session::get('error_edit_exercise') == 3)
        <script>
            var x = document.getElementById("toast_message");
            x.innerHTML = '{{ __('messages.UpdatedSuccessfully') }}';
            x.className = "show";
            setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);

        </script>
    @endif


@stop
