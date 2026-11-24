#!/usr/bin/env node
/**
 * STOP DEV SERVER (scripts/stop-server.js)
 */

const { execSync } = require('child_process');

try {
    if (process.platform === 'win32') {
        execSync('powershell -Command "Stop-Process -Name httpd, ngrok -Force -ErrorAction SilentlyContinue"');
    } else {
        execSync('pkill httpd || true; pkill ngrok || true');
    }
    console.log('[Server] Đã dừng server Apache và tunnel Ngrok.');
} catch (e) {
    // Silently continue
}

