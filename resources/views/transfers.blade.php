@extends('master')

@section('content')

    <h3 class="i-name">{{ __('messages.Transfers') }}</h3>

    <!-- edit Transfers Modal -->
    @include('modals.editPlayersModal')

    <div id="TableView">
        <div class="TopItemInTable">
            <div class="buttonsTable">
                <!-- HTML !-->
                <a id="All" class="buttonsTable-button @if(!isset($_COOKIE['SortBy']) || $_COOKIE['SortBy'] == 'All') {{ 'button-Active' }} @endif" href="#">{{ __('messages.All') }}</a>
                <a id="New" class="buttonsTable-button @if(!isset($_COOKIE['SortBy']) || $_COOKIE['SortBy'] == 'New') {{ 'button-Active' }} @endif" href="#">{{ __('messages.New') }}</a>
                <a id="Closed" class="buttonsTable-button @if(!isset($_COOKIE['SortBy']) || $_COOKIE['SortBy'] == 'Closed') {{ 'button-Active' }} @endif" href="#">{{ __('messages.Closed') }}</a>
            </div>
            <div class="Space"></div>
            <div class="SearchTable">
                <div class="input-group">
                    <input name="SearchText" type="text" class="form-control" placeholder="{{ __('messages.SearchPlaceholderByPlayerId') }}" aria-label="Recipient's username" aria-describedby="button-addon2">
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
                    url: "{{ url('/TransfersSearching') }}",
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
                window.location.href="{{ route('Transfers') }}";
            }
        });

        $(document).on('click','#PlayerDetails', function(){

            let currentRow=$(this).closest("tr");
            let PlayerId = currentRow.find("td:eq(3)").text(); // get current PlayerId

            document.cookie  = "PlayerId = " + PlayerId;

            window.location.href="{{ route('PlayerDetails') }}";
        });

        $(document).on('click','#FromTeamDetails', function(){

            let currentRow=$(this).closest("tr");
            let FromTeamId = currentRow.find("td:eq(5)").text(); // get current FromTeamId

            document.cookie  = "TeamId = " + FromTeamId;

            window.location.href="{{ route('TeamDetails') }}";
        });

        $(document).on('click','#ToTeamDetails', function(){

            let currentRow=$(this).closest("tr");
            let ToTeamId = currentRow.find("td:eq(7)").text(); // get current ToTeamId

            document.cookie  = "TeamId = " + ToTeamId;

            window.location.href="{{ route('TeamDetails') }}";
        });

        $(document).on('click','.Edit', function(){

            let currentRow=$(this).closest("tr");
            let UserId = currentRow.find("td:eq(0)").text(); // get current Id
            let Name = currentRow.find("td:eq(1)").text(); // get current Name
            let Phone = currentRow.find("td:eq(2)").text(); // get current Phone
            let Email = currentRow.find("td:eq(3)").text(); // get current Email


            $("#editPlayersModal").find('input[name="UserId"]').val(UserId);
            $("#editPlayersModal").find('input[name="Name"]').val(Name);
            $("#editPlayersModal").find('input[name="Phone"]').val(Phone);
            $("#editPlayersModal").find('input[name="Email"]').val(Email);

            var Status = currentRow.find("td:eq(10)").text(); // get current Status
            $("#Status").val(Status).attr("selected","selected");

            // To remove validation
            $("#editPlayersModal").find('small').hide();

            $("#editPlayersModal").modal('show');

        });
    </script>

    <script>

        $(document).ready(function () {

            $(document).on('click', '#All', function () {
                document.getElementById("All").classList.add('button-Active');

                document.getElementById("New").classList.remove('button-Active');
                document.getElementById("Closed").classList.remove('button-Active');

                document.cookie  = "SortBy = All";

                window.location.href="{{ route('Transfers') }}";
            });

            $(document).on('click', '#New', function () {
                document.getElementById("New").classList.add('button-Active');

                document.getElementById("All").classList.remove('button-Active');
                document.getElementById("Closed").classList.remove('button-Active');

                document.cookie  = "SortBy = New";

                window.location.href="{{ route('Transfers') }}";
            });

            $(document).on('click', '#Closed', function () {
                document.getElementById("Closed").classList.add('button-Active');

                document.getElementById("New").classList.remove('button-Active');
                document.getElementById("All").classList.remove('button-Active');

                document.cookie  = "SortBy = Closed";

                window.location.href="{{ route('Transfers') }}";
            });
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
