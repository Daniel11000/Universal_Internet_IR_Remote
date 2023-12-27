
<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="style.css" type="text/css" />
    <link rel="stylesheet" href="img/fontello/css/fontello.css" type="text/css" />
	<title>Add Remote Button</title>
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
            // echo "Add buttons to your new remote";
            echo "Add buttons to your remote";
            echo '<div style="display: flex;">';
            echo '<a href="index.php" class="bubble" style="font-size: 12px; width: 3%; margin-top: 30px; margin-right: 15px; border-radius: 25px; background-image: url("nothing.png");" onmouseover="this.title=\'HOME PAGE\';" onmouseout="this.title=\'\';";><i class="icon-home" style= "font-size: 20px;"></i></a>';
            echo '<a href="remote_edit.php?table=' . $table_name . '" class="bubble" style="font-size: 20px; width: 7%; height: 2%; margin-top: 30px; margin-right: 15px; margin-left: 15px; border-radius: 25px; background-image: url("nothing.png");";>EDIT</a>';
            echo '<a href="add_button.php?table=' . $table_name . '" class="bubble" style="font-size: 12px; width: 3%; height: 2%; margin-top: 30px; margin-left: 15px; border-radius: 25px; background-image: url("nothing.png");" onmouseover="this.title=\'REFRESH\';" onmouseout="this.title=\'\';";><i class="icon-arrows-cw" style= "font-size: 20px;"></i></a>';
            echo '</div>';
            echo '</div>';


            require_once "connect.php";
            $conn = new mysqli($host, $db_user , $db_password, $db_name);

            
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
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
                
                
                function isDataExists($connPDO, $table_name, $buttonName, $buttonColumn, $buttonRow) {
                    if (empty($buttonName) && !is_numeric($buttonColumn) && !is_numeric($buttonRow)) {
                        return false;
                    }
            
            
                    if(empty($buttonColumn)){
                        $buttonColumn = getDataToButton($connPDO, $table_name, 'button_column');
                    }
                    if(empty($buttonRow)){
                        $buttonRow = getDataToButton($connPDO, $table_name, 'button_row');
                    }
                    
            
                    $sql = "SELECT * FROM `$table_name` WHERE (`button_name` = :buttonName OR (`button_column` = :buttonColumn AND `button_row` = :buttonRow))";
                    $stmt = $connPDO->prepare($sql);
                    $stmt->bindParam(':buttonName', $buttonName);
                    $stmt->bindParam(':buttonColumn', $buttonColumn);
                    $stmt->bindParam(':buttonRow', $buttonRow);
                    $stmt->execute();
            
                    return $stmt->rowCount() > 0;
                }
            
                function getButtonData($connPDO, $table_name, $button_name) {
                    $sql = "SELECT * FROM `$table_name` WHERE `button_name` = :button_name";
                    $stmt = $connPDO->prepare($sql);
                    $stmt->bindParam(':button_name', $button_name);
                    $stmt->execute();
            
                    return $stmt->fetch(PDO::FETCH_ASSOC);
                }
            
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

                function update_action_table($connPDO, $table_name, $ButtonName, $ButtonColumn, $ButtonRow) {

                    $tb = "action_table";
                    $sql = "UPDATE `$tb` SET 
                        `ESP_action` = 'ESP_recv_IR', 
                        `current_table` = :table_name, 
                        `button_name` = :ButtonName, 
                        `button_column` = :ButtonColumn, 
                        `button_row` = :ButtonRow
                        WHERE `tab_id` = 1";
            
                    $stmt = $connPDO->prepare($sql);
                    $stmt->bindParam(':table_name', $table_name);
                    $stmt->bindParam(':ButtonName', $ButtonName);
                    $stmt->bindParam(':ButtonColumn', $ButtonColumn);
                    $stmt->bindParam(':ButtonRow', $ButtonRow);
            
                    return $stmt->execute();
                }



                if ($_SERVER["REQUEST_METHOD"] == "POST") {

                            // Obsługa Dodawania
                    if(isset($_POST["Add_Button"])){
                        
                        $ButtonName = $_POST['button_name'];
                        // $ButtonRepNob = $_POST['button_rep_nob'];
                        $ButtonColumn = $_POST['button_column'];
                        $ButtonRow = $_POST['button_row'];
              
                
                        if (isDataExists($connPDO, $table_name, $ButtonName, $ButtonColumn, $ButtonRow)) {
                            // echo '<p style="color: red;">Takie dane już występują w tabeli.</p>';
                            echo '<p style="color: red;">Such data already appears in the table.</p>';
                        } else {
                            sleep(1);
                            update_action_table($connPDO, $table_name, $ButtonName, $ButtonColumn, $ButtonRow);
                            $encodedButtonName = urlencode($ButtonName);
                            sleep(2);
                            header("Location: get_data_from_Device.php?table=$table_name&ButtonName=$encodedButtonName&ButtonColumn=$ButtonColumn&ButtonRow=$ButtonRow");
                            exit();
                        }
                } // Dodawanie

                }
                else {
                    echo '<div style="display: flex; justify-content: center;">';
                    print ("
                    <div class=\"form-container\">
                    <form method=\"post\" action=\"\">
                    <p><div class=\"form-container_title\">Add a Button <br> </p></div> 
                        <p>button_name:<br> <input type=\"text\" name=\"button_name\"/></p>
                        <p>button_column:<br> <input type=\"text\" name=\"button_column\"/></p>
                        <p>button_row:<br> <input type=\"text\" name=\"button_row\"/></p>
                        <p><br></p>
                        <INPUT type=\"submit\" name=\"Add_Button\" value=\"Add Button\">
                    </form>
                    </div>
                    ");
                    echo'</div>';

                }


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

