<?php
include("scripts/settings.php");
error_reporting(E_ALL);


combine_report();

function combine_report(){
    $i=1;
    global $db;
    $sql = 'select * from master_district';
    $result = mysqli_query($db, $sql);
    
    echo '<table border="1">';
    echo '<tr>
    <td>S.No.</td>
    <td>District Name</td>
    <td>Block Union</td>
    <td>Marketing Society</td>
    <td>Jila Sahkari Sangh</td>
    <td>Consumer Society</td>
    </tr>';
    
    while($row = mysqli_fetch_assoc($result)){
        $sql = 'SELECT * FROM `marketing` where is_deleted != 1 and district_id="'.$row['sno'].'"';
        //echo $sql;
        $marketing = mysqli_num_rows(mysqli_query($db, $sql));
        
        $sql = 'SELECT * FROM `block_union` where is_deleted != 1 and janpad_name="'.$row['sno'].'"';
        $block = mysqli_num_rows(mysqli_query($db, $sql));
        
        $sql = 'SELECT * FROM `jila_sehkari` where is_deleted != 1 and janpad_name="'.$row['sno'].'"';
        $jila_sehkari = mysqli_num_rows(mysqli_query($db, $sql));
        
        $sql = 'SELECT * FROM `upss` where is_deleted != 1 and janpad_name="'.$row['sno'].'"';
        $upss = mysqli_num_rows(mysqli_query($db, $sql));
        
        echo '<tr>
        <td>'.$i++.'</td>
        <td>'.$row['district_name'].'</td>
        <td>'.$block.'</td>
        <td>'.$marketing.'</td>
        <td>'.$jila_sehkari.'</td>
        <td>'.$upss.'</td>
        </tr>';
    }
    
    $i=1;
    global $db;
    $sql = 'select * from master_division';
    $result = mysqli_query($db, $sql);
    
    echo '<table border="1">';
    echo '<tr>
    <td>S.No.</td>
    <td>Division Name</td>
    <td>Block Union</td>
    <td>Marketing Society</td>
    <td>Jila Sahkari Sangh</td>
    <td>Consumer Society</td>
    </tr>';
    
    while($row = mysqli_fetch_assoc($result)){
        
        $sql = 'SELECT * FROM `marketing` where is_deleted != 1 and division_id="'.$row['sno'].'"';
        //echo $sql;
        $marketing = mysqli_num_rows(mysqli_query($db, $sql));
        
        $sql = 'SELECT * FROM `block_union` where is_deleted != 1 and mandal_name="'.$row['sno'].'"';
        $block = mysqli_num_rows(mysqli_query($db, $sql));
        
        $sql = 'SELECT * FROM `jila_sehkari` where is_deleted != 1 and mandal_name="'.$row['sno'].'"';
        $jila_sehkari = mysqli_num_rows(mysqli_query($db, $sql));
        
        $sql = 'SELECT * FROM `upss` where is_deleted != 1 and mandal_name="'.$row['sno'].'"';
        $upss = mysqli_num_rows(mysqli_query($db, $sql));

        echo '<tr>
        <td>'.$i++.'</td>
        <td>'.$row['division_name'].'</td>
        <td>'.$block.'</td>
        <td>'.$marketing.'</td>
        <td>'.$jila_sehkari.'</td>
        <td>'.$upss.'</td>
        </tr>';
    }
    

}
?>