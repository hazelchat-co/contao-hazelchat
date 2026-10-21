<?php

declare(strict_types=1);

/*
 * This file is part of the Hazelchat bundle for Contao.
 *
 * (c) 2026 Hazelchat
 *
 * @license LGPL-3.0-or-later
 */

namespace Hazelchat\Contao;

/**
 * The public widget key and the one script tag, as plain functions with no Contao
 * dependency, so they can be tested with PHP alone. The key is public: it names
 * an agent and grants no access to the Hazelchat account.
 */
final class Key
{
    // The only address this bundle ever prints.
    public const SCRIPT_URL = 'https://app.hazelchat.co/widget.js';

    // The origin a site's Content-Security-Policy must allow.
    public const ORIGIN = 'https://app.hazelchat.co';

    // Hazelchat counts installations of this bundle by this id.
    public const SCRIPT_ID = 'hazelchat-contao';

    // Room for a pasted installation code; the key itself is 64 characters.
    public const MAX_INPUT = 1000;

    public static function valid(mixed $key): bool
    {
        return \is_string($key) && 1 === preg_match('/\A[a-f0-9]{64}\z/', $key);
    }

    /**
     * The setting takes the whole installation code or just the key. A bare 64-hex value is
     * the key; otherwise the text must hold exactly one data-agent attribute (outside HTML
     * comments) whose value is 64 lowercase hex. Anything else gives null.
     */
    public static function fromInput(mixed $value): string|null
    {
        if (!\is_string($value)) {
            return null;
        }

        $value = trim($value);

        if ('' === $value || \strlen($value) > self::MAX_INPUT) {
            return null;
        }

        if (self::valid($value)) {
            return $value;
        }

        // A key that is only mentioned in a comment is not the code.
        $value = preg_replace('/<!--.*?-->/s', '', $value) ?? '';

        if (str_contains($value, '<!--')) {
            return null;
        }

        // Exactly one data-agent attribute; look-alikes such as xdata-agent or
        // data-agent-x are not it, and two are ambiguous.
        $attribute = '(?<![A-Za-z0-9_:-])data-agent(?![A-Za-z0-9_:-])\s*=';

        if (1 !== preg_match_all('/'.$attribute.'/i', $value)) {
            return null;
        }

        if (1 !== preg_match('/'.$attribute.'\s*(?:"([a-f0-9]{64})"|\'([a-f0-9]{64})\'|([a-f0-9]{64})(?=[\s>\/]|\z))/', $value, $match, PREG_UNMATCHED_AS_NULL)) {
            return null;
        }

        $key = $match[1] ?? $match[2] ?? $match[3];

        return self::valid($key) ? $key : null;
    }

    /**
     * The script tag, or null when nothing may be printed. No inline code, no
     * data-platform and no address but SCRIPT_URL; the key is 64 hex characters, so
     * nothing in it needs escaping.
     */
    public static function tag(mixed $key, bool $requireConsent): string|null
    {
        if (!\is_string($key) || !self::valid($key)) {
            return null;
        }

        return \sprintf(
            '<script id="%s" data-agent="%s"%s src="%s" defer></script>',
            self::SCRIPT_ID,
            $key,
            $requireConsent ? ' data-require-consent="true"' : '',
            self::SCRIPT_URL,
        );
    }
}
