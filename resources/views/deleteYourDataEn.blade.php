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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="{{URL::asset('css/masterStyle.css')}}" />
    <link rel="stylesheet" href="{{URL::asset('css/terms_of_use_style.css')}}" />
    <link rel="stylesheet" href="{{URL::asset('css/terms_of_use_en_style.css')}}" />

    <style>

        input[type=text], input[type=email] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .MyButton {
            background: #3E0277;
            border-radius: 8px;
            border: none;
            width: 100px;
            height: 43px;
            cursor: pointer;
            text-decoration: none;
            margin-bottom: 20px;
            font-size: 16px;
            color: white;
        }


        .MyButton:hover {
            background: #408dab;
        }

        .ButtonDisabled {
            background: #2d3748;
        }

        /* Start Toast css*/
        #toast_message {
            visibility: hidden;
            background-color: #408dab;
            color: white;
            text-align: center;
            padding-top: 10px;
            padding-bottom: 10px;
            /*position: fixed;*/
            z-index: 1;
            left: 50%;
            margin: auto;
            width: 50%;
            bottom: 30px;
            font-weight: 500;
            font-size: 14px;
            font-style: normal;
            border-radius: 25px;
        }

        #toast_message.show {
            visibility: visible;
            -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }

        a {
            text-decoration: none;
        }
    </style>
</head>
<body>

<!-- header section starts  -->


<header>

    <a href="#" class="logo"><img src="{{URL::asset('images/logo.png')}}" alt=""></a>

    <div id="menu" class="fas fa-bars"></div>

    <nav class="navbar">
        <ul>
            <li><a href="{{ route('LandingEn') }}">Home</a></li>
            <li><a href="{{ route('LandingEn') }}">About Sporent</a></li>
            <li><a href="{{ route('LandingEn') }}">Contact us</a></li>
            <li><a href="{{ route('DeleteYourData') }}">Ar</a></li>

        </ul>
    </nav>

</header>

<!-- header section ends -->

<!-- Social Media-->
@include('socialMedia')

<!-- feature section starts  -->

<section class="home" id="home">
    <div class="heading"><span>Delete your data</span></div>
    <div class="box-container">
        <div class="box">

            <p>You can delete your data from our platform by submition this form:</p>
            <div class="MyTitle"></div>

            <div>
                <form method="POST" action="{{ route('sendDeleteUserData') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <p>Your full name: @error('Name') <small id="ErrorName" class="form-text text-danger">{{ $message }}</small> @enderror</p>
                        <input type="text" id="Name" class="form-control" name="Name" required autofocus>
                    </div>
                    <div class="mb-3">
                        <p>Your phone number: @error('Phone') <small id="ErroPhone" class="form-text text-danger">{{ $message }}</small> @enderror</p>
                        <input type="text" id="Phone" class="form-control" name="Phone" required autofocus>
                    </div>
                    <div class="mb-3">
                        <p>Your Email:</p>
                        <input type="email"  id="Email">
                    </div>

                    <button id="button" type="submit" class="MyButton">Submit</button>
                </form>
                <div id="toast_message">Send successfully, We will send you a confirm request on your account</div>
            </div>

        </div>
    </div>

    @if(!empty(Session::get('send_successfully')) && Session::get('send_successfully') == 2)
        <script>
            alert("Send successfully, We will send you a confirm request on your account");
        </script>
    @endif

    @if(!empty(Session::get('error_send')) && Session::get('error_send') == 3)
        <script>
            alert("You have already submit a request");
        </script>
    @endif

</section>


<!-- footer section ends -->


<!-- jquery cdn link  -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- owl carousel js cdn link  -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<!-- custom js file link  -->
<script src="{{URL::asset('js/landing_page_js.js')}}"></script>

</body>
</html>
