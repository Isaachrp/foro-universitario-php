<?php
class Database {
    public function connect() {
        return new PDO(
            "mysql:host=host_name;dbname=db_name;charset=utf8",
            "usuario",
            "password",
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }
}
