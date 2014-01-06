<?php
session_start();

// to restrict Access, simply check for some previously defined Sessions and exit if not set.
// if (!isset($_SESSION['login_to_filemanager_is_ok'])){ exit('<h1>no access</h1>'); }

// Load configuration
require_once('./config.php');

// Load functions
require_once('./functions.php');

// Load language variables
require_once('./lang/' . $lang . '.php');

$errors = [];

$actpath = isset($_GET['path']) ? str_replace('..', '', $_GET['path']) : '';

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
    <link rel="stylesheet" href="style/style.css">
    <!-- Modal Styles -->
    <link rel="stylesheet" href="style/modal.css">
</head>
<body>
<div id="main">
    <ul class="menu">
        <li><a href="<?= $_SERVER['PHP_SELF'] ?>"><?= $labels['home_page'] ?></a></li>
        <li><a href="#create_dir"><?= $labels['create_dir_page'] ?></a></li>
        <li><a href="#upload"><?= $labels['upload_page'] ?></a></li>
        <li><a href="#other"><?= $labels['other_page'] ?></a></li>
    </ul>

    <?php
    foreach ($errors as $error) {
        echo '<p class="error">' . $error . '</p>';
    }
    ?>
    <div class="breadcrumbs"><? breadCrumbs($actpath) ?></div>

    <div id="result">
        <table width="100%">
            <tr align="center">
                <th><?= $labels['file_name'] ?></th>
                <th><?= $labels['file_date'] ?></th>
                <th><?= $labels['file_size'] ?></th>
                <th colspan="2"><?= $labels['action'] ?></th>
            </tr>

            <?php
            showList($actpath);
            ?>
        </table>
    </div>
</div>

<div class="modal" id="create_dir">
    <form class="modal-content" method="post" action="<?= $_SERVER['PHP_SELF'] . '?action=cd&path=' . $actpath ?>">
        <a href="#" class="close">×</a>
        <h2><?= $labels['create_new_directory'] ?></h2>
        <input type="text" name="newdir" placeholder="<?= $labels['dir_name'] ?>" required="required">
        <button type="submit"><?= $labels['create_new_directory'] ?></button>
    </form>
</div>

<div class="modal" id="upload">
    <form class="modal-content" method="post"
          action="<?= $_SERVER['PHP_SELF'] . '?action=uf&path=' . $actpath ?>"
          enctype="multipart/form-data">
        <a href="#" class="close">×</a>
        <h2><?= $labels['upload_file'] ?></h2>
        <input type="file" name="file" required="required">
        <button type="submit"><?= $labels['upload'] ?></button>
    </form>
</div>

<div class="modal" id="other">
    <form class="modal-content" method="post"
          action="<?= $_SERVER['PHP_SELF'] . '?action=uf&path=' . $actpath ?>"
          enctype="multipart/form-data">
        <a href="#" class="close">×</a>
        <h2><?= $labels['other_page'] ?></h2>
        <p><?= $labels['for_a_future'] ?></p>
    </form>
</div>



<script>
    // ask the User & delete the File
    function del(path) {
        var q = confirm('<?= $labels['really_delete'] ?>');
        if (q) {
            window.location = '<?= $_SERVER['PHP_SELF'] . '?action=dl&path=' . $actpath ?>&filepath=' + path;
        }
    }
    // get the File-Path (do something useful like transfer the File-Link to a Text-Editor)
    function get(path) {
        alert(path)
    }
</script>

</body>
</html>
