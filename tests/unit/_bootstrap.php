<?php
// Here you can initialize variables that will for your tests

//устанавливаем часовой пояс, to avoid warning in date function
date_default_timezone_set('Europe/Moscow');

require_once __DIR__ . '/../../_core/cachefs.php';
require_once __DIR__ . '/../../_core/captchafs.php';
require_once __DIR__ . '/../../_core/calendar.php';
require_once __DIR__ . '/../../_core/importcbr.php';
require_once __DIR__ . '/../../_core/importgismeteo.php';
require_once __DIR__ . '/../../_core/importrss.php';
require_once __DIR__ . '/../../_core/db.php';
require_once __DIR__ . '/../../_core/tools.php';
require_once __DIR__ . '/../../_core/insertions.php';
require_once __DIR__ . '/../../config.php';
