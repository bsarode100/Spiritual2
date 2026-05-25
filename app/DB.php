<?php
// PDO singleton + small query helpers.
class DB {
    private static ?PDO $pdo = null;

    public static function init(array $cfg): void {
        if (self::$pdo) return;
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $cfg['host'], $cfg['port'], $cfg['database'], $cfg['charset']);
        $tries = 0;
        while (true) {
            try {
                self::$pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
                return;
            } catch (PDOException $e) {
                // In docker compose, db may take a few seconds to come up.
                if (++$tries > 30) throw $e;
                sleep(1);
            }
        }
    }

    public static function pdo(): PDO {
        if (!self::$pdo) throw new RuntimeException('DB not initialised');
        return self::$pdo;
    }

    public static function q(string $sql, array $params = []): PDOStatement {
        $st = self::pdo()->prepare($sql);
        $st->execute($params);
        return $st;
    }

    public static function one(string $sql, array $params = []): ?array {
        $row = self::q($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public static function all(string $sql, array $params = []): array {
        return self::q($sql, $params)->fetchAll();
    }

    public static function val(string $sql, array $params = []) {
        $r = self::q($sql, $params)->fetchColumn();
        return $r === false ? null : $r;
    }

    public static function insert(string $table, array $data): int {
        $cols = array_keys($data);
        $ph   = array_map(fn($c) => ':' . $c, $cols);
        $sql  = sprintf('INSERT INTO `%s` (%s) VALUES (%s)',
            $table,
            implode(',', array_map(fn($c) => "`$c`", $cols)),
            implode(',', $ph));
        self::q($sql, $data);
        return (int) self::pdo()->lastInsertId();
    }

    public static function update(string $table, array $data, array $where): int {
        $set = implode(',', array_map(fn($c) => "`$c` = :$c", array_keys($data)));
        $wh  = [];
        $wp  = [];
        foreach ($where as $k => $v) {
            $wh[] = "`$k` = :w_$k";
            $wp["w_$k"] = $v;
        }
        $sql = "UPDATE `$table` SET $set WHERE " . implode(' AND ', $wh);
        return self::q($sql, array_merge($data, $wp))->rowCount();
    }
}
