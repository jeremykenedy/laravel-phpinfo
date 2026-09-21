const fs = require('node:fs');
const path = require('node:path');

const root = path.resolve(__dirname, '../src/resources/assets');
const css = fs.readFileSync(path.join(root, 'css/php-info.css'), 'utf8');
const outputs = {
    'scss/_php-info.scss': css,
    'css/php-info.min.css': css.replace(/\s+/g, ' ').replace(/\s*([{}:;,])\s*/g, '$1').trim() + '\n',
};

for (const [file, content] of Object.entries(outputs)) {
    const destination = path.join(root, file);
    if (process.argv.includes('--check')) {
        if (fs.readFileSync(destination, 'utf8') !== content) {
            throw new Error(`${file} is out of date. Run npm run build.`);
        }
    } else {
        fs.writeFileSync(destination, content);
    }
}
