#!/bin/bash

phpunit --colors --strict ./functional/app_test.php

phpunit --colors --strict ./functional/db/pdo/mysql_test.php
phpunit --colors --strict ./functional/db/pdo/pgsql_test.php
phpunit --colors --strict ./functional/db/pdo/sqlite_test.php