@extends('master')

@section('content')

    <head>
        <link rel="stylesheet" href="{{ URL::asset('css/detailStyle.css') }}" />

    </head>

    <h3 class="i-name">{{ __('messages.Details') }}</h3>

    <!-- edit Organizers Modal -->
    @include('modals.editOrganizersModal')

    <!-- Show User Info -->
    @include('reusable.showUserInfo')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>

        $(document).on('click','.EditInfo', function(){

            var Id = document.getElementById("Id").value;

            jQuery.ajax({
                // Controller Name is Orders/getItems
                url: "{{ url('/GetOrganizersInfo') }}",
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

                    $("#editOrganizersModal").find('input[name="UserId"]').val(Id);
                    $("#editOrganizersModal").find('input[name="Name"]').val(Name);
                    $("#editOrganizersModal").find('input[name="Category"]').val(OrganizeCategory);
                    $("#editOrganizersModal").find('input[name="Phone"]').val(Phone);
                    $("#editOrganizersModal").find('input[name="Email"]').val(Email);
                    $("#editOrganizersModal").find('input[name="Location"]').val(Location);

                    $("#Status").val(Status).attr("selected","selected");

                    // To remove validation
                    $("#editOrganizersModal").find('small').hide();

                    $("#editOrganizersModal").modal('show');

                }});

        });

    </script>


    @if(!empty(Session::get('error_organizers')) && Session::get('error_organizers') == 1)
        <script>
            $(function() {
                $('#editOrganizersModal').modal('show');
            });
        </script>
    @endif

    <div id="toast_message">Added successfully</div>

    @if(!empty(Session::get('error_edit_organizers')) && Session::get('error_edit_organizers') == 3)
        <script>
            var x = document.getElementById("toast_message");
            x.innerHTML = '{{ __('messages.UpdatedSuccessfully') }}';
            x.className = "show";
            setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);

        </script>
    @endif
@stop
