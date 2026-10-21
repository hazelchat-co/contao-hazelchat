<?php

declare(strict_types=1);

/*
 * This file is part of the Hazelchat bundle for Contao.
 *
 * (c) 2026 Hazelchat
 *
 * @license LGPL-3.0-or-later
 */

namespace Hazelchat\Contao\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;

/**
 * Contao Cookiebar (oveleon/contao-cookiebar) gets a cookie type "Hazelchat": the
 * owner adds it to a group of the banner, and the chat then starts only after the
 * visitor agrees there, with no script to paste. ChatListener sees the published
 * cookie and makes the tag wait for consent.
 */
#[AsHook('addCookieType')]
final class CookiebarListener
{
    public const TYPE = 'hazelchat';

    // Contao Cookiebar runs a stored consent before the chat's deferred script has
    // started, so the start signal waits for the document; a click on the banner
    // runs it at once.
    public const START = "(function (start) { document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', start) : start(); })(function () { window.dispatchEvent(new CustomEvent('hazelchat-consent', { detail: true })); });";

    public const STOP = "window.dispatchEvent(new CustomEvent('hazelchat-consent', { detail: false }));";

    // Oveleon\ContaoCookiebar\AbstractCookie::LOAD_CONFIRMED, LOAD_UNCONFIRMED and
    // POS_BELOW; the bundle works without Contao Cookiebar installed.
    private const LOAD_CONFIRMED = 1;

    private const LOAD_UNCONFIRMED = 2;

    private const POS_BELOW = 1;

    public function __invoke(string $type, object $cookie): void
    {
        if (self::TYPE !== $type || !method_exists($cookie, 'addScript')) {
            return;
        }

        $cookie->addScript(self::START, self::LOAD_CONFIRMED, self::POS_BELOW);
        $cookie->addScript(self::STOP, self::LOAD_UNCONFIRMED, self::POS_BELOW);
    }
}
