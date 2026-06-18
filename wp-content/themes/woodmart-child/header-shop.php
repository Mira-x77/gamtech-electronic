<?php
/**
 * Header (shop) — proxy to unified header.php so get_header('shop') uses child header
 */
defined( 'ABSPATH' ) || exit;

// Reuse the main child theme header to ensure dark layout is applied for named headers
require_once get_stylesheet_directory() . '/header.php';
