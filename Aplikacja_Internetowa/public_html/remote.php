
<!DOCTYPE HTML>
<html lang="pl">
<head>
       <!-- <meta charset="utf-8" /> -->
       <link rel="stylesheet" href="style.css" type="text/css" />
       <link rel="stylesheet" href="img/fontello/css/fontello.css" type="text/css" />
	<title>Your Remote</title>
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


<script>
        document.addEventListener('DOMContentLoaded', function () {
            // Przywróć pozycję scrolla
            var scrollPosition = localStorage.getItem('scrollPosition');
            if (scrollPosition) {
                window.scrollTo(0, parseInt(scrollPosition));
            }

            // Zapisz aktualną pozycję scrolla po jego zmianie
            window.addEventListener('scroll', function () {
                localStorage.setItem('scrollPosition', window.scrollY);
            });
        });
    </script>
</head>
 
<body>
 
    <a href="#" class="scrollup"></a>
    
    <?php

        if (isset($_GET['table'])) {
            $table_name = $_GET['table'];
            
            echo '<div class="ir_h2_title">';
                echo "Remote: $table_name";
                echo '<div style="display: flex;">';
                echo '<a href="index.php" class="bubble" style="font-size: 12px; width: 3%; margin-top: 30px; margin-right: 15px; border-radius: 25px; background-image: url("nothing.png");" onmouseover="this.title=\'HOME PAGE\';" onmouseout="this.title=\'\';"><i class="icon-home" style= "font-size: 20px;"></i></a>';
                    echo '<a href="remote.php?table=' . $table_name . '" class="bubble" style="font-size: 12px; width: 3%; height: 2%; margin-top: 30px; margin-left: 15px; border-radius: 25px; background-image: url("nothing.png");" onmouseover="this.title=\'REFRESH\';" onmouseout="this.title=\'\';";><i class="icon-arrows-cw" style= "font-size: 20px;"></i></a>';
            echo '</div>';
            echo '</div>';
            
            require_once "connect.php";
            $conn = new mysqli($host, $db_user , $db_password, $db_name);

            
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

                        if (isset($_GET['snd'])) {
                            $snd_name = $_GET['snd'];

                            $tb = "action_table";
                            $esp_action = "ESP_send_IR";
                            $nothing_data = NULL;
                            $nnd = 0;
                            $sql = "UPDATE `$tb` SET 
                            `ESP_action` = '" . $esp_action . "', 
                            `current_table` = '" . $table_name . "', 
                            `button_name` = '" . $snd_name . "',

                            `button_protocol` = '" . $nothing_data . "', 
                            `button_address` = '" . $nothing_data . "',
                            `button_command` = '" . $nothing_data . "', 
                            `button_rep_nob` = '" . $nnd . "',
                            `button_column` = '" . $nnd . "', 
                            `button_row` = '" . $nnd . "'
                            WHERE `tab_id` = 1";

                            $result = $conn->query($sql);

                            header("Location: remote.php?table=$table_name");
                            exit();
                            
                        } else {
                            // echo 'Nieprawidłowe wywołanie strony.';
                        }
            
            // Zapytanie do pobrania danych z bazy
            $sql = "SELECT * FROM $table_name";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                // Tworzenie tablicy 2D do przechowywania danych o przyciskach
                $buttons = array();
                for ($i = 0; $i < 32; $i++) {
                    for ($j = 0; $j < 105; $j++) {
                        $buttons[$i][$j] = ""; // Początkowo ustaw każdą komórkę na pustą wartość
                    }
                }
                
                // Przetwarzanie wyników zapytania i wstawianie wartości do odpowiednich komórek tabeli
                $max_column = 0;
                $max_rowNum = 0;
                while ($row = $result->fetch_assoc()) {
                    $column = $row["button_column"] - 1; // Indeksujemy od 0
                    $rowNum = $row["button_row"] - 1; // Indeksujemy od 0
                    if($max_column < $column){
                        $max_column = $column;
                    }
                    if($max_rowNum < $rowNum){
                        $max_rowNum = $rowNum;
                    }
            
                    // Jeśli dane są w zakresie tabeli, wstaw wartość
                    if ($column >= 0 && $column < 32 && $rowNum >= 0 && $rowNum < 105) {
                        $buttons[$column][$rowNum] = $row["button_name"];
                    }
                }

                echo '<table id=rem_tab table>';
                for ($i = 0; $i < $max_rowNum+1; $i++) {
                    echo '<tr>';
                    for ($j = 0; $j < $max_column+1; $j++) {
                        if($buttons[$j][$i] != ""){
                        echo '<td>' . '<a href="remote.php?table=' . $table_name . '&snd=' . $buttons[$j][$i] . '" class="edit-button">' . $buttons[$j][$i] . '</a>' . '</td>';
                        }else{
                            echo '<td>' . $buttons[$j][$i] . '</td>';
                        }
                    }
                    echo '</tr>';
                }
                echo '</table>';


                // Dodanie przycisku "EDIT" z odnośnikiem do kolejnej strony
                echo '<a href="remote_edit.php?table=' . $table_name . '" class="edit-button">EDIT <i class="icon-edit" style= "font-size: 20px;"></i></a>';


            } else {
                // echo "Brak danych do wyświetlenia.<br>";
                echo "No data to display.<br>";
                echo '<a href="remote_edit.php?table=' . $table_name . '" class="edit-button">EDIT</a>';
            }


            $conn->close();
        } else {
            // echo 'Nieprawidłowe wywołanie strony.';
            echo 'Invalid page call.';
        }
?>

     
</body>
</html>

