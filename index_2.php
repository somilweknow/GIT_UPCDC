<?php
include("scripts/settings.php");
$msg = '';
$tab = 1;

page_header_start();
page_header_end();
?>

<!-- Sidebar -->
<div style="width:220px; float:left; height:100vh; 
            background:#3ae2e0; padding:20px; box-shadow:2px 0 10px rgba(66, 73, 75, 0.2);">
    <h3 style="text-align:center; margin-bottom:30px; 
               color:#1e3a8a; font-family:Arial, sans-serif; font-weight:bold;">
        UPCDC
    </h3>
    
    <div style="display:flex; flex-direction:column; gap:15px;">
        <a href="http://localhost/upcdc/index.php" target="_blank" class="sidebar-btn">
            <span style="font-size:1.2rem;">🏢</span> UPCDC
        </a>
        <a href="https://upcod.in" target="_blank" class="sidebar-btn">
            <span style="font-size:1.2rem;">📊</span> PDMP
        </a>
        <a href="https://upsfms.in" target="_blank" class="sidebar-btn">
            <span style="font-size:1.2rem;">💻</span> UPSFMS
        </a>
    </div>
</div>

<!-- Main Content -->
<div style="margin-left:240px; padding:20px; text-align:center;">

    <!-- Heading -->
    <h1 style="font-size:2.5rem; color:#1e3a8a; font-weight:bold; 
               margin-bottom:60px; line-height:1.3;">
        उत्तर प्रदेश कोऑपरेटिव डेटाबेस सेंटर
    </h1>

    <!-- Logo -->
    <img src="images/coop_logo.png" alt="Logo" style="max-width:300px; height:auto; border-radius:15px; 
         box-shadow:0 6px 20px rgba(0,0,0,0.3); margin-top:20px; transition:0.3s;" 
         onmouseover="this.style.transform='scale(1.08)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.4)';" 
         onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.3)';">

</div>

<!-- Sidebar Button Styles -->
<style>
.sidebar-btn {
    padding:12px;
    display:flex;
    align-items:center;
    gap:10px;
    color:#1e3a8a;
    text-decoration:none;
    border-radius:6px;
    font-weight:bold;
    transition:0.3s;
}
.sidebar-btn:hover {
    background: rgba(255, 255, 255, 0.5); /* light hover background */
    box-shadow: 0 3px 8px rgba(0,0,0,0.2);
    transform: scale(1.02);
}
</style>

<?php
page_footer_end();
?>
