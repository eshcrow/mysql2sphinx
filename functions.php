<?php

function remove_NULL(&$array) {
    foreach ($array as &$find_null) {
        if ($find_null === NULL) {
            $find_null = '';
        }
    }
}

function need_start() {
    return (sphinx_index_count() != mysql_table_count);
}

function TimeSince($original) { // $original should be the original date and time in Unix format
    // Common time periods as an array of arrays
    $periods = array(
        array(60 * 60 * 24 * 365, 'year'),
        array(60 * 60 * 24 * 30, 'month'),
        array(60 * 60 * 24 * 7, 'week'),
        array(60 * 60 * 24, 'day'),
        array(60 * 60, 'hour'),
        array(60, 'minute'),
        array(1, 'second'));

    $today = time();
    $since = $today - $original; // Find the difference of time between now and the past
    // Loop around the periods, starting with the biggest
    for ($i = 0, $j = count($periods); $i < $j; $i++) {
        $seconds = $periods[$i][0];
        $name = $periods[$i][1];

        // Find the biggest whole period
        if (($count = floor($since / $seconds)) != 0) {
            break;
        }
    }

    $output = ($count == 1) ? '1 ' . $name : "$count {$name}s";

    if ($i + 1 < $j) {
        // Retrieving the second relevant period
        $seconds2 = $periods[$i + 1][0];
        $name2 = $periods[$i + 1][1];

        // Only show it if it's greater than 0
        if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
            $output .= ($count2 == 1) ? ', 1 ' . $name2 : ", $count2 {$name2}s";
        }
    }
    return $output;
}

function mysql2sphinx_realtime_index() {
    global $mysql_config;
    global $sphinx_config;
    $offset = sphinx_index_count();
    $sphinx_config->error_handler = false;

    if (!need_start()) {
        return;
    }

    do {
        $select_query = $mysql_config->query("select * from angelco2 limit $offset,1000");
        $offset += 1000;

        foreach ($select_query as &$value) {
            $tmp = $value[mysql_config::$id_column];
            unset($value[mysql_config::$id_column]);
            $value['id'] = $tmp;
            remove_NULL($value);
            $sphinx_config->insert(sphinx_config::$rt_index, $value);
        }
    } while (count($select_query) > 0);
}

function sphinx_index_count() {

    global $sphinx_config;
    $select_query = $sphinx_config->queryFirstRow("select count(*) as a from " . sphinx_config::$rt_index);

    return $select_query['a'];
}

function mysql_table_count() {

    global $mysql_config;
    $select_query = $mysql_config->queryFirstRow("select count(*) as a from " . mysql_config::$table);

    return $select_query['a'];
}

function mysql_columns() {
    global $mysql_config;
    $select_query = $mysql_config->query('select * from ' . mysql_config::$table . ' where 1=1 limit 1');
    if (count($select_query) == 0) {
        echo 'insert one row at least into the table';
        return;
    }
    $cols = [];
    foreach ($select_query as $row) {
        foreach ($row as $col => $value) {
            $cols[] = $col;
            $value = null;
        }
    }
    return $cols;
}

function mysql2sphinx_index() {
    $columns = mysql_columns();
    foreach ($columns as $col) {
        echo '	rt_field		= ' . $col . '<br/>';
    }

    echo '<br/>';
    foreach ($columns as $col) {
        echo '	rt_attr_string		= ' . $col . '<br/>';
    }
}
