
<?php

require_once "connect.php";

$conn = new mysqli($host, $db_user , $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// ////////////////////////////////////////////////////////

function update_action_table($conn, $ButtonProtocol, $ButtonAddress, $ButtonCommand, $Button_Rep_Nob) {
    $tb = "action_table";
    $esp_action = "Received_from_ESP";
    $sql = "UPDATE `$tb` SET 
        `ESP_action` = '" . $esp_action . "', 
        `button_protocol` = '" . $ButtonProtocol . "', 
        `button_address` = '" . $ButtonAddress . "', 
        `button_command` = '" . $ButtonCommand . "', 
        `button_rep_nob` = '" . $Button_Rep_Nob . "'
        WHERE `tab_id` = 1";

        if ($conn->query($sql) === TRUE) {
            echo "Data Received and Updated successfully";
        } 
        else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
}

// ///////////////////////////////////////////////////////
 

 
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $protocol = test_input($_POST["protocol"]);
    $address = test_input($_POST["address"]);
    $command = test_input($_POST["command"]);
    $rep_nob = test_input($_POST["rep_nob"]);

    echo "Received Darta: ";
    echo"<br>";
    echo "IR_protocol=$protocol";
    echo"<br>";
    echo "IR_address=$address";
    echo"<br>";
    echo "IR_command=$command";
    echo"<br>";
    echo "IR_rep_nob=$button_arep_nobddress";
    
    if($protocol != "" && $address != "" && $command != "" && $rep_nob != "") 
    {
        update_action_table($conn, $protocol, $address, $command, $rep_nob);
    
        $conn->close();
    }
echo "Data Received Successfully";
 
}
else {

    $sql = "SELECT * FROM action_table WHERE tab_id = 1 AND ESP_action = 'Received_from_ESP'";
    $result = $conn->query($sql);

    // Sprawdzenie wyników zapytania
    if ($result->num_rows > 0) {
            // Rekord istnieje
        echo "Data Received Successfully";
    } else {
            // Rekord nie istnieje
        echo "No data posted with HTTP POST.";
    }

    // echo "No data posted with HTTP POST.";
}
 
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$conn->close();
?>
