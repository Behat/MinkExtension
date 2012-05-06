<?php

/*
 * This file is part of the Behat\MinkExtension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

$phar = new \Phar('behat-mink-extension.phar', 0, 'behat-mink-extension.phar');
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

Phar::mapPhar('behat-mink-extension.phar');

return require_once 'phar://behat-mink-extension.phar/init.php';

__HALT_COMPILER();
STUB
);
$phar->stopBuffering();

unset($phar);

function addFileToPhar($phar, $path) {
    $phar->addFromString($path, __DIR__.'/'.$path);
}
