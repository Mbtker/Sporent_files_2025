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

    <!-- custom css file link  -->
    <link rel="stylesheet" href="{{URL::asset('css/masterStyle.css')}}" />
    <link rel="stylesheet" href="{{URL::asset('css/landing_page_style.css')}}" />
    <link rel="stylesheet" href="{{URL::asset('css/landing_page_en_style.css')}}" />


</head>
<body>

<!-- header section starts  -->

<header>

    <a href="#" class="logo"><img src="{{URL::asset('images/logo.png')}}" alt=""></a>

    <div id="menu" class="fas fa-bars"></div>

    <nav class="navbar">
        <ul>
            <li><a class="active" href="#home">Home</a></li>
            <li><a href="#feature">About Sporent</a></li>
            <li><a href="#ic">Contact us</a></li>
            <li><a href="{{ route('LandingAr') }}">Ar</a></li>

        </ul>
    </nav>

</header>

<!-- header section ends -->

<!-- Social Media-->
@include('socialMedia')

<!-- home section starts  -->

<section class="home" id="home">

    <div class="content">
        <h1><span>Sporent</span></h1>
        <p>It is a sports platform that is concerned with various sports and targets young people of all ages to provide them with a flexible electronic platform that helps them communicate, organize exercises and teams, develop their talents and develop their skills in all fields of sports, which helps them build their profile in a giant sports social platform.</p>
        <div class="buttons">
            <div class="mobile_view">
                <ul>
                    <li><a href="https://itunes.apple.com/app/id6475586795" target="_blank"><button class="btn ios_store"><img src="{{URL::asset('images/app_store_icon.png')}}" alt=""></button></a></li>
                    <li><a href="https://play.google.com/store/apps/details?id=mbtkerteam.com.spornt" target="_blank"><button class="btn android_store"><img src="{{URL::asset('images/google_play_icon.png')}}" alt=""></button></a></li>
                </ul>
            </div>
            <div class="web_view">
                <a href="https://itunes.apple.com/app/id6475586795" target="_blank"><button class="btn"><img src="{{URL::asset('images/app_store_icon.png')}}" alt=""></button></a>
                <a href="https://play.google.com/store/apps/details?id=mbtkerteam.com.spornt" target="_blank"><button class="btn"><img src="{{URL::asset('images/google_play_icon.png')}}" alt=""></button></a>
            </div>
        </div>
    </div>

    <div class="image">
        <img src="{{URL::asset('images/app_screens.png')}}" alt="">
    </div>

</section>

<!-- home section ends -->

<!-- feature section starts  -->

<section class="feature" id="feature">

    <h1 class="heading"><span>Who We Are</span></h1>
    <div class="box-container">
        <div class="box">
            <p>A Saudi Team Specialized In The Field Of Sports And Supporting Players And Amateurs For Football And All Sports To Practice Their Favorite Sport, To Show Their Talents And Facilitate Services Dedicated To Everything The Amateur Needs.</p>
        </div>
    </div>

    <h1 class="heading"><span>Our vision</span></h1>
    <div class="box-container">
        <div class="box">
            <p>To Become '<span>Sporent</span>' The Primary Arab Destination For Practicing/Playing Sport Games In The Streets And Cities.</p>
        </div>
    </div>

    <h1 class="heading"><span>Our mission</span></h1>
    <div class="box-container">
        <div class="box">
            <p>Is Facilitating sports practices through a well-connected sports communities, in a wide service platform, to turn sports from a hobby to a professional level.</p>
        </div>
    </div>

</section>

<!-- download section starts  -->

<section class="download" id="download">
    <div class="box-container">
        <div class="box">
            <img class="imge_5" src="{{URL::asset('images/startup_icon.png')}}" alt="">
            <h3>+1200 </h3>
            <p>Installs</p>
        </div>
        <div class="box">
            <img class="imge_4" src="{{URL::asset('images/shops_icon.png')}}" alt="">
            <h3>{{ $Clinics }}</h3>
            <p>clinics</p>
        </div>
        <div class="box">
            <img class="imge_3" src="{{URL::asset('images/publications_icon.png')}}" alt="">
            <h3>{{ $Media }}</h3>
            <p>Publications</p>
        </div>
        <div class="box">
            <img class="imge_2" src="{{URL::asset('images/exercises_icon.png')}}" alt="">
            <h3>{{ $Exercises }}</h3>
            <p>Exercises</p>
        </div>
        <div class="box">
            <img class="imge_1" src="{{URL::asset('images/users_icon.png')}}" alt="">
            <h3>{{ $Users }}</h3>
            <p>Users</p>
        </div>
    </div>
</section>

<!-- download section ends -->

<!-- footer section starts  -->

<section class="footer">

    <div class="box-container">


        <div class="box">
            <img class="img_1" src="{{URL::asset('images/Logo_bottom.png')}}" alt="">

        </div>

        <div class="box">
            <h3>Quick Links</h3>
            <a href="#">Home</a>
            <a href="#">Download the app</a>
            <a href="#">Stats</a>

        </div>
        <div class="box">
            <h3>Important Links</h3>
            <a href="{{ route('TermsOfUse') }}">Terms of use</a>
            <a href="{{ route('PrivacyPolicyEn') }}">Privacy Policy</a>
            <a href="{{ route('DeleteYourDataEn') }}">Delete your data</a>

        </div>
        <div class="box">
            <h3>Connect us</h3>
            <div class="ic"  id="ic">
                <a href="#"> <img src="{{URL::asset('images/web_icon.png')}}" alt="">www.sporent.net</a>
                <a href="#"> <img src="{{URL::asset('images/email_icon.png')}}" alt="">support@sporent.net</a>
                <a href="https://wa.me/message/IKZ7NYIJZ5MXL1" target="_blank" > <img src="{{URL::asset('images/whats_icon.png')}}" alt="">+966-532781020</a>
                <a href="tel:00966532781020" target="_blank" > <img src="{{URL::asset('images/call_icon.png')}}" alt="">+966-532781020</a>


            </div>


            <div class="icons_sh">


                <a href="https://twitter.com/Sporent_ar" target="_blank"> <img src="{{URL::asset('images/tutr.png')}}" alt=""> </a>
                <a href="https://www.instagram.com/sporent_ar" target="_blank"> <img src="{{URL::asset('images/instg.png')}}" alt=""> </a>
                <a href="https://t.snapchat.com/kPRutV7b" target="_blank"> <img src="{{URL::asset('images/sna.png')}}" alt=""> </a>


            </div>
        </div>

    </div>

    <h1 class="credit"> <a href="#">Sporent - Sports Services - All rights reserved </a> </h1>

</section>

<!-- footer section ends -->


<!-- jquery cdn link  -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- owl carousel js cdn link  -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<!-- custom js file link  -->
<script src="{{URL::asset('js/landing_page_js.js')}}"></script>


<script>

    if( /iPhone|iPad|iPod|Opera Mini/i.test(navigator.userAgent) ) {
        // alert('This is iOS');
        [].forEach.call(document.querySelectorAll('.android_store'), function (el) {
            el.style.visibility = 'hidden';
        });
    }


    if( /Android|Opera Mini/i.test(navigator.userAgent) ) {
        // alert('This is Android');
        [].forEach.call(document.querySelectorAll('.ios_store'), function (el) {
            el.style.visibility = 'hidden';
        });
    }

</script>

</body>
</html>
