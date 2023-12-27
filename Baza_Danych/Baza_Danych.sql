DROP TABLE IF EXISTS action_table;


CREATE TABLE action_table
(
        tab_id             int,
    ESP_action             VARCHAR(20),
    current_table          VARCHAR(40),
    button_name            VARCHAR(40),
    button_protocol        VARCHAR(20),
    button_address         VARCHAR(15),
    button_command         VARCHAR(15),
    button_rep_nob         INT,
    button_column          INT,
    button_row             INT

)ENGINE=INNODB;

-- Dopuszczalne czynności: ESP_send_IR , ESP_recv_IR, nothing, Received_from_ESP, IR_data_sent

   -- INSERT INTO action_table(ESP_action, current_table, button_name, button_protocol, button_address, button_command, button_rep_nob, button_column, button_row) VALUES ("ESP_send_IR", "TV_salon", "VOL+", "Samsung", "0x707", "0x7", 1, 1, 1);

INSERT INTO action_table(tab_id, ESP_action) VALUES (1, "nothing");




/*
                                 -- Przykładowa Tabela jaką można umieścić w Bazie Danych (gdy chce się wgrać piloty i przyciski aby były dostępne "od startu")
DROP TABLE IF EXISTS TV_salon;

CREATE TABLE TV_salon
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
)ENGINE=INNODB;


INSERT INTO TV_salon (button_name, button_protocol, button_address, button_command, button_rep_nob, button_column, button_row) VALUES ("VOL+", "Samsung", "0x707", "0x7", 1, 1, 1);
INSERT INTO TV_salon (button_name, button_protocol, button_address, button_command, button_rep_nob, button_column, button_row) VALUES ("VOL-", "Samsung", "0x707", "0xB", 1, 1, 2);
INSERT INTO TV_salon (button_name, button_protocol, button_address, button_command, button_rep_nob, button_column, button_row) VALUES ("ON/OFF", "Samsung", "0x7", "0xBA", 1, 1, 3);
INSERT INTO TV_salon (button_name, button_protocol, button_address, button_command, button_rep_nob, button_column, button_row) VALUES ("SOURCE", "Sony", "0x1", "0x701", 1, 2, 1);
INSERT INTO TV_salon (button_name, button_protocol, button_address, button_command, button_rep_nob, button_column, button_row) VALUES ("MUTE", "Samsung", "0xC", "0x7", 1, 2, 3);
*/
