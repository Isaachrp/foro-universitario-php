<?php

function rateLimitCheck($key, $maxAttempts = 5, $decaySeconds = 300)
{
    if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
    }

    $now = time();

    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = [
            'attempts' => 0,
            'expires' => $now + $decaySeconds
        ];
    }

    $entry = &$_SESSION['rate_limit'][$key];

    // reset si ya expiró
    if ($now > $entry['expires']) {
        $entry = [
            'attempts' => 0,
            'expires' => $now + $decaySeconds
        ];
    }

    if ($entry['attempts'] >= $maxAttempts) {
        return false;
    }

    return true;
}

function rateLimitHit($key, $decaySeconds = 300)
{
    $now = time();

    $_SESSION['rate_limit'][$key]['attempts']++;
    $_SESSION['rate_limit'][$key]['expires'] = $now + $decaySeconds;
}

function rateLimitClear($key)
{
    unset($_SESSION['rate_limit'][$key]);
}