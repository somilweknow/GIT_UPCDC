<?php

// Database connection
$conn = mysqli_connect("localhost","root","mysql","upcdc_2025");

if(!$conn){
    die("Database connection failed: " . mysqli_connect_error());
}

$survey_id = $_GET['survey_id'];

$file_name = "मानव_संपदा_विवरण_सर्वे_आईडी_" . $survey_id . ".xls";

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$file_name");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";

echo "<tr>
<th>क्रम संख्या</th>
<th>स्टाफ प्रकार</th>
<th>पद (एच०आर०आईडी)</th>
<th>स्वीकृत पद</th>
<th>रिक्त पद</th>
<th>पद (स्टाफ०आईडी)</th>
<th>कर्मचारी का नाम</th>
<th>स्थिति</th>
<th>पिता का नाम</th>
<th>जन्म तिथि</th>
<th>मोबाइल नंबर</th>
<th>शैक्षणिक योग्यता</th>
<th>रिकॉर्ड बनाने की तिथि</th>
</tr>";

$query = "SELECT h.*, 
                 hr.post_name AS hr_post_name,
                 st.post_name AS staff_post_name
          FROM apex_human_resource_info h
          
          LEFT JOIN master_posts_apex_1 hr 
          ON h.hr_post_id = hr.sno
          
          LEFT JOIN master_posts_apex_1 st 
          ON h.staff_post_id = st.sno
          
          WHERE h.survey_id='$survey_id'";

$result = mysqli_query($conn,$query);

$sr = 1;

if($result && mysqli_num_rows($result) > 0){

    while($row = mysqli_fetch_assoc($result)){
        $staff_type = ($row['staff_type'] == 'tech') ? 'Technical' : 'Non-Technical';

        echo "<tr>";

        echo "<td>".$sr++."</td>";
        echo "<td>".$staff_type."</td>";
        echo "<td>".$row['hr_post_name']."</td>";
        echo "<td>".$row['sanctioned_post']."</td>";
        echo "<td>".$row['vacant_post']."</td>";
        echo "<td>".$row['staff_post_name']."</td>";
        echo "<td>".$row['staff_name']."</td>";
        echo "<td>".$row['staff_sthiti']."</td>";
        echo "<td>".$row['staff_father']."</td>";
        echo "<td>".$row['staff_dob']."</td>";
        echo "<td>".$row['staff_mobile']."</td>";
        echo "<td>".$row['staff_qualification']."</td>";
        echo "<td>".$row['created_at']."</td>";

        echo "</tr>";
    }

}

echo "</table>";
?>