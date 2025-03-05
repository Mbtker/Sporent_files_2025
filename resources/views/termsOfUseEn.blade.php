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
    <link rel="stylesheet" href="{{URL::asset('css/terms_of_use_style.css')}}" />
    <link rel="stylesheet" href="{{URL::asset('css/terms_of_use_en_style.css')}}" />


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
            <li><a href="{{ route('TermsOfUse') }}">Ar</a></li>

        </ul>
    </nav>

</header>

<!-- header section ends -->

<!-- Social Media-->
@include('socialMedia')

<!-- feature section starts  -->

<section class="home" id="home">
    <div class="heading"><span>Terms Of Use</span></div>
    <div class="box-container">
        <div class="box">
            <p>A Saudi Team Specialized In The Field Of Sports And Supporting Players And Amateurs For Football And All Sports To Practice Their Favorite Sport, To Show Their Talents And Facilitate Services Dedicated To Everything The Amateur Needs.
            A Saudi Team Specialized In The Field Of Sports And Supporting Players And Amateurs For Football And All Sports To Practice Their Favorite Sport, To Show Their Talents And Facilitate Services Dedicated To Everything The Amateur Needs.
            A Saudi Team Specialized In The Field Of Sports And Supporting Players And Amateurs For Football And All Sports To Practice Their Favorite Sport, To Show Their Talents And Facilitate Services Dedicated To Everything The Amateur Needs.</p>
            <p>A Saudi Team Specialized In The Field Of Sports And Supporting Players And Amateurs For Football And All Sports To Practice Their Favorite Sport, To Show Their Talents And Facilitate Services Dedicated To Everything The Amateur Needs.</p>
            <p>A Saudi Team Specialized In The Field Of Sports And Supporting Players And Amateurs For Football And All Sports To Practice Their Favorite Sport, To Show Their Talents And Facilitate Services Dedicated To Everything The Amateur Needs.</p>
            <p>A Saudi Team Specialized In The Field Of Sports And Supporting Players And Amateurs For Football And All Sports To Practice Their Favorite Sport, To Show Their Talents And Facilitate Services Dedicated To Everything The Amateur Needs.</p>
            <p>A Saudi Team Specialized In The Field Of Sports And Supporting Players And Amateurs For Football And All Sports To Practice Their Favorite Sport, To Show Their Talents And Facilitate Services Dedicated To Everything The Amateur Needs.</p>
            <p>A Saudi Team Specialized In The Field Of Sports And Supporting Players And Amateurs For Football And All Sports To Practice Their Favorite Sport, To Show Their Talents And Facilitate Services Dedicated To Everything The Amateur Needs.</p>
            <p>A Saudi Team Specialized In The Field Of Sports And Supporting Players And Amateurs For Football And All Sports To Practice Their Favorite Sport, To Show Their Talents And Facilitate Services Dedicated To Everything The Amateur Needs.</p>
            <p>A Saudi Team Specialized In The Field Of Sports And Supporting Players And Amateurs For Football And All Sports To Practice Their Favorite Sport, To Show Their Talents And Facilitate Services Dedicated To Everything The Amateur Needs.</p>
        </div>
    </div>

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
