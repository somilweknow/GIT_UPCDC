<!DOCTYPE html>
<html lang="hi">
<head>
<meta charset="UTF-8">
<title>जिला सहकारी संघों से संबंधित सूचना</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{background:#f4f6f9}
.section-title{
    background:#4e8edb;
    color:#fff;
    padding:8px 12px;
    border-radius:4px;
    font-weight:600;
    margin-bottom:15px;
}
#map{
    width:100%;
    height:260px;
    border-radius:6px;
}
</style>
</head>

<body>
<div class="container my-4">

<h5 class="text-center text-primary fw-bold mb-4">
जिला सहकारी संघों से संबंधित सूचना
</h5>

<!-- ================= LOCATION ================= -->
<div class="card mb-3">
<div class="card-body">
<!-- <div class="section-title">📍 लोकेशन</div>

<div class="row">
    <div class="col-md-3">
        <label>Latitude</label>
        <input type="text" id="lat" class="form-control" readonly>
    </div>

    <div class="col-md-3">
        <label>Longitude</label>
        <input type="text" id="lng" class="form-control" readonly>
    </div>

    <div class="col-md-3 d-flex align-items-end">
        <button type="button" class="btn btn-primary w-100" onclick="getLocation()">
            लोकेशन रिफ्रेश करें
        </button>
    </div>
</div> -->

<!-- MAP -->
<div class="mt-3" id="mapBox" style="display:none;">
    <iframe
        id="mapFrame"
        width="100%"
        height="260"
        style="border:0;border-radius:6px;"
        loading="lazy"
        allowfullscreen>
    </iframe>
</div>
</div>
</div>

<!-- ================= BASIC INFO ================= -->
<div class="card mb-3">
<div class="card-body">
<div class="section-title">📄 मूलभूत जानकारी</div>

<div class="row">
<div class="col-md-4">
<label>मंडल</label>
<select class="form-select">
<option>AGRA</option>
</select>
</div>

<div class="col-md-4">
<label>जनपद</label>
<select class="form-select">
<option>AGRA</option>
</select>
</div>

<div class="col-md-4">
<label>क्या समिति सक्रिय है</label>
<select class="form-select">
<option>--चुनें--</option>
<option>हाँ</option>
<option>नहीं</option>
</select>
</div>
</div>
</div>
</div>

<!-- ================= LAND DETAILS ================= -->
<div class="card mb-3">
<div class="card-body">
<div class="section-title">🏠 खाली भूमि का विवरण</div>

<div class="row mb-2">
<div class="col-md-4">
<label>भूमि का क्षेत्रफल (हे.)</label>
<input class="form-control">
</div>
<div class="col-md-4">
<label>भूमि की स्थिति</label>
<select class="form-select">
<option>--select--</option>
<option>खाली</option>
<option>उपयोग में</option>
</select>
</div>
<div class="col-md-4">
<label>स्थान (ग्रामीण/शहरी)</label>
<select class="form-select">
<option>--select--</option>
<option>ग्रामीण</option>
<option>शहरी</option>
</select>
</div>
</div>

<div class="row mb-2">
<div class="col-md-4">
<label>समिति भवन का स्वामित्व</label>
<select class="form-select">
<option>समिति के पास</option>
<option>किराये पर</option>
</select>
</div>
<div class="col-md-4">
<label>गोदाम हेतु उपयुक्त?</label>
<select class="form-select">
<option>--select--</option>
<option>हाँ</option>
<option>नहीं</option>
</select>
</div>
<div class="col-md-4">
<label>रेलवे स्टेशन दूरी (KM)</label>
<input class="form-control">
</div>
</div>

<div class="row">
<div class="col-md-4">
<label>पहुंच मार्ग</label>
<select class="form-select">
<option>पक्का</option>
<option>कच्चा</option>
</select>
</div>
<div class="col-md-4">
<label>कब्जा / विवादित</label>
<select class="form-select">
<option>--चुनें--</option>
<option>हाँ</option>
<option>नहीं</option>
</select>
</div>
<div class="col-md-4">
<label>राजस्व अभिलेख में दर्ज?</label>
<select class="form-select">
<option>--चुनें--</option>
<option>हाँ</option>
<option>नहीं</option>
</select>
</div>
</div>

</div>
</div>

<!-- ================= BUSINESS ================= -->
<div class="card mb-3">
<div class="card-body">
<div class="section-title">💼 सत्यापन</div>

<div class="row">
<div class="col-md-4">
<label>सत्यापन कर रहे व्यक्ति का नाम </label>
        <input class="form-control">
        </div>
        <div class="col-md-4">
        <label>मोबाइल नंबर 
        </label>
        <input class="form-control">
        </div>
        <div class="col-md-4">
        <label>पदनाम </label>
        <input class="form-control">
        </div>
     <div class="col">
            <label>फिजिबिलिटी रिपोर्ट  (PDF)</label>
            <input type="file" accept="application/pdf">
            <div class="small"></div>
        </div>

        <div class="col">
            <label>लेआउट प्लान(PDF)</label>
            <input type="file" accept="application/pdf">
            <div class="small"></div>
        </div>   
</div>
</div>
</div>

<!-- ================= OTHER INFO ================= -->
<div class="card mb-3">
    <div class="card-body">
        <div class="section-title">💼 सत्यापन</div>

        <div class="row">
            <div class="col-md-12">
                <label class="form-label fw-bold">व्यवसाय सुझाव</label>
                <textarea 
                    class="form-control" 
                    name="business_suggestion" 
                    rows="4" 
                    placeholder="भूमि के आधार पर संभावित व्यवसाय सुझाव यहाँ लिखें...">
                </textarea>
            </div>
        </div>
    </div>
</div>


 

<!-- ================= MAP SCRIPT ================= -->
<!-- <script>
function getLocation() {
    if (!navigator.geolocation) {
        alert("❌ Browser location support nahi karta");
        return;
    }

    navigator.geolocation.getCurrentPosition(
        function(position) {
            let lat = position.coords.latitude.toFixed(6);
            let lng = position.coords.longitude.toFixed(6);

            document.getElementById("lat").value = lat;
            document.getElementById("lng").value = lng;

            document.getElementById("mapFrame").src =
                "https://www.google.com/maps?q=" + lat + "," + lng + "&z=15&output=embed";

            document.getElementById("mapBox").style.display = "block";
        },
        function() {
            alert("❌ Location permission allow karo");
        }
    );
}
</script> -->


</body>
</html>
