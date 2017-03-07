<?php
/* Initialize Sqlite DB */
$sqli = new SQLite3("$path/common/$dbfilename");
if (!$sqli) {
    die("err1");
}
$sql = <<<EOF
      CREATE TABLE IF NOT EXISTS IPLOG
      (ID INTEGER PRIMARY KEY AUTOINCREMENT,
      IPADDR           TEXT    NOT NULL,
      GEOLOC            TEXT     NOT NULL,
      REQUESTDATA        TEXT NOT NULL,
      COUNT         INT NOT NULL,
      CREATED_ON    INT NOT NULL,
      UPDATED_ON    INT NULL,
      FLAGGED       INT NULL);
EOF;

$sqli->exec($sql);

