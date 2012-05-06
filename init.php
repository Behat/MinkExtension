<?php

/*
 * This file is part of the Behat\MinkExtension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require_once __DIR__.'/src/Behat/MinkExtension/Compiler/SelectorsPass.php';
require_once __DIR__.'/src/Behat/MinkExtension/Compiler/SessionsPass.php';
require_once __DIR__.'/src/Behat/MinkExtension/Context/MinkAwareContextInterface.php';
require_once __DIR__.'/src/Behat/MinkExtension/Context/MinkAwareContextInitializer.php';
require_once __DIR__.'/src/Behat/MinkExtension/Context/RawMinkContext.php';
require_once __DIR__.'/src/Behat/MinkExtension/Context/MinkContext.php';
require_once __DIR__.'/src/Behat/MinkExtension/Configuration.php';
require_once __DIR__.'/src/Behat/MinkExtension/Extension.php';

return new Behat\MinkExtension\Extension;
