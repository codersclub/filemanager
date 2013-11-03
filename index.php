<?php
session_start();

// to restrict Access, simply check for some previously defined Sessions and exit if not set.
// if (!isset($_SESSION['login_to_filemanager_is_ok'])){ exit('<h1>no access</h1>'); }

require_once('./config.php');
require_once('./lang/' . $lang . '.php');

//////////////////////////////////////////////// DO NOT TOUCH ///////////////////////////////////////////////////
$errors = array();

function showList($path)
{
    global $basepath, $labels, $hiddenFiles, $badExtensions;

    $fullpath = $basepath . '/' . $path;
    $fulldir = str_replace('\\', '/', realpath($basepath)) . '/' . $path;

//echo 'path=', $path, "<br>";
//echo 'fullpath=', $fullpath, "<br>";
//echo 'fulldir=', $fulldir, "<br>";

    if ($handle = opendir($fullpath)) {
        $up = trim(dirname($path), '/.');

        if (strlen($path) > 0) {
            echo '<tr><td colspan="5"><img src="style/go-up.png" class="ico"> <a href="' . $_SERVER['PHP_SELF'] . '?path=' . $up . '">' . $labels['level_up'] . "</a></td></tr>\n";
        }

        while (false !== ($file = readdir($handle))) {

            if ($file != "." && $file != ".." && !in_array(basename($file), $hiddenFiles)) {

                $filepath = trim($fullpath . '/' . $file, '/');
                $relpath = trim($path . '/' . $file, '/');

                if (is_file($filepath)) {
                    $i = pathinfo($filepath);
                    if (!in_array($i['extension'], $badExtensions)) {
                        echo '<tr>'
                            // Mime-Icon and Filename with Link
                            . '<td><span class="ico ' . $i['extension'] . '"></span> <a target="_blank" href="' . $filepath . '">' . $file . '</a></td>'
                            // show creation-Date
                            . '<td align="right">' . date("d-m-Y H:i:s", filemtime($filepath)) . '</td>'
                            // show Filesize
                            . '<td align="right">' . size($filepath) . '</td>'
                            // create a Button to delete the File
                            . '<td align="right"><img title="' . $labels['delete_file'] . '" class="button" onclick="del(\'' . $file . '\')" src="style/delete.png" alt="delete"></td>'
                            // create a Button to transfer Filepaths (e.g. to a parent Window)
                            . '<td align="right"><img title="' . $labels['get_filepath'] . '" class="button" onclick="get(\'' . $relpath . '\')" src="style/ok.png" alt="get"></td>'
                            . "</tr>\n";
                    }
                } elseif (is_dir($filepath)) {
                    echo '<tr><td colspan="5"><img class="ico" src="style/folder.png" alt="dir"> <a href="' . $_SERVER['PHP_SELF'] . '?path=' . $relpath . '">' . $file . "</a></td></tr>\n";
                }
            }
        }

        closedir($handle);
    }

}

function size($path)
{
    $bytes = sprintf('%u', filesize($path));

    if ($bytes > 0) {
        $unit = intval(log($bytes, 1024));
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        if (array_key_exists($unit, $units) === true) {
            return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
        }
    }

    return $bytes;
}


$actpath = isset($_GET['path']) ? str_replace('..', '', $_GET['path']) : '';

//echo 'actpath=', $actpath, "<br>";

if (isset($_GET['action'])) {
    // create Directory
    if ($_GET['action'] == 'cd' && isset($_POST['newdir'])) {
        $newPath = $basepath . '/' . $actpath . '/' . preg_replace('/\W/', '', $_POST['newdir']);
        if (!mkdir($newPath, 0776)) {
            $errors[] = $labels['dir_not_writable'];
        }
    }

    // File-Upload
    if ($_GET['action'] == 'uf' && is_uploaded_file($_FILES['file']['tmp_name'])) {
        $i = pathinfo($_FILES['file']['name']);
        if (!in_array($i['extension'], $badExtensions)) {
            $origin = strtolower(basename(preg_replace('/[^0-9a-z.]+/i', '', $_FILES['file']['name'])));
            $fulldest = $basepath . '/' . $actpath . '/' . $origin;
            $filename = $origin;
            // if the file already exists create a new name
            for ($i = 1; file_exists($fulldest); $i++) {
                $fileext = array_pop(explode('.', $origin));
                $filename = substr($origin, 0, strlen($origin) - strlen($fileext) - 1) . '[' . $i . '].' . $fileext;
                $fulldest = $basepath . '/' . $actpath . '/' . $filename;
            }

            if (!move_uploaded_file($_FILES['file']['tmp_name'], $fulldest)) {
                $errors[] = $labels['file_not_writable'];
            }
        } else {
            $errors[] = $labels['file_type_not_allowed'];
        }
    }

    // delete File
    if ($_GET['action'] == 'dl' && isset($_GET['filepath'])) {
        $fullpath = $basepath . '/' . $actpath . '/' . $_GET['filepath'];
        if (!@unlink($fullpath)) {
            $errors[] = $labels['file_not_deletable'] . htmlspecialchars($_GET['filepath']);
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>File Browser</title>
    <meta charset="utf-8">
    <link href="style/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="main">
    <?php
    foreach ($errors as $error) {
        echo '<p class="error">' . $error . '</p>';
    }
    ?>
    <div id="result">
        <table width="100%">
            <?php
            showList($actpath);
            ?>
        </table>
    </div>
    <form method="post" action="<?= $_SERVER['PHP_SELF'] . '?action=cd&path=' . $actpath; ?>">
        <input type="text" name="newdir" placeholder="<?= $labels['directory_name'] ?>">
        <input type="submit" value="<?= $labels['create_new_directory'] ?>">
    </form>
    <form method="post" action="<?= $_SERVER['PHP_SELF'] . '?action=uf&path=' . $actpath; ?>"
          enctype="multipart/form-data">
        <input type="file" name="file">
        <input type="submit" value="<?= $labels['upload_file'] ?>">
    </form>
</div>
<script>
    // ask the User & delete the File
    function del(path) {
        var q = confirm('<?= $labels['really_delete'] ?>');
        if (q) {
            window.location = '<?= $_SERVER['PHP_SELF'] . '?action=dl&path=' . $actpath; ?>&filepath=' + path;
        }
    }
    // get the File-Path (do something useful like transfer the File-Link to a Text-Editor)
    function get(path) {
        alert(path)
    }
</script>
</body>
