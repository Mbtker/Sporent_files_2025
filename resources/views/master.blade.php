<!DOCTYPE html>
<html dir="rtl">
<head>
    <title>Spornt</title>
    <meta charset="UTF-8">
    <!-- Import font awesome-->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"  />
    <!-- Import bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    {{--    <meta name="viewport" content="width=device-width, initial-scale=0.1">--}}
    <link rel="stylesheet" href="{{URL::asset('css/masterStyle.css')}}" />
    <link rel="stylesheet" href="{{URL::asset('css/Style.css')}}" />

</head>
<body>


<!-- Header -->
@include('includes.header')

<!-- SideMenu-->
@include('includes.sideMenu')


<section class="sectionContent">
    @yield('content')
</section>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>


<!-- jquery-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<!--  JS -->
<script src="{{URL::asset('js/script.js')}}"></script>

<script>
    $(document).ready(function () {

        const body = document.querySelector("body");
        var SideLabel = document.querySelector("SideLabel");
        var modeText = body.querySelector(".language-switch");

        let locate = "{{ config('app.locale') }}";

        if(locate === 'ar')
        {
            body.classList.add("Ar");
            modeText.innerText = 'En';

        } else
        {
            body.classList.remove("Ar");
            modeText.innerText = 'Ar';
        }

        $(document).on('click', '.language-switch', function () {

            setTimeout( function(){
                // Do something after 1 second

                let locate = "{{ config('app.locale') }}";

                if(locate === 'ar')
                {
                    body.classList.remove("Ar");
                    modeText.innerText = 'Ar';

                } else
                {
                    body.classList.add("Ar");
                    modeText.innerText = 'En';
                }

            }  , 600 );

        });

    })
</script>

<script>

    $(document).ready(function () {

        $(document).on('click', '#RestSortBy', function () {

            document.cookie  = "SortBy = All";

            {{--window.location.href="{{ route('Supervisors') }}";--}}
        });
    });

</script>

</body>
</html>
