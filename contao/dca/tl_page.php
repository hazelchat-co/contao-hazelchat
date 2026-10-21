<?php

declare(strict_types=1);

/*
 * This file is part of the Hazelchat bundle for Contao.
 *
 * (c) 2026 Hazelchat
 *
 * @license LGPL-3.0-or-later
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Hazelchat\Contao\Key;

// The website root holds the code; every page below it gets the chat. The save
// callback (InstallationCodeListener) stores only the key, so the raw request
// data never reaches the database.
$GLOBALS['TL_DCA']['tl_page']['fields']['hazelchatKey'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => Key::MAX_INPUT, 'useRawRequestData' => true, 'tl_class' => 'clr long'],
    'sql' => ['type' => 'string', 'length' => 64, 'default' => '', 'platformOptions' => ['collation' => 'ascii_bin']],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['hazelchatConsent'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => false],
];

PaletteManipulator::create()
    ->addLegend('hazelchat_legend', 'publish_legend', PaletteManipulator::POSITION_BEFORE)
    ->addField(['hazelchatKey', 'hazelchatConsent'], 'hazelchat_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('root', 'tl_page')
    ->applyToPalette('rootfallback', 'tl_page')
;
