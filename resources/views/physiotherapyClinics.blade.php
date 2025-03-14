@extends('master')

@section('content')

    <h3 class="i-name">{{ __('messages.PhysiotherapyClinics') }}</h3>

    <!-- edit PhysiotherapyClinics Modal -->
    @include('modals.editPhysiotherapyClinicsModal')

    <div id="TableView">
        <div class="TopItemInTable">
            <div class="buttonsTable">
                <!-- HTML !-->
                <a id="All" class="buttonsTable-button @if(!isset($_COOKIE['SortBy']) || $_COOKIE['SortBy'] == 'All') {{ 'button-Active' }} @endif" href="#">{{ __('messages.All') }}</a>
                <a id="Active" class="buttonsTable-button @if(!isset($_COOKIE['SortBy']) || $_COOKIE['SortBy'] == 'Active') {{ 'button-Active' }} @endif" href="#">{{ __('messages.Active') }}</a>
                <a id="Inactive" class="buttonsTable-button @if(!isset($_COOKIE['SortBy']) || $_COOKIE['SortBy'] == 'Inactive') {{ 'button-Active' }} @endif" href="#">{{ __('messages.Inactive') }}</a>
            </div>
            <div class="Space"></div>
            <div class="SearchTable">
                <div class="input-group">
                    <input name="SearchText" type="text" class="form-control" placeholder="{{ __('messages.SearchPlaceholder') }}" aria-label="Recipient's username" aria-describedby="button-addon2">
                    <button class="btn btn-outline-secondary" type="button" id="SearchButton"> <i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>

        {{-- Table View --}}
        <div id="ShowTableView">
            {!! $TableView !!}
        </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>

        $(document).on('click','#SearchButton', function(){

            let SearchText = $(document).find('input[name="SearchText"]').val();

            if(SearchText !== '')
            {
                jQuery.ajax({
                    url: "{{ url('/PhysiotherapyClinicsSearching') }}",
                    method: 'get',
                    data: {
                        SearchText: SearchText
                    },
                    success: function(result){

                        var y = document.getElementById("ShowTableView");
                        y.innerHTML = result;
                    }});
            } else
            {
                window.location.href="{{ route('PhysiotherapyClinics') }}";
            }
        });

        $(document).on('click','#TeamDetails', function(){

            let currentRow=$(this).closest("tr");
            let TeamId = currentRow.find("td:eq(7)").text(); // get current TeamId

            alert(TeamId);
        });

        $(document).on('click','.Details', function(){

            let currentRow=$(this).closest("tr");
            let PhysiotherapyClinicId = currentRow.find("td:eq(0)").text(); // get current Id

            document.cookie  = "PhysiotherapyClinicId = " + PhysiotherapyClinicId;

            window.location.href="{{ route('PhysiotherapyClinicDetails') }}";

        });

        $(document).on('click','.Edit', function(){

            let currentRow=$(this).closest("tr");
            let UserId = currentRow.find("td:eq(0)").text(); // get current Id
            let Name = currentRow.find("td:eq(2)").text(); // get current Name
            let Phone = currentRow.find("td:eq(3)").text(); // get current Phone
            let Email = currentRow.find("td:eq(4)").text(); // get current Email


            $("#editPhysiotherapyClinicsModal").find('input[name="UserId"]').val(UserId);
            $("#editPhysiotherapyClinicsModal").find('input[name="Name"]').val(Name);
            $("#editPhysiotherapyClinicsModal").find('input[name="Phone"]').val(Phone);
            $("#editPhysiotherapyClinicsModal").find('input[name="Email"]').val(Email);

            var Status = currentRow.find("td:eq(9)").text(); // get current Status
            $("#Status").val(Status).attr("selected","selected");

            // To remove validation
            $("#editPhysiotherapyClinicsModal").find('small').hide();

            $("#editPhysiotherapyClinicsModal").modal('show');

        });
    </script>

    <script>

        $(document).ready(function () {

            $(document).on('click', '#All', function () {
                document.getElementById("All").classList.add('button-Active');

                document.getElementById("Active").classList.remove('button-Active');
                document.getElementById("Inactive").classList.remove('button-Active');

                document.cookie  = "SortBy = All";

                window.location.href="{{ route('PhysiotherapyClinics') }}";
            });

            $(document).on('click', '#Active', function () {
                document.getElementById("Active").classList.add('button-Active');

                document.getElementById("All").classList.remove('button-Active');
                document.getElementById("Inactive").classList.remove('button-Active');

                document.cookie  = "SortBy = Active";

                window.location.href="{{ route('PhysiotherapyClinics') }}";
            });

            $(document).on('click', '#Inactive', function () {
                document.getElementById("Inactive").classList.add('button-Active');

                document.getElementById("Active").classList.remove('button-Active');
                document.getElementById("All").classList.remove('button-Active');

                document.cookie  = "SortBy = Inactive";

                window.location.href="{{ route('PhysiotherapyClinics') }}";
            });
        });

    </script>

    @if(!empty(Session::get('error_physiotherapy_clinics')) && Session::get('error_physiotherapy_clinics') == 1)
        <script>
            $(function() {
                $('#editPhysiotherapyClinicsModal').modal('show');
            });
        </script>
    @endif

    <div id="toast_message">Added successfully</div>

    @if(!empty(Session::get('error_edit_physiotherapy_clinics')) && Session::get('error_edit_physiotherapy_clinics') == 3)
        <script>
            var x = document.getElementById("toast_message");
            x.innerHTML = '{{ __('messages.UpdatedSuccessfully') }}';
            x.className = "show";
            setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);

        </script>
    @endif
@stop
