
<?php

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
    echo "Błąd zapytania: " . $conn->error;
}

// Zamknięcie połączenia z bazą danych
$conn->close();

?>
