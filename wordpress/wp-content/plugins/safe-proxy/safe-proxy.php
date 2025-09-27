<?php
/**
 * Plugin Name: Safe Proxy (Hardened Example)
 * Description: Demonstrates safer handling of user-supplied URLs (baseline only).
 * Version: 1.0.0
 */

if ( ! defined('ABSPATH') ) { exit; }

add_action('wp_ajax_nopriv_sp_proxy', 'sp_proxy_handler');
add_action('wp_ajax_sp_proxy', 'sp_proxy_handler');

function sp_proxy_handler() {
    $url = isset($_REQUEST['url']) ? trim($_REQUEST['url']) : '';
    if (!$url) {
        status_header(400);
        echo "Missing url";
        exit;
    }

    if (! sp_is_allowed_url($url)) {
        status_header(403);
        echo "URL not allowed";
        exit;
    }

    $response = wp_remote_get($url, [
        'timeout'     => 5,
        'redirection' => 1,
    ]);

    if (is_wp_error($response)) {
        status_header(502);
        echo "Fetch failed";
        exit;
    }

    $ctype = wp_remote_retrieve_header($response, 'content-type');
    if ($ctype && ! preg_match('#^image/(png|jpeg|gif|webp)$#', $ctype)) {
        status_header(415);
        echo "Disallowed content type";
        exit;
    }

    status_header(200);
    echo wp_remote_retrieve_body($response);
    exit;
}

function sp_is_allowed_url($url) {
    $parts = wp_parse_url($url);
    if (!$parts) return false;

    $scheme = strtolower($parts['scheme'] ?? '');
    if (! in_array($scheme, ['http','https'], true)) return false;

    $host = $parts['host'] ?? '';
    if (!$host) return false;

    $ip = gethostbyname($host);
    if (! $ip || ! filter_var($ip, FILTER_VALIDATE_IP)) return false;

    if (sp_is_private_ip($ip)) return false;

    return true;
}

function sp_is_private_ip($ip) {
    $rangeList = [
        '10.0.0.0|10.255.255.255',
        '172.16.0.0|172.31.255.255',
        '192.168.0.0|192.168.255.255',
        '127.0.0.0|127.255.255.255',
        '169.254.0.0|169.254.255.255',
    ];
    $long = ip2long($ip);
    if ($long === false) return true;
    foreach ($rangeList as $range) {
        list($start, $end) = explode('|', $range);
        if ($long >= ip2long($start) && $long <= ip2long($end)) {
            return true;
        }
    }
    return false;
}
