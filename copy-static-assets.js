import { copyFile, mkdir, readdir } from 'fs/promises';
import { existsSync, readdirSync } from 'fs';
import { join } from 'path';

const publicDir = './public';
const cssDir = join(publicDir, 'css');
const jsDir = join(publicDir, 'js');
const webfontsDir = join(publicDir, 'webfonts');
const nodeModulesFontawesome = './node_modules/@fortawesome/fontawesome-free/webfonts';

// Файлы для копирования (если они есть в public/)
const cssFilesToKeep = ['dashboard.css', 'sort-arrows.css', 'gallery.css', 'auth.css'];
const jsFilesToKeep = ['gallery.js', 'axios.min.js'];

async function copyStaticAssets() {
    console.log('Копирование статических CSS/JS файлов...');

    // Создаем директории если их нет
    if (!existsSync(cssDir)) {
        await mkdir(cssDir, { recursive: true });
    }
    if (!existsSync(jsDir)) {
        await mkdir(jsDir, { recursive: true });
    }
    if (!existsSync(webfontsDir)) {
        await mkdir(webfontsDir, { recursive: true });
    }

    // Копируем шрифты Font Awesome
    if (existsSync(nodeModulesFontawesome)) {
        const fontFiles = readdirSync(nodeModulesFontawesome);
        for (const file of fontFiles) {
            const source = join(nodeModulesFontawesome, file);
            const dest = join(webfontsDir, file);
            await copyFile(source, dest);
            console.log(`Копируем шрифт: ${file}`);
        }
    }

    console.log('Статические файлы готовы к использованию.');
}

copyStaticAssets().catch(console.error);
