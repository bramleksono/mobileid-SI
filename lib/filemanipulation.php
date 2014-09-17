<?php
function clean($string) {
    return $string = trim(preg_replace('/\s+/', ' ', $string));
}

function findline($search,$inputfile) {
    $lines       = file($inputfile);
    $line_number = false;

    while (list($key, $line) = each($lines) and !$line_number) {
       $line_number = (strpos($line, $search) !== FALSE) ? $key + 1 : $line_number;
    }
    //nomor line diubah sehingga mulai dari 0
    return ($line_number-1);
}

function getline($linenumber,$inputfile) {
    $lines = file( $inputfile , FILE_IGNORE_NEW_LINES );
    return $lines[$linenumber];
}

function writeline($searchline,$newline,$inputfile) {
    //fungsi untuk mencatat pada file.
    //jika line tidak ditemukan, dibuat baris baru.
    //jika line ditemukan, fungsi menimpa (overwrite) line lama
    $linenumber = findline($searchline,$inputfile);
    $lines = file( $inputfile , FILE_IGNORE_NEW_LINES );
    $lines[$linenumber] = $newline;
    file_put_contents( $inputfile , implode( "\n", $lines ) );
}

function deleteline($inputline,$inputfile) {
    //input line harus exact. pastikan cari string sebelum dihapus
    $files = file($inputfile);

    $new_file = array();
    foreach ($files as $line) {
        if (!strcmp(clean($line),$inputline)) {
            //If you delete the line, do not push array and do nothing
        } else {
            $new_file[] = $line; // push line new array
        }
     }
    file_put_contents($inputfile, $new_file);
}
?>