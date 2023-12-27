
<!DOCTYPE HTML>
<html lang="pl">
<head>
       <!-- <meta charset="utf-8" /> -->
       <link rel="stylesheet" href="style.css" type="text/css" />
       <link rel="stylesheet" href="img/fontello/css/fontello.css" type="text/css" />
	<title>Edit Remote</title>
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
 
    <a href="#" class="scrollup"></a>


    <?php
        if (isset($_GET['table'])) {
            $table_name = $_GET['table'];
            echo '<div class="ir_h2_title">';
                echo "Remote: $table_name";
                echo '<div style="display: flex;">';
                    echo '<a href="index.php" class="bubble" style="font-size: 12px; width: 3%; height: 2%; margin-top: 30px; margin-right: 15px; border-radius: 25px; background-image: url("nothing.png");" onmouseover="this.title=\'HOME PAGE\';" onmouseout="this.title=\'\';";><i class="icon-home" style= "font-size: 20px;"></i></a>';
                    echo '<a href="remote.php?table=' . $table_name . '" class="bubble" style="font-size: 20px; width: 14%; margin-top: 30px; margin-left: 15px; margin-right: 15px; border-radius: 25px; background-image: url("nothing.png");";><i class="icon-left-open-outline" style= "font-size: 20px;">REMOTE</i></a>';
                    echo '<a href="remote_edit.php?table=' . $table_name . '" class="bubble" style="font-size: 12px; width: 3%; height: 2%; margin-top: 30px; margin-left: 15px; border-radius: 25px; background-image: url("nothing.png");" onmouseover="this.title=\'REFRESH\';" onmouseout="this.title=\'\';";><i class="icon-arrows-cw" style= "font-size: 20px;"></i></a>';
                echo '</div>';
            echo '</div>';
            
            require_once "connect.php";
            $conn = new mysqli($host, $db_user , $db_password, $db_name);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Pobierz nazwy kolumn z danej tabeli
            $sql_columns = "SHOW COLUMNS FROM " . $table_name;
            $result_columns = $conn->query($sql_columns);

            if ($result_columns->num_rows > 0) {
                echo '<table class=minimal-table>';
                echo '<tr>';
                while ($row_columns = $result_columns->fetch_assoc()) {
                    echo '<th>' . $row_columns['Field'] . '</th>';
                }
                echo '</tr>';

                // Pobierz dane z danej tabeli
                $sql_data = "SELECT * FROM " . $table_name;
                $result_data = $conn->query($sql_data);

                if ($result_data->num_rows > 0) {
                    while ($row_data = $result_data->fetch_assoc()) {
                        echo '<tr>';
                        foreach ($row_data as $column => $value) {
                            echo '<td>' . $value . '</td>';
                        }
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="' . $result_columns->num_rows . '">No data to display.</td></tr>';
                }

                echo '</table>';
            } else {
                // echo 'Brak kolumn w danej tabeli.';
                echo 'There are no columns in the given table.';
            }

            require_once "connectPDO.php";

try {
    $connPDO = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $connPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    function getDataToButton($conn, $tableName, $buttonId, $selectedColumn) {
        try {
            $sql = "SELECT * FROM `$tableName` WHERE `button_id` = :buttonId";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':buttonId', $buttonId);
            $stmt->execute();
    
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Sprawdź, czy kolumna istnieje w wynikach zapytania
            if (isset($result[$selectedColumn])) {
                return $result[$selectedColumn];
            } else {
                return null; // Zwraca null, jeśli kolumna nie istnieje w wynikach
            }
        } catch (PDOException $e) {
            // echo '<p style="color: red;">Błąd pobierania danych z bazy: ' . $e->getMessage() . '</p>';
            echo '<p style="color: red;">Error retrieving data from the database: ' . $e->getMessage() . '</p>';
            return null;
        }
    }
    
    
    function isDataExists($connPDO, $table_name, $buttonId, $buttonName, $buttonColumn, $buttonRow) {
        if (empty($buttonName) && !is_numeric($buttonColumn) && !is_numeric($buttonRow)) {
            return false;
        }


        if(empty($buttonColumn)){
            $buttonColumn = getDataToButton($connPDO, $table_name, $buttonId, 'button_column');
        }
        if(empty($buttonRow)){
            $buttonRow = getDataToButton($connPDO, $table_name, $buttonId, 'button_row');
        }
        

        $sql = "SELECT * FROM `$table_name` WHERE (`button_name` = :buttonName OR (`button_column` = :buttonColumn AND `button_row` = :buttonRow)) AND `button_id` <> :buttonId";
        $stmt = $connPDO->prepare($sql);
        $stmt->bindParam(':buttonName', $buttonName);
        $stmt->bindParam(':buttonColumn', $buttonColumn);
        $stmt->bindParam(':buttonRow', $buttonRow);
        $stmt->bindParam(':buttonId', $buttonId);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    function getButtonData($connPDO, $table_name, $buttonId) {
        $sql = "SELECT * FROM `$table_name` WHERE `button_id` = :buttonId";
        $stmt = $connPDO->prepare($sql);
        $stmt->bindParam(':buttonId', $buttonId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function updateButtonData($connPDO, $table_name, $buttonId, $newButtonName, $newButtonProtocol, $newButtonAddress, $newButtonCommand, $newButtonRepNob, $newButtonColumn, $newButtonRow) {
        $sql = "UPDATE `$table_name` SET 
                `button_name` = IF(:newButtonName <> '', :newButtonName, `button_name`), 
                `button_protocol` = IF(:newButtonProtocol <> '', :newButtonProtocol, `button_protocol`), 
                `button_address` = IF(:newButtonAddress <> '', :newButtonAddress, `button_address`), 
                `button_command` = IF(:newButtonCommand <> '', :newButtonCommand, `button_command`), 
                `button_rep_nob` = IF(:newButtonRepNob <> '', :newButtonRepNob, `button_rep_nob`), 
                `button_column` = IF(:newButtonColumn <> '', :newButtonColumn, `button_column`), 
                `button_row` = IF(:newButtonRow <> '', :newButtonRow, `button_row`) 
                WHERE `button_id` = :buttonId";

        $stmt = $connPDO->prepare($sql);
        $stmt->bindParam(':newButtonName', $newButtonName);
        $stmt->bindParam(':newButtonProtocol', $newButtonProtocol);
        $stmt->bindParam(':newButtonAddress', $newButtonAddress);
        $stmt->bindParam(':newButtonCommand', $newButtonCommand);
        $stmt->bindParam(':newButtonRepNob', $newButtonRepNob);
        $stmt->bindParam(':newButtonColumn', $newButtonColumn);
        $stmt->bindParam(':newButtonRow', $newButtonRow);
        $stmt->bindParam(':buttonId', $buttonId);

        return $stmt->execute();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Obsługa Zmian
        if(isset($_POST["Zatwierdz_Zmiany"])){
        $buttonId = $_POST['button_id'];
        $newButtonName = $_POST['button_name'];
        $newButtonProtocol = $_POST['button_protocol'];
        $newButtonAddress = $_POST['button_address'];
        $newButtonCommand = $_POST['button_command'];
        $newButtonRepNob = $_POST['button_rep_nob'];
        $newButtonColumn = $_POST['button_column'];
        $newButtonRow = $_POST['button_row'];


        if (isDataExists($connPDO, $table_name, $buttonId, $newButtonName, $newButtonColumn, $newButtonRow)) {
            echo '<p style="color: red;">Such data already appears in the table.</p>';
        } else {
            $existingData = getButtonData($connPDO, $table_name, $buttonId);

            if ($existingData) {
                if (updateButtonData($connPDO, $table_name, $buttonId, $newButtonName, $newButtonProtocol, $newButtonAddress, $newButtonCommand, $newButtonRepNob, $newButtonColumn, $newButtonRow)) {
                    // v
                    // echo '<p style="color: green;">Zmiany zostały zatwierdzone.</p>';
                    echo '<p style="color: green;">The changes have been approved.</p>';
                    header("Refresh:3");
                    exit();
                } else {
                    // echo '<p style="color: red;">Błąd podczas aktualizacji danych.</p>';
                    echo '<p style="color: red;">Error updating data.</p>';
                }
            } else {
                // echo '<p style="color: yellow;">Nie ma takiego button_id w tabeli.</p>';
                echo '<p style="color: yellow;">There is no such button_id in the table.</p>';
            }
        }
} // obsluga zmian

    // Obsluga dodawania
    if(isset($_POST["Add_btn"])){
        
        header("Location: add_button.php?table=$table_name");   // Przenies na strone do dodawania przycisku
        exit();

    }

        // Obsluga usuwania
        if(isset($_POST["Usun_Dane"])){
            $buttonId = $_POST['button_id'];
            $existingData = getButtonData($connPDO, $table_name, $buttonId);
            if ($existingData) {
                $sql = "DELETE FROM `$table_name` WHERE NOT `button_id` <> :buttonId";
                $stmt = $connPDO->prepare($sql);
                $stmt->bindParam(':buttonId', $buttonId);
                $stmt->execute();
                echo '<meta http-equiv="refresh" content="0">'; // odświezenie strony
            } else {
                // echo '<p style="color: yellow;">Nie ma takiego button_id w tabeli.</p>';
                echo '<p style="color: yellow;">There is no such button_id in the table.</p>';
            }

        } // usuwanie

        if(isset($_POST["Usun_Tab"])){
            $sql = "DROP TABLE `$table_name`";
            $stmt = $connPDO->prepare($sql);
            $stmt->execute();

            header("Location: index.php"); // Przeniesienie na stronę główną
            exit();

        }
    }
    else {
        echo '<div style="display: flex; justify-content: center;">';
        echo '<div style="display: flex; flex-direction: row;">';
        // Zmiana
        print ("
        <div class=\"form-container\">
        <form method=\"post\" action=\"\">
        <p><div class=\"form-container_title\">Remote Control Change <br> </p></div>
            <p style='color: orange;'>button_id:<br> <input type=\"text\" name=\"button_id\"/></p>
            <p>button_name:<br> <input type=\"text\" name=\"button_name\"/></p>
            <p>button_protocol:<br> <input type=\"text\" name=\"button_protocol\"/></p>
            <p>button_address:<br> <input type=\"text\" name=\"button_address\"/></p>
            <p>button_command:<br> <input type=\"text\" name=\"button_command\"/></p>
            <p>button_rep_nob:<br> <input type=\"text\" name=\"button_rep_nob\"/></p>
            <p>button_column:<br> <input type=\"text\" name=\"button_column\"/></p>
            <p>button_row:<br> <input type=\"text\" name=\"button_row\"/></p>
            <p><br></p>
            <INPUT type=\"submit\" name=\"Zatwierdz_Zmiany\" value=\"Confirm Changes\">
        </form>
        </div>
        ");
        
        echo '<div style="display: flex; flex-direction: column;">';

            // Dodawanie przycisku
        print("
        <div class=\"form-container\">
        <form method=\"post\" action=\"\">   
        <p><div class=\"form-container_title\">Add a Button <i class='icon-plus-outline' style= 'font-size: 20px;'></i> <br> </p></div>       
            <INPUT type=\"submit\" name=\"Add_btn\" value=\"Add Button\">
        </form>
        </div>
        ");

        // Usuwanie krotki
        print("
        <div class=\"form-container\">
        <form method=\"post\" action=\"\">
        <p><div class=\"form-container_title\" style='color: red;'>Delete a Button <i class='icon-cancel' style= 'font-size: 20px;'></i> <br> </p></div>
            <p style='color: orange;'>button_id:<br> <input type=\"text\" name=\"button_id\"/></p>        
            <INPUT type=\"submit\" name=\"Usun_Dane\" value=\"Delete Button\">
        </form>
        </div>
        ");

        // Usuwanie Tabeli
        print("
        <div class=\"form-container\">
        <form method=\"post\" action=\"\">   
        <p><div class=\"form-container_title\" style='color: red;'>Delete a Remote <i class='icon-trash' style= 'font-size: 20px;'></i> <br> </p></div>       
            <INPUT type=\"submit\" name=\"Usun_Tab\" value=\"Remove Remote\">
        </form>
        </div>
        ");
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


$connPDO = null;

            $conn->close();
        } else {
            // echo 'Nieprawidłowe wywołanie strony.';
            echo 'Invalid page call.';
        }
    ?>


</body>
</html>

