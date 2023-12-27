
<!DOCTYPE HTML>
<html lang="pl">
<head>
<meta charset="utf-8" />
    <meta http-equiv="refresh" content="2" >
    <link rel="stylesheet" href="style.css" type="text/css" />
    <link rel="stylesheet" href="img/fontello/css/fontello.css" type="text/css" />
	<title>Get Data from ESP</title>
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


    <?php
        require_once "connectPDO.php";

        if (isset($_GET['table'])) {
            $table_name = $_GET['table'];

            $NOTdecodedButtonName  = $_GET['ButtonName'];
            $ButtonColumn = $_GET['ButtonColumn'];
            $ButtonRow = $_GET['ButtonRow'];

            $ButtonName = urldecode($NOTdecodedButtonName);

            echo '<div class="ir_h2_title">';
                echo "Receiving Data from Device";
                echo '<a href="index.php" class="bubble" style="font-size: 12px; width: 5%; margin-top: 30px; border-radius: 25px; background-image: url("nothing.png");" onmouseover="this.title=\'HOME PAGE\';" onmouseout="this.title=\'\';";><i class="icon-home" style= "font-size: 20px;"></i></a>';
            echo '</div>';
            
            echo '<p style="color: yellow;">Point the IR Remote control at the device and press the button you want to assign to the remote control.</p>';

            // ////////////////////////////////////////////////////////////////

            function AddButtonData($connPDO, $table_name, $ButtonName, $ButtonProtocol, $ButtonAddress, $ButtonCommand, $ButtonRepNob, $ButtonColumn, $ButtonRow) {
                $sql = "INSERT INTO `$table_name` 
                        (button_name, button_protocol, button_address, button_command, button_rep_nob, button_column, button_row) 
                        VALUES (:ButtonName, :ButtonProtocol, :ButtonAddress, :ButtonCommand, :ButtonRepNob, :ButtonColumn, :ButtonRow)";
        
                $stmt = $connPDO->prepare($sql);
                $stmt->bindParam(':ButtonName', $ButtonName);
                $stmt->bindParam(':ButtonProtocol', $ButtonProtocol);
                $stmt->bindParam(':ButtonAddress', $ButtonAddress);
                $stmt->bindParam(':ButtonCommand', $ButtonCommand);
                $stmt->bindParam(':ButtonRepNob', $ButtonRepNob);
                $stmt->bindParam(':ButtonColumn', $ButtonColumn);
                $stmt->bindParam(':ButtonRow', $ButtonRow);
        
                return $stmt->execute();
            }


            function update_action_table_to_basic_state($connPDO) {
                $tb = "action_table";
                $esp_action = "nothing";
                $basic_row_column = 0;
                $basic_varchar = NULL;
                
                $sql = "UPDATE `$tb` SET 
                    `ESP_action` = 'nothing', 
                    `current_table` = :basic_varchar,
                    `button_name` = :basic_varchar,

                    `button_protocol` = :basic_varchar, 
                    `button_address` = :basic_varchar,
                    `button_command` = :basic_varchar, 
                    `button_rep_nob` = :basic_varchar,

                    `button_column` = 0, 
                    `button_row` = 0
                    WHERE `tab_id` = 1";
        
                $stmt = $connPDO->prepare($sql);
                $stmt->bindParam(':basic_varchar', $basic_varchar);
                $stmt->bindParam(':basic_row_column', $basic_row_column);
        
                return $stmt->execute();
            }


            // =============================================================

            try {

                $spr = 0; // Jeśli 0 --> nic  ,  Jeśli 1 --> dane odebrane  ,  Jeśli 2 --> błąd
                    $ESP_action = "";
                    $current_table = "";
                    $button_name = "";
                    $button_protocol = "";
                    $button_address = "";
                    $button_command = "";
                    $button_rep_nob = "";
                    $button_column = "";
                    $button_row = "";

                $connPDO = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                $connPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


                // Zapytanie SQL
                $stmt = $connPDO->prepare("SELECT * FROM action_table WHERE tab_id = 1 AND ESP_action = 'Received_from_ESP'");
                $stmt->execute();

                // Sprawdzenie liczby wierszy w wyniku zapytania
                $rowCount = $stmt->rowCount();

                // Wyświetlenie wyniku
                if ($rowCount > 0) {
                    // echo "wszystko się zgadza";

                    $spr = 1;

            // /////////////////////////////////////////////////////////////////////////////////////////////////////
                    require_once "connect.php";

                    $conn = new mysqli($host, $db_user , $db_password, $db_name);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }



                    $tb = "action_table";
                    $sql = "SELECT * FROM `$tb` WHERE tab_id = 1";

                    $result = $conn->query($sql);

                    if ($result) {
                        $rows = $result->fetch_all(MYSQLI_ASSOC);

                        foreach ($rows as $row) {
                            $ESP_action = $row['ESP_action'];
                            $current_table = $row['current_table'];
                            $button_name = $row['button_name'];
                            $button_protocol = $row['button_protocol'];
                            $button_address = $row['button_address'];
                            $button_command = $row['button_command'];
                            $button_rep_nob = $row['button_rep_nob'];
                            $button_column = $row['button_column'];
                            $button_row = $row['button_row'];

                            // Operacje na zmiennych

                            echo "ESP_action=$ESP_action|";
                        echo"<br>";
                        echo "current_table=$current_table|";
                        echo"<br>";
                        echo "button_name=$button_name|";
                        echo"<br>";
                        echo "button_protocol=$button_protocol|";
                        echo"<br>";
                        echo "button_address=$button_address|";
                        echo"<br>";
                        echo "button_command=$button_command|";
                        echo"<br>";
                        echo "button_rep_nob=$button_rep_nob|";
                        echo"<br>";
                        echo "button_column=$button_column|";
                        echo"<br>";
                        echo "button_row=$button_row|";
                        }
                    } else {
                        // echo "Błąd zapytania: " . $conn->error;
                        echo "Query error: " . $conn->error;
                    }

                    // Zamknięcie połączenia z bazą danych
                    $conn->close();

            // /////////////////////////////////////////////////////////////////////////////////////////////////////

                } else {
                    // echo "ERROR";
                    $spr = 2;
                }
            

                // -------------------------------------------------------------

                if ($spr == 1) {
                    if (AddButtonData($connPDO, $table_name, $ButtonName, $button_protocol, $button_address, $button_command, $button_rep_nob, $ButtonColumn, $ButtonRow)) {
                        // v
                        // echo '<p style="color: green;">Zmiany zostały zatwierdzone.</p>';
                        echo '<p style="color: green;">The changes have been approved.</p>';

                        update_action_table_to_basic_state($connPDO);
                        sleep(4);
                        header("Location: remote_edit.php?table=$table_name");
                        exit();

                    } else {
                        // echo '<p style="color: red;">Błąd podczas aktualizacji danych.</p>';
                        echo '<p style="color: red;">Error updating data.</p>';
                    }
                } else {
                    // echo '<p style="color: yellow;">Nie ma takiego button_id w tabeli.</p>';
                }

                // -------------------------------------------------------------

            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }

        } else {
            // echo 'Nieprawidłowe wywołanie strony.';
            echo 'Invalid page call.';
        }

    ?>

     
</body>
</html>

