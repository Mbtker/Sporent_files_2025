<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سبورنت - Sporent</title>

    <!-- owl carousel css cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">

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
    <label class="LeagueTopic">{{ $LeagueTopic }}</label>
</div>
<div class="centralize StatisticLogin" style="background: rgba(0,0,0,0.57); border-radius: 20px; margin-top: 20px;align-content: center; align-items: center; padding-top: 10px; padding-bottom: 10px;">
    <div class="input-group mb-3" style="width: 60%; display: flex; margin-right: auto; margin-left: auto; margin-top: 40px">
        <input type="password" id="password" class="form-control" aria-label="Sizing example input" placeholder="Login password" style="width: 300px; border-radius: 15px; height: 40px; text-align: center; font-size: 16px; background-color: #f7f7f7" aria-describedby="inputGroup-sizing-default">
    </div>
    <div style="display: flex; margin-right: auto; margin-left: auto; width: 180px; padding-top: 15px; margin-bottom: 40px">
        <button class="LoginButton" id="LoginButton" style="color: black; text-decoration: none; font-size: 16px; width: 180px; height: 40px; cursor: pointer; border-radius: 15px">Login</button>
    </div>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>

    $(document).ready(function () {

        $(document).on('click', '#LoginButton', function () {

            let GetPass = document.getElementById('password').value;

            if (GetPass === '') {
                alert('Enter the password!');
            } else if (GetPass === 'Ramadan@25') {
                <?php use Illuminate\Support\Facades\Session;Session::put('leagueStatisticLogin', 'okey');?>
                window.location.href="{{ route('leagueStatistic') }}";
            } else {
                alert('Wrong password!');
            }
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
