<?php

require_once 'config.php';

$file = fopen("runtime.lock", "w");

if (flock($file, LOCK_EX | LOCK_NB)) {
    $file_time = fopen("runtime.time", "w");
    fwrite($file_time, time());
    fclose($file_time);

    mysql2sphinx_realtime_index();
    flock($file, LOCK_UN);
} else {
    $file_time = file_get_contents('runtime.time');

    echo "Currently Executing  Inserted Data Count(" . sphinx_index_count() . ') time (' . TimeSince($file_time) . ')';
}

fclose($file);


