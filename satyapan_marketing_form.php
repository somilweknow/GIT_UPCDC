<!DOCTYPE html>
<html lang="hi">
<head>
<meta charset="UTF-8">
<title>क्रय-विक्रय सहकारी समिति</title>

<style>
body{
    font-family: Arial, sans-serif;
    background:#f2f4f7;
    margin:0;
    padding:20px;
}
.container{
    max-width:1200px;
    margin:auto;
    background:#fff;
    padding:20px;
    border-radius:8px;
}
.section{
    margin-bottom:25px;
    border:1px solid #dcdcdc;
    border-radius:6px;
}
.section h3{
    margin:0;
    padding:10px 15px;
    background:#4a8ad4;
    color:#fff;
    font-size:16px;
}
.section-body{
    padding:15px;
}
.row{
    display:flex;
    gap:15px;
    margin-bottom:12px;
}
.col{
    flex:1;
}
label{
    font-size:13px;
    font-weight:600;
    display:block;
    margin-bottom:4px;
}
input, select, textarea{
    width:100%;
    padding:8px;
    border:1px solid #ccc;
    border-radius:4px;
}
textarea{
    resize:none;
}
button{
    background:#4a8ad4;
    color:#fff;
    border:none;
    padding:10px 30px;
    border-radius:5px;
    font-size:15px;
    cursor:pointer;
}
#map{
    width:100%;
    height:250px;
    border-radius:5px;
}
.location-box{
    display:flex;
    gap:15px;
}
.small{
    font-size:12px;
    color:#666;
}
</style>
</head>

<body>

<div class="container">
<h2 style="text-align:center;"></h2>

<!-- Basic Info -->
<div class="section">
<h3>📄 मूलभूत जानकारी</h3>
<div class="section-body">
    <div class="row">
        <div class="col">
            <label>मंडल का नाम</label>
            <select>
                <option>AGRA</option>
                <!-- <option>ALIGARH</option>
                <option>FAIZABAD</option>
                <option>LUCKNOW</option>
                <option>KANPUR</option>
                <option>VARANASI</option>
                <option>GORAKHPUR</option> -->
                <!-- Add other mandals if needed -->
            </select>
        </div>
        <div class="col">
            <label>जनपद का नाम</label>
            <select>
                <option>AGRA</option>
                <!-- <option>ALIGARH</option>
                <option>AMBEDKAR NAGAR</option>
                <option>AZAMGARH</option>
                <option>BAREILLY</option>
                <option>BANDA</option>
                <option>BAGHPAT</option>
                <option>BASTI</option>
                <option>BHADOHI</option>
                <option>BULANDSHAHR</option>
                <option>DEORIA</option>
                <option>FAIZABAD</option>
                <option>FARRUKHABAD</option>
                <option>FATEHPUR</option>
                <option>FIROZABAD</option>
                <option>GHAZIABAD</option>
                <option>GHAZIPUR</option>
                <option>GORAKHPUR</option>
                <option>HAMIRPUR</option>
                <option>HAPUR</option>
                <option>JAUNPUR</option>
                <option>JHANSI</option>
                <option>KANPUR DEHAT</option>
                <option>KANPUR NAGAR</option>
                <option>KASGANJ</option>
                <option>KAUSHAMBI</option>
                <option>KHERI</option>
                <option>LUCKNOW</option>
                <option>MAHOBA</option>
                <option>MAU</option>
                <option>MEERUT</option>
                <option>MUZAFFARNAGAR</option>
                <option>PALWAL</option>
                <option>PRATAPGARH</option>
                <option>RAE BARELI</option>
                <option>SAHARANPUR</option>
                <option>SANT KABIR NAGAR</option>
                <option>SANT RAVIDAS NAGAR</option>
                <option>VARANASI</option> -->
                <!-- Add all other districts as needed -->
            </select>
        </div>
        <div class="col">
            <label>तहसील का नाम </label>
            <select>
                <option>AGRA</option>
           </select>
           </div>

        <div class="col">
            <label>ब्लॉक का नाम </label>
            <select>
                <option>AGRA</option>
               
            </select>

            
        </div>
        <div class="col">
            <label>समिति का नाम  </label>
            <input type="text">

            
        </div>
    </div>
</div>
</div>

<!-- Land Info -->
<div class="section">
<h3>🏡 खाली भूमि का विवरण</h3>
<div class="section-body">
    <div class="row">
        <div class="col">
            <label>भूमि का क्षेत्रफल (हेक्टेयर)</label>
            <input type="text">
        </div>
        <div class="col">
            <label>कब्जा / विवाद ?</label>
            <select>
                <option>--चुनें--</option>
                <option>कब्जा</option>
                <option>विवाद</option>
            </select>
        </div>
        <div class="col">
            <label>राजस्व अभिलेखों में स्थिति</label>
            <select>
                <option>--चुनें--</option>
                <option>दर्ज है</option>
                <option>दर्ज नहीं है</option>
            </select>
        </div>
    </div>
</div>
</div>

<!-- Business Info -->
 <div class="section">
   <h3 class="section-heading">💼 सत्यापन</h3>
   <br>

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
<h3>📝 भूमि के आधार पर व्यावसायिक सुझाव </h3>
<div class="section-body">
    <label>अन्य विवरण</label>
    <textarea rows="4"></textarea>
</div>
</div>

<div style="text-align:center;">
<button type="submit">Submit</button>
</div>

</div>

</body>
</html>
