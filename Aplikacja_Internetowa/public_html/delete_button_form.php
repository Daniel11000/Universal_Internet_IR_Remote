<?php
include_once('connect.php'); // Załaduj plik z połączeniem do bazy danych

// Sprawdź, czy przekazano buttonId do usunięcia
if(isset($_GET['buttonId'])){
    $buttonIdToDelete = $_GET['buttonId'];

    // Sprawdź, czy buttonId istnieje w bazie danych
    $isButtonExists = isButtonIdExists($connPDO, $table_name, $buttonIdToDelete);

    if ($isButtonExists) {
        // Usuń przycisk o podanym buttonId
        deleteButton($connPDO, $table_name, $buttonIdToDelete);
        echo '<p style="color: green;">Przycisk został pomyślnie usunięty.</p>';
    } else {
        echo '<p style="color: yellow;">Nie ma takiego button_id w tabeli.</p>';
    }
} else {
    // Jeśli nie przekazano buttonId, wyświetl komunikat o braku danych
    echo '<p style="color: red;">Nieprawidłowe dane.</p>';
}

// Funkcja sprawdzająca istnienie button_id w bazie danych
function isButtonIdExists($connPDO, $table_name, $buttonIdToDelete) {
    $sql = "SELECT * FROM `$table_name` WHERE `button_id` = :buttonIdToDelete";
    $stmt = $connPDO->prepare($sql);
    $stmt->bindParam(':buttonIdToDelete', $buttonIdToDelete);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}

// Funkcja usuwająca przycisk o podanym buttonId
function deleteButton($connPDO, $table_name, $buttonIdToDelete) {
    $sql = "DELETE FROM `$table_name` WHERE `button_id` = :buttonIdToDelete";
    $stmt = $connPDO->prepare($sql);
    $stmt->bindParam(':buttonIdToDelete', $buttonIdToDelete);
    $stmt->execute();
}
?>
