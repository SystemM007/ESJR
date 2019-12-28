<?

if (isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] === "dev") {
    return array(
        'mysql_host' => 'esjr-mysql',
        'mysql_user' => 'root',
        'mysql_passwd' => '',
        'mysql_database' => 'esjr_nl_esjr',
    );
}

return array(
    'mysql_host' => 'localhost',
    'mysql_user' => 'esjr_nl_esjr',
    'mysql_passwd' => '43uXCq83FsuK',
    'mysql_database' => 'esjr_nl_esjr',
);
