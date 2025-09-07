<?php
if ($argc != 2) {
    echo "必须指定版本号";
    exit;
}

$version = $argv[1];

$dirs = array(".");

$ignores = array(
    '.gitignore',
    'data',
    'README.md',
    'nginx.conf',
    'build.php',
    'install.php',
);

$files = '';

build($dirs, $files);

$template = file_get_contents("core/installer.php");
$template = str_replace('/*DOUPRESS_VERSION*/', $version, $template);
$template = str_replace('/*DOUPRESS_FILES*/', $files, $template);

file_put_contents("install.php", $template);
echo "安装文件已保存到 install.php \n";

function build($dirs, &$files)
{
    global $ignores;

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            echo "目录\"$dir\"不存在";
            exit;
        }

        if ($dh = opendir($dir)) {
            $sub_dirs = array();

            while (($item = readdir($dh)) !== false) {
                // 保留 .htaccess 文件，移除其他 .开头的隐藏文件
                if ($item[0] == '.' && $item != '.htaccess') {
                    continue;
                }

                if ($dir == '.') {
                    $file = $item;
                } else {
                    $file = $dir . "/" . $item;
                }
                // 忽略文件
                if (in_array($file, $ignores)) {
                    continue;
                }

                if (is_dir($file)) {
                    $sub_dirs[] = $file;
                } else {
                    $files .= "install('$file', '";
                    $files .= base64_encode(gzcompress(file_get_contents($file)));
                    $files .= "');\n";
                    echo "已添加文件 $file\n";
                }
            }

            closedir($dh);
            build($sub_dirs, $files);
        } else {
            echo "目录\"$dir\"无法访问";
            exit;
        }
    }
}
