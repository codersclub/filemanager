# Simple File-Manager

This is a simple File-Manager giving access to a Start-Folder

### Credits
* Basic code was taken from: [microFileBrowser](http://www.phptoys.com/download/micro-file-browser-script.html)
* Mime-Icons taken from: [firejune](//github.com/firejune/mime)

### Implementation

* copy config.sample.php file to config.php
* define a Session in your main App to restrict access
* check for this Session at the top of the Script
* define the Start-path in the config.php file
* if you want transfer Filepaths (e.g. to a parent Application like a Text Editor) adapt the JS-Funtion at the very bottom of the Script
* to restrict Access to some Files or Mimetypes simply adapt the Arrays $hiddenFiles and $badExtensions at the top of the Script
* to adapt Language-Labels simply edit/replace $labels (see label_storage.txt)

if you find security Issues let me know! *taubmann AT more-elements DOT com*

have fun

https://github.com/taubmann/filemanager
