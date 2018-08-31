<?php

// database config
$config = [
    'host'     => '10.0.50.31',
    'user'     => '***',
    'password' => '***',
];

Export('***', $config);


/**
 * export database dictionary function
 *
 * @param String $dbname
 * @param Array $config
 * @return markdown
 */
function Export(String $dbname, Array $config) {

    echo "数据字典导出开始!".PHP_EOL;

    $title = $dbname.' 数据字典';
    $dsn   = 'mysql:dbname='.$dbname.';host='.$config['host'];

    try {
        $conn = new PDO($dsn, $config['user'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    } catch (PDOException $e) {
        die('Connection failed: ' . $e->getMessage());
    }

    echo "数据库连接成功!".PHP_EOL;
    $tables = $conn->query('SHOW tables')->fetchAll(PDO::FETCH_COLUMN);
    echo "共计".count($tables)."张表！".PHP_EOL;

    foreach ($tables as $table) {
        $_tables[]['TABLE_NAME'] = $table;
    }

    foreach ($_tables as $k => $v) {  

        $sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$dbname}'";
        $tr  = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);

        $_tables[$k]['TABLE_COMMENT'] = $tr['TABLE_COMMENT'];

        $sql          = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$dbname}'";
        $fields       = [];
        $field_result = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($field_result as $fr)
        {
            $fields[] = $fr;
        }
        $_tables[$k]['COLUMN'] = $fields;
    }  
    unset($conn);

    $mark = '';

    // tables
    foreach ($_tables as $k => $v) {  

        $mark .= '## '.$v['TABLE_NAME'].' '.$v['TABLE_COMMENT'].PHP_EOL;
        $mark .= ''.PHP_EOL;
        $mark .= '|  字段名  |  数据类型  |  默认值  |  允许非空  |  自动递增  |  备注  |'.PHP_EOL;
        $mark .= '| ------ | ------ | ------ | ------ | ------ | ------ |'.PHP_EOL;
        foreach ($v['COLUMN'] as $f) {  
            $mark .= '| '.$f['COLUMN_NAME'].' | '.$f['COLUMN_TYPE'].' | '.$f['COLUMN_DEFAULT'].' | '.$f['IS_NULLABLE'].' | '.($f['EXTRA'] == 'auto_increment' ? '是' : '').' | '.(empty($f['COLUMN_COMMENT']) ? '-' : str_replace('|', '/', $f['COLUMN_COMMENT'])).' |'.PHP_EOL;
        }  
        $mark .= ''.PHP_EOL;

    }  

    $out_time = date('Y-m-d H:i:s');
    // markdown
    $md_tplt = <<<EOT
# {$title}
>   导出时间: {$out_time}

{$mark}
EOT;

    file_put_contents($dbname.'.md', $md_tplt);
    echo "{$dbname}.md 导出成功!".PHP_EOL;
    echo "导出结束!".PHP_EOL;
}