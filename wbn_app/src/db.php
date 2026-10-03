<?php
declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', config('db.host'), config('db.name'));
        $pdo = new PDO($dsn, config('db.user'), config('db.pass'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $pdo->exec("SET time_zone = '" . (new DateTime())->format('P') . "'");
    }
    return $pdo;
}

/** Satır ekler, ref alanını (ör. WBN-2026-001) doldurur ve [id, ref] döner. */
function db_insert(string $table, array $row, string $refPrefix): array
{
    $cols = array_keys($row);
    $sql = sprintf(
        'INSERT INTO %s (%s) VALUES (%s)',
        $table,
        implode(', ', $cols),
        implode(', ', array_map(fn ($c) => ':' . $c, $cols))
    );
    db()->prepare($sql)->execute($row);
    $id = (int) db()->lastInsertId();
    $ref = sprintf('%s-%s-%03d', $refPrefix, date('Y'), $id);
    db()->prepare("UPDATE $table SET ref = ? WHERE id = ?")->execute([$ref, $id]);
    return [$id, $ref];
}
