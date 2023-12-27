<!DOCTYPE HTML>
<html lang="pl">
<head>
    <!-- <meta charset="utf-8" /> -->
    <link rel="stylesheet" href="style.css" type="text/css" />
	<title>Internet IR Remote</title>
    <link rel="shortcut icon" href="img/remote_icon.png"; type="image/png" />
	
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	
	<script src="jquery.scrollTo.min.js"></script>

	<script>
		
		jQuery(function($)
		{
			//zresetuj scrolla
			$.scrollTo(0);
			$('.scrollup').click(function() { $.scrollTo($('body'), 1000); });
		}
		);
		
		//pokaż podczas przewijania
		$(window).scroll(function()
		{
			if($(this).scrollTop()>300) $('.scrollup').fadeIn();
			else $('.scrollup').fadeOut();		
		}
		);
	
	
	</script>

</head>
 
<body>
    
    <div class="ir_h1_title" style="display: flex; align-items: center; justify-content: center;">
        <div style="text-align: left;"> <a href="index.php"> <img src="img/remote_icon.png" alt='icon' class='image' style="height: 150px; width: 150px; border-radius: 40px; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.2)'; this.title='refresh';" onmouseout="this.style.transform='scale(1)'; this.title='';"> </a> </div>
        <!-- <div style="text-align: center; margin-left: auto; margin-right: auto;"> Twoje Piloty </div> -->
        <div style="text-align: center; margin-left: 30px;"> Your Remotes </div>
    </div>
    <a href="#" class="scrollup"></a>


<?php

require_once "connect.php";

$conn = new mysqli($host, $db_user , $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Zapytanie do bazy danych
$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    while($row = $result->fetch_row()) {
            if ($row[0] != "action_table"){
        echo '<a href="remote.php?table=' . $row[0] . '" class="bubble">';
        echo '<bubble_h2>' . $row[0] . '</bubble_h2>';
        echo '</div>';
            }
    }

} else {
    // echo "Brak tabel w bazie danych.";
    echo "There are no tables in the database.";
}

// Dodanie przycisku "+" z odnośnikiem do kolejnej strony do dodawania pilota (tworzenia tabeli)
echo '<a href="add_table.php" class="add_btn">+</a>';

// Zamknięcie połączenia
$conn->close();
?>
     
</body>
</html>


