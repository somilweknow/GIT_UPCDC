<?php
include("scripts/settings.php");
error_reporting(E_ALL);
page_header_start();
?>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;600&display=swap');
  @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');

  body {
    font-family: "Noto Sans Devanagari", sans-serif;
    background: linear-gradient(135deg, #e3f2fd, #fce4ec);
    margin: 0;
    padding: 30px 15px;
  }

  h2 {
    text-align: center;
    color: #1a237e;
    margin-bottom: 30px;
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
    display: block;
    margin-top: 8px;
  }

  .card a:hover {
    color: #0078d7;
  }

  .icon {
    font-size: 1.8rem;
    color: white;
    background: linear-gradient(45deg, #42a5f5, #478ed1);
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    margin: 0 auto 8px auto;
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
  }

  /* Individual accent colors */
  .card:nth-child(1) .icon { background: linear-gradient(45deg,#42a5f5,#1e88e5); }
  .card:nth-child(2) .icon { background: linear-gradient(45deg,#66bb6a,#43a047); }
  .card:nth-child(3) .icon { background: linear-gradient(45deg,#ef5350,#e53935); }
  .card:nth-child(4) .icon { background: linear-gradient(45deg,#ab47bc,#8e24aa); }
  .card:nth-child(5) .icon { background: linear-gradient(45deg,#ffa726,#fb8c00); }
  .card:nth-child(6) .icon { background: linear-gradient(45deg,#26c6da,#00acc1); }
  .card:nth-child(7) .icon { background: linear-gradient(45deg,#8d6e63,#6d4c41); }
  .card:nth-child(8) .icon { background: linear-gradient(45deg,#5c6bc0,#3949ab); }
  .card:nth-child(9) .icon { background: linear-gradient(45deg,#f06292,#ec407a); }
  .card:nth-child(10) .icon { background: linear-gradient(45deg,#9ccc65,#7cb342); }
  .card:nth-child(11) .icon { background: linear-gradient(45deg,#26a69a,#00897b); }
  .card:nth-child(12) .icon { background: linear-gradient(45deg,#ff7043,#f4511e); }
  .card:nth-child(13) .icon { background: linear-gradient(45deg,#7e57c2,#5e35b1); }
  .card:nth-child(14) .icon { background: linear-gradient(45deg,#78909c,#546e7a); }
  .card:nth-child(15) .icon { background: linear-gradient(45deg,#ec407a,#ad1457); }
  .card:nth-child(16) .icon { background: linear-gradient(45deg,#29b6f6,#0288d1); }

  /* Make card clickable entirely */
  .card a {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
</style>
<?php
page_header_end();
page_sidebar();
?>
<body>
  <h2>सहकारी संस्थान</h2>

  <div class="grid">
    <?php
    
    // if(isset($_SESSION['apex_id']) && $_SESSION['apex_id']!=""){
    //   $sql = "SELECT `sno`, `apex_name`, `apex_icon`, `apex_link` FROM `apex` where sno ='".$_SESSION['apex_id']."' ORDER BY `sno` ASC";
    // }else{
    //  $sql = "SELECT `sno`, `apex_name`, `apex_icon`, `apex_link` FROM `apex` ORDER BY `sno` ASC";
    // }
    $sql = "SELECT `sno`, `apex_name`, `apex_icon`, `apex_link1` FROM `apex` ORDER BY `sno` ASC";
    
    $result = execute_query($sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $sno  = htmlspecialchars($row['sno']);
            $name = htmlspecialchars($row['apex_name']);
            $link = htmlspecialchars($row['apex_link1']);
            $icon = htmlspecialchars($row['apex_icon']);

            $final_link = $link . '?exdid=' . $sno;
            
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
?>
