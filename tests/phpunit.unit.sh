#!/bin/bash

phpunit --bootstrap bootstrap.php --colors --strict ./unit/app_test.php
phpunit --bootstrap bootstrap.php --colors --strict ./unit/app/compiler_test.php
phpunit --bootstrap bootstrap.php --colors --strict ./unit/app/controller_test.php
phpunit --bootstrap bootstrap.php --colors --strict ./unit/app/dispatcher_test.php
phpunit --bootstrap bootstrap.php --colors --strict ./unit/app/helper_test.php
phpunit --bootstrap bootstrap.php --colors --strict ./unit/app/router_test.php
phpunit --bootstrap bootstrap.php --colors --strict ./unit/app/route_test.php
phpunit --bootstrap bootstrap.php --colors --strict ./unit/app/settings_test.php

phpunit --bootstrap bootstrap.php --colors --strict ./unit/db/model_test.php
phpunit --bootstrap bootstrap.php --colors --strict ./unit/db/pdo_test.php

phpunit --bootstrap bootstrap.php --colors --strict ./unit/http/request_test.php
phpunit --bootstrap bootstrap.php --colors --strict ./unit/http/response_test.php

phpunit --bootstrap bootstrap.php --colors --strict ./unit/view_test.php
