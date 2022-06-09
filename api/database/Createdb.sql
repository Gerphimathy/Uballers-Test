CREATE DATABASE uballers;

USE uballers;

CREATE TABLE users(
                     id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                     login VARCHAR(64) UNIQUE NOT NULL,
                     password CHAR(128) NOT NULL,
                     firstname varchar(32) NOT NULL,
                     lastname varchar(32) NOT NULL,
                     genre CHAR (1) NOT NULL,
                     birthdate DATE NOT NULL
);

CREATE TABLE tokens(
                       token CHAR(30),
                       client VARCHAR(255) NOT NULL,
                       id_user INT NOT NULL,
                       expires DATETIME,

                       FOREIGN KEY (id_user) REFERENCES users(id),
                       PRIMARY KEY (client, id_user)
);