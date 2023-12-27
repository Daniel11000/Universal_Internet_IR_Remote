
<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="style.css" type="text/css" />
    <link rel="stylesheet" href="img/fontello/css/fontello.css" type="text/css" />
	<title>Add Remote</title>
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
 
    <!-- <h1>Add Your Ner Remote</h1> -->



    <?php

        echo '<div class="ir_h2_title">';
        echo "Add Your New Remote";
        echo '<div style="display: flex;">';
        echo '<a href="index.php" class="bubble" style="font-size: 12px; width: 3%; margin-top: 30px; margin-right: 15px; border-radius: 25px; background-image: url("nothing.png");" onmouseover="this.title=\'HOME PAGE\';" onmouseout="this.title=\'\';";><i class="icon-home" style= "font-size: 20px;"></i></a>';
        echo '<a href="add_table.php" class="bubble" style="font-size: 12px; width: 3%; height: 2%; margin-top: 30px; margin-left: 15px; border-radius: 25px; background-image: url("nothing.png");" onmouseover="this.title=\'REFRESH\';" onmouseout="this.title=\'\';";><i class="icon-arrows-cw" style= "font-size: 20px;"></i></a>';
        echo '</div>';
        echo '</div>';


        require_once "connectPDO.php";

        $connPDO = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $connPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        
        function zamienSpacje($text) {
            return str_replace(' ', '_', $text);
            return preg_replace('/[^\w\s]/', '_', $text);
        }
        
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $table_name = zamienSpacje($_POST["Remote_Name"]);
        
                // Sprawdzenie, czy tabela już istnieje
                $sprawdzCzyTabelaIstnieje = $conn->prepare("SHOW TABLES LIKE :table_name");
                $sprawdzCzyTabelaIstnieje->bindParam(':table_name', $table_name);
                $sprawdzCzyTabelaIstnieje->execute();
        
                if ($sprawdzCzyTabelaIstnieje->rowCount() > 0) {
                    // echo "Pilot o tej nazwie już istnieje.";
                    echo "A remote with this name already exists.";
                } else {
                    // Utworzenie tabeli
                    $utworzTabeleSQL = "
                        CREATE TABLE $table_name
                        (
                            button_id              INT             NOT NULL AUTO_INCREMENT,
                            button_name            VARCHAR(40)     NOT NULL,
                            button_protocol        VARCHAR(20)     NOT NULL,
                            button_address         VARCHAR(15)     NOT NULL,
                            button_command         VARCHAR(15)     NOT NULL,
                            button_rep_nob         INT             NOT NULL,
                            button_column          INT             NOT NULL,
                            button_row             INT             NOT NULL,
                            PRIMARY KEY (button_id),
                            UNIQUE KEY (button_name),
                            UNIQUE KEY (button_column, button_row)
                        ) ENGINE=INNODB;
                    ";
        
                    $conn->exec($utworzTabeleSQL);
                    // echo "Tabela została utworzona.";
                    echo "The table has been created.";
                    // sleep(2);


                    header("Location: add_button.php?table=$table_name");
                    exit();
                    // echo '<a href="remote_edit.php?table=' . $table_name . '" class="edit-button">EDIT</a>';
                }
            }
        } catch (PDOException $e) {
            // echo "Błąd połączenia: " . $e->getMessage();
            echo "Connection error: " . $e->getMessage();
        }
        
        $conn = null;

    ?>

    <div style="display: flex; justify-content: center;">
    <div class="form-container">
    <form method="post" action="">
    <div class="form-container_title"><label for="Remote_Name">Nazwa Tabeli:</label></div><br>
        <input type="text" name="Remote_Name" required><br><br>
        <input type="submit" value="Create">
    </form>
    </div>
    </div>

     
</body>
</html>

