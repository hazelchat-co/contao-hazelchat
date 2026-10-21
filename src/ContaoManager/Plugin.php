<?php

declare(strict_types=1);

/*
 * This file is part of the Hazelchat bundle for Contao.
 *
 * (c) 2026 Hazelchat
 *
 * @license LGPL-3.0-or-later
 */

namespace Hazelchat\Contao\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Hazelchat\Contao\HazelchatBundle;

/**
 * Lets the Contao Manager load the bundle without any configuration.
 */
final class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            // After Contao Cookiebar, if installed, so its cookie types can gain
            // "Hazelchat" (contao/dca/tl_cookie.php).
            BundleConfig::create(HazelchatBundle::class)->setLoadAfter([ContaoCoreBundle::class, 'Oveleon\\ContaoCookiebar\\ContaoCookiebar']),
        ];
    }
}
