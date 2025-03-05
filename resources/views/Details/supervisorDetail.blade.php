@extends('master')

@section('content')

    <head>
        <link rel="stylesheet" href="{{ URL::asset('css/detailStyle.css') }}" />

    </head>

    <h3 class="i-name">{{ __('messages.Details') }}</h3>

    <!-- edit Supervisor Modal -->
    @include('modals.editSupervisorModal')

    <!-- Show User Info -->
    @include('reusable.showUserInfo')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>

        $(document).on('click','.EditInfo', function(){

            var Id = document.getElementById("Id").value;

            jQuery.ajax({
                // Controller Name is Orders/getItems
                url: "{{ url('/GetSupervisorsInfo') }}",
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
                    var AreaRange = '';
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
                        } else if (key === "AreaRange") {
                            AreaRange = value;
                        } else if (key === "Status") {
                            Status = value;
                        }
                    });

                    $("#editSupervisorModal").find('input[name="UserId"]').val(Id);
                    $("#editSupervisorModal").find('input[name="Name"]').val(Name);
                    $("#editSupervisorModal").find('input[name="Phone"]').val(Phone);
                    $("#editSupervisorModal").find('input[name="Email"]').val(Email);
                    $("#editSupervisorModal").find('input[name="Location"]').val(Location);
                    $("#editSupervisorModal").find('input[name="AreaRange"]').val(AreaRange);

                    $("#Status").val(Status).attr("selected","selected");

                    // To remove validation
                    $("#editSupervisorModal").find('small').hide();

                    $("#editSupervisorModal").modal('show');

                }});

        });

    </script>


    @if(!empty(Session::get('error_supervisor')) && Session::get('error_supervisor') == 1)
        <script>
            $(function() {
                $('#editSupervisorModal').modal('show');
            });
        </script>
    @endif

    <div id="toast_message">Added successfully</div>

    @if(!empty(Session::get('error_edit_supervisor')) && Session::get('error_edit_supervisor') == 3)
        <script>
            var x = document.getElementById("toast_message");
            x.innerHTML = '{{ __('messages.UpdatedSuccessfully') }}';
            x.className = "show";
            setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);

        </script>
    @endif
@stop
