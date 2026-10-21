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

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Hazelchat\Contao\Key;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The website root takes the pasted installation code or just its key, and stores
 * only the key. Anything else is refused with one plain sentence, so the owner
 * knows at once instead of finding no chat on the site.
 */
#[AsCallback(table: 'tl_page', target: 'fields.hazelchatKey.save')]
final class InstallationCodeListener
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function __invoke(mixed $value): string
    {
        if (!\is_string($value) || '' === trim($value)) {
            return '';
        }

        return Key::fromInput($value) ?? throw new \RuntimeException($this->translator->trans('tl_page.hazelchatInvalid', [], 'contao_tl_page'));
    }
}
