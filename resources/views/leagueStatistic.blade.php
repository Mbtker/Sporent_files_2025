<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سبورنت - Sporent</title>

    <!-- owl carousel css cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- custom css file link  -->
    <link rel="stylesheet" href="{{URL::asset('css/landing_page_style.css')}}" />

    <style>

        ul {
            padding: 5px;
        }
        a {
            font-size: 14px;
            color: #848484;
            text-decoration: none;
        }

        .nav-link {
            color: #c4c4c4;
        }

        .active {
            color: #fff;
        }

        .unactive {
            color: #c4c4c4;
        }

        .nav-item {
            color: #fff;
            margin-right: 10px;
        }

        .nav-item :hover{
            color: #fff;
        }

        .table {
            color: white;
            font-size: 18px;
        }

        .NoData {
            text-align: center;
            color: #dcdcdc;
            font-size: 12px;
            margin-top: 50px;
        }

        .Team_logo {
            margin-right: 10px;
            float: right;
        }
        .Team_logo label {
             margin-right: 10px;
         }

        .Team_logo img {
            width: 40px;
            height: 40px;
            border-radius: 20px;
        }
    </style>

</head>
<body style="background-color: #6b7280;">
<div style="padding-top: 15px; background-color: #6b7280">
    <img style="border-radius: 40px; width: 80px; height: 80px; display: block; margin-left: auto; margin-right: auto;" src='{{URL::asset('images/logo_dark.jpg')}}'  alt="logo"/>
    <h3 style="text-align: center; padding: 8px; color: white; font-weight: bolder">{{ $LeagueTopic }}</h3>
</div>
<div class="centralize">
    <nav class="navbar navbar-dark bg-dark justify-content-center" style="border-top-right-radius: 20px; border-top-left-radius: 20px;">
        <ul class="nav justify-content-center">
            <li class="nav-item">
                <a class="nav-link  @if(isset($_COOKIE['SortBy']) && $_COOKIE['SortBy'] == 'RedCard') {{ 'active' }} @endif" id="RedCardButton" aria-current="page" href="#">كرت احمر</a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(isset($_COOKIE['SortBy']) && $_COOKIE['SortBy'] == 'YellowCard') {{ 'active' }} @endif" id="YellowCardButton" href="#">كرت اصفر</a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(isset($_COOKIE['SortBy']) && $_COOKIE['SortBy'] == 'Assistant') {{ 'active' }} @endif" id="AssistantButton" href="#">صانع اهداف</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  @if(!isset($_COOKIE['SortBy']) || $_COOKIE['SortBy'] == 'Goal') {{ 'active' }} @endif" id="GoalButton" href="#">اهداف</a>
            </li>
        </ul>
    </nav>
    {!! $MyTable !!}
<!-- To show no data if array count is 0 -->

{{--    <div style="margin: 8px; font-size: 11px">--}}
{{--        @if (isset($MyArray))--}}
{{--            {{ $MyArray->onEachSide(1)->links() }}--}}
{{--        @endif--}}
{{--    </div>--}}

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>

    $(document).ready(function () {

        $(document).on('click', '#GoalButton', function () {
            document.getElementById("GoalButton").classList.add('active');

            document.getElementById("AssistantButton").classList.remove('active');
            document.getElementById("YellowCardButton").classList.remove('active');
            document.getElementById("RedCardButton").classList.remove('active');

            document.cookie  = "SortBy = Goal";

            window.location.href="{{ route('leagueStatistic') }}";
        });

        $(document).on('click', '#AssistantButton', function () {
            document.getElementById("AssistantButton").classList.add('active');

            document.getElementById("GoalButton").classList.remove('active');
            document.getElementById("YellowCardButton").classList.remove('active');
            document.getElementById("RedCardButton").classList.remove('active');

            document.cookie  = "SortBy = Assistant";

            window.location.href="{{ route('leagueStatistic') }}";
        });

        $(document).on('click', '#YellowCardButton', function () {
            document.getElementById("YellowCardButton").classList.add('active');

            document.getElementById("GoalButton").classList.remove('active');
            document.getElementById("AssistantButton").classList.remove('active');
            document.getElementById("RedCardButton").classList.remove('active');

            document.cookie  = "SortBy = YellowCard";

            window.location.href="{{ route('leagueStatistic') }}";
        });

        $(document).on('click', '#RedCardButton', function () {
            document.getElementById("RedCardButton").classList.add('active');

            document.getElementById("GoalButton").classList.remove('active');
            document.getElementById("AssistantButton").classList.remove('active');
            document.getElementById("YellowCardButton").classList.remove('active');

            document.cookie  = "SortBy = RedCard";

            window.location.href="{{ route('leagueStatistic') }}";
        });

    });

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>
</html>
