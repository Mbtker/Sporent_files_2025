@extends('master')

@section('content')

    <h3 class="i-name">{{ __('messages.Exercises') }}</h3>

    <!-- edit Exercises Modal -->
    @include('modals.editExerciseModal')

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
                    <input name="SearchText" type="text" class="form-control" placeholder="{{ __('messages.SearchPlaceholderIdName') }}" aria-label="Recipient's username" aria-describedby="button-addon2">
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
                    url: "{{ url('/ExercisesSearching') }}",
                    method: 'get',
                    data: {
                        SearchText: SearchText
                    },
                    success: function(result){

                        var y = document.getElementById("ShowTableView");
                        y.innerHTML = result;
                    }});
            }
        });

        $(document).on('click','#StadiumDetails', function(){

            let currentRow=$(this).closest("tr");
            let StadiumId = currentRow.find("td:eq(7)").text(); // get current Id

            document.cookie  = "StadiumId = " + StadiumId;

            window.location.href="{{ route('StadiumDetails') }}";
        });

        $(document).on('click','#CreatByDetails', function(){

            let currentRow=$(this).closest("tr");
            let CreatById = currentRow.find("td:eq(9)").text(); // get current Id

            document.cookie  = "PlayerId = " + CreatById;

            window.location.href="{{ route('PlayerDetails') }}";
        });

        $(document).on('click','.Details', function(){

            let currentRow=$(this).closest("tr");
            let ExerciseId = currentRow.find("td:eq(0)").text(); // get current Id

            document.cookie  = "ExerciseId = " + ExerciseId;

            window.location.href="{{ route('ExerciseDetails') }}";

        });

        $(document).on('click','.Edit', function(){

            let currentRow=$(this).closest("tr");
            let Id = currentRow.find("td:eq(0)").text(); // get current Id
            let Topic = currentRow.find("td:eq(1)").text(); // get current Topic
            let ExerciseType = currentRow.find("td:eq(2)").text(); // get current ExerciseType
            let Location = currentRow.find("td:eq(5)").text(); // get current Location
            let Fee = currentRow.find("td:eq(11)").text(); // get current Fee
            let ExerciseDate = currentRow.find("td:eq(12)").text(); // get current ExerciseDate
            let Status = currentRow.find("td:eq(14)").text(); // get current Status

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

        });

    </script>

    <script>

        $(document).ready(function () {

            $(document).on('click', '#All', function () {
                document.getElementById("All").classList.add('button-Active');

                document.getElementById("Active").classList.remove('button-Active');
                document.getElementById("Inactive").classList.remove('button-Active');

                document.cookie  = "SortBy = All";

                window.location.href="{{ route('Exercises') }}";
            });

            $(document).on('click', '#Active', function () {
                document.getElementById("Active").classList.add('button-Active');

                document.getElementById("All").classList.remove('button-Active');
                document.getElementById("Inactive").classList.remove('button-Active');

                document.cookie  = "SortBy = Active";

                window.location.href="{{ route('Exercises') }}";
            });

            $(document).on('click', '#Inactive', function () {
                document.getElementById("Inactive").classList.add('button-Active');

                document.getElementById("Active").classList.remove('button-Active');
                document.getElementById("All").classList.remove('button-Active');

                document.cookie  = "SortBy = Inactive";

                window.location.href="{{ route('Exercises') }}";
            });
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
