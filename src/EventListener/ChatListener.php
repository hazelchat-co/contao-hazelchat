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
use Contao\CoreBundle\Event\LayoutEvent;
use Contao\CoreBundle\Routing\ResponseContext\Csp\CspHandler;
use Contao\CoreBundle\Routing\ResponseContext\ResponseContextAccessor;
use Contao\PageModel;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DbalException;
use Hazelchat\Contao\Key;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * Adds the Hazelchat chat to every page of a website whose root holds an
 * installation code: one script from app.hazelchat.co with that website's public
 * widget key, at the end of the body.
 *
 * Classic page layouts run the generatePage hook; the Twig page layouts dispatch
 * the LayoutEvent instead (5.7.1 and later; composer.json refuses 5.6 and 5.7.0,
 * whose Twig layouts have neither). Both print $GLOBALS['TL_BODY'] at the end of
 * the body, and one fixed key keeps it to one tag. The tag is part of the
 * generated page and so of the page cache. Nothing is sent from Contao to
 * Hazelchat, and the back end never loads the chat.
 */
final class ChatListener
{
    public function __construct(
        private readonly Connection $connection,
        private readonly ResponseContextAccessor $responseContextAccessor,
    ) {
    }

    #[AsHook('generatePage')]
    public function onGeneratePage(PageModel $page): void
    {
        $this->addChat($page);
    }

    #[AsEventListener]
    public function onLayout(LayoutEvent $event): void
    {
        $this->addChat($event->getPage());
    }

    private function addChat(PageModel $page): void
    {
        $page->loadDetails();

        $tag = $this->tag((int) $page->rootId, (string) $page->domain, $this->cookiebarAsks((int) $page->rootId));

        if (null === $tag) {
            return;
        }

        $body = \is_array($GLOBALS['TL_BODY'] ?? null) ? $GLOBALS['TL_BODY'] : [];
        $body['hazelchat'] = $tag;
        $GLOBALS['TL_BODY'] = $body;

        // Only matters where the website root's Content-Security-Policy is on (it is off
        // by default): the chat's script, its frame and its requests come from
        // app.hazelchat.co. A directive the site does not set stays unset.
        $context = $this->responseContextAccessor->getResponseContext();

        if ($context?->has(CspHandler::class)) {
            $csp = $context->get(CspHandler::class);

            foreach (['script-src', 'frame-src', 'connect-src'] as $directive) {
                $csp->addSource($directive, Key::ORIGIN);
            }
        }
    }

    /**
     * The code saved in the page's website root. A website with one root per language
     * needs it only once: a root without a code uses the one saved in another
     * published root of the same domain, the fallback language first.
     */
    private function tag(int $rootId, string $domain, bool $requireConsent): string|null
    {
        $settings = $this->connection->fetchAssociative(
            'SELECT hazelchatKey, hazelchatConsent FROM tl_page WHERE id = ?',
            [$rootId],
        );

        if (!Key::valid($settings['hazelchatKey'] ?? null)) {
            $settings = $this->connection->fetchAssociative(
                "SELECT hazelchatKey, hazelchatConsent FROM tl_page WHERE type = 'root' AND dns = ? AND published = 1 AND hazelchatKey != '' ORDER BY fallback DESC, sorting",
                [$domain],
            );
        }

        if (false === $settings) {
            return null;
        }

        return Key::tag($settings['hazelchatKey'] ?? null, $requireConsent || (bool) ($settings['hazelchatConsent'] ?? false));
    }

    /**
     * Whether the Contao Cookiebar of this website root lists a published "Hazelchat"
     * cookie: then the chat waits for the visitor's consent there, without the owner
     * ticking "Wait for my cookie banner".
     */
    private function cookiebarAsks(int $rootId): bool
    {
        if (!class_exists('Oveleon\\ContaoCookiebar\\ContaoCookiebar')) {
            return false;
        }

        try {
            return false !== $this->connection->fetchOne(
                'SELECT 1 FROM tl_page p JOIN tl_cookie_group g ON g.pid = p.cookiebarConfig AND g.published = 1 JOIN tl_cookie c ON c.pid = g.id AND c.published = 1 AND c.type = ? WHERE p.id = ? AND p.activateCookiebar = 1',
                [CookiebarListener::TYPE, $rootId],
            );
        } catch (DbalException) {
            // Contao Cookiebar is installed, but the database is not updated yet.
            return false;
        }
    }
}
