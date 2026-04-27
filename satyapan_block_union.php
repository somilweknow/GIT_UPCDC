<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <title>सहकारी संघों का विवरण</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }
        .section-title {
            background: #4e8edb;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        #mapBox {
            display: none;
            margin-top: 10px;
        }
        #mapFrame {
            width: 100%;
            height: 250px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>

<div class="container mt-4">

    <h4 class="text-center mb-4 fw-bold text-primary">
        सहकारी संघों (ब्लॉक यूनियन) का जनपदवार विवरण
    </h4>

    <!-- District -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">जनपद</label>
                    <select class="form-select">
                        <option>--चुनें--</option>
                        <option selected>AGRA</option>
                        <option>LUCKNOW</option>
                        <option>KANPUR</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">मंडल</label>
                    <select class="form-select">
                        <option>--चुनें--</option>
                        <option selected>AGRA</option>
                        <option>LUCKNOW</option>
                        <option>KANPUR</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="card">
        <div class="card-body">

            <div class="section-title">🏠 खाली भूमि का विवरण</div>

            <form>

                <!-- Location Row -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Latitude</label>
                        <input type="text" id="latitude" class="form-control" readonly>
                    </div>

                    <div class="col-md-4">
                        <label>Longitude</label>
                        <input type="text" id="longitude" class="form-control" readonly>
                    </div>

                    <!-- <div class="col-md-4 d-flex align-items-end">
                        <button type="button" class="btn btn-primary w-100" onclick="getLocation()">
                            📍 लोकेशन रिफ्रेश करें
                        </button>
                    </div>
                </div> -->

                <!-- Map -->
                <div id="mapBox">
                    <iframe id="mapFrame"></iframe>
                </div>

                <!-- Row 2 -->
                

                    <div class="col-md-3">
                        <label>भूमि क्षेत्रफल (हे.)</label>
                        <input type="text" class="form-control">
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>भूमि की स्थिति</label>
                        <select class="form-select">
                            <option>--चुनें--</option>
                            <option>खाली</option>
                            <option>उपयोग में</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>समिति भवन का स्वामित्व</label>
                        <select class="form-select">
                            <option>--चुनें--</option>
                            <option>स्वयं</option>
                            <option>किराये का</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>स्थान</label>
                        <select class="form-select">
                            <option>--चुनें--</option>
                            <option>ग्रामीण</option>
                            <option>शहरी</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>गोदाम उपयुक्त?</label>
                        <select class="form-select">
                            <option>--चुनें--</option>
                            <option>हाँ</option>
                            <option>नहीं</option>
                        </select>
                    </div>
                </div>

                <!-- Row 4 -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>रेल दूरी (कि.मी.)</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>पहुंच मार्ग</label>
                        <select class="form-select">
                            <option>--चुनें--</option>
                            <option>पक्का</option>
                            <option>कच्चा</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>कब्जा / विवादित?</label>
                        <select class="form-select">
                            <option>--चुनें--</option>
                            <option>हाँ</option>
                            <option>नहीं</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>राजस्व अभिलेख में दर्ज?</label>
                        <select class="form-select">
                            <option>--चुनें--</option>
                            <option>हाँ</option>
                            <option>नहीं</option>
                        </select>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-end gap-3">
                    <button type="button" class="btn btn-success">नई पंक्ति जोड़े [+]</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>

                <div class="col-md-3">
                        <label>💼 सत्यापन</label>
    <div class="card-body">
        <div class="row g-3">

            <div>
                <label class="form-label">सत्यापन कर रहे व्यक्ति का नाम</label>
                <input type="text" class="form-control" placeholder="नाम दर्ज करें">
            </div>

            <div>
                <label class="form-label">मोबाइल नंबर</label>
                <input type="text" class="form-control" placeholder="मोबाइल नंबर">
            </div>

            <div >
                <label class="form-label">पदनाम</label>
                <input type="text" class="form-control" placeholder="पदनाम">
            </div>

            <div >
                <label class="form-label">फिजिबिलिटी रिपोर्ट (PDF)</label>
                <input type="file" class="form-control" accept="application/pdf">
            </div>

            <div>
                <label class="form-label">लेआउट प्लान (PDF)</label>
                <input type="file" class="form-control" accept="application/pdf">
            </div>

        </div>
    </div>
</div>

            <!-- Other Info -->
            <div class="section">
            <h3>📝 भूमि के आधार पर व्यवसायक सुझाव </h3>
            <div class="section-body">
                <label>अन्य विवरण</label>
                <textarea rows="4"></textarea>
            </div>
            </div>

                        </form>

                    </div>
                </div>

            </div>

<!-- JS -->
<!-- <script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                let lat = position.coords.latitude.toFixed(6);
                let lng = position.coords.longitude.toFixed(6);

                document.getElementById("latitude").value = lat;
                document.getElementById("longitude").value = lng;

                document.getElementById("mapFrame").src =
                    "https://www.google.com/maps?q=" + lat + "," + lng + "&output=embed";

                document.getElementById("mapBox").style.display = "block";
            },
            function() {
                alert("❌ Location permission allow karo");
            }
        );
    } else {
        alert("❌ Browser geolocation support nahi karta");
    }
}
</script> -->

</body>
</html>
