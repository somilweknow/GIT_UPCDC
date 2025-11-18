<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uttar Pradesh Cooperative Database </title>
    <link rel="stylesheet" href="frontend/csss/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="frontend/csss/style.css" type="text/css">
    <link rel="stylesheet" href="frontend/csss/slick/slick.css?v2022" type="text/css">
    <link rel="stylesheet" href="frontend/csss/slick/slick-theme.css?v2022" type="text/css">
    <link rel="stylesheet" href="frontend/csss/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="frontend/csss/media.css">
    <link rel="stylesheet" href="frontend/csss/baguetteBox.min.css">
    <link rel="stylesheet" href="frontend/csss/all.min.css">
    <link rel="stylesheet" href="frontend/csss/fontawesome.min.css">












    <style>
        /* ✅ Default styles (desktop and tablets) */
        .multipurpose_societies_total .mid {
            margin: 0 auto;
            background: #f7f7f7;
            max-width: 50%;
            border-radius: 20px;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        }

        /* Info text */
        .nsh6 {
            text-align: center;
            color: red;
            margin-top: -24px;
            margin-bottom: -20px;
        }

        /* ✅ Mobile override styles (responsive) */
        @media only screen and (min-width: 300px) and (max-width: 499px) {
            .multipurpose_societies_total .mid {
                margin: 20px auto;
                max-width: 95%;
                width: 95%;
                padding: 15px;
                max-height: unset;
                /* Remove height restriction on small screens */
            }

            .nsh6 {
                margin-top: 10px;
                margin-bottom: 10px;
                font-size: 13px;
            }
        }
    </style>
    <style>
        .under-construction-box {
    display: flex;
    align-items: center;
    background: #dee7f5;
    border-left: 5px solid #ff0707;
    border-right: 5px solid #ff0707;
    /* border-top: 5px solid #ff0707;
    border-bottom: 5px solid #ff0707; */
    padding: 20px 25px;
    margin-top: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    gap: 20px;
    font-family: 'Segoe UI', sans-serif;
    animation: fadeIn 1s ease-in-out;
}

.under-construction-box .icon {
    font-size: 40px;
}

.under-construction-box .message h4 {
    margin: 0 0 8px;
    font-size: 27px;
    font-weight: 600;
    color: #df1a48;
}

.under-construction-box .message p {
    margin: 0;
    font-size: 18px;
    color: #0a5394;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

    </style>
</head>

<body id="increasetext_body" class="photo_popup color-blue popup_show">

    <script src="frontend/jss/owl.carousel.js"></script>
    <script src="js/chart/highmaps.js"></script>
    <script src="js/chart/map_data.js"></script>
    <script src="js/chart/exporting.js"></script>
    <script src="frontend/jss/global.js"></script>
    <script src="js/chart/highcharts.js"></script>
    <script src="js/chart/highcharts-3d.js"></script>
    <script src="js/chart/accessibility.js"></script>
    <script src="frontend/jss/animation_aos.js"></script>
    <script src="frontend/jss/baguetteBox.min.js"></script>
    <!--<script src="/frontend/jss/jquery-3.6.0.min.js"></script>
<script src="/frontend/jss/jquery-1.11.0.min.js"></script>-->
    <script src="frontend/jss/bootstrap.bundle.min.js"></script>

    <script nonce="AbC123xyz">
        $(document).ready(function () {

            // Show Automatically After 1 sec
            setTimeout(showModal, 1000);
            $("#closeBtn").click(function () {
                $("#myModal").hide()
            });

            function showModal() {
                // get value from localStorage
                var is_modal_show = sessionStorage.getItem('alreadyShow');
                if (is_modal_show != 'alredy shown') {
                    $("#myModal").show()
                    sessionStorage.setItem('alreadyShow', 'alredy shown');
                } else {
                    console.log(is_modal_show);
                }
            }
        });
        baguetteBox.run('.tz-gallery');

        jQuery(document).ready(function ($) {
            $('.select2').select2();

            $(".banner-slider").owlCarousel({
                loop: true,
                nav: false,
                autoplayTimeout: 3000,
                autoplay: true,
                responsive: {
                    0: {
                        items: 1,
                    }
                },
            });
            $(".logo-slider").owlCarousel({
                loop: false,
                nav: false,
                dots: false,
                margin: 10,
                autoplayTimeout: 3000,
                autoplay: false,
                responsive: {
                    300: {
                        items: 1,
                        loop: true,
                        autoplay: true,
                    },
                    500: {
                        items: 2,
                    },
                    700: {
                        items: 3,
                    },
                    1000: {
                        items: 4,
                    },
                    1350: {
                        items: 4,
                    },
                },
            });
        });
    </script>


    <div class="left_menu">
        <div class="hamburger">
            <div class="logo-ham">
                <a href="/en" title="Go to home" rel="home">
                    <img src="frontend/img/logo.png" alt="">
                </a>
            </div>
            <div class="fa_times">
                <i class="fas fa-times" style="color: #ffffff;"></i>
            </div>
        </div>

        <ul id="accordionExample">
            <li><a href="/en" title="Home">Home</a></li>
            <li><a href="#" title="Journey of UPCDC">Journey of MoC / UPCDC</a></li>

            <li>
                <a href="#" class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Reports</a>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                    data-bs-parent="#accordionExample">
                    <div class="menu_box_mobile">
                        <ul>
                            <li>
                                <a href="#" id='sub_button1' class="menu__link" role="link">Primary Cooperatives<i
                                        class="fas fa-sort-down"></i></a>
                                <div class="menu_box_mobile">
                                    <ul>
                                        <li><a href="/en/home/state-type-wise-sector">State Wise Cooperatives</a></li>
                                        <li><a href="/en/home/sector-type-wise-sector">Sector Wise Cooperatives</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a href="#" id='sub_button1' class="menu__link" role="link">Cooperative banks <i
                                        class="fas fa-sort-down"></i></a>
                                <div class="menu_box_mobile">
                                    <ul>
                                        <li><a href="/en/home/scb-reports">S<em>t</em>CB</a></li>
                                        <li><a href="/en/home/dccb-reports">DCCB</a></li>
                                        <li><a href="/en/home/ucb-reports">UCB</a></li>
                                        <li><a href="/en/home/sacard">SCARDB</a></li>
                                        <li><a href="/en/home/pacard">PCARDB</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li><a href="/en/home/federation-reports" class="menu__link" role="link">National coop /
                                    Federations</a></li>
                            <li><a href="/en/home/cooperative-multistate-reports" class="menu__link"
                                    role="link">Multistate Cooperatives</a></li>
                            <li><a href="/en/home/educationtraining" class="menu__link" role="link">COOPERATIVE E&T
                                    INSTITUTIONS</a></li>
                            <li><a href="/en/home/ncdcoffice" class="menu__link" role="link">UPCDC Offices</a></li>
                        </ul>
                    </div>
                </div>
            </li>

            <li>
                <a href="#" class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseThreee" aria-expanded="false"
                    aria-controls="collapseThreee">Coop-Ranking</a>
                <div id="collapseThreee" class="accordion-collapse collapse" aria-labelledby="headingThreee"
                    data-bs-parent="#accordionExample">
                    <div class="menu_box_mobile">
                        <ul>
                            <li><a href="/en/ranking-score-generate/rank-score-generation" class="menu__link"
                                    role="link">State Level</a></li>
                            <li><a href="/en/ranking-analysis" class="menu__link" role="link">Ranking Analysis</a></li>
                            <li><a href="/en/ranking-analysis/trendgraph" class="menu__link" role="link">Quarterly
                                    Trends</a></li>
                        </ul>
                    </div>
                </div>
            </li>

            <li><a href="/en/home/latest-event" title="Events">Events</a></li>
            <li><a href="/en/home/faq" title="Faq">FAQ</a></li>
            <li><a href="/en/home/contact" title="Contact Us">Contact Us</a></li>
            <li><a href="/en/multipurpose-societies" title="login" class="login">mPACS | MDCS | MFCS</a></li>
            <li><a href="/en/ncd-meta-data" title="Ncd-Meta-Data" class="login">UPCDC Meta Data</a></li>
            <li><a href="/userl/en/users/login" title="login" class="login">Login</a></li>
        </ul>
    </div>

    <header id="header">
        <div class="topStrip">
            <div class="container abhayen">
                <div class="row">
                    <div class="col-4 left-sec">
                        <div class="common-left clearfix">
                            <ul>
                                <li class="gov-india">
                                    <span class="li_eng responsive_go_eng">
                                        Welcome to Uttar Pradesh cooperative database(UPCDC) </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-8 text-right">
                        <ul class="topNav">
                            <li class="topNav_height">
                                <a href="#main_content">
                                    Skip To Main Content </a>
                            </li>
                            <li class="topNav_height">
                                <a href="/en/home/screen-reader" target="_blank">
                                    Screen Reader Access </a>
                            </li>
                            <li class="topNav_height">
                                <div class="textResizeWrapper cf" id="accessControl">
                                    <input type="button" name="font_normal" value="A-"
                                        onClick="set_font_size('decrease');" id="font_normal" title="Decrease Font Size"
                                        class="fontScaler normal font-normal current">
                                    <input type="button" name="font_large" value="A" onClick="set_font_size('normal');"
                                        id="font_large" title="Normal Font Size" class="fontScaler large font-large">
                                    <input type="button" name="font_larger" value="A+"
                                        onClick="set_font_size('increase');" id="font_larger" title="Increase Font Size"
                                        class="fontScaler largest font-largest">
                                </div>
                            </li>
                            <li>
                                <div class="colorscheme">
                                    <div class="patch color-green"></div>
                                    <div class="patch color-blue"></div>
                                    <div class="patch color-brown"></div>
                                </div>
                            </li>
                            <li>
                                <label for="dark-mode-switch" class="slider theme-switch">
                                    <input type="checkbox" id="dark-mode-switch">
                                    <div class="slider round"></div>
                                </label>
                            </li>
                            <li class="topNav_height">
                                <select id="language-select">
                                    <option value="en" selected="selected">English</option>
                                    <option value="hi">Hindi/हिन्दी</option>
                                </select>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row pt-2 pb-2 position">
                <div class="col-sm-3">
                    <div class="logo">
                        <a href="https://cooperatives.gov.in/" target="_blank" title="" class="site_logo" rel="home">
                            <img id="logo" class="emblem" src="frontend/img/coop_logo.png" alt=""
                                style="width: 108px;height: 94px;">
                        </a>
                    </div>
                </div>
                <div class="col-sm-6 mid_logo">
                    <h2>
                        <a href="/en">
                            उत्तर प्रदेश को-आपरेटिव डेटाबेस सेंटर<span><em>U</em>ttar <em><em>P</em>radesh
                                    <em></em>C</em>ooperative <em>D</em>atabase <em>C</em>enter</span>
                        </a>
                    </h2>
                </div>
                <div class="col-sm-3">
                    <div class="logosblock">
                        <div class="logo-in lastb">
                            <a href="#" target="_blank" class="new_coop_25">
                                <img src="img/../frontend/img/cooperatives-2025-bold-logo.png" alt="" /> </a>
                            <a href="#" target="_blank">
                                <img src="img/../frontend/img/mscs_logo_new-04-09.svg" alt="" /> </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <nav class="wrapper nav-wrapper">
            <div class="container nav-container">
                <div class="login-link"></div>
                <div id="main-menu">
                    <div
                        class="menu-block-wrapper menu-block-2 menu-name-main-menu parent-mlid-0 menu-level-1 d-flex justify-content-between">
                        <ul class="menu" id="nav">
                            <li class="menu__item is-active is-leaf first leaf menu-mlid-218">
                                <a href="/en" class="menu__link active_menu" role="link">
                                    Home </a>
                            </li>


                            <li class="menu__item is-parent is-leaf leaf has-children menu-mlid-2626">
                                <a href="#" class="menu__link" role="link">
                                    Journey of MoC / UPCDC </a>
                            </li>

                            <li class="menu__item is-expanded expanded menu-mlid-951">
                                <a href="#" class="menu__link" role="link">
                                    Reports <i class="fas fa-sort-down"></i>
                                </a>
                                <div class="drop_box reports">
                                    <ul>
                                        <li class="menu__item is-expanded expanded menu-mlid-951">
                                            <a href="/en/home/state-type-wise-sector" class="menu__link" role="link">
                                                Primary Cooperatives <i class="fas fa-sort-down"></i> </a>
                                            <div class="sub_menu">
                                                <ul>
                                                    <li class="menu__item is-expanded expanded menu-mlid-951">
                                                        <a href="/en/home/state-type-wise-sector" class="menu__link"
                                                            role="link">State Wise</a>
                                                    </li>
                                                    <li class="menu__item is-expanded expanded menu-mlid-951">
                                                        <a href="/en/home/sector-type-wise-sector" class="menu__link"
                                                            role="link">Sector Wise</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        <li class="menu__item is-expanded expanded menu-mlid-951">
                                            <a href="#" class="menu__link" role="link">
                                                Cooperative Banks <i class="fas fa-sort-down"></i>
                                            </a>
                                            <div class="sub_menu">
                                                <ul>
                                                    <li class="menu__item is-expanded expanded menu-mlid-951">
                                                        <a href="/en/home/scb-reports" class="menu__link"
                                                            role="link">S<em>t</em>CB</a>
                                                    </li>
                                                    <li class="menu__item is-expanded expanded menu-mlid-951">
                                                        <a href="/en/home/dccb-reports" class="menu__link"
                                                            role="link">DCCB</a>
                                                    </li>
                                                    <li class="menu__item is-expanded expanded menu-mlid-951">
                                                        <a href="/en/home/ucb-reports" class="menu__link"
                                                            role="link">UCB</a>
                                                    </li>
                                                    <li class="menu__item is-expanded expanded menu-mlid-951">
                                                        <a href="/en/home/sacard" class="menu__link"
                                                            role="link">SCARDB</a>
                                                    </li>
                                                    <li class="menu__item is-expanded expanded menu-mlid-951">
                                                        <a href="/en/home/pacard" class="menu__link"
                                                            role="link">PCARDB</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        <li class="menu__item is-expanded expanded menu-mlid-951">
                                            <a href="/en/home/federation-reports" class="menu__link" role="link">
                                                State Coop / Federations </a>
                                        </li>

                                        <li class="menu__item is-expanded expanded menu-mlid-951">
                                            <a href="#" class="menu__link" role="link">
                                                Multistate Cooperatives <i class="fas fa-sort-down"></i>
                                            </a>
                                            <div class="sub_menu sub_menu_second">
                                                <ul>
                                                    <li class="menu__item is-expanded expanded menu-mlid-951">
                                                        <a href="/en/home/cooperative-multistate-list-reports"
                                                            class="menu__link" role="link">
                                                            All Registered Societies </a>
                                                    </li>
                                                    <!-- <li class="menu__item is-expanded expanded menu-mlid-951">
                                                        <a href="/en/home/cooperative-multistate-reports"
                                                            class="menu__link" role="link">
                                                            State Wise MSCS </a>
                                                    </li> -->
                                                    <li class="menu__item is-expanded expanded menu-mlid-951">
                                                        <a href="/en/home/cooperative-multistate-reports-year-wise"
                                                            class="menu__link" role="link">
                                                            Year Wise MSCS </a>
                                                    </li>
                                                    <li class="menu__item is-expanded expanded menu-mlid-951">
                                                        <a href="/en/home/cooperative-multistate-reports-ao-wise"
                                                            class="menu__link" role="link">
                                                            Area of Operation Wise MSCS </a>
                                                    </li>
                                                    <li class="menu__item is-expanded expanded menu-mlid-951">
                                                        <a href="/en/home/cooperative-multistate-reports-sector-wise"
                                                            class="menu__link" role="link">
                                                            Sector Wise MSCS </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        <li>
                                            <a href="/en/home/educationtraining" class="menu__link" role="link">
                                                COOPERATIVE E&T INSTITUTIONS </a>
                                        </li>

                                        <li>
                                            <a href="/en/home/ncdcoffice" class="menu__link" role="link">
                                                UPCDC Offices </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li class="menu__item is-expanded expanded menu-mlid-951">
                                <a href="#" class="menu__link" role="link">
                                    Coop-Ranking <i class="fas fa-sort-down"></i>
                                </a>
                                <div class="drop_box reports">
                                    <ul>
                                        <li><a href="/en/ranking-score-generate/rank-score-generation"
                                                class="menu__link" role="link">
                                                State Level </a></li>
                                        <li><a href="/en/ranking-analysis" class="menu__link" role="link">
                                                Ranking Analysis </a></li>
                                        <li><a href="/en/ranking-analysis/trendgraph" class="menu__link" role="link">
                                                Quarterly Trends </a></li>
                                    </ul>
                                </div>
                            </li>

                            <li class="menu__item is-parent is-leaf leaf has-children menu-mlid-2626">
                                <a href="/en/home/latest-event" class="menu__link" role="link">
                                    Events </a>
                            </li>

                            <li class="menu__item is-expanded expanded menu-mlid-951">
                                <a href="/en/home/faq" class="menu__link" role="link">
                                    FAQ<em>s</em> </a>
                            </li>

                            <li class="menu__item is-expanded expanded menu-mlid-3498">
                                <a href="/en/home/contact" class="menu__link" role="link">
                                    Contact Us </a>
                            </li>

                            <a href="/approved-metadata-for-ncd-(07.07.2025).xlsx" class="ncd-btn"
                                title="Download UPCDC-MetaData">
                                UPCDC-MetaData </a>

                            <li class="menu__item is-expanded expanded menu-mlid-3463 last">
                                <a href="/userl/en/users/login" role="link">
                                    <i class="far fa-user"></i>
                                    Login </a>
                            </li>
                        </ul>

                        <a href="/en/multipurpose-societies" class="mobile_login">
                            <button class="multipurpose_poup_btn ">
                                <span class="constant-tilt-shake">new</span>
                                <b><em>m</em>pacs</b>
                                <b>mdcs</b>
                                <b>mfcs</b>
                            </button>
                        </a>

                        <div class="mobile_menu">
                            <i class="fas fa-bars" style="color: #ffffff;"></i>
                            <a href="/userl/en/users/login" class="mobile_login">
                                <i class="far fa-user"></i> Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header><!-- Banner from API -->
    <section class="banner_section">
        <div class="banner-slider">
        </div>
    </section>
    <div class="new_heading" style="text-align: right;padding-right: 72px;margin-top:15px;">
        <!-- <em style="top: 2px; right: 0; font-size: 17px; font-style: normal; font-weight: 500; color: yellow; padding: 4px 12px; background: red; border-radius: 5px;
                 animation: blinkText 1s steps(5, start) infinite;">As on 01/08/2025 12:47 PM</em> -->
    </div>
    <div class="multipurpose_societies_total">
        <div class="container">
            <div class="mid">
                <div class="under-construction-box" style="margin-bottom: 30px !important;">
                    <div class="icon">🚧</div>
                    <div class="message">
                        <h4>THE PORTAL IS UNDER CONSTRUTION</h4>
                        <p>We're building something great here. This content will be available shortly. Thank you for your patience and support!</p>
                    </div>
                </div>

                <!-- <h6 class="nsh6"><sup></sup>MPACS - Includes PACS &amp; FSS Societies</h6> -->
            </div>
        </div>
    </div>

    <section class="organization_chart_section" id="main_content">
        <div class="container">
            <div class="heading">
                <div class="heading_text">
                    <div class="row">
                        <div class="col-md-4">
                            <a href="/en/home/statesectorprimarycoop" class="coperative-sector-btn">State
                                Cooperatives-State/Sector-wise</a>
                        </div>

                        <div class="col-md-4">
                            <!-- <p>State's Cooperative Database : </p> -->
                            <p>Districts : </p>
                            <div class="flex-col">
                                <div class="input-area select required">
                                    <div class="form-group">
                                        <select name="district_code" required="required" id="feedback_suggestion"
                                            class="form-control">
                                            <option value="">-Select-</option>
                                            <option value="1">AGRA</option>
                                            <option value="2">ALIGARH</option>
                                            <option value="3">ALLAHABAD</option>
                                            <option value="4">AMBEDKAR NAGAR</option>
                                            <option value="5">AMETHI</option>
                                            <option value="6">AMROHA</option>
                                            <option value="7">AURAIYA</option>
                                            <option value="8">AZAMGARH</option>
                                            <option value="9">BAGHPAT</option>
                                            <option value="10">BAHRAICH</option>
                                            <option value="11">BALLIA</option>
                                            <option value="12">BALRAMPUR</option>
                                            <option value="13">BANDA</option>
                                            <option value="14">BARABANKI</option>
                                            <option value="15">BAREILLY</option>
                                            <option value="16">BASTI</option>
                                            <option value="17">BHADOHI</option>
                                            <option value="18">BIJNOR</option>
                                            <option value="19">BUDAUN</option>
                                            <option value="20">BULANDSHAHR</option>
                                            <option value="21">CHANDAULI</option>
                                            <option value="22">CHITRAKOOT</option>
                                            <option value="23">DEORIA</option>
                                            <option value="24">ETAH</option>
                                            <option value="25">ETAWAH</option>
                                            <option value="26">FAIZABAD</option>
                                            <option value="27">FARRUKHABAD</option>
                                            <option value="28">FATEHPUR</option>
                                            <option value="29">FIROZABAD</option>
                                            <option value="30">GAUTAM BUDDH NAGAR</option>
                                            <option value="31">GHAZIABAD</option>
                                            <option value="32">GHAZIPUR</option>
                                            <option value="33">GONDA</option>
                                            <option value="34">GORAKHPUR</option>
                                            <option value="35">HAMIRPUR</option>
                                            <option value="36">HAPUR</option>
                                            <option value="37">HARDOI</option>
                                            <option value="38">HATHRAS</option>
                                            <option value="39">JALAUN</option>
                                            <option value="40">JAUNPUR</option>
                                            <option value="41">JHANSI</option>
                                            <option value="42">KANNAUJ</option>
                                            <option value="43">KANPUR DEHAT</option>
                                            <option value="44">KANPUR NAGAR</option>
                                            <option value="45">KASGANJ</option>
                                            <option value="46">KAUSHAMBI</option>
                                            <option value="47">KHERI</option>
                                            <option value="48">KUSHINAGAR</option>
                                            <option value="49">LALITPUR</option>
                                            <option value="50">LUCKNOW</option>
                                            <option value="51">MAHARAJGANJ</option>
                                            <option value="52">MAHOBA</option>
                                            <option value="53">MAINPURI</option>
                                            <option value="54">MATHURA</option>
                                            <option value="55">MAU</option>
                                            <option value="56">MEERUT</option>
                                            <option value="57">MIRZAPUR</option>
                                            <option value="58">MORADABAD</option>
                                            <option value="59">MUZAFFARNAGAR</option>
                                            <option value="60">PILIBHIT</option>
                                            <option value="61">PRATAPGARH</option>
                                            <option value="62">RAEBARELI</option>
                                            <option value="63">RAMPUR</option>
                                            <option value="64">SAHARANPUR</option>
                                            <option value="65">SAMBHAL</option>
                                            <option value="66">SANT KABEER NAGAR</option>
                                            <option value="67">SANT RAVIDAS NAGAR</option>
                                            <option value="68">SHAHJAHANPUR</option>
                                            <option value="69">SHAMLI</option>
                                            <option value="70">SHRAVASTI</option>
                                            <option value="71">SIDDHARTHNAGAR</option>
                                            <option value="72">SITAPUR</option>
                                            <option value="73">SONBHADRA</option>
                                            <option value="74">SULTANPUR</option>
                                            <option value="75">UNNAO</option>
                                            <option value="76">VARANASI</option>
                                        </select>
                                    </div>
                                </div>
                                <a href="#" class="go_btn go_btn_state"><i class="fas fa-arrow-right"></i></a>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <p>Sector-Wise Cooperatives : </p>
                            <div class="flex-col">
                                <div class="input-area select required">
                                    <div class="form-group"><select name="sector_of_operation" required="required"
                                            id="feedback_suggestion_sector" class="form-control">
                                            <option value="">-Select Sector-</option>
                                            <option value="77">Agriculture &amp; Allied Cooperative</option>
                                            <option value="31">Agro Processing / Industrial Cooperative</option>
                                            <option value="79">Bee Farming Cooperative</option>
                                            <option value="80">Consumer Cooperative</option>
                                            <option value="18">Credit &amp; Thrift Society</option>
                                            <option value="9">Dairy Cooperative</option>
                                            <option value="84">Educational &amp; Training Cooperatives</option>
                                            <option value="20">Farmers Service Societies (FSS)</option>
                                            <option value="10">Fishery Cooperative</option>
                                            <option value="14">Handicraft Cooperative</option>
                                            <option value="13">Handloom Textile &amp; Weavers Cooperative</option>
                                            <option value="47">Housing Cooperative Society</option>
                                            <option value="90">Jute and Coir Cooperative</option>
                                            <option value="117">Khadi Gramodyog</option>
                                            <option value="51">Labour Cooperative</option>
                                            <option value="22">Large Area Multipurpose Society (LAMPS)</option>
                                            <option value="54">Livestock &amp; Poultry Cooperative</option>
                                            <option value="82">Marketing Cooperative Society</option>
                                            <option value="35">Miscellaneous Credit Cooperative Society</option>
                                            <option value="29">Miscellaneous Non Credit</option>
                                            <option value="16">Multipurpose Cooperative</option>
                                            <option value="1">Primary Agricultural Credit Society (PACS)</option>
                                            <option value="96">Sericulture Cooperative</option>
                                            <option value="98">Social Welfare &amp; Cultural Cooperative</option>
                                            <option value="11">Sugar Mills Cooperative</option>
                                            <option value="99">Tourism Cooperative</option>
                                            <option value="68">Transport Cooperative</option>
                                            <option value="102">Tribal-SC/ST Cooperative</option>
                                            <option value="7">Urban Cooperative Bank (UCB)</option>
                                            <option value="15">Women Welfare Cooperative Society</option>
                                        </select></div>
                                </div> <a href="#" class="go_btn go_btn_sector"><i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="new_heading">
                <h2><span>At a Glance</span></h2>

            </div>
            <div class="mid">
                <div class="slider slider-for">
                    <div class="box-inner">
                        <div class="inner_content">
                            <div class="box_first bx003">
                                <ul>
                                    <li>
                                        <div class="content">
                                            <a href="/en/home/all-india-cooperatives">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h4>All State Cooperatives</h4>
                                                        <span>75000</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="content">
                                            <a href="/en/home/sectors">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h4>Total Sectors</h4>
                                                        <span>23</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="box_first bx03">
                                <ul>
                                    <li>
                                        <div class="content">
                                            <a href="/en/home/state-type-wise-sector">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h4>Primary Cooperatives</h4>
                                                        <span>75000</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="content">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h4>Federations</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="content">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h4>Cooperative Banks</h4>
                                                    <span>55</span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li style="margin-right: 0;">
                                        <div class="content">
                                            <a href="/en/home/cooperative-multistate-list-reports">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h4>Multi State Coop Societies</h4>
                                                        <span>1779</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                    <li style="visibility: hidden; display: none;">
                                        <div class="content">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h4>Other Institutions</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="box_second">
                                <ul>
                                    <li>
                                        <div class="content">
                                            <a href="/en/home/all-india-functional-cooperatives">
                                                <div class="inner">
                                                    <p>Functional Cooperatives</p>
                                                    <span>651937</span>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                    <li class="box_second_row">
                                        <div class="content">
                                            <a href="/en/home/otherfederations/3">
                                                <div class="inner">
                                                    <p>District</p>
                                                    <span>75</span>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                    <li class="box_third_row">
                                        <div class="content">
                                            <a href="/en/home/sacard">
                                                <div class="inner">
                                                    <p>SCARDB</p>
                                                    <span>14</span>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                    <li style="visibility:hidden; margin-right: 0;">
                                        <div class="content">
                                            <div class="inner">

                                            </div>
                                        </div>
                                    </li>
                                    <li style="visibility:hidden; display: none;">
                                        <div class="content">
                                            <a href="/en/home/ncdcoffice">
                                                <div class="inner">
                                                    <p>UPCDC</p>
                                                    <span>27</span>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <!-- <div class="box_second">
                                <ul>
                                    <li>
                                        <div class="content">
                                            <a href="/en/home/non-functional-cooperative">
                                                <div class="inner">
                                                    <p>Non-Functional/Dormant</p>                                            
                                                    <span>144646</span>
                                                </div>
                                            </a>
                                        </div>
                                    </li>                                    
                                    <li class="box_second_row">
                                        <div class="content">
                                            <a href="/en/home/otherfederations/2">
                                                <div class="inner">                                            
                                                    <p>State</p>                                            
                                                    <span>237 </span>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                    <li class="box_third_row">
                                        <div class="content">
                                            <a href="/en/home/pacard">
                                                <div class="inner">                                            
                                                    <p>PCARDB</p>                                            
                                                    <span>526</span>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                    <li style="visibility:hidden;margin-right: 0;">
                                        <div class="content">
                                            <div class="inner">                                            
                                                
                                            </div>
                                        </div>
                                    </li>
                                    <li style="visibility:hidden; display: none;">
                                        <div class="content">
                                            <a href="#">
                                                <div class="inner">                                            
                                                    <p>NCCT</p>                                            
                                                    <span>22</span>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </div> -->
                            <div class="box_second box_third_row">
                                <ul>
                                    <li>
                                        <div class="content">
                                            <a href="/en/home/under-liquidation-cooperative">
                                                <div class="inner">
                                                    <p>Under Liquidation</p>
                                                    <span>1000</span>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                    <li class="box_second_row">
                                        <div class="content">
                                            <a href="/en/home/otherfederations/4">
                                                <div class="inner">
                                                    <p>Block</p>
                                                    <span>385</span>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                    <li class="box_third_row">
                                        <div class="content">
                                            <a href="/en/home/scb-reports">
                                                <div class="inner">
                                                    <p>StCB</p>
                                                    <span>1</span>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                    <li style="visibility:hidden;margin-right: 0;">
                                        <div class="content">

                                        </div>
                                    </li>
                                    <li style="visibility:hidden; display: none;">
                                        <div class="content">

                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="box_second box_third_row">
                                <ul>
                                    <li style="visibility:hidden;">
                                        <div class="content">
                                        </div>
                                    </li>
                                    <li class="box_second_row">
                                        <div class="content">
                                            <a href="/en/home/otherfederations/4">
                                                <div class="inner">
                                                    <p>Block</p>
                                                    <span>385</span>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                    <li class="box_third_row">
                                        <div class="content">
                                            <a href="/en/home/dccb-reports">
                                                <div class="inner">
                                                    <p>DCCB</p>
                                                    <span>50</span>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                    <li style="visibility:hidden;margin-right: 0;">
                                        <div class="content">

                                        </div>
                                    </li>
                                    <li style="visibility:hidden; display: none;">
                                        <div class="content">
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="box_second box_third_row">
                                <ul>
                                    <li style="visibility:hidden;">
                                        <div class="content">
                                        </div>
                                    </li>
                                    <li style="visibility:hidden;">
                                        <div class="content">

                                        </div>
                                    </li>
                                    <li style="visibility:hidden;margin-right: 0;">
                                        <div class="content">

                                        </div>
                                    </li>
                                    <li style="visibility:hidden; display: none;">
                                        <div class="content">
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <!-- <div class="box_second">
                                <div class="other_institutions">
                                    <h3>Other Institutions</h3>
                                    <ul>
                                        <li>
                                            <div class="content">
                                                <a href="/en/home/ncdcoffice">
                                                    <div class="inner">
                                                        <p>NCDC</p>
                                                        <span>27</span>
                                                    </div>
                                                </a>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="content">
                                                <a href="/en/home/educationtraining">
                                                    <div class="inner">
                                                        <p>NCCT</p>
                                                        <span>22</span>
                                                    </div>
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div> -->
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </section>


    <section class="important_key" id="main_content" style="display: none;">
        <div class="container">
            <div class="heading">
                <div class="heading_text">
                    <p>State's Cooperative Database : </p>
                    <div class="input-area select required">
                        <div class="form-group"><select name="state_code" required="required" id="feedback_suggestion"
                                class="form-control">
                                <option value="">-Select State/UT-</option>
                                <option value="35">ANDAMAN AND NICOBAR ISLANDS</option>
                                <option value="28">ANDHRA PRADESH</option>
                                <option value="12">ARUNACHAL PRADESH</option>
                                <option value="18">ASSAM</option>
                                <option value="10">BIHAR</option>
                                <option value="4">CHANDIGARH</option>
                                <option value="22">CHHATTISGARH</option>
                                <option value="7">DELHI</option>
                                <option value="30">GOA</option>
                                <option value="24">GUJARAT</option>
                                <option value="6">HARYANA</option>
                                <option value="2">HIMACHAL PRADESH</option>
                                <option value="1">JAMMU AND KASHMIR</option>
                                <option value="20">JHARKHAND</option>
                                <option value="29">KARNATAKA</option>
                                <option value="32">KERALA</option>
                                <option value="37">LADAKH</option>
                                <option value="31">LAKSHADWEEP</option>
                                <option value="23">MADHYA PRADESH</option>
                                <option value="27">MAHARASHTRA</option>
                                <option value="14">MANIPUR</option>
                                <option value="17">MEGHALAYA</option>
                                <option value="15">MIZORAM</option>
                                <option value="13">NAGALAND</option>
                                <option value="21">ODISHA</option>
                                <option value="34">PUDUCHERRY</option>
                                <option value="3">PUNJAB</option>
                                <option value="8">RAJASTHAN</option>
                                <option value="11">SIKKIM</option>
                                <option value="33">TAMIL NADU</option>
                                <option value="36">TELANGANA</option>
                                <option value="38">THE DADRA AND NAGAR HAVELI AND DAMAN AND DIU</option>
                                <option value="16">TRIPURA</option>
                                <option value="9">UTTAR PRADESH</option>
                                <option value="5">UTTARAKHAND</option>
                                <option value="19">WEST BENGAL</option>
                            </select></div>
                    </div>
                    <p>Sector-Wise Cooperatives : </p>
                    <div class="input-area select required">
                        <div class="form-group"><select name="sector_of_operation" required="required"
                                id="feedback_suggestion_sector" class="form-control">
                                <option value="">-Select Sector-</option>
                                <option value="77">Agriculture &amp; Allied Cooperative</option>
                                <option value="31">Agro Processing / Industrial Cooperative</option>
                                <option value="79">Bee Farming Cooperative</option>
                                <option value="80">Consumer Cooperative</option>
                                <option value="18">Credit &amp; Thrift Society</option>
                                <option value="9">Dairy Cooperative</option>
                                <option value="84">Educational &amp; Training Cooperatives</option>
                                <option value="20">Farmers Service Societies (FSS)</option>
                                <option value="10">Fishery Cooperative</option>
                                <option value="14">Handicraft Cooperative</option>
                                <option value="13">Handloom Textile &amp; Weavers Cooperative</option>
                                <option value="47">Housing Cooperative Society</option>
                                <option value="90">Jute and Coir Cooperative</option>
                                <option value="117">Khadi Gramodyog</option>
                                <option value="51">Labour Cooperative</option>
                                <option value="22">Large Area Multipurpose Society (LAMPS)</option>
                                <option value="54">Livestock &amp; Poultry Cooperative</option>
                                <option value="82">Marketing Cooperative Society</option>
                                <option value="35">Miscellaneous Credit Cooperative Society</option>
                                <option value="29">Miscellaneous Non Credit</option>
                                <option value="16">Multipurpose Cooperative</option>
                                <option value="1">Primary Agricultural Credit Society (PACS)</option>
                                <option value="96">Sericulture Cooperative</option>
                                <option value="98">Social Welfare &amp; Cultural Cooperative</option>
                                <option value="11">Sugar Mills Cooperative</option>
                                <option value="99">Tourism Cooperative</option>
                                <option value="68">Transport Cooperative</option>
                                <option value="102">Tribal-SC/ST Cooperative</option>
                                <option value="7">Urban Cooperative Bank (UCB)</option>
                                <option value="15">Women Welfare Cooperative Society</option>
                            </select></div>
                    </div>
                </div>
            </div>
            <div class="new_heading">
                <h2>UPCDC - <span>At a Glance</span></h2>



            </div>
            <ul class="row main_glance">
                <li class="col-md-2">
                    <div class="box_inner">
                        <a href="/en/home/sectors">
                            <div class="img">
                                <img src="img/../frontend/img/Total-Sector.png" alt="" />
                            </div>
                            <h3>Total Cooperative Sectors</h3>
                            <span>30</span>
                        </a>
                    </div>
                </li>
                <li class="col-md-2">
                    <div class="box_inner">
                        <a href="/en/home/all-india-cooperatives">
                            <div class="img">
                                <img src="img/../frontend/img/all-india-cooperative.png" alt="" />
                            </div>
                            <h3>All India Cooperatives</h3>
                            <span>844401</span>
                        </a>
                    </div>
                </li>
                <li class="col-md-2">
                    <div class="box_inner">
                        <a href="/en/home/state-type-wise-sector">
                            <div class="img">
                                <img src="img/../frontend/img/primary-cooperative.png" alt="" />
                            </div>
                            <h3>Primary Cooperatives</h3>
                            <span>840492</span>
                        </a>
                    </div>
                </li>
                <li class="col-md-2">
                    <div class="box_inner">
                        <a href="/en/home/all-india-functional-cooperatives">
                            <div class="img">
                                <img src="img/../frontend/img/functional-cooperatives.png" alt="" />
                            </div>
                            <h3>Functional Cooperatives</h3>
                            <span>651937 </span>
                        </a>
                    </div>
                </li>
                <li class="col-md-2">
                    <div class="box_inner">
                        <a href="/en/home/non-functional-cooperative">
                            <div class="img">
                                <img src="img/../frontend/img/functional.png" alt="" />
                            </div>
                            <h3>Non Functional/Dormant Cooperatives</h3>
                            <span>144646</span>
                        </a>
                    </div>
                </li>
                <li class="col-md-2">
                    <div class="box_inner">
                        <a href="/en/home/under-liquidation-cooperative">
                            <div class="img">
                                <img src="img/../frontend/img/liquidity.png" alt="" />
                            </div>
                            <h3>Cooperatives Under Liquidation</h3>
                            <span>47818</span>
                        </a>
                    </div>
                </li>
                <li class="col-md-2">
                    <div class="box_inner">
                        <a href="/en/home/federation-reports">
                            <div class="img">
                                <img src="img/../frontend/img/national-federation.png" alt="" />
                            </div>
                            <h3>National Federations</h3>
                            <span>19</span>
                        </a>
                    </div>
                </li>
                <li class="col-md-2">
                    <div class="box_inner">
                        <a href="/en/home/otherfederations/2">
                            <div class="img">
                                <img src=img/../frontend/img/state-federation.png" alt="" />
                            </div>
                            <h3>State Federations</h3>
                            <span>237 </span>
                        </a>
                    </div>
                </li>
                <li class="col-md-2">
                    <div class="box_inner">
                        <a href="/en/home/otherfederations/3">
                            <div class="img">
                                <img src="img/../frontend/img/district-federation.png" alt="" />
                            </div>
                            <h3>District Federations</h3>
                            <span>551</span>
                        </a>
                    </div>
                </li>
                <li class="col-md-2">
                    <div class="box_inner">
                        <a href="/en/home/otherfederations/4">
                            <div class="img">
                                <img src="img/../frontend/img/block%2Bregional%20federation.png" alt="" />
                            </div>
                            <h3>Block Federations</h3>
                            <span>385</span>
                        </a>
                    </div>
                </li>
                <li class="col-md-2">
                    <div class="box_inner">
                        <a href="/en/home/otherfederations/5">
                            <div class="img">
                                <img src="img/../frontend/img/functional-mscs.png" alt="" />
                            </div>
                            <h3>Regional Federations</h3>
                            <span>47</span>
                        </a>
                    </div>
                </li>
                <li class="col-md-2">
                    <div class="box_inner">
                        <a href="/en/home/sacard">
                            <div class="img">
                                <img src="img/../frontend/img/SCARDB.png" alt="" />
                            </div>
                            <h3>SCARDB</h3>
                            <span>14</span>
                        </a>
                    </div>
                </li>
                <li class="col-md-2">
                    <div class="box_inner">
                        <a href="/en/home/pacard">
                            <div class="img">
                                <img src="img/../frontend/img/PCARDB.png" alt="" />
                            </div>
                            <h3>PCARDB</h3>
                            <span>526</span>
                        </a>
                    </div>
                </li>
                <li class="col-md-2">
                    <div class="box_inner">
                        <a href="/en/home/scb-reports">
                            <div class="img">
                                <img src="img/../frontend/img/State-Cooperative-Banks.png" alt="" />
                            </div>
                            <h3>State Cooperative Banks</h3>
                            <span>32</span>
                        </a>
                    </div>
                </li>
                <li class="col-md-2">
                    <div class="box_inner">
                        <a href="/en/home/dccb-reports">
                            <div class="img">
                                <img src="img/../frontend/img/district-cooperative-banks.png" alt="" />
                            </div>
                            <h3>District Cooperative Banks</h3>
                            <span>338</span>
                        </a>
                    </div>
                </li>
                <li class="col-md-2">
                    <div class="box_inner">
                        <a href="/en/home/cooperative-multistate-list-reports">
                            <div class="img">
                                <img src="img/../frontend/img/mscs.png" alt="" />
                            </div>
                            <h3>MSCS</h3>
                            <span>1779</span>
                        </a>
                    </div>
                </li>
                <li class="col-md-2">
                    <div class="box_inner">
                        <a href="/en/home/educationtraining">
                            <div class="img">
                                <img src="img/../frontend/img/ncct-logo-mini.png" alt="" />
                            </div>
                            <h3>Cooperative E&T Institutes</h3>
                            <span>22</span>
                        </a>
                    </div>
                </li>
                <li class="col-md-2">
                    <div class="box_inner">
                        <a href="/en/home/ncdcoffice">
                            <div class="img">
                                <img src="img/../frontend/img/ncdc_office_logo.png" alt="" />
                            </div>
                            <h3>UPCDC Offices</h3>
                            <span>27</span>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </section>

    <section class="important_key important_key_add">
        <div class="container important_key_cont">


            <h2>UPCDC - <span>key performance indicators</span></h2>
            <ul class="row">
                <li class="col merge_tile">
                    <div class="box_inner">

                        <div class="img">
                            <img src="img/../frontend/img/Total-Sector.png" alt="" />
                        </div>
                        <h3>New Societies Registered</h3>
                        <em><a href="/en/nscd-key-performance/new-ncd-societies-registered?formmonth=07&amp;formyear=2025"
                                class="mnth_data">This Month</a></em>
                        <em><a href="/en/nscd-key-performance/new-ncd-year-societies-registered?formyear=2025"
                                class="mnth_data">This Year</a></em>
                        <h4>State-Wise summary</h4>
                        <span>279</span>
                        <span>10842</span>

                    </div>
                </li>

                <li class="col">
                    <div class="box_inner">
                        <a href="/en/nscd-key-performance/new-ncd-mscs-societies-registered?formyear=2025">
                            <!-- <a href="<//?=$this->Url->build(['controller'=>'NscdKeyPerformance','action'=>'new_ncd_mscs_societies_registered', "formmonth" => $formmonth,"formyear" => $formyear])?>"> -->
                            <div class="img">
                                <img src="img/../frontend/img/functional-cooperatives.png" alt="" />
                            </div>
                            <h3>List of MSCS Registered</h3>
                            <em>This Year</em>
                            <h4>State-Wise Summary</h4>
                            <span>57</span>
                        </a>
                    </div>
                </li>
                <li class="col">
                    <div class="box_inner">
                        <a href="/en/nscd-key-performance/new-gps-coverage">
                            <div class="img">
                                <img src="img/../frontend/img/national-federation.png" alt="" />
                            </div>
                            <h3>GPs Coverage</h3>
                            <em>Covered GPs</em>
                            <h4 class="more_added"><b>PACS</b><b>Dairy</b><b>Fishery</b></h4>
                            <span>256260</span>
                            <span>84665</span>
                            <span>29490</span>
                        </a>
                    </div>
                </li>
                <li class="col">
                    <div class="box_inner">
                        <a href="/en/nscd-key-performance/gps-uncoverage">
                            <div class="img">
                                <img src="img/../frontend/img/national-federation.png" alt="" />
                            </div>
                            <h3>GPs Non Coverage</h3>
                            <em> Non Covered GPs</em>
                            <h4 class="more_added"><b>PACS</b><b>Dairy</b><b>Fishery</b></h4>
                            <span>13035</span>
                            <span>184628</span>
                            <span>239741</span>
                        </a>
                    </div>
                </li>
                <li class="col formation">
                    <div class="box_inner">
                        <a href="/en/reports-after-formation-of-moc">
                            <div class="img">
                                <img src="img/../frontend/img/primary-cooperative.png" alt="" />
                            </div>
                            <h3>New Cooperatives <br> Since the Formation of MOC(06/07/2021)</h3>
                            <h4 class="more_added" style="visibility: hidden;"></h4>
                            <span>State-Wise</span>
                            <span>Sector-Wise</span>
                        </a>
                    </div>
                </li>

                <li class="col aspirational_tile lastblock-child">
                    <div class="box_inner">
                        <a href="/en/aspirational">
                            <div class="img">
                                <img src="img/../frontend/img/aspirational.png" alt="" />
                            </div>
                            <h3>Cooperatives in Aspirational</h3>
                            <div class="text_inner">
                                <div class="texts">
                                    <em>Districts</em>
                                    <span>112</span>
                                </div>
                                <div class="texts">
                                    <em>Blocks</em>
                                    <span>500</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </li>

            </ul>


        </div>
    </section>

    <section class="about-us about-us-section" style="display: none;">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-lg-12 slide_in" id="slider" style="display: none;">
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="blog-slider__main">
                                <div class="blog-slider__wrp">
                                    <div class="blog-slider__item">
                                        <div class="blog-slider__img">
                                            <img src="/img/../frontend/img/Narendra-Modi-new.jpg" alt="" />
                                        </div>

                                        <div class="blog-slider__content">
                                            <div class="blog-slider__title">Shri Narendra Modi</div>
                                            <span class="blog-slider__code">Prime Minister</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="blog-slider">
                                <div class="blog-slider__wrp">
                                    <div class="blog-slider__item">
                                        <div class="blog-slider__img">
                                            <img src="/img/../frontend/img/amitShah.jpg" alt="" class="un-img" />
                                        </div>

                                        <div class="blog-slider__content">
                                            <div class="blog-slider__title">Shri Amit Shah</div>
                                            <span class="blog-slider__code">Union Minister</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="blog-slider blog-slider-1">
                                <div class="blog-slider__wrp">
                                    <div class="blog-slider__item">
                                        <div class="blog-slider__img cm-img">
                                            <img src="/img/../frontend/img/bVerma.jpg" alt="" />
                                        </div>

                                        <div class="blog-slider__content">
                                            <div class="blog-slider__title">Shri B L Verma</div>
                                            <span class="blog-slider__code">Minister of State</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="help_desk">
                        <h5>Kindly note that the email ID for technical help desk has changed.</h5>
                        <p>Email ID: <a href="mailto:UPCDC.techcoop@gmail.com">UPCDC[dot]techcoop[at]gmail[dot]com</a>, Tele
                            No: <a href="tel:011 20862616">011 20862616</a></p>
                    </div>
                </div>
                <div class="col-sm-12 col-lg-12 slide-in-left" id="slider1">
                    <div class="about-us_intro">
                        <div class="row mid">
                            <div class="col-md-9 left">
                                <h2>About Uttar Pradesh Cooperative Database(UPCDC)</h2>
                                <p>The Ministry of Cooperation was constituted on 6th July, 2021 with a mandate, inter
                                    alia to strengthen cooperative movement in the country & deepening its reach up to
                                    grassroots; and creation of appropriate policy, legal & institutional framework to
                                    help cooperatives realize their potential. To fulfil this mandate, the process for
                                    developing a National Cooperative Database has been initiated by the Ministry in
                                    consultation with the State/UT Governments, National Cooperative/Federations,
                                    Central line Ministries and all other Stakeholders at various levels. Keeping in
                                    view the diverse nature and size of cooperative sector, it was decided to develop
                                    the database in a phased manner The benefits of National Cooperative Database are:
                                </p>
                                <ul>
                                    <li>A single point access to information on about 8 lakh Cooperative Societies.</li>
                                    <li>Identifying gaps in terms of geographical spread of Cooperative Societies.</li>
                                    <li>Information on vertical and horizontal linkages amongst cooperatives.</li>
                                    <li>Facilitate planning, policy making & implementation for all stakeholders.</li>
                                    <li>A Comprehensive, Authentic and Updated data repository.</li>
                                </ul>
                            </div>
                            <div class="col-md-3 right">
                                <div class="inner">
                                    <a href="/Final_National_Cooperative_Database_023.pdf" target="_blank">National
                                        Cooperative Database 2023: A Report <img src="img/../frontend/img/pdf-btn.png"
                                            alt="" /></a>
                                    <a href="https://www.cooperation.gov.in/major-initiative-of-the-ministry"
                                        target="_blank">Major Initiatives of Ministry <img
                                            src="img/../frontend/img/major-btn.png" alt="" /></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- <section class="Event_section">
        <div class="container">
            <h2>Events / Workshop <span class="all"><a href="/en/home/latest-event">View All</a></span></h2>
            <div class="event_slider">
                <div class="slide">
                    <div class="event_post">
                        <a href="/en/home/photos-gallery/2">
                            <div class="img">
                                <img src="frontend/img/files/gallery/PACS-5.jpeg">
                                <div class="overlay">
                                </div>
                            </div>
                            <div class="text">
                                <span>25-12-2024</span>
                                <h3>Launch of 10000 newly formed multipurpose PACS and dairy and fisheries cooperative
                                    societies</h3>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="slide">
                    <div class="event_post">
                        <a href="/en/home/photos-gallery/3">
                            <div class="img">
                                <img src="frontend/img/files/gallery/upload_image_20250311_102655_b04148bd.jpeg">
                                <div class="overlay">
                                </div>
                            </div>
                            <div class="text">
                                <span>19-09-2024</span>
                                <h3>National conference on initiatives taken in 100 days by Ministry of Cooperation
                                    (19-09-2024)</h3>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="slide">
                    <div class="event_post">
                        <a href="/en/home/photos-gallery/1">
                            <div class="img">
                                <img src="frontend/img/files/gallery/image1.jpeg">
                                <div class="overlay">
                                </div>
                            </div>
                            <div class="text">
                                <span>06-08-2023</span>
                                <h3>Launching of Digital portal for CRCS office at Pune(6th August 2023)</h3>
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section> -->

    <section class="social_section">
        <div class="container">
            <ul class="row">
                <!-- <li class="col-md-3">
                    <div class="social_inner">
                        <h3>Twitter</h3>
                        <div class="social_side">
                        <div class="twitter-widgt">
                            <div class="ibContentBox">
                            <div class="twitter-timeline twitter-timeline-rendered" style="display: flex; max-width: 100%; margin-top: 0px; margin-bottom: 0px;"><iframe id="twitter-widget-0" scrolling="no" frameborder="0" allowtransparency="true" allowfullscreen="true" class="" style="position: static; visibility: visible;display: block; flex-grow: 1;" title="Twitter Timeline" src="https://syndication.twitter.com/srv/timeline-profile/screen-name/minofcooperatn?dnt=false&amp;embedId=twitter-widget-0&amp;features=eyJ0ZndfdGltZWxpbmVfbGlzdCI6eyJidWNrZXQiOltdLCJ2ZXJzaW9uIjpudWxsfSwidGZ3X2ZvbGxvd2VyX2NvdW50X3N1bnNldCI6eyJidWNrZXQiOnRydWUsInZlcnNpb24iOm51bGx9LCJ0ZndfdHdlZXRfZWRpdF9iYWNrZW5kIjp7ImJ1Y2tldCI6Im9uIiwidmVyc2lvbiI6bnVsbH0sInRmd19yZWZzcmNfc2Vzc2lvbiI6eyJidWNrZXQiOiJvbiIsInZlcnNpb24iOm51bGx9LCJ0ZndfZm9zbnJfc29mdF9pbnRlcnZlbnRpb25zX2VuYWJsZWQiOnsiYnVja2V0Ijoib24iLCJ2ZXJzaW9uIjpudWxsfSwidGZ3X21peGVkX21lZGlhXzE1ODk3Ijp7ImJ1Y2tldCI6InRyZWF0bWVudCIsInZlcnNpb24iOm51bGx9LCJ0ZndfZXhwZXJpbWVudHNfY29va2llX2V4cGlyYXRpb24iOnsiYnVja2V0IjoxMjA5NjAwLCJ2ZXJzaW9uIjpudWxsfSwidGZ3X3Nob3dfYmlyZHdhdGNoX3Bpdm90c19lbmFibGVkIjp7ImJ1Y2tldCI6Im9uIiwidmVyc2lvbiI6bnVsbH0sInRmd19kdXBsaWNhdGVfc2NyaWJlc190b19zZXR0aW5ncyI6eyJidWNrZXQiOiJvbiIsInZlcnNpb24iOm51bGx9LCJ0ZndfdXNlX3Byb2ZpbGVfaW1hZ2Vfc2hhcGVfZW5hYmxlZCI6eyJidWNrZXQiOiJvbiIsInZlcnNpb24iOm51bGx9LCJ0ZndfdmlkZW9faGxzX2R5bmFtaWNfbWFuaWZlc3RzXzE1MDgyIjp7ImJ1Y2tldCI6InRydWVfYml0cmF0ZSIsInZlcnNpb24iOm51bGx9LCJ0ZndfbGVnYWN5X3RpbWVsaW5lX3N1bnNldCI6eyJidWNrZXQiOnRydWUsInZlcnNpb24iOm51bGx9LCJ0ZndfdHdlZXRfZWRpdF9mcm9udGVuZCI6eyJidWNrZXQiOiJvbiIsInZlcnNpb24iOm51bGx9fQ%3D%3D&amp;frame=false&amp;hideBorder=false&amp;hideFooter=false&amp;hideHeader=false&amp;hideScrollBar=false&amp;lang=en&amp;limit=5&amp;maxHeight=321px&amp;origin=http%3A%2F%2Fwww.cooperation.gov.in%2F&amp;sessionId=f8b5b635d91f1fcc8c723eb53ea61dcbf31642e9&amp;showHeader=true&amp;showReplies=false&amp;transparent=false&amp;widgetsVersion=2615f7e52b7e0%3A1702314776716"></iframe></div> <script async="" src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                            </div>
                        </div>
                    </div>
                    </div>
                </li> -->
                <li class="col-md-4">
                    <div class="social_inner">
                        <h3>Facebook</h3>
                        <iframe style="border: none; overflow: hidden;"
                            src="https://www.facebook.com/v15.0/plugins/page.php?adapt_container_width=true&app_id=&channel=https%3A%2F%2Fstaticxx.facebook.com%2Fx%2Fconnect%2Fxd_arbiter%2F%3Fversion%3D46%23cb%3Dfb3d9f20b694a798c%26domain%3Dwww.cooperation.gov.in%26is_canvas%3Dfalse%26origin%3Dhttps%253A%252F%252Fwww.cooperation.gov.in%252Ff0b8ca167a1dc006f%26relation%3Dparent.parent&hide_cover=false&href=https%3A%2F%2Fwww.facebook.com%2FMinOfCooperatn%2F&locale=en_GB&sdk=joey&show_facepile=true&small_header=true&tabs=timeline"></iframe>
                    </div>
                </li>
                <li class="col-md-4">
                    <div class="social_inner">
                        <h3>Instagram</h3>
                        <div class="social_side">
                            <iframe src="https://www.instagram.com/minofcooperatn/embed" frameborder="0" scrolling="no"
                                allowtransparency="true"></iframe>
                        </div>
                    </div>
                </li>
                <li class="col-md-4">
                    <div class="social_inner">
                        <h3>Youtube</h3>
                        <div class="social_side">
                            <iframe src="https://www.youtube.com/embed/83YURrcJdxc?si=J1nLF51irGKTq1Hq"
                                title="YouTube video player" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen></iframe>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </section>

    <section class="media_section" style="display:none">
        <div class="container">
            <div class="row">
                <div class="col-md-8 left">

                    <h2>Our <span>Gallery</span> <a href="/en/home/latest-event">view all</a></h2>
                    <div class="row">
                        <div class="col-md-5 image">
                            <div class="image_inner">
                                <a href="/en/home/latest-event">
                                    <img src="img/../frontend/img/event-amit-shah.png" alt="" />
                                    <div class="overlay">
                                        <img src="img/../frontend/img/zoom.png" alt="" />
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-7 video">
                            <ul>
                                <li>
                                    <a href="/en/home/latest-event">
                                        <div class="video_inner">
                                            <img src="img/../frontend/img/event-02.png" alt="" />
                                            <div class="overlay">
                                                <i class="fab fa-youtube"></i>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="/en/home/latest-event">
                                        <div class="video_inner">
                                            <img src="img/../frontend/img/new-gallery-02.png" alt="" />
                                            <div class="overlay">
                                                <i class="fab fa-youtube"></i>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 right">
                    <div class="tabs">
                        <div class="right_heading">

                            <h2>Social <span>Media</span></h2>

                            <div class="social_heading">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="twitter-tab" data-bs-toggle="tab"
                                            data-bs-target="#twitter" type="button" role="tab" aria-controls="twitter"
                                            aria-selected="true">
                                            <img src="img/../frontend/img/Twitter.png" alt="" />
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="facebook-tab" data-bs-toggle="tab"
                                            data-bs-target="#facebook" type="button" role="tab" aria-controls="facebook"
                                            aria-selected="false">
                                            <img src="img/../frontend/img/fb-color.png" alt="" />
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="youtube-tab" data-bs-toggle="tab"
                                            data-bs-target="#youtube" type="button" role="tab" aria-controls="youtube"
                                            aria-selected="false">
                                            <img src="img/../frontend/img/yt-color.png" alt="" /> </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="insta-tab" data-bs-toggle="tab"
                                            data-bs-target="#insta" type="button" role="tab" aria-controls="insta"
                                            aria-selected="false">
                                            <img src="img/../frontend/img/instagram-%281%29.png" alt="" /> </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="social-side">
                            <div class="tab-content custom_scroll" id="myTabContent">
                                <div class="tab-pane fade show active" id="twitter" role="tabpanel"
                                    aria-labelledby="twitter-tab">
                                    <div class="twitter-widgt">
                                        <div class="ibContentBox">
                                            <div class="twitter-timeline twitter-timeline-rendered"
                                                style="display: flex; max-width: 100%; margin-top: 0px; margin-bottom: 0px;">
                                                <iframe id="twitter-widget-0" scrolling="no" frameborder="0"
                                                    allowtransparency="true" allowfullscreen="true" class=""
                                                    style="position: static; visibility: visible;display: block; flex-grow: 1;"
                                                    title="Twitter Timeline"
                                                    src="https://syndication.twitter.com/srv/timeline-profile/screen-name/minofcooperatn?dnt=false&amp;embedId=twitter-widget-0&amp;features=eyJ0ZndfdGltZWxpbmVfbGlzdCI6eyJidWNrZXQiOltdLCJ2ZXJzaW9uIjpudWxsfSwidGZ3X2ZvbGxvd2VyX2NvdW50X3N1bnNldCI6eyJidWNrZXQiOnRydWUsInZlcnNpb24iOm51bGx9LCJ0ZndfdHdlZXRfZWRpdF9iYWNrZW5kIjp7ImJ1Y2tldCI6Im9uIiwidmVyc2lvbiI6bnVsbH0sInRmd19yZWZzcmNfc2Vzc2lvbiI6eyJidWNrZXQiOiJvbiIsInZlcnNpb24iOm51bGx9LCJ0ZndfZm9zbnJfc29mdF9pbnRlcnZlbnRpb25zX2VuYWJsZWQiOnsiYnVja2V0Ijoib24iLCJ2ZXJzaW9uIjpudWxsfSwidGZ3X21peGVkX21lZGlhXzE1ODk3Ijp7ImJ1Y2tldCI6InRyZWF0bWVudCIsInZlcnNpb24iOm51bGx9LCJ0ZndfZXhwZXJpbWVudHNfY29va2llX2V4cGlyYXRpb24iOnsiYnVja2V0IjoxMjA5NjAwLCJ2ZXJzaW9uIjpudWxsfSwidGZ3X3Nob3dfYmlyZHdhdGNoX3Bpdm90c19lbmFibGVkIjp7ImJ1Y2tldCI6Im9uIiwidmVyc2lvbiI6bnVsbH0sInRmd19kdXBsaWNhdGVfc2NyaWJlc190b19zZXR0aW5ncyI6eyJidWNrZXQiOiJvbiIsInZlcnNpb24iOm51bGx9LCJ0ZndfdXNlX3Byb2ZpbGVfaW1hZ2Vfc2hhcGVfZW5hYmxlZCI6eyJidWNrZXQiOiJvbiIsInZlcnNpb24iOm51bGx9LCJ0ZndfdmlkZW9faGxzX2R5bmFtaWNfbWFuaWZlc3RzXzE1MDgyIjp7ImJ1Y2tldCI6InRydWVfYml0cmF0ZSIsInZlcnNpb24iOm51bGx9LCJ0ZndfbGVnYWN5X3RpbWVsaW5lX3N1bnNldCI6eyJidWNrZXQiOnRydWUsInZlcnNpb24iOm51bGx9LCJ0ZndfdHdlZXRfZWRpdF9mcm9udGVuZCI6eyJidWNrZXQiOiJvbiIsInZlcnNpb24iOm51bGx9fQ%3D%3D&amp;frame=false&amp;hideBorder=false&amp;hideFooter=false&amp;hideHeader=false&amp;hideScrollBar=false&amp;lang=en&amp;limit=5&amp;maxHeight=321px&amp;origin=http%3A%2F%2Fwww.cooperation.gov.in%2F&amp;sessionId=f8b5b635d91f1fcc8c723eb53ea61dcbf31642e9&amp;showHeader=true&amp;showReplies=false&amp;transparent=false&amp;widgetsVersion=2615f7e52b7e0%3A1702314776716"></iframe>
                                            </div>
                                            <script async="" src="https://platform.twitter.com/widgets.js"
                                                charset="utf-8"></script>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="facebook" role="tabpanel" aria-labelledby="facebook-tab">
                                    <iframe style="border: none; overflow: hidden;"
                                        src="https://www.facebook.com/v15.0/plugins/page.php?adapt_container_width=true&app_id=&channel=https%3A%2F%2Fstaticxx.facebook.com%2Fx%2Fconnect%2Fxd_arbiter%2F%3Fversion%3D46%23cb%3Dfb3d9f20b694a798c%26domain%3Dwww.cooperation.gov.in%26is_canvas%3Dfalse%26origin%3Dhttps%253A%252F%252Fwww.cooperation.gov.in%252Ff0b8ca167a1dc006f%26relation%3Dparent.parent&hide_cover=false&href=https%3A%2F%2Fwww.facebook.com%2FMinOfCooperatn%2F&locale=en_GB&sdk=joey&show_facepile=true&small_header=true&tabs=timeline"></iframe>
                                </div>
                                <div class="tab-pane fade" id="youtube" role="tabpanel" aria-labelledby="youtube-tab">
                                    <iframe src="https://www.youtube.com/embed/83YURrcJdxc?si=J1nLF51irGKTq1Hq"
                                        title="YouTube video player" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen></iframe>
                                </div>
                                <div class="tab-pane fade" id="insta" role="tabpanel" aria-labelledby="insta-tab">
                                    <iframe src="https://www.instagram.com/minofcooperatn/embed" frameborder="0"
                                        scrolling="no" allowtransparency="true"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="logo-slider-section">
        <div class="container">
            <div class="logo-slider">
                <!---->

                <div class="item">
                    <div class="inner">
                        <a href="https://mscs.dac.gov.in/" target="_blank" class="slider-logo">
                            <img src="img/../frontend/img/logo-01.png" alt="" /> </a>
                    </div>
                </div>
                <div class="item">
                    <div class="inner">
                        <a href="https://www.ncdc.in/" target="_blank" class="slider-logo">
                            <img src="img/../frontend/img/logo-02.png" alt="" />
                        </a>
                    </div>
                </div>
                <div class="item">
                    <div class="inner">
                        <a href="https://vamnicom.gov.in/" target="_blank" class="slider-logo">
                            <img src="img/../frontend/img/logo-03.png" alt="" />
                        </a>
                    </div>
                </div>
                <div class="item">
                    <div class="inner">
                        <a href="https://ncct.ac.in/" target="_blank" class="slider-logo">
                            <img src="img/../frontend/img/logo-04.png" alt="" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!--
<div class="custom-model-main multipurpose_poup model-open">
    <div class="custom-model-inner">        
    <div class="close-btn"><i class="far fa-times-circle"></i></div>
        <div class="custom-model-wrap">
            <div class="pop-up-content-wrap">
                        <div class="multipurpose_poup_inner">
            <p>New Multipurpose Primary <em>(PACS/Dairy/Fishery)</em> <b>Cooperatives Since 15-02-2023</b></p>
            <ul class="row">
                <li class="col-md-4">
                    <div class="inner_box">                        
                        <a href="/en/home/multipurpose-pacs" target="_blank" class="box_new">                            
                            <div class="text">
                                <h2>mpacs</h2>                                
                            </div>
                        </a>                        
                    </div>
                    <a href="/en/home/multipurpose-pacs" target="_blank" class="box_number">
                        <span>6128</span>
                    </a>
                </li>
                <li class="col-md-4">
                    <div class="inner_box">                        
                        <a href="/en/home/multipurpose-dairy" target="_blank" class="box_new">
                            <div class="text">
                                <h2>mdcs</h2>                                
                            </div>
                        </a>                        
                    </div>
                    <a href="/en/home/multipurpose-dairy" target="_blank" class="box_number">
                        <span>15675</span>
                    </a>
                </li>
                <li class="col-md-4">
                    <div class="inner_box">                        
                        <a href="/en/home/multipurpose-fishery" target="_blank" class="box_new">
                            <div class="text">
                                <h2>mfcs</h2>                                
                            </div>
                        </a>                        
                    </div>
                    <a href="/en/home/multipurpose-fishery" target="_blank" class="box_number">
                        <span>1650</span>
                    </a>
                </li>
            </ul>
            <div class="bottom"> 
                                <h4>Total: <span>23453</span></h4>
                <h5>As on <em>31-07-2025</em></h5>
                <h6><sup>*</sup><b>m</b>PACS - Includes PACS, LAMPS &amp; FSS Societies</h6>
            </div>
        </div>
        
            </div>
        </div>  
    </div>  
    <div class="bg-overlay"></div>
</div> -->

    <!-- Modal -->


    <!-- Modal -->
    <!--<div class="modal fade multipurpose_poup" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="far fa-times-circle"></i></button>
      </div>
      <div class="modal-body">
                    <div class="multipurpose_poup_inner">
            <p>New Multipurpose Primary <em>(PACS/Dairy/Fishery)</em> <b>Cooperatives Since 15-02-2023</b></p>
            <ul class="row">
                <li class="col-md-4">
                    <div class="inner_box">                        
                        <a href="/en/home/multipurpose-pacs" target="_blank" class="box_new">                            
                            <div class="text">
                                <h2>mpacs</h2>                                
                            </div>
                        </a>                        
                    </div>
                    <a href="/en/home/multipurpose-pacs" target="_blank" class="box_number">
                        <span>6128</span>
                    </a>
                </li>
                <li class="col-md-4">
                    <div class="inner_box">                        
                        <a href="/en/home/multipurpose-dairy" target="_blank" class="box_new">
                            <div class="text">
                                <h2>mdcs</h2>                                
                            </div>
                        </a>                        
                    </div>
                    <a href="/en/home/multipurpose-dairy" target="_blank" class="box_number">
                        <span>15675</span>
                    </a>
                </li>
                <li class="col-md-4">
                    <div class="inner_box">                        
                        <a href="/en/home/multipurpose-fishery" target="_blank" class="box_new">
                            <div class="text">
                                <h2>mfcs</h2>                                
                            </div>
                        </a>                        
                    </div>
                    <a href="/en/home/multipurpose-fishery" target="_blank" class="box_number">
                        <span>1650</span>
                    </a>
                </li>
            </ul>
             <div class="bottom"> 
                                <h4>Total: <span>23453</span></h4>
                 <h5>As On : <em>30-07-2025 12:05 AM</em></h5>
                <h6><sup></sup><b>m</b>PACS - Includes PACS, LAMPS & FSS Societies</h6>
            </div>
        </div>
                
        
      </div>      
    </div>
  </div>
</div>-->


    <div class="sidebar-contact active">
        <!-- <div class="toggle"></div> -->
        <!-- <h2><a href="/en/home/knowyourcoop">know your <span>Cooperative ID <em>(NCD-ID)</em></span></a></h2> -->
    </div>

    <footer>
        <div class="container">

            <div class="footer_main_menu">
                <div class="row">
                    <div class="col-md-4 left same-side">
                        <h4>contact details</h4>
                        <ul>
                            <li><span><i class="fas fa-map-marker-alt"></i></span>
                                <address>14, Vidhan Sabha Marg, Husainganj, Lucknow, Uttar Pradesh 226001</address>
                            </li>
                            <li class="left_list"><span><i class="fas fa-phone-alt"></i></span> <a
                                    href="tel:0522 2721258">0522 2721258</a></li>
                            <li><span><i class="fas fa-envelope"></i></span> <a
                                    href="mailto:UPCDC.techcoop@gmail.com">UPCDC[dot]techcoop[at]gmail[dot]com</a></li>
                            <li><span><i class="fas fa-map-marker-alt"></i></span>
                                <address>Ministry of Cooperation Sachivalaya,. Lucknow – 110003</address>
                            </li>
                            <li class="left_list"><span><i class="fas fa-phone-alt"></i></span> <a
                                    href="tel:011 20909005">011 20909005</a></li>
                            <li><span><i class="fas fa-envelope"></i></span> <a
                                    href="mailto:UPCDC.techcoop@gmail.com">UPCDC[dot]dpt-coop[at]gov[dot]in</a></li>
                        </ul>
                        <a href="/en/feedback-suggestions" target='_blank' class="feedback">Feedback/Suggestions</a>
                    </div>
                    <div class="col-md-4 mid same-side">
                        <h4>quick links</h4>
                        <ul>
                            <li><a href="https://cooperation.gov.in/">Ministry of Cooperation (MOC)</a> </li>
                            <li><a href="https://www.nabard.org/">National Bank For Agriculture And Rural<br>
                                    Development (NABARD)</a> </li>
                            <li><a href="https://www.ncdc.in/">National Cooperative Development Cooperation (NCDC)</a>
                            </li>
                            <li><a href="http://ncui.coop/">National Cooperative Union of India (NCUI)</a> </li>
                            <li><a href="https://ncct.ac.in/en">National Council for Co-operative Training (NCCT)</a>
                            </li>
                            <li><a href="https://vamnicom.gov.in/">Vaikunth Mehta National Institute of Cooperative<br>
                                    Management (VAMNICOM)</a> </li>
                            <li><a href="https://crcs.gov.in/">Central Registrar of Cooperative Societies (CRCS)</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4 right same-side">
                        <h4>National Cooperative / Federations </h4>
                        <ul>
                            <li><a href="https://www.iffco.in/" class="sf-depth-1 sf-external" target="_blank">IFFCO</a>
                            </li>
                            <li><a href="https://www.kribhco.net/" class="sf-depth-1 sf-external"
                                    target="_blank">KRIBHCO</a></li>
                            <li><a href="https://www.nafed-india.com/" class="sf-depth-1 sf-external"
                                    target="_blank">NAFED</a></li>
                            <li><a href="https://ncui.coop/" class="sf-depth-1 sf-external" target="_blank">NCUI</a>
                            </li>
                            <li><a href="https://coopsugar.org/" class="sf-depth-1 sf-external"
                                    target="_blank">NFCSF</a></li>
                            <li><a href="https://nccf-india.com/" class="sf-depth-1 sf-external"
                                    target="_blank">NCCF</a></li>
                            <li><a href="https://trifed.tribal.gov.in/" class="sf-depth-1 sf-external"
                                    target="_blank">TRIFED</a></li>
                            <li><a href="https://ncol.coop/" class="sf-depth-1 sf-external">NCOL</a></li>
                            <li><a href="https://sahakarbeej.in/" class="sf-depth-1 sf-external">BBSSL</a></li>
                            <li><a href="https://ncel.coop/" class="sf-depth-1 sf-external">NCEL</a></li>
                            <li><a href="https://nafcub.org/" class="sf-depth-1 sf-external">NAFCUB</a></li>
                            <li><a href="#" class="sf-depth-1 sf-external">AIHFMCS</a></li>
                            <li><a href="https://labcofed.org/" class="sf-depth-1 sf-external">NLCF</a></li>
                            <li><a href="#" class="sf-depth-1 sf-external">AIFCSML</a></li>
                            <li><a href="http://www.fishcopfed.in/" class="sf-depth-1 sf-external">FISHCOPFED</a></li>
                            <li><a href="https://nafscob.org/" class="sf-depth-1 sf-external">NAFSCOB</a></li>
                            <li><a href="https://ncdfi.coop/" class="sf-depth-1 sf-external">NCDFI</a></li>
                            <li><a href="https://nchfindia.net/" class="sf-depth-1 sf-external">NCHFI</a></li>
                            <li><a href="https://www.nafcard.org/" class="sf-depth-1 sf-external">NAFCARD</a></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <div class="copyright">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <p>UPCDC web portal is designed, developed and being managed by UPCDC, MoC, Govt of Uttar Pradesh.</p>
                        <p>Contents are published by the respective states/UTs RCS offices and other state
                            cooperatives</p>
                    </div>
                    <div class="col-md-6">
                        <ul class="footer_menu">
                            <li><a href="/en/home/terms">Terms of Use</a></li>
                            <li><a href="/en/home/privacy-policy">Privacy Policy</a></li>
                            <li><a href="/en/home/copyright">Copyright Policy</a></li>
                            <li><a href="/en/home/hyperlinking">Hyperlinking Policy</a></li>
                            <li><a href="/en/home/disclaimer">Disclaimer</a></li>
                            <li><a href="/en/home/help">Help</a></li>
                        </ul>
                        <ul>
                            <li> Visitors : 1256</li>
                            <li>Last Updated : 31 July 2025 </li>
                            <li class="vrsn">Version : 2.0</li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <div class="social_sidebar">
        <div class="social facebook">
            <a href="https://www.facebook.com/MinOfCooperatn/" target="_blank">
                <img src="frontend/img/files/gallery/facebook1.png" alt="Facebook"> Follow us on Facebook</a>
        </div>

        <div class="social twitter">
            <a href="https://twitter.com/MinOfCooperatn/" target="_blank">
                <img class="twiter-t" src="frontend/img/files/gallery/twitter.png" alt="Twitter"> Follow us on Twitter</a>
        </div>
        <div class="social youtube">
            <a href="https://www.youtube.com/MinOfCooperatn" target="_blank">
                <img src="frontend/img/youtube.svg" alt="YouTube"> Follow us on YouTube</a>
        </div>
        <div class="social instagram">
            <a href="https://www.instagram.com/minofcooperatn/" target="_blank">
                <img src="frontend/img/instagram.svg" alt="Instagram"> Follow us on Instagram</a>
        </div>
        <div class="social wa">
            <a href="https://whatsapp.com/channel/0029VaEoFwQGU3BNOuIfes1S" target="_blank">
                <img src="frontend/img/whatsapp.svg" alt="Whatsapp"> Follow us on Whatsapp</a>
        </div>
        <div class="social linkdd">
            <a href="https://www.linkedin.com/company/minofcooperatn/" target="_blank">
                <img src="frontend/img/linkedin.svg" alt="linkdin"> Follow us on Linkedin</a>
        </div>
    </div>

    <button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fas fa-arrow-up"
            style="color: #ffffff;"></i></button>

    <link rel="stylesheet" href="/select2/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="frontend/jss/jquery_new2.min.js"></script>
    <script src="frontend/jss/jquery_new1.min.js"></script>
    <script src="frontend/jss/bootstrap.bundle.min.js"></script>
    <!-- <script src="/frontend/jss/slick.js?v2022" type="text/javascript" charset="utf-8"></script>-->
    <script src="frontend/jss/owl.carousel.js"></script>
    <script src="frontend/jss/animation_aos.js"></script>
    <script src="frontend/jss/baguetteBox.min.js"></script>
    <script src="frontend/jss/global.js"></script>
    <!-- <script src="/frontend/jss/all.min.js"></script> -->
    <script src="select2/select2.full.min.js"></script>
    <style>
        footer .footer_main_menu .mid.same-side li a {
            text-decoration: none;
        }
    </style>
    <!-- <script>
 //daynight
function myFunction() {
   var element = document.body;
   element.classList.toggle("dark-theme");
}

</script> -->
    <script>
        $(window).load(function () {
            $('#staticBackdrop').modal('show');
        }); 
    </script>

    <script>

        function selectItem(item) {
            const items = document.querySelectorAll('.sidebar li');
            items.forEach(i => i.classList.remove('active'));
            item.classList.add('active');
        }

        function toggleSection(sectionId, element) {
            const section = document.getElementById(sectionId);
            const icon = element.querySelector('.bx');

            if (section.style.maxHeight && section.style.maxHeight !== "0px") {
                section.style.maxHeight = "0px";
                section.style.padding = "0";
            } else {
                section.style.maxHeight = section.scrollHeight + "px";
                section.style.padding = "10px 0";
            }

            icon.classList.toggle('bx-chevron-down');
            icon.classList.toggle('bx-chevron-up');
        }

        // Initialize sections to be expanded
        document.addEventListener("DOMContentLoaded", function () {
            const sections = document.querySelectorAll('.sidebar ul');
            sections.forEach(section => {
                section.style.maxHeight = section.scrollHeight + "px";
                section.style.padding = "10px 0";
            });

            const icons = document.querySelectorAll('.section-title .bx');
            icons.forEach(icon => {
                icon.classList.add('bx-chevron-up');
                icon.classList.remove('bx-chevron-down');
            });
        });


        // Make the DIV element draggable:
        dragElement(document.getElementById("mySidebar"));

        function dragElement(elmnt) {
            var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
            const dragHandle = document.getElementById("mySidebarHeader");

            dragHandle.onmousedown = dragMouseDown;

            function dragMouseDown(e) {
                e = e || window.event;
                e.preventDefault();
                // get the mouse cursor position at startup:
                pos3 = e.clientX;
                pos4 = e.clientY;
                document.onmouseup = closeDragElement;
                document.onmousemove = elementDrag;

                // Add grabbing cursor
                document.body.classList.add('dragging');
            }

            function elementDrag(e) {
                e = e || window.event;
                e.preventDefault();
                // calculate the new cursor position:
                pos1 = pos3 - e.clientX;
                pos2 = pos4 - e.clientY;
                pos3 = e.clientX;
                pos4 = e.clientY;
                // set the element's new position:
                elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
                elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
            }

            function closeDragElement() {
                // stop moving when mouse button is released:
                document.onmouseup = null;
                document.onmousemove = null;

                // Remove grabbing cursor
                document.body.classList.remove('dragging');
            }
        }
    </script>

    <script>

        $(document).ready(function () {


            // show automatically after 1s
            setTimeout(showModal, 1000);
            $("#closeBtn").click(function () {
                $("#myModal").hide()
            });

            function showModal() {
                // get value from localStorage
                var is_modal_show = sessionStorage.getItem('alreadyShow');
                if (is_modal_show != 'alredy shown') {
                    $("#myModal").show()
                    sessionStorage.setItem('alreadyShow', 'alredy shown');
                } else {
                    console.log(is_modal_show);
                }
            }

        });
        baguetteBox.run('.tz-gallery');

        jQuery(document).ready(function ($) {
            $('.select2').select2();

            $(".banner-slider").owlCarousel({
                loop: true,
                nav: false,
                autoplayTimeout: 3000,
                autoplay: true,
                responsive: {
                    0: {
                        items: 1,
                    }
                },
            });
            $(".logo-slider").owlCarousel({
                loop: false,
                nav: false,
                dots: false,
                margin: 10,
                autoplayTimeout: 3000,
                autoplay: false,
                responsive: {
                    300: {
                        items: 1,
                        loop: true,
                        autoplay: true,
                    },
                    500: {
                        items: 2,
                    },
                    700: {
                        items: 3,
                    },
                    1000: {
                        items: 4,
                    },
                    1350: {
                        items: 4,
                    },
                },
            });
        });
    </script>

    <script nonce="AbC123xyz">
        $(document).ready(function () {
            $('.go_btn_state').click(function () {

                // $('#feedback_suggestion').on('change', function(e) {
                event.preventDefault()
                var state_code = $('#feedback_suggestion').val();
                if (state_code != '') {
                    window.location.href = '/en/state-dashboard/state' + '/' + state_code;

                    // window.location.href='/en/state-dashboard'+'/'+state_code;
                }
            });
        });

        $(document).ready(function () {

            $('.go_btn_sector').click(function () {
                // $('#feedback_suggestion_sector').on('change', function(e) {
                event.preventDefault()
                var sector_of_operation = $('#feedback_suggestion_sector').val();
                if (sector_of_operation != '') {
                    window.location.href = '/en/sector-dashboard/sector' + '/' + sector_of_operation;
                }
            });
        });

        $(document).ready(function () {
            $(document).on('change', '#language-select', function () {
                var val = $(this).val();
                var currentURL = window.location.href; // Get the current URL

                // Extract the base URL (http://localhost/cooperative/) from the current URL
                var baseURL = currentURL.split('/').slice(0, 3).join('/') + '/';

                // Construct the new URL based on the selected value
                var newURL = baseURL + val // You can modify this pattern as needed

                // Redirect the user to the new URL
                window.location.href = newURL;

                // Add your language change logic here
            });
            $('.slide-item').click(function () {
                $('.slide-item').removeClass('active');
                $(this).addClass('active');
            });
            show_map(1);

            Highcharts.chart('sector_bar', {
                chart: {
                    type: 'column',
                    height: 680,
                    marginBottom: 250,
                    // options3d: {
                    //     enabled: true,
                    //     alpha: 0,
                    //     beta: 332,
                    //     depth: 59,
                    //     viewDistance: 25
                    // },


                    scrollablePlotArea: {
                        minWidth: 1000,
                        scrollPositionX: 0
                    }

                },
                title: {
                    text: 'SECTOR-WISE COOPERATIVES SOCIETY'
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    type: 'category',
                    labels: {
                        rotation: -60,
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    // min: 5,

                    title: {
                        text: 'Total Society Count'
                    },
                    type: 'logarithmic',
                    scrollbar: {
                        enabled: true
                    },
                    minWidth: 20,
                },
                legend: {
                    enabled: false
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y} </b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0

                    },
                    series: {
                        colorByPoint: true,
                        pointWidth: 20

                    },
                    column: {
                        minPointLength: 5
                    }
                },
                colors: [
                    '#7cb5ec',
                    '#90ed7d',
                    '#e08d46'
                ],
                series: [{
                    name: '<span style="color:black;">Count</span>',
                    data: [

                    ],
                    dataLabels: {
                        enabled: true,
                        rotation: -90,
                        align: 'right',
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                }]
            });
        });

        function show_map(map_id) {
            var aa = map_id;
            $(".loadingA").show();
            $("#map_pacs").html('');
            $("#map_pacs").removeClass();
            $("#map_pacs").addClass('maps-height-box ss' + aa);
            $.ajax({
                type: 'GET',
                async: false,
                cache: false,
                url: '/en/home/getmap',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]')
                        .val());
                },
                data: {
                    primary_activity: aa,
                },
                success: function (response) {
                    // alert(response);
                    $("#map_pacs").html(response);
                    $(".loadingA").hide();
                },
            });

            show_table(aa);
            show_chart(aa);
            var ss = $('#map_pacs').attr('aria-label');
            var myNewStr = ss.replace(/. Highcharts interactive chart./g, '');

            var myNewStr1 = myNewStr.replace(/.Total./g, ' <em>Total') + '</em>';

            $('#show_map_heasding').html(myNewStr1);

        }


        function showtableandchart() {
            var state = $("#state-code").val();
            var primary_activity = $("#primary_society").val();
            $(".few").text(' ');

            if (state != '') {
                $(".few").text('Major District');

            } else {
                $(".few").text('Major States/UT');
            }

            show_table(primary_activity, state);
            show_chart(primary_activity, state);


        }



        function show_table(map_id, state = null) {
            var aa = map_id;
            $(".loadingA").show();
            $("#table_show").html('');
            $("#primary_society").val(aa);

            $.ajax({
                type: 'GET',
                cache: false,
                url: '/en/home/gettable',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]')
                        .val());
                },
                data: {
                    primary_activity: aa,
                    state: state,
                },
                success: function (response) {
                    // alert(response);
                    $("#table_show").html(response);
                    $(".loadingA").hide();
                },
            });
        }

        function show_chart(map_id, state = null) {
            var aa = map_id;
            $(".loadingA").show();
            $("#graph_container").html('');
            $.ajax({
                type: 'GET',
                cache: false,
                url: '/en/home/getchart',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]')
                        .val());
                },
                data: {
                    primary_activity: aa,
                    state: state,
                },
                success: function (response) {
                    // alert(response);
                    $("#graph_container").html(response);
                    $(".loadingA").hide();
                },
            });

        }

    </script>


    <script>
        var fontSize = 100;
        function set_font_size(fontType) {
            if (fontType == "increase") {
                //alert(fontSize); 
                if (fontSize < 110) {
                    / 80 /
                    fontSize = parseInt(fontSize) + 7;
                }
            } else if (fontType == "decrease") {
                if (fontSize > 80) {
                    fontSize = parseInt(fontSize) - 10;
                }
            } else {
                fontSize = 100;
            }
            //_setCookie("fontSize",fontSize);
            jQuery("body").css("font-size", fontSize + "%");
            //jQuery("#template_three_column").css("font-size",fontSize + "%");
        } 
    </script>

    <!-- Increase/descrease font size -->
    <!-- <script type="text/javascript">        
        $('#increasetext').click(function() {
            curSize = parseInt($('#increasetext_body').css('font-size')) + 2;
            if (curSize <= 22)
                $('#increasetext_body').css('font-size', curSize);
        });

        $('#resettext').click(function() {
            if (curSize != 18)
                $('#increasetext_body').css('font-size', 18);
        });

        $('#decreasetext').click(function() {
            curSize = parseInt($('#increasetext_body').css('font-size')) - 2;
            if (curSize >= 14)
                $('#increasetext_body').css('font-size', curSize);
        });
    </script> -->


    <!-- mySidepane -->
    <script type="text/javascript">
        function openNav() {
            document.getElementById("mySidepanel0").classList.add('sidepanel_add');
        }
        function closeNav() {
            document.getElementById("mySidepanel0").classList.remove('sidepanel_add');
        }

    </script>


    <script>
        $('.patch').click(function () {
            $(this).addClass('active');
            $('.patch').not($(this)).removeClass('active');
            $('body').attr('class', $(this).attr('class').split(' ')[1]);
        });
    </script>


</body>


<script>
    // Show the first tab and hide the rest
    $('#tabs-nav li:first-child').addClass('active');
    $('.tab-content').hide();
    $('.tab-content:first').show();

    // Click function
    $('#tabs-nav li').click(function () {
        $('#tabs-nav li').removeClass('active');
        $(this).addClass('active');
        $('.tab-content').hide();

        var activeTab = $(this).find('a').attr('href');
        $(activeTab).fadeIn();
        return false;
    });
</script>

<script>
    $(".Click-here").on('click', function () {
        $(".custom-model-main").addClass('model-open');
    });
    $(".close-btn, .bg-overlay").click(function () {
        $(".custom-model-main").removeClass('model-open');
    });

</script>

<script nonce="AbC123xyz">
    // State Bar Graph
    const data = ;
    const isHindi = null; // pass the flag to JavaScript
    const formattedData = data.map(item => ({
        name: isHindi ? item.state.hindi_name : item.state.name, // use hindi_name if in Hindi mode
        value: parseInt(item.Count, 10)
    }));
    // Sort the data in descending order
    formattedData.sort((a, b) => b.value - a.value);

    // Separate the sorted names and values
    const categories = formattedData.map(item => item.name);
    const values = formattedData.map(item => item.value);

    Highcharts.chart('state_primary_chart', {
        chart: {
            type: 'bar',
            width: 600,
            height: 1100
        },
        title: {
            text: 'State wise Primary Cooperative Societies'
        },
        xAxis: {
            categories: categories,
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Number of Societies'
            },
            labels: {
                overflow: 'justify'
            }
        },
        tooltip: {
            valueSuffix: ' societies'
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        legend: {
            enabled: false
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'Number of Societies',
            data: values,
            colors: [
                '#ff2d00', '#ff3003', '#ff3307', '#ff360b', '#ff390f', '#ff3d13',
                '#ff4017', '#ff431b', '#ff461f', '#ff4a23', '#ff4d27', '#ff502b',
                '#ff532e', '#ff5632', '#ff5a36', '#ff5d3a', '#ff603e', '#ff6342',
                '#ff6746', '#ff6a4a', '#ff6d4e', '#ff7052', '#ff7456', '#ff775a',
                '#ff7a5d', '#ff7d61', '#ff8065', '#ff8469', '#ff876d', '#ff8a71',
                '#ff8d75', '#ff9179', '#ff947d', '#ff9781', '#ff9a85', '#ff9e89'
            ],
            colorByPoint: true,
        }]
    });


    // Sector Bar Graph
    const sectorData = ;
    const formattedSectorData = sectorData.map(item => ({
        name: isHindi ? item.primary_activity.hindi_name : item.primary_activity.name,
        value: parseInt(item.Count, 10)
    }));

    // Sort the data in descending order
    formattedSectorData.sort((a, b) => b.value - a.value);

    // Separate the sorted names and values
    const sectorCategories = formattedSectorData.map(item => item.name);
    const sectorValues = formattedSectorData.map(item => item.value);

    Highcharts.chart('sector_primary_chart', {
        chart: {
            type: 'bar',
            width: 600,
            height: 1100
        },
        title: {
            text: 'Sector wise Primary Cooperative Societies'
        },
        xAxis: {
            categories: sectorCategories,
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Number of Societies'
            },
            labels: {
                overflow: 'justify'
            }
        },
        tooltip: {
            valueSuffix: ' societies'
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        legend: {
            enabled: false
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'Number of Societies',
            data: sectorValues,
            colors: [
                '#2903ff', '#3003ff', '#3307ff', '#360bff', '#390fff', '#3d13ff',
                '#4017ff', '#431bff', '#461fff', '#4a23ff', '#4d27ff', '#502bff',
                '#532eff', '#5632ff', '#5a36ff', '#5d3aff', '#603eff', '#6342ff',
                '#6746ff', '#6a4aff', '#6d4eff', '#7052ff', '#7456ff', '#775aff',
                '#7a5dff', '#7d61ff', '#8065ff', '#8469ff', '#876dff', '#8a71ff',
                '#8d75ff', '#9179ff', '#947dff', '#9781ff', '#9a85ff', '#9e89ff'
            ],
            colorByPoint: true,
        }]
    });

</script>
<script>
    /* document.addEventListener('DOMContentLoaded', function() {
         // Reset dropdown to empty or default value on page load
         const dropdown = document.getElementById('feedback_suggestion');
         if (dropdown) {
             dropdown.selectedIndex = 0;  // set to first option
         }
     }); */


    $('.go_btn_state').click(function () {
        document.addEventListener('DOMContentLoaded', function () {
            // Reset dropdown to empty or default value on page load
            const dropdown = document.getElementById('feedback_suggestion');
            if (dropdown) {
                dropdown.selectedIndex = 0;  // set to first option
            }
        });
    });

    /// Automatic cron jobs  
    /*(async function runQueriesSequentially() {
        var checkifquery = "0" ; 
            if(checkifquery == 1){
        try {
            
            // First query
            let response1 = await fetch('/en/home/run-sector-query');
            let data1 = await response1.json();
            console.log('First Query Result:', data1);
            // Update the UI with the result if needed
    
            // Second query
            let response2 = await fetch('/en/home/run-sector-state-query');
            let data2 = await response2.json();
            console.log('Second Query Result:', data2);
            // Update the UI with the result if needed
    
            // Third query
            let response3 = await fetch('/en/home/run-kpi-query');
            let data3 = await response3.json();
            console.log('Third Query Result:', data3);
            // Update the UI with the result if needed
        } catch (error) {
            console.error('Error:', error);
        }
    }else{
        //alert('updated') ;
    }
    })();*/
</script>


<script>
    const colorPatches = document.querySelectorAll('.colorscheme .patch');
    colorPatches.forEach(patch => {
        patch.addEventListener('click', changeColorScheme);
    });

    function changeColorScheme(event) {
        const body = document.querySelector('body');
        const colorSchemeClasses = ['color-green', 'color-blue', 'color-brown'];

        // Remove existing color scheme class
        colorSchemeClasses.forEach(cls => body.classList.remove(cls));

        // Add the new color scheme class
        const selectedColorScheme = event.target.classList[1];
        body.classList.add(selectedColorScheme);

        // Store the selected color scheme in localStorage
        localStorage.setItem('colorScheme', selectedColorScheme);
    }

    window.addEventListener('DOMContentLoaded', () => {
        const storedColorScheme = localStorage.getItem('colorScheme');
        if (storedColorScheme) {
            document.body.classList.add(storedColorScheme);
        }
    });

</script>