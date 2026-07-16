import fs from 'node:fs';
import path from 'node:path';
import { defineConfig } from 'vite';

const hotFile = path.resolve(__dirname, 'public/hot');

function isPrivateIPv4(hostname) {
    return /^(10\.|192\.168\.|172\.(1[6-9]|2\d|3[0-1])\.)/.test(hostname);
}

function pickHotUrl(server) {
    const networkUrls = server.resolvedUrls?.network || [];
    const localUrls = server.resolvedUrls?.local || [];

    const networkWithPrivateIPv4 = networkUrls.find((url) => {
        try {
            const hostname = new URL(url).hostname;
            return isPrivateIPv4(hostname);
        } catch (error) {
            return false;
        }
    });

    return networkWithPrivateIPv4 || networkUrls[0] || localUrls[0] || `http://localhost:${server.config.server.port || 5173}/`;
}

function laravelHotFile() {
    return {
        name: 'laravel-hot-file',
        configureServer(server) {
            const writeHotFile = () => {
                const url = pickHotUrl(server);

                fs.mkdirSync(path.dirname(hotFile), { recursive: true });
                fs.writeFileSync(hotFile, url.replace(/\/$/, ''));
            };

            server.httpServer?.once('listening', writeHotFile);
            server.httpServer?.once('close', () => {
                fs.rmSync(hotFile, { force: true });
            });
        },
    };
}

function laravelFullReload() {
    return {
        name: 'laravel-full-reload',
        handleHotUpdate({ file, server }) {
            const normalized = file.split(path.sep).join('/');
            const isBladeView = normalized.includes('/resources/views/') && normalized.endsWith('.blade.php');
            const isRouteFile = normalized.includes('/routes/') && normalized.endsWith('.php');
            const isHttpAppFile = normalized.includes('/app/Http/') && normalized.endsWith('.php');

            if (isBladeView || isRouteFile || isHttpAppFile) {
                server.ws.send({ type: 'full-reload' });
                return [];
            }
        },
    };
}

export default defineConfig({
    plugins: [laravelHotFile(), laravelFullReload()],
    publicDir: false,
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        cors: true,
        headers: {
            'Access-Control-Allow-Origin': '*',
        },
        watch: {
            usePolling: true,
        },
    },
    build: {
        manifest: 'manifest.json',
        outDir: 'public/build',
        emptyOutDir: true,
        rollupOptions: {
            input: {
                app: 'resources/js/vite-app.js',
            },
        },
    },
});
