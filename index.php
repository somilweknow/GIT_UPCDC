<!DOCTYPE html>

<html lang="en-US">



<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Uttar Pradesh Cooperative Database Center</title>

    <link rel="stylesheet" href="frontend/csss/bootstrap.min.css" type="text/css">

    <link rel="stylesheet" href="frontend/csss/style.css" type="text/css">

    <link rel="stylesheet" href="frontend/csss/slick/slick.css?v2022" type="text/css">

    <link rel="stylesheet" href="frontend/csss/slick/slick-theme.css?v2022" type="text/css">

    <link rel="stylesheet" href="frontend/csss/owl.carousel.css" type="text/css">

    <link rel="stylesheet" href="frontend/csss/media.css">

    <link rel="stylesheet" href="frontend/csss/baguetteBox.min.css">

    <link rel="stylesheet" href="frontend/csss/all.min.css">

    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" type="text/css">-->

    <script src="js/stateDashboard/highcharts.js"></script>

    <script src="js/stateDashboard/map.js"></script>



    <style>

        .highcharts-undefined-series,

        .highcharts-color-undefined .highcharts-legend-item highcharts {

            display: none;

        }



        .statedashboard_state .right_corner {

            display: flex align-items: baseline;

            justify-content: normal;

        }



        .go_headeing .go_btn {

            margin-left: 15px;

        }



        .go_headeing .input-area.required {

            float: left;

            width: 80%;

        }



        .heading_text.right_corner {

            margin-bottom: 25px;

        }



        .statedashboard_state p {

            font-size: 16px;

            color: #000;

            padding-top: 10px;

            margin-bottom: 0;

            font-weight: 600;

        }



        .wider-logo {

            width: 150px;   /* increase width */

            height: 94px;   /* keep same height for alignment */

            object-fit: contain; /* keeps aspect ratio clean */

        }

    </style>



</head>



<body id="increasetext_body" class="photo_popup color-blue">

    <!-- Header Section Start -->





    <div class="left_menu">

        <div class="hamburger">

            <div class="logo-ham">

                <a href="/en" title="Go to home" rel="home"> <img src="frontend/img/logo.png" alt=""></a>

            </div>

            <div class="fa_times">

                <i class="fas fa-times" style="color: #ffffff;"></i>

            </div>

        </div>

        <ul id="accordionExample">

            <li><a href="/en" title="Home"><i class="fas fa-home same_icon"></i>Home</a></li>

            <!-- <li><a href="/en/state-dashboard/state/9" title="SCD Home">SCD Home</a></li> -->

            <li><a href="en/home/moc" title="About Moc">About MoC</a></li>

            <li><a href="en/home/applications" title="Journey of UPCDC">Journey of UPCDC</a></li>

            <li><a href="#" class="accordion-button collapsed" type="button" data-bs-toggle="collapse"

                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Reports</a>

                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"

                    data-bs-parent="#accordionExample">

                    <div class="menu_box_mobile">

                        <ul>

                            <li><a href="#" id='sub_button1' class="menu__link" role="link">Primary Cooperatives<i

                                        class="fas fa-sort-down"></i></a>

                                <div class="menu_box_mobile">

                                    <ul>

                                        <li><a href="/en/home/state-type-wise-sector">State Type Cooperatives</a></li>

                                        <li><a href="/en/home/sector-type-wise-sector">Sector Type Cooperatives</a></li>

                                    </ul>

                                </div>

                            </li>

                            <li><a href="#" id='sub_button1' class="menu__link" role="link">Cooperative Banks <i

                                        class="fas fa-sort-down"></i></a>

                                <div class="menu_box_mobile">

                                    <ul>

                                        <li><a href="en/home/scb-reports">s<em>t</em>cb</a></li>

                                        <li><a href="en/home/dccb-reports">dccb</a></li>

                                        <li><a href="en/home/ucb-reports">ucb</a></li>

                                    </ul>

                                </div>

                            </li>

                            <li><a href="/en/home/federation-reports" class="menu__link" role="link">National coop /

                                    Federations</a></li>

                            <li><a href="/en/home/cooperative-multistate-reports" class="menu__link"

                                    role="link">Multistate Cooperatives</a></li>

                            <li><a href="#" id='sub_button2'>SCARDB & PCARDB <i class="fas fa-sort-down"></i></a>

                                <div class="menu_box_mobile">

                                    <ul>

                                        <li><a href="en/state-dashboard/sacard">SCARDB</a></li>

                                        <li><a href="en/state-dashboard/pacard">PCARDB</a></li>

                                    </ul>

                                </div>

                            </li>

                            <li><a href="/en/home/cooperative-liquidation-reports" class="menu__link"

                                    role="link">Cooperatives under liquidation </a></li>

                        </ul>

                    </div>

                </div>

            </li>

            <li>

                <a href="#" class="accordion-button collapsed" type="button" data-bs-toggle="collapse"

                    data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">

                    User Manual

                </a>

                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"

                    data-bs-parent="#accordionExample">

                    <div class="menu_box_mobile">

                        <ul>

                            <li><a href="/frontend/img/user-manual-ncd-v1.4.pdf">Manual</a></li>

                            <li><a href="/en/home/training">Training Video</a></li>

                        </ul>

                    </div>

                </div>

            </li>

            <li><a href="/en/home/faq" title="Faq">Faq</a></li>

            <li><a href="/en/home/contact" title="Contact Us">Contact Us</a></li>

            <li><a href="index_3.php" title="login" class="login">Login</a>

            <li>

        </ul>

    </div>

    <header id="header" class="state_header">

        <div class="topStrip" style="background:#ff4e02">

            <div class="container abhayen">

                <div class="row">

                    <div class="col-4 left-sec">

                        <div class="common-left clearfix">

                            <ul>

                                <li class="gov-india">

                                    <span class="li_eng responsive_go_eng">Uttar Pradesh Cooperative Database Center (UPCDC)</span>

                                </li>

                            </ul>

                        </div>

                    </div>

                    <div class="col-8 text-right">

                        <ul class="topNav">

                            <li class="topNav_height">

                                <a href="#main_content">Skip To Main Content</a>

                            </li>

                            <li class="topNav_height">

                                <a href="en/home/screen-reader" target="_blank">Screen Reader Access</a>

                            </li>

                            <li class="topNav_height">

                                <div class="textResizeWrapper cf" id="accessControl">

                                    <input type="button" name="font_normal" value="A-" id="decreasetext"

                                        title="Decrease Font Size" class="decreaseFont">

                                    <input type="button" name="font_large" value="A" id="resettext"

                                        title="Normal Font Size" class="fontScaler large font-large">

                                    <input type="button" name="font_larger" value="A+" id="increasetext"

                                        title="Increase Font Size" class="increaseFont">

                                </div>

                            </li>

                            <li>

                                <div class="colorscheme">

                                    <div class="patch color-green">

                                    </div>

                                    <div class="patch color-blue">

                                    </div>

                                    <div class="patch color-brown">

                                    </div>

                                </div>

                            </li>

                            <li>

                                <label for="dark-mode-switch" class="slider theme-switch">

                                    <input type="checkbox" id="dark-mode-switch">

                                    <div class="slider round"></div>

                                </label>

                            </li>

                            <li class="topNav_height">

                                <select id="language-select-state">



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

                <div class="col-sm-3 d-flex">

                    <div class="logo me-2">

                        <a href="https://cooperatives.gov.in/" target="_blank" class="site_logo" rel="home">

                            <img id="logo" class="emblem" src="frontend/img/coop_logo.png" alt=""

                                style="width: 95px;height: 94px;">

                        </a>

                    </div>

                    <div class="logo">

                        <a href="https://cooperatives.gov.in/" target="_blank" class="site_logo" rel="home">

                            <img id="up_logo" class="emblem wider-logo" src="frontend/img/up_logo1.jpeg" alt="">

                        </a>

                    </div>

                </div>

                <div class="col-sm-6 mid_logo">

                    <h2>

                        <a href="/en">

                        <span><em>उ</em>त्तर <em>प</em>्रदेश <em>क</em>ो-<em>आ</em>परेटिव <em>ड</em>ेटाबेस <em>स</em>ेंटर</span>

                        <span><em>U</em>ttar <em>P</em>radesh <em>C</em>ooperative <em>D</em>atabase <em>C</em>enter</span>

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



        <nav class="wrapper nav-wrapper" style="background:#198754">

            <div class="container nav-container">

                <div class="login-link"></div>

                <div id="main-menu">

                    <div

                        class="menu-block-wrapper menu-block-2 menu-name-main-menu parent-mlid-0 menu-level-1 d-flex justify-content-between">

                        <ul class="menu" id="nav">

                            <li class="menu__item is-active is-leaf first leaf menu-mlid-218"><a href="index.php"

                                    class="menu__link active_menu" role="link"><i class="fas fa-home same_icon"></i>Home</a></li>

                            <!-- <li class="menu__item is-active is-leaf first leaf menu-mlid-218"><a href="/en/state-dashboard/state/9"

                                class="menu__link active_menu" role="link">SCD Home</a></li> -->

                            <li class="menu__item is-expanded expanded menu-mlid-3498">

                                <a href="javascript:void(0);" class="menu__link" id="about-link">About Us</a>

                            </li>

                            <li class="menu__item is-expanded expanded menu-mlid-3498"><a href="https://iycup2025.in/events.php" class="menu__link" role="link">Events</a> </li>

                            <li class="menu__item is-expanded expanded menu-mlid-3498"><a

                                    href="contact.php" class="menu__link" role="link">Contact us</a>

                            </li><a href="index_3.php" class="ncd-btn" title="Download NCD-MetaData">

                            LOGIN</a>

                            </li>

                            <!-- <li>

                        <a href="<//?=$this->Url->build(['controller'=>'home','action'=>'training_material'])?>" class="menu__link">training material</a>

                        </li>

                        <li class="menu__item is-expanded expanded menu-mlid-951"><a href="<//?=$this->Url->build(['controller'=>'home','action'=>'faq'])?>" class="menu__link"

                                role="link"> FAQ </a> </li> -->

                            <!-- <li class="menu__item is-expanded expanded menu-mlid-3463 last"><a

                                    href="https://upcod.in/index.php" role="link"><i class="far fa-user"></i> Login </a>

                            </li> -->



                        </ul>

                        <div class="search-box">

                            <input type="text" placeholder="Type to search.." class="searchInput">

                            <div class="search-icon">

                                <i class="fas fa-search"></i>

                            </div>

                            <div class="cancel-icon">

                                <i class="fas fa-times"></i>

                            </div>

                            <div class="search-data">

                            </div>

                        </div>

                        <div class="mobile_menu">

                            <i class="fas fa-bars" style="color: #ffffff;"></i>

                            <a href="index_3.php" class="mobile_login"><i class="far fa-user"></i> Login</a>

                        </div>

                    </div>

                </div>

            </div>

        </nav>

    </header>



    <!-- <section class="inner_banner state_inner_banner">

        <div class="overlay">

            <h2>UTTAR PRADESH Cooperative Database </h2>

        </div>

    </section> -->



    <section class="statedashboard_state pb-0">

        <div class="container">



            <div class="heading">

                <div class="heading_text right_corner">

                    <div class="go_headeing col-md-6">

                        <p class="right_corner_para">District's Cooperative Database : </p>

                        <div class="input-area select required">

                            <div class="form-group"><select name="district_code" required="required"

                                    id="changetheurl_district_wise" class="form-control">

                                    <option value="">-Select District-</option>

                                    <option value="118">AGRA</option>

                                    <option value="119">ALIGARH</option>

                                    <option value="121">AMBEDKAR NAGAR</option>

                                    <option value="640">Amethi</option>

                                    <option value="154">AMROHA</option>

                                    <option value="122">AURAIYA</option>

                                    <option value="140">AYODHYA</option>

                                    <option value="123">AZAMGARH</option>

                                    <option value="124">BAGHPAT</option>

                                    <option value="125">BAHRAICH</option>

                                    <option value="126">BALLIA</option>

                                    <option value="127">BALRAMPUR</option>

                                    <option value="128">BANDA</option>

                                    <option value="129">BARABANKI</option>

                                    <option value="130">BAREILLY</option>

                                    <option value="131">BASTI</option>

                                    <option value="179">BHADOHI</option>

                                    <option value="132">BIJNOR</option>

                                    <option value="133">BUDAUN</option>

                                    <option value="134">BULANDSHAHR</option>

                                    <option value="135">CHANDAULI</option>

                                    <option value="136">CHITRAKOOT</option>

                                    <option value="137">DEORIA</option>

                                    <option value="138">ETAH</option>

                                    <option value="139">ETAWAH</option>

                                    <option value="141">FARRUKHABAD</option>

                                    <option value="142">FATEHPUR</option>

                                    <option value="143">FIROZABAD</option>

                                    <option value="144">GAUTAM BUDDHA NAGAR</option>

                                    <option value="145">GHAZIABAD</option>

                                    <option value="146">GHAZIPUR</option>

                                    <option value="147">GONDA</option>

                                    <option value="148">GORAKHPUR</option>

                                    <option value="149">HAMIRPUR</option>

                                    <option value="661">HAPUR</option>

                                    <option value="150">HARDOI</option>

                                    <option value="163">HATHRAS</option>

                                    <option value="151">JALAUN</option>

                                    <option value="152">JAUNPUR</option>

                                    <option value="153">JHANSI</option>

                                    <option value="155">KANNAUJ</option>

                                    <option value="156">KANPUR DEHAT</option>

                                    <option value="157">KANPUR NAGAR</option>

                                    <option value="633">Kasganj</option>

                                    <option value="158">KAUSHAMBI</option>

                                    <option value="159">KHERI</option>

                                    <option value="160">KUSHI NAGAR</option>

                                    <option value="161">LALITPUR</option>

                                    <option value="162">LUCKNOW</option>

                                    <option value="164">MAHARAJGANJ</option>

                                    <option value="165">MAHOBA</option>

                                    <option value="166">MAINPURI</option>

                                    <option value="167">MATHURA</option>

                                    <option value="168">MAU</option>

                                    <option value="169">MEERUT</option>

                                    <option value="170">MIRZAPUR</option>

                                    <option value="171">MORADABAD</option>

                                    <option value="172">MUZAFFARNAGAR</option>

                                    <option value="173">PILIBHIT</option>

                                    <option value="174">PRATAPGARH</option>

                                    <option value="120">PRAYAGRAJ</option>

                                    <option value="175">RAE BARELI</option>

                                    <option value="176">RAMPUR</option>

                                    <option value="177">SAHARANPUR</option>

                                    <option value="659">SAMBHAL</option>

                                    <option value="178">SANT KABEER NAGAR</option>

                                    <option value="180">SHAHJAHANPUR</option>

                                    <option value="660">SHAMLI</option>

                                    <option value="181">SHRAVASTI</option>

                                    <option value="182">SIDDHARTH NAGAR</option>

                                    <option value="183">SITAPUR</option>

                                    <option value="184">SONBHADRA</option>

                                    <option value="185">SULTANPUR</option>

                                    <option value="186">UNNAO</option>

                                    <option value="187">VARANASI</option>

                                </select></div>

                        </div> <a href="#" class="go_btn_inner go_btn_district"><i class="fas fa-arrow-right"></i></a>

                    </div>

                </div>

            </div>



            <div class="row mid">

                <div class="col-md-5 left">

                    <script src="js/mapjson/uttarpradesh.js"></script>

                    <div id="container" style="height: 500px; min-width: 310px; max-width: 800px; margin: 0 auto"></div>

                </div>



                <div class="col-md-7 right">

                    <h3> About </h3>

                    <h2>UTTAR PRADESH</h2>



                    <div id="about-content">

                        <div id="trimmed-content">

                            Cooperative movement started in 1904 in the state and country with the objective to provide

                            short-term crop loan to common people especially rural farmers for their economic

                            development. Later, other program such as fertilizers, distribution of seeds, process

                            units/cold storage, distribution of consumer goods, long-term loans, labour organization,

                            dairy development, sugar cane industry, housing, handloom development etc. were also started

                            through cooperative societies. Due to expansion of cooper...

                        </div>

                        <div id="full-content" style="display:none;">

                            Cooperative movement started in 1904 in the state and country with the objective to provide

                            short-term crop loan to common people especially rural farmers for their economic

                            development. Later, other program such as fertilizers, distribution of seeds, process

                            units/cold storage, distribution of consumer goods, long-term loans, labour organization,

                            dairy development, sugar cane industry, housing, handloom development etc. were also started

                            through cooperative societies. Due to expansion of cooperative movement/programs, the power

                            of Registrar, Cooperative Societies has been delegated to the officers of 9 other

                            departments, viz., Sugarcane, Industry, Khadi and Village Industry, Housing, Milk, Handloom,

                            Fishery, Silk and Horticulture. The cooperative societies are managed by representatives

                            elected in democratic manner which promote people&rsquo;s participation. Uttar Pradesh

                            Cooperative and Village Development Bank provides long-term loans for agriculture and

                            non-agriculture sector through its branches. Urban Cooperative Banks of the State provide

                            consumer loan, housing loan, vehicle loan and business loan in their area of operation. To

                            save the farmers from the exploitation of middleman, wheat and paddy are purchased by the

                            cooperative societies under price support program of state government. Additional storage

                            capacity is being created to enhance the present storage capacity of cooperative societies.

                            A total of 43,362 cooperative societies are registered in 75 districts of Uttar Pradesh with

                            membership of 1,84,64,158 members. Lucknow, Agra and Prayagraj districts have the highest

                            number of societies viz. 1543, 1490 and 1468 respectively. Dairy sector is the largest

                            sector constituting of 41.17% (17,852) cooperatives. It is followed by PACS which constitute

                            17.39% (7541) cooperatives. Notably, 50.84% (93,87,212) of the total membersare engaged in

                            PACS. 77.51% (33,609) of the societies are working in rural areas. Though PACS constitute

                            17.39% of the cooperatives, their Gram Panchayat coverage is extensive (96.16%). Dairy

                            cooperatives cover 27.01% of the Gram Panchayats. Majority of the PACS 5414 are affiliated

                            with Federations/ Unions

                        </div>

                    </div>



                    <br>

                    <a href="javascript:void(0);" id="toggle-link" onclick="toggleContent()">

                        <i class="fas fa-caret-right"></i> Know More </a>





                    <!--

                         <p>

                                                    Cooperative movement started in 1904 in the state and country with the objective to provide short-term crop loan to common people especially rural farmers for their economic development. Later, other program such as fertilizers, distribution of seeds, process units/cold storage, distribution of consumer goods, long-term loans, labour organization, dairy development, sugar cane industry, housing, handloom development etc. were also started through cooperative societies. Due to expansion of cooper....                                                  </p> 

 

                     <a href="/en/state-dashboard/about/9">Know More <i class="fas fa-caret-right"></i></a> -->

                </div>





            </div>

        </div>

    </section>



    <section class="important_key state_important_key pt-0">

        <div class="container">

            <div class="heading">

                <h2>UTTAR PRADESH - <span>At a Glance</span></h2>

                <em id="currentTime"></em>

            </div>



            <script>

                function updateTime() {

                    const now = new Date();



                    // format: DD/MM/YYYY HH:MM AM/PM

                    const options = {

                        day: "2-digit",

                        month: "2-digit",

                        year: "numeric",

                        hour: "2-digit",

                        minute: "2-digit",

                        second: "2-digit",

                        hour12: true

                    };



                    const formatted = now.toLocaleString("en-GB", options);



                    document.getElementById("currentTime").textContent = "As on " + formatted;

                }



                // Run immediately and then every second

                updateTime();

                setInterval(updateTime, 1000);

            </script>

            <ul class="row main_glance">

                <li class="col-md-2">

                    <div class="box_inner">

                        <!-- <a href="/en/state-dashboard/cooperative-sectors-reports-dist/9"> -->

                            <a href="">

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

                        <!-- <a href="/en/state-dashboard/all-cooperatives/9"> -->

                            <a href="">

                            <div class="img">

                                <img src="img/../frontend/img/Total-Sector.png" alt="" />

                            </div>

                            <h3>All Cooperatives</h3>

                            <span>41086</span>

                        </a>

                    </div>

                </li>

                <li class="col-md-2">

                    <div class="box_inner">

                        <!-- <a href="/en/state-dashboard/state-type-wise-sector/9"> -->

                            <a href="">

                            <div class="img">

                                <img src="img/../frontend/img/primary-cooperative.png" alt="" />

                            </div>

                            <h3>Primary Cooperatives</h3> <span>40727</span>

                        </a>

                    </div>

                </li>

                <li class="col-md-2">

                    <div class="box_inner">

                        <!-- <a href="/en/state-dashboard/state-functional-cooperatives/9"> -->

                            <a href="">

                            <div class="img">

                                <img src="img/../frontend/img/functional-cooperatives.png" alt="" />

                            </div>

                            <h3>Functional Cooperatives</h3> <span>21199</span>

                        </a>

                    </div>

                </li>

                <li class="col-md-2">

                    <div class="box_inner">

                        <!-- <a href="/en/state-dashboard/state-non-functional-cooperatives/9"> -->

                            <a href="">

                            <div class="img">

                                <img src="img/../frontend/img/functional.png" alt="" />

                            </div>

                            <h3>Non Functional/Dormant Cooperatives</h3> <span>19057</span>

                        </a>

                    </div>

                </li>

                <li class="col-md-2">

                    <div class="box_inner">

                        <!-- <a href="/en/state-dashboard/state-under-liquidation-cooperatives/9"> -->

                            <a href="">

                            <div class="img">

                                <img src="img/../frontend/img/liquidity.png" alt="" />

                            </div>

                            <h3>Cooperatives Under Liquidation</h3> <span>830</span>

                        </a>

                    </div>

                </li>

                <li class="col-md-2">

                    <div class="box_inner">

                        <!-- <a href="/en/state-dashboard/federations/9/2"> -->

                            <a href="">

                            <div class="img">

                                <img src="img/../frontend/img/state-federation.png" alt="" />

                            </div>

                            <h3>State Federations</h3> <span>14</span>

                        </a>

                    </div>

                </li>

                <li class="col-md-2">

                    <div class="box_inner">

                        <!-- <a href="/en/state-dashboard/federations/9/3"> -->

                            <a href="">

                            <div class="img">

                                <img src="img/../frontend/img/district-federation.png" alt="" />

                            </div>

                            <h3>District Federations</h3> <span>14</span>

                        </a>

                    </div>

                </li>

                <li class="col-md-2">

                    <div class="box_inner">

                        <!-- <a href="/en/state-dashboard/federations/9/4"> -->

                            <a href="">

                            <div class="img">

                                <img src="img/../frontend/img/block_federation.png" alt="" />

                            </div>

                            <h3>Block/Taluka/Mandal Federations</h3> <span>74</span>

                        </a>

                    </div>

                </li>

                <li class="col-md-2">

                    <div class="box_inner">

                        <!-- <a href="/en/state-dashboard/federations/9/5"> -->

                            <a href="">

                            <div class="img">

                                <img src="img/../frontend/img/functional-mscs.png" alt="" />

                            </div>

                            <h3>Regional Federations</h3> <span>9</span>

                        </a>

                    </div>

                </li>

                <!-- <li class="col-md-2">

                    <div class="box_inner">

                        <a href="/en/state-dashboard/sacard/9">

                            <div class="img">

                                <img src="img/../frontend/img/SCARDB.png" alt="" />

                            </div>

                            <h3>SCARDB</h3>

                            <span>1</span>

                        </a>

                    </div>

                </li>

                <li class="col-md-2">

                    <div class="box_inner">

                        <a href="javascript:(void)">

                            <div class="img">

                                <img src="img/../frontend/img/PCARDB.png" alt="" />

                            </div>

                            <h3>PCARDB</h3>

                            <span>0</span>

                        </a>

                    </div>

                </li> -->

                <li class="col-md-2">

                    <div class="box_inner">

                        <!-- <a href="/en/state-dashboard/scb-reports/9"> -->

                            <a href="">

                            <div class="img">

                                <img src="img/../frontend/img/State-Cooperative-Banks.png" alt="" />

                            </div>

                            <h3>State Cooperative Banks</h3> <span>1</span>

                        </a>

                    </div>

                </li>

                <li class="col-md-2">

                    <div class="box_inner">

                        <!-- <a href="/en/state-dashboard/dccb-reports/9"> -->

                            <a href="">

                            <div class="img">

                                <img src="img/../frontend/img/district-cooperative-banks.png" alt="" />

                            </div>

                            <h3>District Cooperative Banks</h3> <span>50</span>

                        </a>

                    </div>

                </li>

                

            </ul>

        </div>

    </section>



    <section class="important_key important_key_add important_key_add_state">

        <div class="container">

            <h2><span>Key Performance Indicators</span></h2>

            <ul class="row">

                <li class="col">

                    <div class="box_inner">

                        <a

                            <a href="">

                            <div class="img">

                                <img src="img/../frontend/img/Total-Sector.png" alt="" />

                            </div>

                            <h3>New Societies</h3> <em>In this month</em>

                            <h4>State-wise summary</h4> <span>7</span>

                        </a>

                    </div>

                </li>

                <!-- <li class="col">

                    <div class="box_inner">

                    <a href="<//?=$this->Url->build(['controller'=>'NscdKeyPerformance','action'=>'new_scd_month_societies_regiserstate', "formmonth" => $formmonth, "formyear" => $formyear, $id])?>">

                            <div class="img">

                                <//?php echo $this->Html->image('../frontend/img/all-india-cooperative.png', array('alt' => '')); ?>

                            </div>

                            <h3>New Societies</h3>

                            <em>In this month</em>

                            <h4>State-wise summary</h4>

                            <span><//?=$sectorWiseCounts[0]->sector_count ; ?></span>

                        </a>

                    </div>

                </li> -->

                <li class="col">

                    <div class="box_inner">

                        <!-- <a href="/en/nscd-key-performance/scd-year-societies-regisered-state/9?formyear=2025"> -->

                            <a href="">

                            <div class="img">

                                <img src="img/../frontend/img/primary-cooperative.png" alt="" />

                            </div>

                            <h3>List of New Cooperatives</h3> <em>In This Year</em>

                            <h4>State-wise summary</h4> <span>1390</span>

                        </a>

                    </div>

                </li>

                <li class="col">

                    <div class="box_inner">

                        <!-- <a href="/en/nscd-key-performance/scd-mscs-societies-regisred-state/9?formyear=2025"> -->

                            <a href="">

                            <div class="img">

                                <img src="img/../frontend/img/functional-cooperatives.png" alt="" />

                            </div>

                            <h3>List of MSCS Registered</h3> <em>In This Year</em>

                            <h4>State-wise summary</h4> <span>11</span>

                        </a>

                    </div>

                </li>



                <li class="col">

                    <div class="box_inner">

                        <!-- <a href="/en/nscd-key-performance/gps-coverage/9"> -->

                            <a href="">

                            <div class="img">

                                <img src="img/../frontend/img/national-federation.png" alt="" />

                            </div>



                            <h3>GPs Coverage</h3> <em>Covered GPs</em>

                            <h4><b>PACS</b><b>Dairy</b><b>Fishery</b></h4> <span>58094</span>

                            <span>12244</span>

                            <span>2790</span>

                        </a>

                    </div>

                </li>

                <li class="col">

                    <div class="box_inner">

                        <!-- <a href="/en/nscd-key-performance/gps-not-coverage/9"> -->

                            <a href="">

                            <div class="img">

                                <img src="img/../frontend/img/national-federation.png" alt="" />

                            </div>



                            <h3>GPs Not Coverage</h3> <em>Not Covered GPs</em>

                            <h4><b>PACS</b><b>Dairy</b><b>Fishery</b></h4> <span>8</span>

                            <span>45859</span>

                            <span>55312</span>

                        </a>

                    </div>

                </li>

            </ul>

        </div>

    </section>

    <section class="sector_data_info">

        <div class="container">

            <!-- <h2> Functional Status of Cooperatives</h2> -->

            <h2>Functional Status of Cooperatives</h2>

            <div class="row">

                <div class="col-md-4 left">

                    <table>

                        <tr>

                            <th colspan="4" style="text-align: center;">

                                UTTAR PRADESH

                            </th>

                        </tr>

                        <tr>

                            <td style="border-bottom: none !important;"></td>

                            <td style="padding: 5px 20px !important"><strong>Rural</strong></td>

                            <td style="padding: 5px 20px !important"><strong>Urban</strong></td>

                            <td style="padding: 5px 20px !important"><strong>Total</strong></td>

                        </tr>



                        <tr>

                            <td>Functional</td>

                            <td>17116</td>

                            <td>3756</td>

                            <td>20872</td>

                        </tr>

                        <tr>

                            <td>Non Functional/Dormant</td>

                            <td>13866</td>

                            <td>5178</td>

                            <td>19044</td>

                        </tr>

                        <tr>

                            <td>Under Liquidation</td>

                            <td>442</td>

                            <td>369</td>

                            <td>811</td>

                        </tr>

                        <tr>

                            <td>Total Societies</td>

                            <td>31424</td>

                            <td>9303</td>

                            <td>40727</td>

                        </tr>

                    </table>

                    <!-- Location Wise Table  -->

                    <table style="margin-top: 20px;">

                        <tr>

                            <th colspan="2" style="text-align: center;">Area Of Operation Wise No. of Societies </th>

                        </tr>

                        <tr>

                            <!-- <td>Urban</td> -->

                            <td>Urban</td>

                            <td>8606</td>

                        </tr>

                        <tr>

                            <!-- <td>Rural</td> -->

                            <td>Rural</td>

                            <td>31625</td>

                        </tr>

                        <tr>

                            <!-- <td>Both</td> -->

                            <td>Both</td>

                            <td>496</td>

                        </tr>

                    </table>

                </div>

                <!-- Column Graphs -->

                <div class="col-md-4 right">

                    <h6 style="text-align: center;"> <b>Functional Status Wise Cooperatives</b> </h6>

                    <figure class="highcharts-figure">

                        <div id="functionality_graph"></div>

                    </figure>

                </div>

                <div class="col-md-4">

                    <h6 style="text-align: center;"> <b>Area of Operation Wise Cooperatives</b> </h6>

                    <figure class="highcharts-figure">

                        <div id="location_graph"></div>

                    </figure>

                </div>

            </div>

        </div>

    </section>



    <!-- Below Section -->

    <section class="sector_data_info sector_data_info_second">

        <div class="container">

            <h2>District-Wise Societies - UTTAR PRADESH</h2>

            <div class="row">

                <div class="col-md-7 left">

                    <div class="sector_table">

                        <div style="max-height: 430px; overflow-y: auto; position: relative; border: 1px solid #ccc;">

                            <table id="sortstSoctyTable" border="0" cellpadding="0" cellspacing="0"

                                style="width: 100%; border-collapse: collapse;">

                                <thead style="position: sticky; top: 0; background-color: #f9f9f9; z-index: 2;">

                                    <tr>

                                        <th>District Name</th>

                                        <th onclick="sortStateTable(1)">No. of Societies<i class="fa fa-sort"></i></th>

                                        <th>% Societies</th>

                                        <th onclick="sortStateTable(3)">No. of Members<i class="fa fa-sort"></i></th>

                                        <th>% Members</th>

                                        </th>

                                    </tr>

                                </thead>

                                <tbody id="table-state">

                                    <tr>

                                        <td value="118">AGRA</td>



                                        <td>

                                            1324 </td>

                                        <td>

                                            3.25 </td>

                                        <td>

                                            290124 </td>

                                        <td>

                                            1.51 </td>

                                    </tr>

                                    <tr>

                                        <td value="119">ALIGARH</td>



                                        <td>

                                            494 </td>

                                        <td>

                                            1.21 </td>

                                        <td>

                                            275649 </td>

                                        <td>

                                            1.43 </td>

                                    </tr>

                                    <tr>

                                        <td value="121">AMBEDKAR NAGAR</td>



                                        <td>

                                            616 </td>

                                        <td>

                                            1.51 </td>

                                        <td>

                                            196769 </td>

                                        <td>

                                            1.02 </td>

                                    </tr>

                                    <tr>

                                        <td value="640">Amethi</td>



                                        <td>

                                            376 </td>

                                        <td>

                                            0.92 </td>

                                        <td>

                                            84930 </td>

                                        <td>

                                            0.44 </td>

                                    </tr>

                                    <tr>

                                        <td value="154">AMROHA</td>



                                        <td>

                                            345 </td>

                                        <td>

                                            0.85 </td>

                                        <td>

                                            381461 </td>

                                        <td>

                                            1.98 </td>

                                    </tr>

                                    <tr>

                                        <td value="122">AURAIYA</td>



                                        <td>

                                            446 </td>

                                        <td>

                                            1.10 </td>

                                        <td>

                                            107749 </td>

                                        <td>

                                            0.56 </td>

                                    </tr>

                                    <tr>

                                        <td value="140">AYODHYA</td>



                                        <td>

                                            554 </td>

                                        <td>

                                            1.36 </td>

                                        <td>

                                            303830 </td>

                                        <td>

                                            1.58 </td>

                                    </tr>

                                    <tr>

                                        <td value="123">AZAMGARH</td>



                                        <td>

                                            964 </td>

                                        <td>

                                            2.37 </td>

                                        <td>

                                            290571 </td>

                                        <td>

                                            1.51 </td>

                                    </tr>

                                    <tr>

                                        <td value="124">BAGHPAT</td>



                                        <td>

                                            290 </td>

                                        <td>

                                            0.71 </td>

                                        <td>

                                            329964 </td>

                                        <td>

                                            1.72 </td>

                                    </tr>

                                    <tr>

                                        <td value="125">BAHRAICH</td>



                                        <td>

                                            543 </td>

                                        <td>

                                            1.33 </td>

                                        <td>

                                            358724 </td>

                                        <td>

                                            1.87 </td>

                                    </tr>

                                    <tr>

                                        <td value="126">BALLIA</td>



                                        <td>

                                            405 </td>

                                        <td>

                                            0.99 </td>

                                        <td>

                                            189200 </td>

                                        <td>

                                            0.98 </td>

                                    </tr>

                                    <tr>

                                        <td value="127">BALRAMPUR</td>



                                        <td>

                                            192 </td>

                                        <td>

                                            0.47 </td>

                                        <td>

                                            146223 </td>

                                        <td>

                                            0.76 </td>

                                    </tr>

                                    <tr>

                                        <td value="128">BANDA</td>



                                        <td>

                                            356 </td>

                                        <td>

                                            0.87 </td>

                                        <td>

                                            100327 </td>

                                        <td>

                                            0.52 </td>

                                    </tr>

                                    <tr>

                                        <td value="129">BARABANKI</td>



                                        <td>

                                            803 </td>

                                        <td>

                                            1.97 </td>

                                        <td>

                                            225893 </td>

                                        <td>

                                            1.18 </td>

                                    </tr>

                                    <tr>

                                        <td value="130">BAREILLY</td>



                                        <td>

                                            828 </td>

                                        <td>

                                            2.03 </td>

                                        <td>

                                            565997 </td>

                                        <td>

                                            2.94 </td>

                                    </tr>

                                    <tr>

                                        <td value="131">BASTI</td>



                                        <td>

                                            479 </td>

                                        <td>

                                            1.18 </td>

                                        <td>

                                            360386 </td>

                                        <td>

                                            1.87 </td>

                                    </tr>

                                    <tr>

                                        <td value="179">BHADOHI</td>



                                        <td>

                                            189 </td>

                                        <td>

                                            0.46 </td>

                                        <td>

                                            54111 </td>

                                        <td>

                                            0.28 </td>

                                    </tr>

                                    <tr>

                                        <td value="132">BIJNOR</td>



                                        <td>

                                            812 </td>

                                        <td>

                                            1.99 </td>

                                        <td>

                                            783879 </td>

                                        <td>

                                            4.08 </td>

                                    </tr>

                                    <tr>

                                        <td value="133">BUDAUN</td>



                                        <td>

                                            502 </td>

                                        <td>

                                            1.23 </td>

                                        <td>

                                            193460 </td>

                                        <td>

                                            1.01 </td>

                                    </tr>

                                    <tr>

                                        <td value="134">BULANDSHAHR</td>



                                        <td>

                                            840 </td>

                                        <td>

                                            2.06 </td>

                                        <td>

                                            452061 </td>

                                        <td>

                                            2.35 </td>

                                    </tr>

                                    <tr>

                                        <td value="135">CHANDAULI</td>



                                        <td>

                                            497 </td>

                                        <td>

                                            1.22 </td>

                                        <td>

                                            117211 </td>

                                        <td>

                                            0.61 </td>

                                    </tr>

                                    <tr>

                                        <td value="136">CHITRAKOOT</td>



                                        <td>

                                            290 </td>

                                        <td>

                                            0.71 </td>

                                        <td>

                                            74663 </td>

                                        <td>

                                            0.39 </td>

                                    </tr>

                                    <tr>

                                        <td value="137">DEORIA</td>



                                        <td>

                                            370 </td>

                                        <td>

                                            0.91 </td>

                                        <td>

                                            298420 </td>

                                        <td>

                                            1.55 </td>

                                    </tr>

                                    <tr>

                                        <td value="138">ETAH</td>



                                        <td>

                                            372 </td>

                                        <td>

                                            0.91 </td>

                                        <td>

                                            131578 </td>

                                        <td>

                                            0.68 </td>

                                    </tr>

                                    <tr>

                                        <td value="139">ETAWAH</td>



                                        <td>

                                            537 </td>

                                        <td>

                                            1.32 </td>

                                        <td>

                                            157113 </td>

                                        <td>

                                            0.82 </td>

                                    </tr>

                                    <tr>

                                        <td value="141">FARRUKHABAD</td>



                                        <td>

                                            519 </td>

                                        <td>

                                            1.27 </td>

                                        <td>

                                            105916 </td>

                                        <td>

                                            0.55 </td>

                                    </tr>

                                    <tr>

                                        <td value="142">FATEHPUR</td>



                                        <td>

                                            708 </td>

                                        <td>

                                            1.74 </td>

                                        <td>

                                            160881 </td>

                                        <td>

                                            0.84 </td>

                                    </tr>

                                    <tr>

                                        <td value="143">FIROZABAD</td>



                                        <td>

                                            310 </td>

                                        <td>

                                            0.76 </td>

                                        <td>

                                            137998 </td>

                                        <td>

                                            0.72 </td>

                                    </tr>

                                    <tr>

                                        <td value="144">GAUTAM BUDDHA NAGAR</td>



                                        <td>

                                            558 </td>

                                        <td>

                                            1.37 </td>

                                        <td>

                                            83986 </td>

                                        <td>

                                            0.44 </td>

                                    </tr>

                                    <tr>

                                        <td value="145">GHAZIABAD</td>



                                        <td>

                                            707 </td>

                                        <td>

                                            1.74 </td>

                                        <td>

                                            147126 </td>

                                        <td>

                                            0.77 </td>

                                    </tr>

                                    <tr>

                                        <td value="146">GHAZIPUR</td>



                                        <td>

                                            848 </td>

                                        <td>

                                            2.08 </td>

                                        <td>

                                            199944 </td>

                                        <td>

                                            1.04 </td>

                                    </tr>

                                    <tr>

                                        <td value="147">GONDA</td>



                                        <td>

                                            711 </td>

                                        <td>

                                            1.75 </td>

                                        <td>

                                            321720 </td>

                                        <td>

                                            1.67 </td>

                                    </tr>

                                    <tr>

                                        <td value="148">GORAKHPUR</td>



                                        <td>

                                            766 </td>

                                        <td>

                                            1.88 </td>

                                        <td>

                                            194345 </td>

                                        <td>

                                            1.01 </td>

                                    </tr>

                                    <tr>

                                        <td value="149">HAMIRPUR</td>



                                        <td>

                                            309 </td>

                                        <td>

                                            0.76 </td>

                                        <td>

                                            110993 </td>

                                        <td>

                                            0.58 </td>

                                    </tr>

                                    <tr>

                                        <td value="661">HAPUR</td>



                                        <td>

                                            276 </td>

                                        <td>

                                            0.68 </td>

                                        <td>

                                            245206 </td>

                                        <td>

                                            1.28 </td>

                                    </tr>

                                    <tr>

                                        <td value="150">HARDOI</td>



                                        <td>

                                            758 </td>

                                        <td>

                                            1.86 </td>

                                        <td>

                                            349535 </td>

                                        <td>

                                            1.82 </td>

                                    </tr>

                                    <tr>

                                        <td value="163">HATHRAS</td>



                                        <td>

                                            202 </td>

                                        <td>

                                            0.50 </td>

                                        <td>

                                            116649 </td>

                                        <td>

                                            0.61 </td>

                                    </tr>

                                    <tr>

                                        <td value="151">JALAUN</td>



                                        <td>

                                            587 </td>

                                        <td>

                                            1.44 </td>

                                        <td>

                                            234129 </td>

                                        <td>

                                            1.22 </td>

                                    </tr>

                                    <tr>

                                        <td value="152">JAUNPUR</td>



                                        <td>

                                            683 </td>

                                        <td>

                                            1.68 </td>

                                        <td>

                                            214699 </td>

                                        <td>

                                            1.12 </td>

                                    </tr>

                                    <tr>

                                        <td value="153">JHANSI</td>



                                        <td>

                                            630 </td>

                                        <td>

                                            1.55 </td>

                                        <td>

                                            203833 </td>

                                        <td>

                                            1.06 </td>

                                    </tr>

                                    <tr>

                                        <td value="155">KANNAUJ</td>



                                        <td>

                                            377 </td>

                                        <td>

                                            0.93 </td>

                                        <td>

                                            99580 </td>

                                        <td>

                                            0.52 </td>

                                    </tr>

                                    <tr>

                                        <td value="156">KANPUR DEHAT</td>



                                        <td>

                                            492 </td>

                                        <td>

                                            1.21 </td>

                                        <td>

                                            116458 </td>

                                        <td>

                                            0.61 </td>

                                    </tr>

                                    <tr>

                                        <td value="157">KANPUR NAGAR</td>



                                        <td>

                                            1086 </td>

                                        <td>

                                            2.67 </td>

                                        <td>

                                            200940 </td>

                                        <td>

                                            1.05 </td>

                                    </tr>

                                    <tr>

                                        <td value="633">Kasganj</td>



                                        <td>

                                            149 </td>

                                        <td>

                                            0.37 </td>

                                        <td>

                                            97583 </td>

                                        <td>

                                            0.51 </td>

                                    </tr>

                                    <tr>

                                        <td value="158">KAUSHAMBI</td>



                                        <td>

                                            445 </td>

                                        <td>

                                            1.09 </td>

                                        <td>

                                            81490 </td>

                                        <td>

                                            0.42 </td>

                                    </tr>

                                    <tr>

                                        <td value="159">KHERI</td>



                                        <td>

                                            565 </td>

                                        <td>

                                            1.39 </td>

                                        <td>

                                            1266413 </td>

                                        <td>

                                            6.59 </td>

                                    </tr>

                                    <tr>

                                        <td value="160">KUSHI NAGAR</td>



                                        <td>

                                            331 </td>

                                        <td>

                                            0.81 </td>

                                        <td>

                                            662077 </td>

                                        <td>

                                            3.44 </td>

                                    </tr>

                                    <tr>

                                        <td value="161">LALITPUR</td>



                                        <td>

                                            331 </td>

                                        <td>

                                            0.81 </td>

                                        <td>

                                            122503 </td>

                                        <td>

                                            0.64 </td>

                                    </tr>

                                    <tr>

                                        <td value="162">LUCKNOW</td>



                                        <td>

                                            1522 </td>

                                        <td>

                                            3.74 </td>

                                        <td>

                                            230689 </td>

                                        <td>

                                            1.20 </td>

                                    </tr>

                                    <tr>

                                        <td value="164">MAHARAJGANJ</td>



                                        <td>

                                            360 </td>

                                        <td>

                                            0.88 </td>

                                        <td>

                                            266301 </td>

                                        <td>

                                            1.39 </td>

                                    </tr>

                                    <tr>

                                        <td value="165">MAHOBA</td>



                                        <td>

                                            214 </td>

                                        <td>

                                            0.53 </td>

                                        <td>

                                            76564 </td>

                                        <td>

                                            0.40 </td>

                                    </tr>

                                    <tr>

                                        <td value="166">MAINPURI</td>



                                        <td>

                                            253 </td>

                                        <td>

                                            0.62 </td>

                                        <td>

                                            97743 </td>

                                        <td>

                                            0.51 </td>

                                    </tr>

                                    <tr>

                                        <td value="167">MATHURA</td>



                                        <td>

                                            458 </td>

                                        <td>

                                            1.12 </td>

                                        <td>

                                            316151 </td>

                                        <td>

                                            1.64 </td>

                                    </tr>

                                    <tr>

                                        <td value="168">MAU</td>



                                        <td>

                                            430 </td>

                                        <td>

                                            1.06 </td>

                                        <td>

                                            142816 </td>

                                        <td>

                                            0.74 </td>

                                    </tr>

                                    <tr>

                                        <td value="169">MEERUT</td>



                                        <td>

                                            797 </td>

                                        <td>

                                            1.96 </td>

                                        <td>

                                            550445 </td>

                                        <td>

                                            2.86 </td>

                                    </tr>

                                    <tr>

                                        <td value="170">MIRZAPUR</td>



                                        <td>

                                            569 </td>

                                        <td>

                                            1.40 </td>

                                        <td>

                                            178559 </td>

                                        <td>

                                            0.93 </td>

                                    </tr>

                                    <tr>

                                        <td value="171">MORADABAD</td>



                                        <td>

                                            825 </td>

                                        <td>

                                            2.03 </td>

                                        <td>

                                            269042 </td>

                                        <td>

                                            1.40 </td>

                                    </tr>

                                    <tr>

                                        <td value="172">MUZAFFARNAGAR</td>



                                        <td>

                                            387 </td>

                                        <td>

                                            0.95 </td>

                                        <td>

                                            578402 </td>

                                        <td>

                                            3.01 </td>

                                    </tr>

                                    <tr>

                                        <td value="173">PILIBHIT</td>



                                        <td>

                                            376 </td>

                                        <td>

                                            0.92 </td>

                                        <td>

                                            569665 </td>

                                        <td>

                                            2.96 </td>

                                    </tr>

                                    <tr>

                                        <td value="174">PRATAPGARH</td>



                                        <td>

                                            482 </td>

                                        <td>

                                            1.18 </td>

                                        <td>

                                            192680 </td>

                                        <td>

                                            1.00 </td>

                                    </tr>

                                    <tr>

                                        <td value="120">PRAYAGRAJ</td>



                                        <td>

                                            1271 </td>

                                        <td>

                                            3.12 </td>

                                        <td>

                                            319230 </td>

                                        <td>

                                            1.66 </td>

                                    </tr>

                                    <tr>

                                        <td value="175">RAE BARELI</td>



                                        <td>

                                            778 </td>

                                        <td>

                                            1.91 </td>

                                        <td>

                                            233985 </td>

                                        <td>

                                            1.22 </td>

                                    </tr>

                                    <tr>

                                        <td value="176">RAMPUR</td>



                                        <td>

                                            397 </td>

                                        <td>

                                            0.97 </td>

                                        <td>

                                            380919 </td>

                                        <td>

                                            1.98 </td>

                                    </tr>

                                    <tr>

                                        <td value="177">SAHARANPUR</td>



                                        <td>

                                            768 </td>

                                        <td>

                                            1.89 </td>

                                        <td>

                                            643668 </td>

                                        <td>

                                            3.35 </td>

                                    </tr>

                                    <tr>

                                        <td value="659">SAMBHAL</td>



                                        <td>

                                            270 </td>

                                        <td>

                                            0.66 </td>

                                        <td>

                                            200994 </td>

                                        <td>

                                            1.05 </td>

                                    </tr>

                                    <tr>

                                        <td value="178">SANT KABEER NAGAR</td>



                                        <td>

                                            237 </td>

                                        <td>

                                            0.58 </td>

                                        <td>

                                            65004 </td>

                                        <td>

                                            0.34 </td>

                                    </tr>

                                    <tr>

                                        <td value="180">SHAHJAHANPUR</td>



                                        <td>

                                            572 </td>

                                        <td>

                                            1.40 </td>

                                        <td>

                                            519790 </td>

                                        <td>

                                            2.70 </td>

                                    </tr>

                                    <tr>

                                        <td value="660">SHAMLI</td>



                                        <td>

                                            171 </td>

                                        <td>

                                            0.42 </td>

                                        <td>

                                            273571 </td>

                                        <td>

                                            1.42 </td>

                                    </tr>

                                    <tr>

                                        <td value="181">SHRAVASTI</td>



                                        <td>

                                            239 </td>

                                        <td>

                                            0.59 </td>

                                        <td>

                                            52292 </td>

                                        <td>

                                            0.27 </td>

                                    </tr>

                                    <tr>

                                        <td value="182">SIDDHARTH NAGAR</td>



                                        <td>

                                            423 </td>

                                        <td>

                                            1.04 </td>

                                        <td>

                                            121115 </td>

                                        <td>

                                            0.63 </td>

                                    </tr>

                                    <tr>

                                        <td value="183">SITAPUR</td>



                                        <td>

                                            810 </td>

                                        <td>

                                            1.99 </td>

                                        <td>

                                            554174 </td>

                                        <td>

                                            2.88 </td>

                                    </tr>

                                    <tr>

                                        <td value="184">SONBHADRA</td>



                                        <td>

                                            194 </td>

                                        <td>

                                            0.48 </td>

                                        <td>

                                            113207 </td>

                                        <td>

                                            0.59 </td>

                                    </tr>

                                    <tr>

                                        <td value="185">SULTANPUR</td>



                                        <td>

                                            457 </td>

                                        <td>

                                            1.12 </td>

                                        <td>

                                            96228 </td>

                                        <td>

                                            0.50 </td>

                                    </tr>

                                    <tr>

                                        <td value="186">UNNAO</td>



                                        <td>

                                            711 </td>

                                        <td>

                                            1.75 </td>

                                        <td>

                                            86191 </td>

                                        <td>

                                            0.45 </td>

                                    </tr>

                                    <tr>

                                        <td value="187">VARANASI</td>



                                        <td>

                                            984 </td>

                                        <td>

                                            2.42 </td>

                                        <td>

                                            147357 </td>

                                        <td>

                                            0.77 </td>

                                    </tr>

                                </tbody>

                                <tfoot style="position: sticky; bottom: 0; background-color: #f9f9f9; z-index: 2;">

                                    <tr align="center">

                                        <td><strong>Total</strong></td>

                                        <td><b>40727</b></td>

                                        <td><b>100%</b></td>

                                        <td><b>19221079</b></td>

                                        <td><b>100%</b></td>

                                    </tr>

                                </tfoot>

                            </table>

                        </div>

                    </div>

                </div>

                <!-- Pie Chart -->

                <div class="col-md-5 right">



                    <h6 style="text-align: center; background-color:white; margin-bottom: 0px; padding-top:10px;"><b>Top

                            5 Districts in UTTAR PRADESH</b>

                    </h6>



                    <figure class="highcharts-figure">

                        <div id="districtPieChart"></div>

                    </figure>

                </div>

            </div>

            <br>

            <section class="sector_data_info">

                <div class="container">

                    <h2>Sector Wise Cooperatives In - UTTAR PRADESH</h2>

                    <div class="row">

                        <div class="col-md-6 left">

                            <div class="sector_table">

                                <div

                                    style="max-height: 430px; overflow-y: auto; position: relative; border: 1px solid #ccc;">

                                    <table id="sortstCooperTable" border="0" cellpadding="0" cellspacing="0"

                                        style="width: 100%; border-collapse: collapse;">

                                        <thead style="position: sticky; top: 0; background-color: #f9f9f9; z-index: 2;">

                                            <tr>

                                                <th style="width: 290px;"> Cooperative Sector Name </th>

                                                <th onclick="sortstSectorTable(1)"> No of Societies <i

                                                        class="fa fa-sort"></th>

                                                <th> % Societies </th>

                                            </tr>

                                        </thead>

                                        <tbody id="table-state-cooper">

                                            <tr class="odd">

                                                <td valign="top">DAIRY COOPERATIVE</td>

                                                <td valign="top">14496</td>

                                                <td>

                                                    35.61 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">PRIMARY AGRICULTURAL CREDIT SOCIETY (PACS)</td>

                                                <td valign="top">7871</td>

                                                <td>

                                                    19.34 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">AGRO PROCESSING / INDUSTRIAL COOPERATIVE</td>

                                                <td valign="top">3991</td>

                                                <td>

                                                    9.81 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">HOUSING COOPERATIVE SOCIETY</td>

                                                <td valign="top">3253</td>

                                                <td>

                                                    7.99 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">CREDIT &amp; THRIFT SOCIETY</td>

                                                <td valign="top">2246</td>

                                                <td>

                                                    5.52 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">HANDLOOM TEXTILE &amp; WEAVERS COOPERATIVE</td>

                                                <td valign="top">1819</td>

                                                <td>

                                                    4.47 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">MARKETING COOPERATIVE SOCIETY</td>

                                                <td valign="top">1796</td>

                                                <td>

                                                    4.41 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">FISHERY COOPERATIVE</td>

                                                <td valign="top">1468</td>

                                                <td>

                                                    3.61 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">AGRICULTURE &amp; ALLIED COOPERATIVE</td>

                                                <td valign="top">1311</td>

                                                <td>

                                                    3.22 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">CONSUMER COOPERATIVE</td>

                                                <td valign="top">1002</td>

                                                <td>

                                                    2.46 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">LABOUR COOPERATIVE</td>

                                                <td valign="top">551</td>

                                                <td>

                                                    1.35 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">FARMERS SERVICE SOCIETIES (FSS)</td>

                                                <td valign="top">236</td>

                                                <td>

                                                    0.58 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">MULTIPURPOSE COOPERATIVE</td>

                                                <td valign="top">187</td>

                                                <td>

                                                    0.46 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">TRANSPORT COOPERATIVE</td>

                                                <td valign="top">85</td>

                                                <td>

                                                    0.21 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">MISCELLANEOUS NON CREDIT</td>

                                                <td valign="top">70</td>

                                                <td>

                                                    0.17 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">URBAN COOPERATIVE BANK (UCB)</td>

                                                <td valign="top">55</td>

                                                <td>

                                                    0.14 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">LARGE AREA MULTIPURPOSE SOCIETY (LAMPS)</td>

                                                <td valign="top">49</td>

                                                <td>

                                                    0.12 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">HANDICRAFT COOPERATIVE</td>

                                                <td valign="top">49</td>

                                                <td>

                                                    0.12 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">LIVESTOCK &amp; POULTRY COOPERATIVE</td>

                                                <td valign="top">43</td>

                                                <td>

                                                    0.11 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">SERICULTURE COOPERATIVE</td>

                                                <td valign="top">43</td>

                                                <td>

                                                    0.11 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">SUGAR MILLS COOPERATIVE</td>

                                                <td valign="top">28</td>

                                                <td>

                                                    0.07 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">WOMEN WELFARE COOPERATIVE SOCIETY</td>

                                                <td valign="top">28</td>

                                                <td>

                                                    0.07 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">MISCELLANEOUS CREDIT COOPERATIVE SOCIETY</td>

                                                <td valign="top">6</td>

                                                <td>

                                                    0.01 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">EDUCATIONAL &amp; TRAINING COOPERATIVES</td>

                                                <td valign="top">6</td>

                                                <td>

                                                    0.01 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">BEE FARMING COOPERATIVE</td>

                                                <td valign="top">6</td>

                                                <td>

                                                    0.01 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">JUTE AND COIR COOPERATIVE</td>

                                                <td valign="top">3</td>

                                                <td>

                                                    0.01 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">SOCIAL WELFARE &amp; CULTURAL COOPERATIVE</td>

                                                <td valign="top">2</td>

                                                <td>

                                                    0.00 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">TOURISM COOPERATIVE</td>

                                                <td valign="top">1</td>

                                                <td>

                                                    0.00 </td>

                                            </tr>

                                            <tr class="odd">

                                                <td valign="top">TRIBAL-SC/ST COOPERATIVE</td>

                                                <td valign="top">1</td>

                                                <td>

                                                    0.00 </td>

                                            </tr>

                                        </tbody>

                                        <tfoot

                                            style="position: sticky; bottom: 0; background-color: #f9f9f9; z-index: 2;">

                                            <tr class="odd">

                                                <td valign="top"><b>Total</b></td>

                                                <td valign="top"><b>40702</b></td>

                                                <td valign="top"><b>100%</b></td>

                                            </tr>

                                            </tfood>

                                    </table>

                                </div>

                            </div>

                        </div>

                        <!-- Column Graphs -->

                        <div class="col-md-6 right">

                            <h6

                                style="text-align: center; background-color:white; margin-bottom: 0px; padding-top:10px;">

                                <b>Top 5 Sector In UTTAR PRADESH</b>

                            </h6>

                            <figure class="highcharts-figure">

                                <div id="sectorPieChart"></div>

                            </figure>

                        </div>

                    </div>

                </div>

            </section>

            <section class="sector_data_info">

                <div class="heading">

                    <!-- <h2>Sector Wise Cooperatives In UTTAR PRADESH</h2> -->

                    <h2>Sector-Wise Cooperatives in UTTAR PRADESH : District Map View </h2>

                </div>

                <div class="row">

                    <div class="col-md-12">



                        <div class="slick d-flex justify-content-center mt-50">

                            <div class="slick-wrapper">

                                <div id="slick1">

                                    <span onClick="show_map(1);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/1.png" alt="" />

                                            </div>

                                            <span>PACS</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(20);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/20.png" alt="" />

                                            </div>

                                            <span>FSS</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(22);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/22.png" alt="" />

                                            </div>

                                            <span>LAMPS</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(77);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/Agriculture.png" alt="" />

                                            </div>

                                            <span>Agriculture and Allied</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(80);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/non_credit_16.png" alt="" />

                                            </div>

                                            <span>Consumer</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(9);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/imag.png" alt="" />

                                            </div>

                                            <span>Dairy</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(31);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/e.png" alt="" />

                                            </div>

                                            <span>Agro Processing/Industrial</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(18);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/4.png" alt="" />

                                            </div>

                                            <span>Credit and Thrift Society</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(10);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/non_credit_2.png" alt="" />

                                            </div>

                                            <span>Fishery</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(79);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/bEe.png" alt="" />

                                            </div>

                                            <span>Bee Farming</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(84);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/non_credit_18.png" alt="" />

                                            </div>

                                            <span>Educational and Training</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(14);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/non_credit_5.png" alt="" />

                                            </div>

                                            <span>Handicraft</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(90);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/non_credit_19.png" alt="" />

                                            </div>

                                            <span>Jute and Coir</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(82);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/non_credit_17.png" alt="" />

                                            </div>

                                            <span>Marketing</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(13);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/non_credit_4.png" alt="" />

                                            </div>

                                            <span>Handloom and Textile</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(51);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/Labour.png" alt="" />

                                            </div>

                                            <span>Labour</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(29);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/d.png" alt="" />

                                            </div>

                                            <span>Miscellaneous</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(47);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/non_credit_10.png" alt="" />

                                            </div>

                                            <span>Housing</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(54);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/b.png" alt="" />

                                            </div>

                                            <span>Livestock and Poultry</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(35);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/c.png" alt="" />

                                            </div>

                                            <span>Miscellaneous Credit</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(16);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/non_credit_7.png" alt="" />

                                            </div>

                                            <span>Multipurpose</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(11);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/non_credit_3.png" alt="" />

                                            </div>

                                            <span>Sugar Mills</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(102);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/non_credit_23.png" alt="" />

                                            </div>

                                            <span>Tribal-SC/ST</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(96);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/non_credit_20.png" alt="" />

                                            </div>

                                            <span>Sericulture</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(99);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/ii.png" alt="" />

                                            </div>

                                            <span>Tourism</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(7);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/Untitled.png" alt="" />

                                            </div>

                                            <span>Urban Cooperative Bank (UCB)</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(98);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/non_credit_21.png" alt="" />

                                            </div>

                                            <span>Social welfare and Cultural</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(68);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/non_credit_13.png" alt="" />

                                            </div>

                                            <span>Transport</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(15);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/non_credit_6.png" alt="" />

                                            </div>

                                            <span>Women Welfare</span>

                                        </div>

                                    </span>

                                    <span onClick="show_map(117);">

                                        <div class="slide-item" data-aos="fade-up">

                                            <div class="inner_box">

                                                <img src="img/../frontend/img/non_credit_4.png" alt="" />

                                            </div>

                                            <span>Khadi Gramodyog</span>

                                        </div>

                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- <div class="col-md-6">

                        <div class="map_inner_loader">

                            <div id="stateMap"

                                style="height: 500px; min-width: 310px; max-width: 800px; margin: 0 auto"></div>

                            <!-- <div class="loadingA"><img src="frontend/img/loader-new.gif" alt="" /></div> -->

                        </div>

                    </div> -->

                </div>

            </section>

            <div class="clearfix"> </div>

            <!-- YearWise Chart -->

            <div class="col-md-12">

                <figure class="highcharts-figure" style="background-color:white;">

                    <h5 style="text-align: center; background-color:white; padding-bottom: 10px;padding-top: 10px;">

                        <b>Decennial Progress of Cooperatives in UTTAR PRADESH</b>

                    </h5>

                    <div id="yearwise"></div>

                </figure>

            </div>

        </div>

        </div>

    </section>

    <!-- Footer Section Start -->

    <footer>

        <div class="container">

            <div class="footer_main_menu">

                <div class="row">

                    <!-- 1st part -->

                    <div class="col-md-4 left same-side">

                        <h4>Contact Details</h4>

                        Shri J.P.S Rathore- Minister of Cooperation 0522-2238066, 2235687

                        sahkaritamantriup@gmail.com

                        <h4>Other Contact Details</h4>

                        Shri Yogesh Kumar Commissioner and Registrar 0522-2289267, 2289490

                        comm.coop.up@gmail.com

                    </div>



                    <!-- 2nd part -->

                    <div class="col-md-4 right same-side">

                        <h4>State Federations - UTTAR PRADESH </h4>

                        <h4>

                            <a href="#" class="ftr_popup" data-bs-toggle="modal" data-bs-target="#exampleModal">

                                List Of State Federations

                            </a>

                        </h4>

                    </div>



                    <!-- 3rd part Newsletter -->

                    <div class="col-md-4 mid same-side">

                        <div class="newsletter-box p-3 rounded shadow-sm">

                            <h4>📩 Newsletter</h4>

                            <p class="small">Get the latest updates directly in your inbox.</p>

                            <form>

                                <input type="email" class="form-control mb-2" placeholder="Your email address" required>

                                <button type="submit" class="btn btn-success w-100">Subscribe</button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        <div class="copyright" style="background:#236647">

            <div class="container">

                <div class="row">

                    <div class="col-md-6">

                        <p>UPCDC web portal is designed, developed and being managed by UPCDC, MoC, Govt of Uttar Pradesh.

                        </p>

                        <p>Contents are published by the UPCDC and RCS offices.</p>

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

                            <li> Visitors : 31508760</li>

                            <li>Last Updated : 16 August 2025 </li>



                        </ul>

                    </div>

                </div>

            </div>

        </div>

    </footer>



    <div class="social_sidebar">

        <div class="social facebook">

            <a href="https://www.facebook.com/" target="_blank">

                <img src="frontend/img/files/gallery/facebook1.png" alt="Facebook"> Follow us on Facebook</a>

        </div>



        <div class="social twitter">

            <a href="https://twitter.com/" target="_blank">

                <img class="twiter-t" src="frontend/img/files/gallery/twitter.png" alt="Twitter"> Follow us on Twitter</a>

        </div>

        <div class="social youtube">

            <a href="https://www.youtube.com/" target="_blank">

                <img src="frontend/img/youtube.svg" alt="YouTube"> Follow us on YouTube</a>

        </div>

        <div class="social instagram">

            <a href="https://www.instagram.com/" target="_blank">

                <img src="frontend/img/instagram.svg" alt="Instagram"> Follow us on Instagram</a>

        </div>

        <div class="social wa">

            <a href="https://whatsapp.com/" target="_blank">

                <img src="frontend/img/whatsapp.svg" alt="Whatsapp"> Follow us on Whatsapp</a>

        </div>

        <div class="social linkdd">

            <a href="https://www.linkedin.com/" target="_blank">

                <img src="frontend/img/linkedin.svg" alt="linkdin"> Follow us on Linkedin</a>

        </div>

    </div>









    <button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fas fa-arrow-up"

            style="color: #ffffff;"></i></button>





    <!-- Modal -->

    <div class="modal fade footer_popup" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"

        aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i

                            class="far fa-times-circle"></i></button>

                </div>

                <div class="modal-body">

                    <div class="sector_data_info">

                        <h4> List Of state Federations In (UTTAR PRADESH)</h4>

                        <table>

                            <tbody>

                                <tr>

                                    <th>Sr. No.</th>

                                    <th>State Federation Name</th>

                                </tr>

                                <tr>

                                <tr class="odd">

                                    <td valign="top">1</td>

                                    <td valign="top">SAHKARI SANGH LTD SHIVGARH</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">2</td>

                                    <td valign="top">Disrict cooperative fedration ltd Kanpur Nagar</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">3</td>

                                    <td valign="top">SAHKARI SANGH LTD JAMKHURI</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">4</td>

                                    <td valign="top">sekhari sangh lunited ghoredeeh</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">5</td>

                                    <td valign="top">sehkari sangh limited mauaima</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">6</td>

                                    <td valign="top">sehkari sangh limited sahson</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">7</td>

                                    <td valign="top">sehkari sangh limited hanumanganj</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">8</td>

                                    <td valign="top">sehkari sangh limited anapur</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">9</td>

                                    <td valign="top">sehkari sangh limited nawabganj</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">10</td>

                                    <td valign="top">sehkari sangh limited raiya</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">11</td>

                                    <td valign="top">SAHKARI SANGH LTD PIPALGAON KAURIHAR</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">12</td>

                                    <td valign="top">sehkari sangh limited shripur ismailganj</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">13</td>

                                    <td valign="top">sehkari sangh limited braut</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">14</td>

                                    <td valign="top">sehkari sangh limited mandwa</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">15</td>

                                    <td valign="top">sehkari sangh limited saidabad</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">16</td>

                                    <td valign="top">sehkari sangh limited chandopara</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">17</td>

                                    <td valign="top">sehkari sangh limited rokree</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">18</td>

                                    <td valign="top">SAHKAR SANGH LTD KARMA KAUNDHIYARA</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">19</td>

                                    <td valign="top">SAHKARI SANGH LTD BHARAT NAGAR</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">20</td>

                                    <td valign="top">SAHKARI SANGH LTD JASRA</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">21</td>

                                    <td valign="top">SAHAKRI SANGH LTD JARI</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">22</td>

                                    <td valign="top">MANDA SAHKARI SANGH LTD</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">23</td>

                                    <td valign="top">sehkari sangh limited badoakhar</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">24</td>

                                    <td valign="top">SAHKARI SANGH LTD RAMNAGAR</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">25</td>

                                    <td valign="top">SAHKARI SANGH LTD ISMAILGANJ</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">26</td>

                                    <td valign="top">sehkari sangh limited meja</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">27</td>

                                    <td valign="top">sehkari sangh limited koraon</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">28</td>

                                    <td valign="top">KANPUR THOK KENDRIYA UPBHOKTA SAHKARI BHANDAR LTD.</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">29</td>

                                    <td valign="top">SAHKARI SANGH LTD LEDIYARI</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">30</td>

                                    <td valign="top">sehkari sangh limited khodaypur</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">31</td>

                                    <td valign="top">sehkari sangh limited berna</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">32</td>

                                    <td valign="top">sahkari sangh kamlanagar</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">33</td>

                                    <td valign="top">sehkari sangh limited karnaipur</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">34</td>

                                    <td valign="top">sehkari sangh limited garapur</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">35</td>

                                    <td valign="top">sehkari sangh limited tharwai</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">36</td>

                                    <td valign="top">sehkari sangh limited handia</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">37</td>

                                    <td valign="top">sehkari sangh limited dhanupur</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">38</td>

                                    <td valign="top">sehkari sangh limited damgarha</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">39</td>

                                    <td valign="top">sehkari sangh limited baharia</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">40</td>

                                    <td valign="top">sehkari sangh limited pratappur</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">41</td>

                                    <td valign="top">sehkari sangh limited mailhan</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">42</td>

                                    <td valign="top">sehkari sangh limited karchhana</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">43</td>

                                    <td valign="top">sehkari sangh limited bheerpur</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">44</td>

                                    <td valign="top">sehkari sangh limited holagara</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">45</td>

                                    <td valign="top">U. P. Co-operative Cane Unions Federation Ltd., Lucknow</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">46</td>

                                    <td valign="top">Dugdh Utpadak Sahakari Sangh Ltd. Prayagraj</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">47</td>

                                    <td valign="top">GONDA DUGDH UTPADAK SAHKARI SANGH LIMITED-GONDA</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">48</td>

                                    <td valign="top">Dugdh Utpadak Sahakari Sangh Ltd. Varanasi</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">49</td>

                                    <td valign="top">GONDA DUGDH UTPADAK SAHKARI SANGH LIMITED-GONDA</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">50</td>

                                    <td valign="top">U.P. Cooperative Sugar Factories Federation Ltd, Lucknow</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">51</td>

                                    <td valign="top">SHAHJAHANPUR MAHILA DUGDH UTPADAK SAHKARI SANGH LTD SHAHJAHNAPUR

                                    </td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">52</td>

                                    <td valign="top">Dugdh utpadak sahakari sangh limited gorakhpur</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">53</td>

                                    <td valign="top">PRADESHIK CO-OPERATIVE DAIRY FEDERATION</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">54</td>

                                    <td valign="top">Lucknow Co-operative Dairy Milk Union.Ltd.</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">55</td>

                                    <td valign="top">Kanpur Dugdh Utpadak sahkari sangh Ltd</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">56</td>

                                    <td valign="top">dugdh utpadak sahkari sangh ltd azamgarh</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">57</td>

                                    <td valign="top">GANGOL SAHKARI DUGDH UTPADAK SANGH LTD</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">58</td>

                                    <td valign="top">DUGDH UTPADAK SAHKARI SANGH LTD MORADABAD</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">59</td>

                                    <td valign="top">DUSS, MUZAFFARNAGAR</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">60</td>

                                    <td valign="top">M/S UTTAR PRADESH HANDLOOM SILK MARKETING COOPERATIVE FEDERATION

                                        LIMITED</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">61</td>

                                    <td valign="top">M/S MBD DISTRICT WEAVERS CENTRAL COOPERATIVE STORE, WARD 11 BHOJPUR

                                        MORADABAD</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">62</td>

                                    <td valign="top">M/s UP Anjum Handloom Export Apex Federation Limited, Fareednagar

                                        Mordabad</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">63</td>

                                    <td valign="top">M/S DISTRICT CENTRAL COOPERATIVE BUNKAR TEXTILE UNION LIMITED</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">64</td>

                                    <td valign="top">Vasundhara Sahkari Awas Samiti Ltd. Meerut</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">65</td>

                                    <td valign="top">JILA SAHKARI MATSYA VIKAS AVAM VIPRAN FEDERATION LTD LKO</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">66</td>

                                    <td valign="top">ALIGARH DUGDH UTPADAK SAHKARI SANGH LIMITED ALIGARH</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">67</td>

                                    <td valign="top">dcdf meerut</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">68</td>

                                    <td valign="top">QAS TEST 3009</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">69</td>

                                    <td valign="top">Uttar Praesh Matsya Jivi Sahkari Sangh Ltd </td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">70</td>

                                    <td valign="top">UTTAR PRADESH COOPERATIVE BANK LIMITED LUCKNOW</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">71</td>

                                    <td valign="top">UTTAR PRADESH COOPERATIVE FEDERATION LIMITED</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">72</td>

                                    <td valign="top">UTTAR PRADESH COOPERATIVE UNION LIMITED</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">73</td>

                                    <td valign="top">UTTAR PRADESH RAJYA NIRMAAN AVM VIKAS SAHKARI SANGH LIMITED LUCKNOW

                                        LIMITED</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">74</td>

                                    <td valign="top">UTTAR PRADESH UPBHOKTA SAHKARI SANGH LIMITED LUCKNOW</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">75</td>

                                    <td valign="top">UTTAR PRADESH RAJYA NIRMAAN SAHKARI SANGH LIMITEDITED</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">76</td>

                                    <td valign="top">UTTAR PRADESH SAHKARI GRAM VIKAS BANK LIMITED LUCKNOW</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">77</td>

                                    <td valign="top">UTTAR PRADESH SAHKARI JUTE AVM KRISHI VIKAS SANGH LIMITED</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">78</td>

                                    <td valign="top">MASTSYA JIVI SAHAKARI SAMITI LIMITED SURSA</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">79</td>

                                    <td valign="top">मत्स्य जीवी सहकारी समिति लिमिटेड बहर</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">80</td>

                                    <td valign="top">MATSYA JIVI SAHKARI SAMITI LTD MADEPURA </td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">81</td>

                                    <td valign="top">मत्स्य जीवी सहकारी समिति लिमिटेड भुड़िया पोस्ट अटवा कटैया</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">82</td>

                                    <td valign="top">MATSYA JIVI SAHKARI SMITI LTD SHIVGANJ RAMPURA MADHOGRAH JALAUN

                                    </td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">83</td>

                                    <td valign="top">मत्स्य जीवी सहकारी समिति लि0, अटवा कटैया</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">84</td>

                                    <td valign="top">MATSYA JIVI SAHKARI SMITI LTD BHADEKH KUTHAUND JALAUN</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">85</td>

                                    <td valign="top">मत्स्य जीवी सहकारी समिति लिमिटेड धोंधी</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">86</td>

                                    <td valign="top">MATSYA JIVI SAHKARI SAMITI LTD KANJAUSA RAMPURA MADHOGARH JALAUN

                                    </td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">87</td>

                                    <td valign="top">मत्स्य जीवी सहकारी समिति लिमिटेड अठौआ</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">88</td>

                                    <td valign="top">mastya jivi sahakari samiti limited pariyar</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">89</td>

                                    <td valign="top">MATASYA JEEVI SAHKARI SAMITI LIMITED UGARPUR</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">90</td>

                                    <td valign="top">mastya jivi sahakari samiti limited pariyar</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">91</td>

                                    <td valign="top">MATASYA JEEVI SAHKARI SAMITI LIMITED GEVARA GUNDERA</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">92</td>

                                    <td valign="top">Mastya Jivi Sahakari Samiti Limited Patyoladasi</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">93</td>

                                    <td valign="top">MATSYA JIVI SAHAKARI SAMITI LIMITED SADARPUR</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">94</td>

                                    <td valign="top">MASTSYA JIVI SAHAKARI SAMITI LIMITED SURSA</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">95</td>

                                    <td valign="top">मत्स्य जीवी सहकारी समिति लिमिटेड बहर</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">96</td>

                                    <td valign="top">मत्स्य जीवी सहकारी समिति लिमिटेड भुड़िया पोस्ट अटवा कटैया</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">97</td>

                                    <td valign="top">मत्स्य जीवी सहकारी समिति लि0, अटवा कटैया</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">98</td>

                                    <td valign="top">मत्स्य जीवी सहकारी समिति लिमिटेड धोंधी</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">99</td>

                                    <td valign="top">मत्स्य जीवी सहकारी समिति लिमिटेड अठौआ</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">100</td>

                                    <td valign="top">मत्स्य जीवी सहकारी समिति लि0 थोक माधौ</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">101</td>

                                    <td valign="top">मत्स्य जीवी सहकारी समिति लि0 गर्रा नदी रामपुर मझियारा</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">102</td>

                                    <td valign="top">नवीन मछुआ विकास मत्स्य जीवी सहकारी समिति लि0 पाली</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">103</td>

                                    <td valign="top">मत्स्य जीवी सहकारी समिति लिमिटेड अरवल</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">104</td>

                                    <td valign="top">MATASYA JIVI SAHKARI SAMITI LIMITED CHHIBRAMAU</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">105</td>

                                    <td valign="top">मत्स्य जीवी सहकारी समिति लि0 अरवल रामगंगा</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">106</td>

                                    <td valign="top">मत्स्य जीवी सहकारी समिति लि0 अहिरावाँ बेरुआ</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">107</td>

                                    <td valign="top">मत्स्य जीवी सहकारी समिति लि0 गौहानी, खजोहना</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">108</td>

                                    <td valign="top">MATSYA JIVI SAHAKARI SAMITI LIMITED SADARPUR</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">109</td>

                                    <td valign="top">B-PACS BICHPURI</td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">110</td>

                                    <td valign="top">RESHAM KOYA UTPADAN SEWHA SAMITI </td>

                                </tr>

                                <tr class="odd">

                                    <td valign="top">111</td>

                                    <td valign="top">SARVA KALYAN KARI RESHAM SAMITI</td>

                                </tr>





                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>



    <link rel="stylesheet" href="select2/select2.min.css">

    <script src="frontend/jss/jquery_new2.min.js"></script>

    <script>

        $(document).ready(function () {

            var st_name = jQuery(".st_name").text() + " nodal officer of cooperative Societies address";

            var one = 'https://www.google.com/maps?q=';

            var two = '&output=embed';

            var url = one + st_name + two;

            jQuery(".nodal_address").attr('src', url);

            var st_name = jQuery(".st_name").text() + " Co-operative Societies Office";

            var one = 'https://www.google.com/maps?q=';

            var two = '&output=embed';

            var url = one + st_name + two;

            jQuery(".rcs_address").attr('src', url);

        });

    </script>

    <script src="frontend/jss/jquery_new1.min.js"></script>

    <script src="frontend/jss/bootstrap.bundle.min.js"></script>

    <!-- <script src="/frontend/jss/slick.js?v2022" type="text/javascript" charset="utf-8"></script>-->

    <script src="frontend/jss/owl.carousel.js"></script>

    <script src="frontend/jss/animation_aos.js"></script>

    <script src="frontend/jss/baguetteBox.min.js"></script>

    <script src="frontend/jss/global.js"></script>

    <script src="select2/select2.full.min.js"></script>







    <script>

        jQuery(document).ready(function ($) {

            jQuery(document).on('change', '#language-select-state', function () {

                var val = $(this).val();

                var currentURL = window.location.href; // Get the current URL

                // var baseURLHE = currentURL.substr(currentURL.indexOf('/cooperative/')+15); // Get the hindi and english value current 

                var baseURLHE = currentURL.substr(currentURL.indexOf('/') + 25);

                // Extract the base URL (http://localhost/cooperative/) from the current URL

                var baseURL = currentURL.split('/').slice(0, 3).join('/') + '/';

                // Construct the new URL based on the selected value

                // alert(baseURL);

                var newURLEH = baseURL + val + '/' + baseURLHE;// You can modify this pattern as needed

                console.log(newURLEH); //exit;

                //Redirect the user to the new URL

                // window.location.href = newURLEH;

                window.location.href = newURLEH;

                // Add your language change logic here

            })

        });

    </script>

    <script>

        $(document).ready(function () {



            $('.go_btn_district').click(function () {



                // $('#changetheurl_district_wise').on('change', function(event) {

                event.preventDefault();

                // var district_code = $(this).val();

                var district_code = $('#changetheurl_district_wise').val();

                if (district_code != '') {

                    window.location.href = '/en/district-dashboard/district' + '/' + district_code;

                }

            })



            //when change in state name

            $('.go_btn_state').click(function () {

                event.preventDefault()

                var state_code = $('#change_state_code').val();

                if (state_code != '') {

                    window.location.href = '/en/state-dashboard/state' + '/' + state_code;



                }

            });



        });

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



    </script>





    <script>

        baguetteBox.run('.tz-gallery');

    </script>





    <script>

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

                loop: true,

                nav: true,

                dots: false,

                margin: 10,

                autoplayTimeout: 3000,

                autoplay: true,

                responsive: {

                    300: {

                        items: 1,

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

                        items: 5,

                    },

                },

            });

        });

    </script>





    <script>

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



        function show_map_old(map_id) {

            var aa = map_id;

            $(".loadingA").show();

            $("#map_pacs").html('');

            $("#map_pacs").removeClass();

            $("#map_pacs").addClass('maps-height-box ss' + aa);

            $.ajax({

                type: 'GET',

                async: false,

                cache: false,

                url: '/en/state-dashboard/getmap',

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

                url: '/en/state-dashboard/gettable',

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

                url: '/en/state-dashboard/getchart',

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





    <!-- Increase/descrease font size -->

    <script type="text/javascript">

        $('#increasetext').click(function () {

            curSize = parseInt($('#increasetext_body').css('font-size')) + 2;

            if (curSize <= 22)

                $('#increasetext_body').css('font-size', curSize);

        });



        $('#resettext').click(function () {

            if (curSize != 18)

                $('#increasetext_body').css('font-size', 18);

        });



        $('#decreasetext').click(function () {

            curSize = parseInt($('#increasetext_body').css('font-size')) - 2;

            if (curSize >= 14)

                $('#increasetext_body').css('font-size', curSize);

        });

    </script>





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



    <script src="js/stateDashboard/highcharts-3d.js"></script>

    <script src="js/stateDashboard/exporting.js"></script>

    <script src="js/stateDashboard/export-data.js"></script>

    <script src="js/stateDashboard/accessibility.js"></script>

    <script src="js/stateDashboard/data.js"></script>

    <script src="js/stateDashboard/drilldown.js"></script>



    <!-- Highcharts  -->

    <script>



        Highcharts.mapChart('container', {

            chart: {

                map: 'custom/uttarpradesh'

            },

            title: {

                text: 'Uttarpradesh State Map with District Names'

            },

            subtitle: {

                text: 'District names are displayed on the map'

            },

            mapNavigation: {

                enabled: true,

                buttonOptions: {

                    verticalAlign: 'bottom'

                }

            },

            colorAxis: {

                min: 0,

                minColor: '#FFFFFF',

                maxColor: Highcharts.getOptions().colors[0]

            },

            series: [{

                name: 'uttarpradesh Districts',

                borderColor: '#A0A0A0', // Border color for districts

                //  nullColor: 'rgba(200, 200, 200, 0.3)', // Color for areas with no data

                //  nullColor: 'green',



                showInLegend: false, // Do not show in legend

                dataLabels: {

                    enabled: true,

                    useHTML: true, // important: allows proper HTML

                    format: '<b>{point.properties.district}</b><br/>({point.sector})',

                    style: {

                        fontSize: '10px',

                    }

                },

                tooltip: {

                    headerFormat: '',

                    pointFormat: '<b>{point.properties.district}</b><br/>({point.sector})'

                },



                mapData: 'custom/uttarpradesh',

                joinBy: 'dt_code', // Key to join the data to the map

                keys: ['dt_code', 'name', 'value', 'sector', 'color'],

                states: {

                    hover: {

                        color: '#BADA55' // Color when hovering over a district

                    }

                },

                // Define district areas here

                data: [

                    // Example data format: ['hc-key', 'District Name', value]

                    ["185", "SULTANPUR", "1", "457", "#9dbd85"], ["137", "DEORIA", "1", "370", "#debe65"], ["166", "MAINPURI", "1", "253", "#dfddf1"], ["173", "PILIBHIT", "1", "376", "#b3e4f4"], ["118", "AGRA", "1", "1324", "#a1c440"], ["177", "SAHARANPUR", "1", "768", "#db9059"], ["119", "ALIGARH", "1", "494", "#e4fbcc"], ["143", "FIROZABAD", "1", "310", "#f9e638"], ["161", "LALITPUR", "1", "331", "#c8fc88"], ["169", "MEERUT", "1", "797", "#f1bfa8"], ["126", "BALLIA", "1", "405", "#dbc91a"], ["159", "KHERI", "1", "565", "#ebe9d0"], ["183", "SITAPUR", "1", "810", "#c8d64a"], ["136", "CHITRAKOOT", "1", "290", "#dbcf32"], ["133", "BUDAUN", "1", "502", "#cb8528"], ["187", "VARANASI", "1", "984", "#9f9fa6"], ["182", "SIDDHARTH NAGAR", "1", "423", "#f68859"], ["146", "GHAZIPUR", "1", "848", "#b3e6f4"], ["164", "MAHARAJGANJ", "1", "360", "#dedbf5"], ["178", "SANT KABEER NAGAR", "1", "237", "#e4fbcc"], ["155", "KANNAUJ", "1", "377", "#a6cc45"], ["135", "CHANDAULI", "1", "497", "#360008"], ["131", "BASTI", "1", "479", "#edbaa3"], ["148", "GORAKHPUR", "1", "766", "#d5b358"], ["661", "HAPUR", "1", "276", "#f4eedb"], ["172", "MUZAFFARNAGAR", "1", "387", "#833381"], ["167", "MATHURA", "1", "458", "#29574c"], ["142", "FATEHPUR", "1", "708", "#75c676"], ["147", "GONDA", "1", "711", "#b8571f"], ["144", "GAUTAM BUDDHA NAGAR", "1", "558", "#d6675f"], ["138", "ETAH", "1", "372", "#6a663b"], ["125", "BAHRAICH", "1", "543", "#dc1b15"], ["180", "SHAHJAHANPUR", "1", "572", "#5103d5"], ["633", "Kasganj", "1", "149", "#e0c155"], ["121", "AMBEDKAR NAGAR", "1", "616", "#685337"], ["124", "BAGHPAT", "1", "290", "#25b658"], ["179", "BHADOHI", "1", "189", "#032a03"], ["186", "UNNAO", "1", "711", "#b1686f"], ["165", "MAHOBA", "1", "214", "#a32937"], ["151", "JALAUN", "1", "587", "#595c58"], ["156", "KANPUR DEHAT", "1", "492", "#04f2df"], ["659", "SAMBHAL", "1", "270", "#713321"], ["162", "LUCKNOW", "1", "1522", "#beeb34"], ["149", "HAMIRPUR", "1", "309", "#3e0939"], ["128", "BANDA", "1", "356", "#e6c338"], ["640", "Amethi", "1", "376", "#1cc03e"], ["171", "MORADABAD", "1", "825", "#7d8d0b"], ["157", "KANPUR NAGAR", "1", "1086", "#272a28"], ["181", "SHRAVASTI", "1", "239", "#a2b805"], ["175", "RAE BARELI", "1", "778", "#7e2224"], ["132", "BIJNOR", "1", "812", "#eef2f3"], ["153", "JHANSI", "1", "630", "#840784"], ["145", "GHAZIABAD", "1", "707", "#240000"], ["123", "AZAMGARH", "1", "964", "#235386"], ["134", "BULANDSHAHR", "1", "840", "#d6e538"], ["152", "JAUNPUR", "1", "683", "#e85817"], ["154", "AMROHA", "1", "345", "#17561a"], ["139", "ETAWAH", "1", "537", "#ae750f"], ["130", "BAREILLY", "1", "828", "#27db24"], ["158", "KAUSHAMBI", "1", "445", "#595c59"], ["140", "AYODHYA", "1", "554", "#7e5c0f"], ["163", "HATHRAS", "1", "202", "#551d38"], ["168", "MAU", "1", "430", "#c97d60"], ["122", "AURAIYA", "1", "446", "#f8dc21"], ["127", "BALRAMPUR", "1", "192", "#5b1b53"], ["660", "SHAMLI", "1", "171", "#eff2f7"], ["120", "PRAYAGRAJ", "1", "1271", "#b9f00e"], ["129", "BARABANKI", "1", "803", "#92483f"], ["160", "KUSHI NAGAR", "1", "331", "#7e0a58"], ["170", "MIRZAPUR", "1", "569", "#b15bb3"], ["176", "RAMPUR", "1", "397", "#78933f"], ["174", "PRATAPGARH", "1", "482", ""], ["141", "FARRUKHABAD", "1", "519", ""], ["150", "HARDOI", "1", "758", ""], ["184", "SONBHADRA", "1", "194", ""],],



            }]

        });



        Highcharts.chart('districtPieChart', {

            chart: {

                plotBackgroundColor: null,

                plotBackgroundColor: null,

                plotBorderWidth: null,

                plotShadow: false,

                type: 'pie',

                options3d: {

                    enabled: true,

                    alpha: 45,

                    beta: 5

                },

            },



            title: {



                text: '<br> Total Societies - 40727',

                align: 'center'

            },



            tooltip: {

                pointFormat: '{series.name}: <b>{point.y}</b>(<b>{point.p}%)</b>'

            },

            accessibility: {

                point: {

                    valueSuffix: '%'

                }

            },

            plotOptions: {

                pie: {

                    allowPointSelect: true,

                    cursor: 'pointer',

                    depth: 35,

                        dataLabels: {

                            enabled: true,

                            useHTML: true, // important if you want HTML line breaks

                            format: '{point.name}<br/>: {point.y}<br/>({point.p}%)',

                            style: {

                                fontSize: '12px',

                                fontFamily: 'Verdana, sans-serif',

                                fontWeight: 'normal'

                            }

                        }

                }

            },

            series: [{

                name: '',

                colorByPoint: true,

                data: [



                    {

                        name: 'LUCKNOW',

                        y: 1522,

                        p: 3.74,



                    },



                    {

                        name: 'AGRA',

                        y: 1324,

                        p: 3.25,



                    },



                    {

                        name: 'PRAYAGRAJ',

                        y: 1271,

                        p: 3.12,



                    },



                    {

                        name: 'KANPUR NAGAR',

                        y: 1086,

                        p: 2.67,



                    },



                    {

                        name: 'VARANASI',

                        y: 984,

                        p: 2.42,



                    },



                    {

                        name: 'OTHER',

                        y: 34540,

                        p: 84.81,

                        // sliced: true,

                        //selected: true



                    },

                ]

            }]

        });



        // Top 5 Sector Map

        Highcharts.chart('sectorPieChart', {

            chart: {

                plotBackgroundColor: null,

                plotBorderWidth: null,

                plotShadow: false,

                type: 'pie',

                options3d: {

                    enabled: true,

                    alpha: 45,

                    beta: 5

                }

            },

            title: {

                text: '<br> Total Societies - 40702',

                align: 'center'

            },

            tooltip: {

                pointFormat: '{series.name}: <b>{point.y}</b>(<b>{point.p}%)</b>'

            },

            accessibility: {

                point: {

                    valueSuffix: '%'

                }

            },

            plotOptions: {

                pie: {

                    allowPointSelect: true,

                    cursor: 'pointer',

                    depth: 35,

                    dataLabels: {

                        enabled: true,

                        style: {

                            fontSize: '12px',

                            fontFamily: 'Verdana, sans-serif',

                            fontWeight: 'normal'

                        },

                        format: '{point.name}<br/>{point.value}' // use value for maps

                    }

                }

            },

            series: [{

                name: '',

                colorByPoint: true,

                data: [



                    {

                        name: 'DAIRY COOPERATIVE',

                        y: 14496,

                        p: 35.61,



                    },



                    {

                        name: 'PRIMARY AGRICULTURAL CREDIT SOCIETY (PACS)',

                        y: 7871,

                        p: 19.34,



                    },



                    {

                        name: 'AGRO PROCESSING / INDUSTRIAL COOPERATIVE',

                        y: 3991,

                        p: 9.81,



                    },



                    {

                        name: 'HOUSING COOPERATIVE SOCIETY',

                        y: 3253,

                        p: 7.99,



                    },



                    {

                        name: 'CREDIT & THRIFT SOCIETY',

                        y: 2246,

                        p: 5.52,



                    },



                    {

                        name: 'Others',

                        y: 8845,

                        p: 21.73

                    }

                ]

            }]

        });



        // Location Urban Rural Chart

        Highcharts.chart('location_graph', {

            chart: {

                type: 'column'

            },

            title: {

                align: 'left',

                text: 'Primary Cooperative Database'

            },

            accessibility: {

                announceNewData: {

                    enabled: true

                }

            },

            xAxis: {

                type: 'category',

                title: {

                    text: '<b>Location Wise</b>'

                }

            },

            yAxis: {

                title: {

                    text: '<b>No. of Societies</b>'

                }

            },

            legend: {

                enabled: false

            },

            plotOptions: {

                series: {

                    borderWidth: 0,

                    dataLabels: {

                        enabled: true,

                        format: '{point.y}'

                    }

                }

            },

            tooltip: {

                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',

                pointFormat: '<span style="color:{point.color}">{point.name}</span>: ' +

                    '<b>{point.y}</b><br/>'

            },

            series: [

                {

                    name: 'Location',

                    colorByPoint: true,

                    data: [{ "name": "Urban", "y": 8606 }, { "name": "Rural", "y": 31625 }, { "name": "Both", "y": 496 }]

                }

            ]

        });

        // Functionality Chart

        Highcharts.chart('functionality_graph', {

            chart: {

                type: 'column'

            },

            title: {

                align: 'left',

                text: 'Location Cooperative Data'

            },

            accessibility: {

                announceNewData: {

                    enabled: true

                }

            },

            xAxis: {

                type: 'category',

                title: {

                    text: '<b>Functional Status</b>'

                }

            },

            yAxis: {

                title: {

                    text: '<b>No. of Societies</b>'

                }

            },

            legend: {

                enabled: false

            },

            plotOptions: {

                series: {

                    borderWidth: 0,

                    dataLabels: {

                        enabled: true,

                        format: '{point.y}'

                    }

                }

            },

            tooltip: {

                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',

                pointFormat: '<span style="color:{point.color}">{point.name}</span>: ' +

                    '<b>{point.y}</b><br/>'

            },

            series: [

                {

                    name: 'Status',

                    colorByPoint: true,

                    data: [{ "name": "Functional", "y": 20872 }, { "name": "Non Functional", "y": 19044 }, { "name": "Under Liquidation", "y": 811 }]

                }

            ]

        });

        // Year Wise Chart

        Highcharts.chart('yearwise', {

            chart: {

                zooming: {

                    type: 'xy'

                }

            },

            title: {

                text: 'Year Wise Societies',

                align: 'center'

            },

            xAxis: [{

                categories: [

                    '(1900-1923)', '(1924-1947)', '(1948-1950)', '(1951-1970)', '(1971-1990)', '(1991-2010)',

                    '(2011-2025)'

                ],

                crosshair: true,

                title: {

                    text: '<b>Year Wise Societies Progress</b>'

                }

            }],

            yAxis: {

                labels: {

                    format: '{value}',

                    style: {

                        color: Highcharts.getOptions().colors[1]

                    }

                },

                title: {

                    text: '<b>Number of Societies</b>',

                    style: {

                        color: Highcharts.getOptions().colors[1]

                    }

                }

            },

            tooltip: {

                shared: true

            },

            legend: {

                align: 'left',

                x: 80,

                verticalAlign: 'top',

                y: 60,

                floating: true,

                backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || 'rgba(255,255,255,0.25)'

            },

            series: [

                {

                    name: 'Count Of Societies ',

                    type: 'column',

                    data: [{ "y": 86, "color": "#000033" }, { "y": 509, "color": "#c0ecc6" }, { "y": 934, "color": "#ffac8d" }, { "y": 5441, "color": "#ff848d" }, { "y": 11481, "color": "#20c997" }, { "y": 14036, "color": "#198754" }, { "y": 8240, "color": "#ffc107" }],

                    dataLabels: {

                        enabled: true,

                        format: '{y}',

                        style: {

                            fontWeight: 'bold',

                            color: 'black'

                        }

                    }

                },

                {

                    name: 'Cumulative Count ',

                    type: 'spline',

                    data: [

                        86,

                        595,

                        1529,

                        6970,

                        18451,

                        32487,

                        40727],

                    dataLabels: {

                        enabled: false,

                        format: '{y}',

                        style: {

                            fontWeight: 'bold',

                            color: 'blue'

                        }

                    },

                    marker: {

                        enabled: true,

                        symbol: 'circle',

                        radius: 4

                    }

                }

            ]

        });



        function show_map(map_id) {

            var aa = map_id;

            $(".loadingA").show();

            $("#stateMap").html('');

            $("#stateMap").removeClass();

            $("#stateMap").addClass('maps-height-box ss' + aa);

            $.ajax({

                type: 'GET',

                async: false,

                cache: false,

                url: '/en/state-dashboard/getmap',

                beforeSend: function (xhr) {

                    xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]')

                        .val());

                },

                data: {

                    primary_activity: aa,

                    state_code: 9,

                },

                success: function (response) {

                    //alert(response);

                    $("#stateMap").html(response);

                    $(".loadingA").hide();

                },

            });

            // show_table(aa);

            // show_chart(aa);

            var ss = $('#stateMap').attr('aria-label');

            var myNewStr = ss.replace(/. Highcharts interactive chart./g, '');

            var myNewStr1 = myNewStr.replace(/.Total./g, ' <em>Total') + '</em>';

            $('#show_map_heasding').html(myNewStr1);

        }

    </script>

    <script>

        let sortDirection = true; // true for ascending, false for descending

        function sortStateTable(columnIndex) {

            const table = document.getElementById("sortstSoctyTable");

            const rows = Array.from(table.rows).slice(1, -1); // Skip header and footer rows

            const isNumericColumn = columnIndex === 1 || columnIndex === 3; // Columns for count and members are numeric



            rows.sort(function (rowA, rowB) {

                const cellA = rowA.cells[columnIndex].textContent.trim();

                const cellB = rowB.cells[columnIndex].textContent.trim();



                let valueA = isNumericColumn ? parseFloat(cellA.replace('%', '').trim()) : cellA;

                let valueB = isNumericColumn ? parseFloat(cellB.replace('%', '').trim()) : cellB;



                if (sortDirection) {

                    return valueA > valueB ? 1 : valueA < valueB ? -1 : 0;

                } else {

                    return valueA < valueB ? 1 : valueA > valueB ? -1 : 0;

                }

            });



            // Re-append the sorted rows

            const tbody = table.querySelector('tbody');

            rows.forEach(row => tbody.appendChild(row));



            // Toggle sort direction

            sortDirection = !sortDirection;

        }



        function sortstSectorTable(columnIndex) {

            const table = document.getElementById("sortstCooperTable");

            const rows = Array.from(table.rows).slice(1, -1); // Skip header and footer rows

            const isNumericColumn = columnIndex === 1 || columnIndex === 3; // Columns for count and members are numeric



            rows.sort(function (rowA, rowB) {

                const cellA = rowA.cells[columnIndex].textContent.trim();

                const cellB = rowB.cells[columnIndex].textContent.trim();



                let valueA = isNumericColumn ? parseFloat(cellA.replace('%', '').trim()) : cellA;

                let valueB = isNumericColumn ? parseFloat(cellB.replace('%', '').trim()) : cellB;



                if (sortDirection) {

                    return valueA > valueB ? 1 : valueA < valueB ? -1 : 0;

                } else {

                    return valueA < valueB ? 1 : valueA > valueB ? -1 : 0;

                }

            });



            // Re-append the sorted rows

            const tbody = table.querySelector('tbody');

            rows.forEach(row => tbody.appendChild(row));



            // Toggle sort direction

            sortDirection = !sortDirection;

        }



        function toggleContent() {

            var trimmedContent = document.getElementById('trimmed-content');

            var fullContent = document.getElementById('full-content');

            var toggleLink = document.getElementById('toggle-link');



            if (fullContent.style.display === 'none' || fullContent.style.display === '') {

                trimmedContent.style.display = 'none';

                fullContent.style.display = 'block';

                toggleLink.innerHTML = '<i class="fas fa-caret-down"></i> Show Less';

            } else {

                trimmedContent.style.display = 'block';

                fullContent.style.display = 'none';

                toggleLink.innerHTML = '<i class="fas fa-caret-right"></i> Know More';

            }

        }





    </script>

</body>



</html>



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



<script>

    // toggleContent function (already working for Know More)

    function toggleContent() {

        var trimmed = document.getElementById("trimmed-content");

        var full = document.getElementById("full-content");

        var link = document.getElementById("toggle-link");



        if (full.style.display === "none") {

            full.style.display = "block";

            trimmed.style.display = "none";

            link.innerHTML = '<i class="fas fa-caret-up"></i> Show Less';

        } else {

            full.style.display = "none";

            trimmed.style.display = "block";

            link.innerHTML = '<i class="fas fa-caret-right"></i> Know More';

        }

    }



    // When About Us in menu is clicked → trigger toggleContent

    document.getElementById("about-link").addEventListener("click", function (e) {

        e.preventDefault(); // prevent navigation

        toggleContent();    // expand content same as "Know More"

        document.getElementById("about-content").scrollIntoView({ behavior: "smooth" });

    });

</script>

