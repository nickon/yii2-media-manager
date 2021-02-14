CREATE TABLE media_manager (
     id int(11) NOT NULL AUTO_INCREMENT,
     name varchar(255) NOT NULL DEFAULT '',
     title varchar(255) NOT NULL DEFAULT '',
     description text NOT NULL,
     extension varchar(10) NOT NULL DEFAULT '',
     size varchar(255) NOT NULL DEFAULT '',
     path varchar(255) NOT NULL DEFAULT '',
     date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
     type varchar(100) NOT NULL,
     is_image tinyint(1) NOT NULL DEFAULT 0,
     PRIMARY KEY (id)
)
ENGINE = INNODB,
AUTO_INCREMENT = 12976,
AVG_ROW_LENGTH = 214,
CHARACTER SET utf8,
COLLATE utf8_general_ci;