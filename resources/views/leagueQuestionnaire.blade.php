<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سبورنت - Sporent</title>
    <meta name="description" content="Questionnaire (AlUla Ramadan Championship 2024)">

    <!-- owl carousel css cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- custom css file link  -->
    <link rel="stylesheet" href="{{URL::asset('css/landing_page_style.css')}}" />

    <style>

        .nav-item :hover{
            color: #fff;
        }

        .Team_logo label {
             margin-right: 10px;
         }

        .Team_logo img {
            width: 40px;
            height: 40px;
            border-radius: 20px;
        }

        .formTopic {
            color: black;
            font-size: 15px;
            font-weight: bold;
            padding-top: 10px;
            padding-bottom: 5px;
            margin-right: 5px;
        }

        .formInput {
            color: black;
            width: 100%;
            height: 43px;
            background-color: #f5f9fd;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 8px;
            font-size: 13px;
        }

        .selectOption {
            text-align: right;
            padding: 5px;
            margin: 5px;
            font-size: 13px;
        }

        .MySlider {
            accent-color: #3E0277;
        }

        .Items {
            margin-top: 8px;
        }

        .ButtDiv {
            margin-top: 12px;
            display: flex;
            margin-right: auto;
            margin-left: auto;
            width: 120px;
        }

        .MyButton {
            border-radius: 20px;
            border: none;
            text-decoration: none;
            height: 38px;
            width: 120px;
            font-size: 15px;
            background-color: #3E0277;
            color: white;
            text-align: center;
        }

        .MyButton:hover {
            background-color: #58029c;
        }

        .radioInput {
            font-size: 13px;
            accent-color: #3E0277;
        }

        .radiolabel {
            font-size: 13px;
            margin-right: 5px;
        }

    </style>
</head>

<body style="background-color: #6b7280;">
<div style="padding-top: 15px; background-color: #6b7280">
    <img style="border-radius: 40px; width: 80px; height: 80px; display: block; margin-left: auto; margin-right: auto;" src='{{URL::asset('images/logo_dark.jpg')}}'  alt="logo"/>
    <h3 style="text-align: center; padding: 10px; color: white; font-weight: bolder">{{ $LeagueTopic }}</h3>
</div>
<div class="centralize">
{{--    background-color: #f5f9fd; --}}
    <div style="width: 100%; border-radius: 20px; background-color: #ebf2fc!important; direction: rtl; margin-bottom: 10px">

        <h2 style="width: 100%; height: 50px; background-color: #212529; border-top-right-radius: 20px; border-top-left-radius: 20px; text-align: center; padding-top: 13px; font-size: 18px; color: white; font-weight: bold;">استبيان</h2>

        <div style="margin-top: 10px; margin-right: 20px; margin-left: 20px;">
            <label style="font-size: 16px; color: #000000; font-weight: bold; margin: 10px; line-height: 1.7; text-align: justify; text-justify: inter-word;">
                انطلاقاً من إيماننا بأن رضا العملاء أحد العوامل الحاسمة في نجاح أية منظمة أو شركة, وحرصاً منا على فهم احتياجات وتوقعات عملائنا وتلبيتها بشكل فعال, وسعينا الجاد لتقديم التكنولوجيا الاحدث والافضل لتعزيز كفائة عمليات منصة سبورنت. نسعد بمشاركتك لهذا الاستبيان القصير.
            </label>
        </div>

        <div style="width: 80%; height: calc(100% - 60px); display: flex; margin-right: auto; margin-left: auto">
            <form id="form" style="width: 100%;">
                @csrf
                <div class="Items">
                    <label class="formTopic" for="inputAddress2">مدى الرضى العام عن خدمات المنصة:</label>
                    <label style="color: black; font-size: 14px; padding-top: 10px;  padding-bottom: 5px;  margin-right: 5px; margin-bottom: 5px" id="SliderValue" for="inputAddress2">50 %</label>
                    <input id="Slider" class="MySlider" style="display: block; width: 230px; background-color: #1a202c; color: #3E0277" type="range" min="1" max="100" value="50">
                </div>
                <div class="Items">
                    <label class="formTopic" for="exampleFormControlSelect1">نوع الحساب:</label>
                    <select class="formInput" id="selectAccount">
                        <option value="-1" class="selectOption" selected>اختــر ..</option>
                        @foreach($AccountType as $Type)
                            <option class="selectOption" value="{{ $Type['Id'] }}" >{{ $Type['Name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="Items">
                    <label class="formTopic">بوابة المنصة المستخدمة:</label>
                    <div style="display: flex; width: 100%; display: block">
                        <div style="width: 100%;">
                            <div style="width: 100%; padding: 10px;">
                                <input class="radioInput" type="radio" id="radioiPhone" name="iPhone" value="100">
                                <label class="radiolabel" for="age3">الأجهزة الذكية (ايفون)</label>
                            </div>
                            <div style="width: 100%; padding: 10px;">
                                <input class="radioInput" type="radio" id="radioiPad" name="iPad" value="100">
                                <label class="radiolabel" for="age3">الأجهزة اللوحية (ايباد)</label>
                            </div>
                        </div>
                        <div style="width: 100%;">
                            <div style="width: 100%; padding: 10px;">
                                <input class="radioInput" type="radio" id="radioandroid" name="Android" value="60">
                                <label class="radiolabel" for="age2">الأجهزة الذكية (اندرويد)</label><br>
                            </div>
                            <div style="width: 100%; padding: 10px;">
                                <input class="radioInput" type="radio" id="radioWeb" name="Web" value="30">
                                <label class="radiolabel" for="age1">لوحة التحكم على الويب</label><br>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="Items">
                    <label class="formTopic" for="inputAddress2">الاسم:</label>
                    <input class="formInput" type="text" id="Name" placeholder="(اختياري)">
                </div>
                <div class="Items">
                    <label class="formTopic" for="inputAddress3">رقم الجوال:</label>
                    <input class="formInput" type="number" pattern="[0-9]*" inputmode="numeric" id="Phone" placeholder="(اختياري)">
                </div>
                <div class="Items">
                    <label class="formTopic" for="inputAddress4">ما مدى رضاك عن الخيارات والمميزات الخاصة بحسابك لدينا بالمنصة:</label>
                    <div style="display: flex; width: 100%; display: block">
                        <div style="width: 100%;">
                            <div style="width: 100%; padding: 10px;">
                                <input class="radioInput" type="radio" id="Excellent" name="VeryGood" value="100">
                                <label class="radiolabel" for="age3">ممتازة جداً</label>
                            </div>
                            <div style="width: 100%; padding: 10px;">
                                <input class="radioInput" type="radio" id="VeryGood" name="Good" value="100">
                                <label class="radiolabel" for="age3">جيدة جداً</label>
                            </div>
                        </div>
                        <div style="width: 100%;">
                            <div style="width: 100%; padding: 10px;">
                                <input class="radioInput" type="radio" id="notBad" name="notBad" value="60">
                                <label class="radiolabel" for="age2">جيدة</label><br>
                            </div>
                            <div style="width: 100%; padding: 10px;">
                                <input class="radioInput" type="radio" id="Bad" name="Bad" value="30">
                                <label class="radiolabel" for="age1">غير مرضية</label><br>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="Items">
                    <label class="formTopic" for="inputAddress4">ملاحظات:</label>
                    <textarea class="formInput" style="height: 80px;" type="text" id="Note" placeholder="اي ملاحظات تود ابلاغنا عنها .." ></textarea>
                </div>
                <div class="Items">
                    <label class="formTopic" for="inputAddress4">اقتراحات:</label>
                    <textarea class="formInput" style="height: 80px;" type="text" id="Suggestions" placeholder="اي تحسينات ترغب باضافتها .."></textarea>
                </div>
                <div class="Items">
                    <label class="formTopic" for="inputAddress4">هل تسمح لنا بالتواصل معك عند الحاجة لاخذ تفاصيل اكثر؟</label>
                    <div style="display: flex; width: 100%; display: block">
                        <div style="width: 100%; padding: 10px;">
                            <input class="radioInput" type="radio" id="ContactYes" name="ContactYes" value="100">
                            <label class="radiolabel" for="age3">نعم</label>
                            <input style="margin-right: 20px" class="radioInput" type="radio" id="ContactNo" name="ContactNo" value="100">
                            <label class="radiolabel" for="age3">لا</label>
                        </div>
                    </div>
                </div>
                <div style="width: 100%; padding-bottom: 15px; padding-top: 8px">
                    <div class="ButtDiv">
                        <button id="submitButton" type="submit" class="MyButton">ارسال</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


@if(!empty(Session::get('error_questionnaire')) && Session::get('error_questionnaire') == 1)
    <script>
        alert('الرجاء اختيار نوع الحساب!');
    </script>
@endif

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>

    $(document).ready(function () {


        $(document).on('click', '#submitButton', function(e) {
            e.preventDefault();

            let SliderValue = document.getElementById('Slider').value;
            let selectAccount = $("#selectAccount").val();

            let radioiPhone = $("#radioiPhone").is(':checked');
            let radioiPad = $("#radioiPad").is(':checked');
            let radioandroid = $("#radioandroid").is(':checked');
            let radioWeb = $("#radioWeb").is(':checked');

            let Name = document.getElementById('Name').value;
            let Phone = document.getElementById('Phone').value;

            let Excellent = $("#Excellent").is(':checked');
            let VeryGood = $("#VeryGood").is(':checked');
            let notBad = $("#notBad").is(':checked');
            let Bad = $("#Bad").is(':checked');

            let ContactYes = $("#ContactYes").is(':checked');
            let ContactNo = $("#ContactNo").is(':checked');

            let Note = document.getElementById('Note').value;
            let Suggestions = document.getElementById('Suggestions').value;

            if (selectAccount === '-1') {
                alert('الرجاء اختيار نوع الحساب!');
            } else if (!radioiPhone && !radioiPad && !radioandroid && !radioWeb){
                alert('الرجاء اختيار بوابة المنصة المستخدمة!');
            } else if (!Excellent && !VeryGood && !notBad && !Bad){
                alert('الرجاء تقييم مدى الرضى عن الخدمات التقنية المقدمة!');
            } else if (!ContactYes && !ContactNo){
                alert('الرجاء اختيار ما اذا كان تسمح لنا بالتواصل معك!');
            } else if (ContactYes && Phone === '') {
                alert('الرجاء ادخال رقم الجوال!');
            }  else if (ContactYes && Phone.trim().length !== 10) {
                alert('الرجاء ادخال رقم الجوال بشكل صحيح!');
            } else {

                var AllowToContact = '';

                if (ContactYes) {
                    AllowToContact = 'Yes';
                } else if (ContactNo) {
                    AllowToContact = 'No';
                }

                jQuery.ajax({
                    url: "{{ route('sendQuestionnaire') }}",
                    method: 'get',
                    data: {
                        SliderValue: SliderValue,
                        selectAccount: selectAccount,
                        radioiPhone: radioiPhone,
                        radioiPad: radioiPad,
                        radioandroid: radioandroid,
                        radioWeb: radioWeb,
                        Name: Name,
                        Phone: Phone,
                        Excellent: Excellent,
                        VeryGood: VeryGood,
                        notBad: notBad,
                        Bad: Bad,
                        Note: Note,
                        Suggestions: Suggestions,
                        AllowToContact: AllowToContact
                    },

                    success: function(result){
                        alert('تم الارسال بنجاح, نشكر لك مشاركتك');
                        document.getElementById('Slider').value = 50;
                        document.getElementById("SliderValue").innerHTML = "50 %";
                        $("#form").trigger('reset');
                    }});
            }

        });


        $(document).on('change', '#ContactYes', function() {
            let ContactYes = $("#ContactYes").is(':checked');
            if (ContactYes) {
                document.getElementById('ContactNo').checked = false;
            }
        });

        $(document).on('change', '#ContactNo', function() {
            let ContactNo = $("#ContactNo").is(':checked');
            if (ContactNo) {
                document.getElementById('ContactYes').checked = false;
            }
        });

        document.getElementById('Slider').value = 50;

        $(document).on('change', '#Slider', function() {
            let value = document.getElementById('Slider').value;

            document.getElementById("SliderValue").innerHTML = value + " %";
        });

        $(document).on('change', '#radioiPhone', function() {
            document.getElementById('radioiPad').checked = false;
            document.getElementById('radioandroid').checked = false;
            document.getElementById('radioWeb').checked = false;
        });

        $(document).on('change', '#radioiPad', function() {
            document.getElementById('radioiPhone').checked = false;
            document.getElementById('radioandroid').checked = false;
            document.getElementById('radioWeb').checked = false;
        });

        $(document).on('change', '#radioandroid', function() {
            document.getElementById('radioiPhone').checked = false;
            document.getElementById('radioiPad').checked = false;
            document.getElementById('radioWeb').checked = false;
        });

        $(document).on('change', '#radioWeb', function() {
            document.getElementById('radioiPhone').checked = false;
            document.getElementById('radioiPad').checked = false;
            document.getElementById('radioandroid').checked = false;
        });



        $(document).on('change', '#Excellent', function() {
            document.getElementById('VeryGood').checked = false;
            document.getElementById('notBad').checked = false;
            document.getElementById('Bad').checked = false;
        });

        $(document).on('change', '#VeryGood', function() {
            document.getElementById('Excellent').checked = false;
            document.getElementById('notBad').checked = false;
            document.getElementById('Bad').checked = false;
        });

        $(document).on('change', '#notBad', function() {
            document.getElementById('Excellent').checked = false;
            document.getElementById('VeryGood').checked = false;
            document.getElementById('Bad').checked = false;
        });

        $(document).on('change', '#Bad', function() {
            document.getElementById('Excellent').checked = false;
            document.getElementById('VeryGood').checked = false;
            document.getElementById('notBad').checked = false;
        });






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
