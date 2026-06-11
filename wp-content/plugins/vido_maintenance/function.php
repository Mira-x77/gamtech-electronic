<?php
/*
Plugin Name: Vido Maintenance
Description: Plugin de maintenance pour les sites WordPress.
Version: 1.0
Author: Vido IT
License: GPL2
*/

// Activation du mode maintenance lors de l'activation du plugin
register_activation_hook(__FILE__, 'activer_mode_maintenance');
function activer_mode_maintenance() {
    if (!current_user_can('activate_plugins')) return;
    $maintenance_file = get_stylesheet_directory() . '/maintenance.php';
    if (!file_exists($maintenance_file)) {
        $default_maintenance_content = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance en cours</title>
</head>
<body style="background-image:url('.get_site_url().'/wp-content/plugins/vido_maintenance/sourire.jpg); background-size: cover; background-repeat: no-repeat;" >

    <div style="text-align: center; padding: 100px;">
        <h1>Maintenance en cours</h1>
        <p>Nous sommes en train de procéder à des améliorations sur notre site. Nous serons de retour bientôt. Merci de votre patience.</p>
    </div>
</body>
</html>';
        file_put_contents($maintenance_file, $default_maintenance_content);
    }
}

// Désactivation du mode maintenance lors de la désactivation du plugin
register_deactivation_hook(__FILE__, 'desactiver_mode_maintenance');
function desactiver_mode_maintenance() {
    if (!current_user_can('activate_plugins')) return;
    $maintenance_file = get_stylesheet_directory() . '/maintenance.php';
    if (file_exists($maintenance_file)) {
        unlink($maintenance_file);
    }
}

// Fonction pour rediriger les visiteurs vers la page de maintenance
add_action('template_redirect', 'rediriger_vers_maintenance');
function rediriger_vers_maintenance() {
    if (!current_user_can('activate_plugins') && !is_admin()) {
        $maintenance_file = get_stylesheet_directory() . '/maintenance.php';
        if (file_exists($maintenance_file)) {
            include_once($maintenance_file);
            exit;
        }
    }
}