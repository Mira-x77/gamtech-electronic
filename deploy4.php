<?php
define('DEPLOY_SECRET', 'gamtech2026deploy');
define('REPO_URL', 'https://github.com/Mira-x77/gamtech-electronic.git');
define('REPO_PATH', '/home/c2423708c/public_html');
define('GIT_BRANCH', 'main');

$payload = file_get_contents('php://input');
$sig = isset($_SERVER['HTTP_X_HUB_SIGNATURE_256']) ? $_SERVER['HTTP_X_HUB_SIGNATURE_256'] : '';
$expected = 'sha256=' . hash_hmac('sha256', $payload, DEPLOY_SECRET);
if (!hash_equals($expected, $sig)) { http_response_code(403); die('Unauthorized'); }

$data = json_decode($payload, true);
if (isset($data['ref']) && $data['ref'] !== 'refs/heads/' . GIT_BRANCH) { die('Skipping'); }

if (is_dir(REPO_PATH . '/.git')) {
    $cmd = 'cd ' . escapeshellarg(REPO_PATH) . ' && git pull origin ' . GIT_BRANCH . ' 2>&1';
} else {
    $cmd = 'cd ' . escapeshellarg(REPO_PATH)
         . ' ; git init'
         . ' ; (git remote remove origin 2>/dev/null || true)'
         . ' ; git remote add origin ' . escapeshellarg(REPO_URL)
         . ' ; git fetch origin ' . GIT_BRANCH
         . ' ; git checkout -f origin/' . GIT_BRANCH
         . ' 2>&1';
}

$output = shell_exec($cmd);
http_response_code(200);
echo "OK:\n" . $output;
