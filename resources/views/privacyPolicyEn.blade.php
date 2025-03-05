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
            <li><a href="{{ route('PrivacyPolicy') }}">Ar</a></li>

        </ul>
    </nav>

</header>

<!-- header section ends -->

<!-- Social Media-->
@include('socialMedia')


<!-- feature section starts  -->

<section class="home" id="home">
    <div class="heading"><span>Privacy Policy</span></div>
    <div class="box-container">
        <div class="box">
            <p>This page is used to inform visitors regarding our policies with the collection, use, and disclosure of Personal Information if anyone decided to use our Service. If you choose to use our Service, then you agree to the collection and use of information in relation to this policy. The Personal Information that we collect is used for providing and improving the service. We will not use or share your information with anyone except as described in this Privacy Policy.</p>
            <div class="MyTitle"></div>
            <p>The terms used in this Privacy Policy have the same meanings as in our Terms and Conditions, which is accessible at our platform unless otherwise defined in this Privacy Policy. Information Collection and Use For a better experience, while using our Service, we may require you to provide us with certain personally identifiable information. The information that we request will be retained by us and used as described in this privacy policy.</p>
            <p>The app does use third party services that may collect information used to identify you. Link to privacy policy of third party service providers used by the app:</p>
            <ul class="MyLink"><li><a href="https://www.google.com/policies/privacy/" target="_blank">Google Play Services</a></li></ul>
            <ul class="MyLink"><li><a href="https://urway.sa/policy" target="_blank">URWAY Privacy Policy</a></li></ul>

            <div class="MyTitle">Log Data</div>
            <p>We want to inform you that whenever you use our Service, in a case of an error in the app we collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (“IP”) address, device name, operating system version, the configuration of the app when utilizing our Service, the time and date of your use of the Service, and other statistics.</p>

            <div class="MyTitle">Cookies</div>
            <p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your device's internal memory. This Service does not use these “cookies” explicitly. However, the app may use third party code and libraries that use “cookies” to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>

            <div class="MyTitle">Service Providers</div>
            <p>We may employ third-party companies and individuals due to the following reasons:</p>
            <ul class="MyList">
                <li>To facilitate our Service</li>
                <li>To provide the Service on our behalf</li>
                <li>To perform Service-related services or to assist us in analyzing how our Service is used</li>
            </ul>
            <p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>

            <div class="MyTitle">Security</div>
            <p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that the internet, or method of electronic storage is 100% secure and reliable, and we cannot guarantee its absolute security.</p>

            <div class="MyTitle">Links to Other Sites</div>
            <p>This Service may contain links to other sites. If you click on a third-party link, you will be directed to that site. Note that these external sites are not operated by us. Therefore, we strongly advise you to review the Privacy Policy of these websites. We have no control over and assume no responsibility for the content, privacy policies, or practices of any third-party sites or services.</p>

            <div class="MyTitle">Children’s Privacy</div>
            <p>These Services do not address anyone under the age of 8. We do not knowingly collect personally identifiable information from children under 13. In the case we discover that a child under 8 has provided us with personal information, we immediately delete this from our servers. If you are a parent or guardian and you are aware that your child has provided us with personal information, please contact us so that we will be able to do necessary actions.</p>

            <div class="MyTitle">Changes to This Privacy Policy</div>
            <p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page. These changes are effective immediately after they are posted on this page.</p>

            <div class="MyTitle">Contact Us</div>
            <p>Contact Us If you have any questions or suggestions about our Privacy Policy, do not hesitate to contact us.</p>

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
