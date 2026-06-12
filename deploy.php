<?php
/**
 * Auto-deploy webhook — triggered by GitHub on every push to main.
 * Secured with a secret token.
 */

define( 'DEPLOY_SECRET', 'gamtech2026deploy' );
define( 'REPO_PATH',     '/home/c2423708c/public_html' );
define( 'GIT_BRANCH',    'main' );

// Verify GitHub signature
$payload   = file_get_contents( 'php://input' );
$signature = isset( $_SERVER['HTTP_X_HUB_SIGNATURE_256'] )
    ? $_SERVER['HTTP_X_HUB_SIGNATURE_256']
    : '';

$expected = 'sha256=' . hash_hmac( 'sha256', $payload, DEPLOY_SECRET );

if ( ! hash_equals( $expected, $signature ) ) {
    http_response_code( 403 );
    die( 'Unauthorized' );
}

// Only deploy on push to main
$data = json_decode( $payload, true );
if ( isset( $data['ref'] ) && $data['ref'] !== 'refs/heads/' . GIT_BRANCH ) {
    http_response_code( 200 );
    die( 'Not main branch, skipping.' );
}

// Run git pull
$output = shell_exec( 'cd ' . escapeshellarg( REPO_PATH ) . ' && git pull origin ' . GIT_BRANCH . ' 2>&1' );

http_response_code( 200 );
echo "Deploy triggered:\n" . $output;
