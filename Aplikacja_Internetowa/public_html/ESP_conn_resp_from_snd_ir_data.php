
<?php

require_once "connect.php";

$conn = new mysqli($host, $db_user , $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// ////////////////////////////////////////////////////////

function update_action_table($conn) {
    $tb = "action_table";
    $esp_action = "nothing";
    $nothing_data = NULL;
    $nnd = 0;
    $sql = "UPDATE `$tb` SET 
        `ESP_action` = '" . $esp_action . "', 
        `current_table` = '" . $nothing_data . "', 
        `button_name` = '" . $nothing_data . "',
        `button_protocol` = '" . $nothing_data . "', 
         `button_address` = '" . $nothing_data . "',
        `button_command` = '" . $nothing_data . "', 
        `button_rep_nob` = '" . $nnd . "',
        `button_column` = '" . $nnd . "', 
        `button_row` = '" . $nnd . "'
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
    $snd_resp = test_input($_POST["snd_resp"]);

    echo "Received Darta: ";
    echo"<br>";
    echo "Response from ESP=$snd_resp";
    echo"<br>";
    
    
    if($snd_resp != "") 
    {
    
        update_action_table($conn);
    
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
