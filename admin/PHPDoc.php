<?php

/**
 * Adatbázis kapcsolatot kezelő osztály.
 * 
 * Ez az osztály kapcsolatot létesít egy MySQL adatbázissal,
 * és lehetővé teszi az SQL utasítások végrehajtását.
 */
class DatabaseConnection {
    /**
     * @var mysqli $connection A MySQL adatbázis kapcsolat objektuma.
     */
    private $connection;

    /**
     * Létrehozza az adatbázis kapcsolatot.
     * 
     * @param string $host Az adatbázis kiszolgáló neve.
     * @param string $user Az adatbázis felhasználóneve.
     * @param string $password Az adatbázis felhasználó jelszava.
     * @param string $database Az adatbázis neve.
     * 
     * @throws Exception Ha nem sikerül a kapcsolat létrehozása.
     */
    public function __construct($host, $user, $password, $database) {
        $this->connection = new mysqli($host, $user, $password, $database);

        if ($this->connection->connect_error) {
            throw new Exception('Kapcsolódási hiba: ' . $this->connection->connect_error);
        }
    }

    /**
     * Végrehajt egy SELECT lekérdezést és visszaadja az eredményt.
     * 
     * @param string $sql A végrehajtandó SQL lekérdezés.
     * @param int $id A lekérdezésben használt azonosító.
     * 
     * @return array A lekérdezés eredménye tömbként.
     */
    public function select($sql, $id) {
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Bezárja az adatbázis kapcsolatot.
     */
    public function close() {
        $this->connection->close();
    }
}

