<?php

/*
 * This file is part of the Behat\MinkExtension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

if (file_exists('mink_extension.phar')) {
    unlink('mink_extension.phar');
}

$phar = new \Phar('mink_extension.phar', 0, 'extension.phar');
$phar->setSignatureAlgorithm(\Phar::SHA1);
$phar->startBuffering();

addFileToPhar($phar, 'src/Behat/MinkExtension/Compiler/SelectorsPass.php');
addFileToPhar($phar, 'src/Behat/MinkExtension/Compiler/SessionsPass.php');
addFileToPhar($phar, 'src/Behat/MinkExtension/Context/MinkAwareContextInterface.php');
addFileToPhar($phar, 'src/Behat/MinkExtension/Context/MinkAwareContextInitializer.php');
addFileToPhar($phar, 'src/Behat/MinkExtension/Context/RawMinkContext.php');
addFileToPhar($phar, 'src/Behat/MinkExtension/Context/MinkContext.php');
addFileToPhar($phar, 'src/Behat/MinkExtension/Configuration.php');
addFileToPhar($phar, 'src/Behat/MinkExtension/Extension.php');
addFileToPhar($phar, 'src/Behat/MinkExtension/services/mink.xml');
addFileToPhar($phar, 'src/Behat/MinkExtension/services/sessions/goutte.xml');
addFileToPhar($phar, 'src/Behat/MinkExtension/services/sessions/sahi.xml');
addFileToPhar($phar, 'src/Behat/MinkExtension/services/sessions/zombie.xml');
addFileToPhar($phar, 'src/Behat/MinkExtension/services/sessions/selenium.xml');
addFileToPhar($phar, 'src/Behat/MinkExtension/services/sessions/selenium2.xml');
addFileToPhar($phar, 'init.php');

$phar->setStub(<<<STUB
<?php

/*
 * This file is part of the Behat\MinkExtension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

Phar::mapPhar('extension.phar');

return require 'phar://extension.phar/init.php';

__HALT_COMPILER();
STUB
);
$phar->stopBuffering();

function addFileToPhar($phar, $path) {
    $phar->addFromString($path, file_get_contents(__DIR__.'/'.$path));
}
