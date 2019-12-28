<?php

exit;

//Headers
// header('Content-type: application/x-gzip');
// header('Content-Disposition: attachment; filename="backup.tar.gz"');

// LET OP: CREEERT BACKUP. Nu zelf downloaden.

//tar-command maken
$cmd = "tar cz ../ > backup.tar.gz"; // use modifier z for compressing

passthru($cmd);

exit;
