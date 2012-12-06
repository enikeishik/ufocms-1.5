<?php
/*
algorithm
1. init PHP
2. init debug
3. get core obect
4. try use cache
5. get page data (execute module to generate main page data, execute insertions and inclusions to generate page blocks data)
6. render page
7. reindex and save cache if needed
8. debug output
9. shutdown routines
*/

//index.php
require_once 'config.php';
require_once 'classes/UfoTools.php';
require_once 'classes/UfoCore.php';
UfoCore::main();
