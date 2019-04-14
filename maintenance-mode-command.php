<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

$autoload = __DIR__ . '/vendor/autoload.php';

if ( file_exists( $autoload ) ) {
	require_once $autoload;
}

WP_CLI::add_command( 'maintenance-mode', '\WP_CLI\MaintenanceMode\MaintenanceModeCommand' );
