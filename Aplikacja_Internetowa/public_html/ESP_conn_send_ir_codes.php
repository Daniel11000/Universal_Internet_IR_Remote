
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

            // echo "Exec";
            // echo "<br>";
    }
} else {
    echo "Błąd zapytania: " . $conn->error;
}

if ($ESP_action == "ESP_send_IR"){

    $sql = "SELECT * FROM $current_table WHERE button_name = '$button_name'";
    $result = $conn->query($sql);

    if ($result) {
        $rows = $result->fetch_all(MYSQLI_ASSOC);

        foreach ($rows as $row) {

            $IR_button_id = $row['button_id'];
            $IR_button_name = $row['button_name'];
            $IR_button_protocol = $row['button_protocol'];
            $IR_button_address = $row['button_address'];
            $IR_button_command = $row['button_command'];
            $IR_button_rep_nob = $row['button_rep_nob'];
            $IR_button_column = $row['button_column'];
            $IR_button_row = $row['button_row'];
            // echo "Executing ESP_send_IR block";
            // echo "<br>";


            echo "IR_button_protocol=$IR_button_protocol|";
            echo"<br>";
            echo "IR_button_address=$IR_button_address|";
            echo"<br>";
            echo "IR_button_command=$IR_button_command|";
            echo"<br>";
            echo "IR_button_rep_nob=$IR_button_rep_nob|";

        }

    } else {
        echo "Błąd zapytania: " . $conn->error;
    }

}


// Zamknięcie połączenia z bazą danych
$conn->close();

?>
