const fs = require('fs');
let content = fs.readFileSync('page-sobre-mim.php', 'utf8');

const helperFunc = `get_header();

// Função para resolver Case Sensitivity dos SVGs no Linux Server
function get_actual_logo_uri($filename) {
    static $logos_map = null;
    $theme_uri = get_template_directory_uri();
    if ($logos_map === null) {
        $logos_map = [];
        $logos_dir = get_template_directory() . '/assets/images/logos';
        if (is_dir($logos_dir)) {
            $files = scandir($logos_dir);
            if ($files !== false) {
                foreach ($files as $f) {
                    if ($f !== '.' && $f !== '..') {
                        $logos_map[strtolower($f)] = $f;
                    }
                }
            }
        }
    }
    $lower_filename = strtolower($filename);
    if (isset($logos_map[$lower_filename])) {
        return $theme_uri . '/assets/images/logos/' . $logos_map[$lower_filename];
    }
    return $theme_uri . '/assets/images/logos/' . $filename;
}`;

if (!content.includes('get_actual_logo_uri')) {
    content = content.replace('get_header();', helperFunc);
    const regex = /<\?php echo get_template_directory_uri\(\);\ ?>\/assets\/images\/logos\/([a-zA-Z0-9_\-\(\)\.]+)/g;
    content = content.replace(regex, (match, filename) => {
        return `<?php echo get_actual_logo_uri('${filename}'); ?>`;
    });
    fs.writeFileSync('page-sobre-mim.php', content);
    console.log('Fixed page-sobre-mim.php successfully.');
} else {
    console.log('Already fixed.');
}
