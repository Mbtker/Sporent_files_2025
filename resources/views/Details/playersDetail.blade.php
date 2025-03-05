@extends('master')

@section('content')

    <head>
        <link rel="stylesheet" href="{{ URL::asset('css/detailStyle.css') }}" />

    </head>

    <h3 class="i-name">{{ __('messages.Details') }}</h3>

    <!-- edit Players Modal -->
    @include('modals.editPlayersModal')

    <!-- Show User Info -->
    @include('reusable.showUserInfo')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>

        $(document).on('click','.EditInfo', function(){

            var Id = document.getElementById("Id").value;

            jQuery.ajax({
                // Controller Name is Orders/getItems
                url: "{{ url('/GetPlayerInfo') }}",
                method: 'get',
                data: {
                    Id: Id
                },
                success: function(result){
                    console.log(result);

                    var Name = '';
                    var OrganizeCategory = '';
                    var Phone = '';
                    var Email = '';
                    var Location = '';
                    var Status = '';

                    $.each(result, function (key, value) {

                        if (key === "Name") {
                            Name = value;
                        } else if (key === "OrganizeCategory") {
                            OrganizeCategory = value;
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

                    $("#editPlayersModal").find('input[name="UserId"]').val(Id);
                    $("#editPlayersModal").find('input[name="Name"]').val(Name);
                    $("#editPlayersModal").find('input[name="Phone"]').val(Phone);
                    $("#editPlayersModal").find('input[name="Email"]').val(Email);

                    $("#Status").val(Status).attr("selected","selected");

                    // To remove validation
                    $("#editPlayersModal").find('small').hide();

                    $("#editPlayersModal").modal('show');

                }});

        });

    </script>


    @if(!empty(Session::get('error_players')) && Session::get('error_players') == 1)
        <script>
            $(function() {
                $('#editPlayersModal').modal('show');
            });
        </script>
    @endif

    <div id="toast_message">Added successfully</div>

    @if(!empty(Session::get('error_edit_players')) && Session::get('error_edit_players') == 3)
        <script>
            var x = document.getElementById("toast_message");
            x.innerHTML = '{{ __('messages.UpdatedSuccessfully') }}';
            x.className = "show";
            setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);

        </script>
    @endif
@stop
