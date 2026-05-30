<?php
include("scripts/settings.php");
// error_reporting(E_ALL);
page_header_start();
?>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;600&display=swap');
  @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');

  body {
    font-family: "Noto Sans Devanagari", sans-serif;
    background: linear-gradient(135deg, #e3f2fd, #fce4ec);
    margin: 0;
    /* padding: 30px 15px; */
  }

  h2 {
    text-align: center;
    color: #1a237e;
    margin: 25px 0 15px 0;
    font-weight: 700;
    font-size: 1.8rem;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
  }

  .grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
    gap: 20px;
    max-width: 1200px;
    margin: 0 auto;
  }
  /* .grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    max-width: 1200px;
    margin: 0 auto;
  } */

  .card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    padding: 18px 15px;
    text-align: center;
    transition: all 0.3s ease;
  }

  .card:hover {
    transform: translateY(-6px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.15);
  }

  .card a {
    text-decoration: none;
    color: #2c3e50;
    font-size: 1rem;
    font-weight: 600;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .card a:hover {
    color: #0078d7;
  }

  .icon {
    font-size: 1.8rem;
    color: white;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    margin: 0 auto 8px auto;
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
  }

  .card:nth-child(odd) .icon { background: linear-gradient(45deg,#42a5f5,#1e88e5); }
  .card:nth-child(even) .icon { background: linear-gradient(45deg,#66bb6a,#43a047); }

</style>
<?php
page_header_end();
page_sidebar();
?>


<div class="grid">

<?php
// ===== SQL Query (with sort field) =====
if(isset($_SESSION['apex_id']) && $_SESSION['apex_id']!=""){
    $sql = "SELECT sno, apex_name, apex_icon, apex_link1, sort 
            FROM apex 
            WHERE sno ='".$_SESSION['apex_id']."' 
            ORDER BY sort ASC";
}else{
    $sql = "SELECT sno, apex_name, apex_icon, apex_link1, sort 
            FROM apex 
            ORDER BY sort ASC";
}

$result = execute_query($sql);

if ($result && mysqli_num_rows($result) > 0) {

    $heading1_printed = false;
    $heading2_printed = false;

    while ($row = mysqli_fetch_assoc($result)) {

        $sno   = htmlspecialchars($row['sno']);
        $name  = htmlspecialchars($row['apex_name']);
        $link  = htmlspecialchars($row['apex_link1']);
        $icon  = htmlspecialchars($row['apex_icon']);
        $sort  = (int)$row['sort'];

        $final_link = $link . '?exdid=' . $sno;

        // ===== Heading 1 (Sort 1–11) =====
        if ($sort <= 11 && !$heading1_printed) {
            echo '<div style="grid-column:1/-1;"><h2>शीर्ष सहकारी संस्थान</h2></div>';
            $heading1_printed = true;
        }

        // ===== Heading 2 (Sort 12+) =====
        if ($sort >= 12 && !$heading2_printed) {
            echo '<div style="grid-column:1/-1;"><h2>सहकारिता के अन्य अनुसांगिक विभाग</h2></div>';
            $heading2_printed = true;
        }

        // ===== Card =====
        echo '
        <div class="card">
            <a href="'.$final_link.'" target="_blank">
                <div class="icon"><i class="fa-solid '.$icon.'"></i></div>
                '.$name.'
            </a>
        </div>';
    }

} else {
    echo '<p>कोई संस्था नहीं मिली</p>';
}
?>

</div>

<?php
page_footer_start();
page_footer_end();
?>