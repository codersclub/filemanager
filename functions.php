<?php
// FileManage Functions

// Show File Row
function showFile($row=[])
{
    global $basepath, $labels, $hiddenFiles, $badExtensions;

    if ($row['is_file']) {
        $i = pathinfo($filepath);
        if (!in_array($i['extension'], $badExtensions)) {
            echo '<tr align="right">'
                // Mime-Icon and Filename with Link
                . '<td align="left"><i class="ico ' . $row['icon'] . '"></i> <a target="_blank" href="' . $row['url'] . '">' . $row['file'] . '</a></td>'
                // show creation-Date
                . '<td>' . $row['date'] . '</td>'
                // show Filesize
                . '<td>' . $row['size'] . '</td>'
                // create a Button to delete the File
                . '<td><i class="ico delete" title="' . $labels['delete_file'] . '" onclick="del(\'' . $row['file'] . '\')"></td>'
                // create a Button to transfer Filepaths (e.g. to a parent Window)
                . '<td><i class="ico ok" title="' . $labels['get_filepath'] . '" onclick="get(\'' . $row['relpath'] . '\')"></td>'
                . "</tr>\n";
        }
    } else {
        echo '<tr><td><i class="ico ' . $row['icon'] . '"></i> <a href="' . $row['url'] . '">' . $row['file'] . '</a></td>'
           . '<td>&nbsp;</td>'
           . '<td>&nbsp;</td>'
           . '<td>&nbsp;</td>'
           . '<td>&nbsp;</td>'
           . "</tr>\n";
    }
}

// Show File List
function showList($path)
{
    global $basepath, $labels, $hiddenFiles, $badExtensions;

    $fullpath = $basepath . '/' . $path;
    $fulldir = str_replace('\\', '/', realpath($basepath)) . '/' . $path;

    $dirs = [];
    $files = [];

    if ($handle = opendir($fullpath)) {
        $up = trim(dirname($path), '/');
        if ($up == '.') { $up = ''; }

        if (strlen($path) > 0) {
            $dirs[] = [
                'icon' => 'go-up',
                'url' => $_SERVER['PHP_SELF'] . '?path=' . $up,
                'relpath' => $relpath,
                'file' => $labels['level_up'],
                'is_file' => 0,
            ];
        }

        while (false !== ($file = readdir($handle))) {

            if ($file != '.' && $file != '..' && !in_array(basename($file), $hiddenFiles)) {

                $filepath = trim($fullpath . '/' . $file, '/');
                $relpath = trim($path . '/' . $file, '/');

                if (is_file($filepath)) {
                    $i = pathinfo($filepath);
                    if (!in_array($i['extension'], $badExtensions)) {
                        $files[] = [
                            'icon' => $i['extension'],
                            'url' => $filepath,
                            'relpath' => $relpath,
                            'file' => $file,
                            'date' => date("d-m-Y H:i:s", filemtime($filepath)),
                            'size' => size($filepath),
                            'is_file' => 1,
                        ];
                    }
                } elseif (is_dir($filepath)) {
                    $dirs[] = [
                        'icon' => 'folder',
                        'url' => $_SERVER['PHP_SELF'] . '?path=' . $relpath,
                        'file' => $file,
                        'is_file' => 0,
                    ];
                }
            }
        }

        closedir($handle);

        $files = array_merge($dirs, $files);

        foreach($files as $row) {
            showFile($row);
        }

    }

}

// Calculate File Size
function size($path)
{
    $bytes = sprintf('%u', filesize($path));

    if ($bytes > 0) {
        $unit = intval(log($bytes, 1024));
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        if (array_key_exists($unit, $units) === true) {
            return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
        }
    }

    return $bytes;
}

// Show Breadcrumbs
function breadCrumbs($path='')
{
    global $labels;

    $crumbs = explode('/', $path);
    if(!empty($crumbs[0])) {
        array_unshift($crumbs, '');
    }

    echo $labels['actual_path'], ': ';

    $url = '';
    foreach($crumbs as $dir) {
        $name = empty($dir) ? $labels['root_dir'] : $dir;
        $url = empty($dir) ? '' : $url . (!empty($url) ? '/' : '') . $dir;
        $prefix = empty($dir) ? '' : '&raquo;';
        echo $prefix . '<a href="' . $url_SERVER['PHP_SELF'] . '?action=cd&path=' . $url . '">' . $name . '</a> ';
    }
}
