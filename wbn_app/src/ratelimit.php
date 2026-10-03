<?php
declare(strict_types=1);

/** Aynı IP'den $windowSeconds içinde $max denemeden fazlasını engeller. */
function rate_limit_hit(string $bucket, int $max, int $windowSeconds): bool
{
    $stmt = db()->prepare(
        'SELECT COUNT(*) FROM rate_limits WHERE bucket = ? AND ip = ? AND created_at > (NOW() - INTERVAL ? SECOND)'
    );
    $stmt->execute([$bucket, client_ip(), $windowSeconds]);
    return (int) $stmt->fetchColumn() >= $max;
}

function rate_limit_record(string $bucket): void
{
    db()->prepare('INSERT INTO rate_limits (bucket, ip) VALUES (?, ?)')->execute([$bucket, client_ip()]);
    // Eski kayıtları ara sıra temizle
    if (random_int(1, 50) === 1) {
        db()->exec('DELETE FROM rate_limits WHERE created_at < (NOW() - INTERVAL 1 DAY)');
    }
}

function rate_limit_reset(string $bucket): void
{
    db()->prepare('DELETE FROM rate_limits WHERE bucket = ? AND ip = ?')->execute([$bucket, client_ip()]);
}
