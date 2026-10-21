<?php

declare(strict_types=1);

/*
 * This file is part of the Hazelchat bundle for Contao.
 *
 * (c) 2026 Hazelchat
 *
 * @license LGPL-3.0-or-later
 */

use Hazelchat\Contao\EventListener\CookiebarListener;

// Contao Cookiebar's cookie types gain "Hazelchat" (CookiebarListener). The chat
// sets no cookies, so the type asks for no token or lifetime.
if (isset($GLOBALS['TL_DCA']['tl_cookie']['fields']['type']['options'])) {
    $GLOBALS['TL_DCA']['tl_cookie']['fields']['type']['options'][] = CookiebarListener::TYPE;
    $GLOBALS['TL_DCA']['tl_cookie']['palettes'][CookiebarListener::TYPE] = '{title_legend},title,type,provider,showProvider;{description_legend:collapsed},description,detailDescription;{expert_legend:collapsed},priority;{publish_legend},published,checked;';
}
