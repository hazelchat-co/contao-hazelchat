# Hazelchat for Contao

Adds your [Hazelchat](https://hazelchat.co/?utm_source=contao&utm_medium=extension&utm_campaign=readme) AI chat agent to a Contao website from one pasted installation code. The agent answers visitors from the pages you approved, shows its sources and passes on their enquiries.

- Contao 5.3 LTS, 5.7 LTS and 6.0, with classic and Twig page layouts.
- Needs a Hazelchat account. The Free plan covers one website and stays free.
- Hosted service: visitors' browsers load the chat from `app.hazelchat.co`, and Hazelchat processes their questions and any enquiry details they send ([privacy notice](https://hazelchat.co/privacy/), [terms](https://hazelchat.co/terms/)). The bundle itself sends nothing.

## Install

1. In Hazelchat, add your website and copy its installation code ([create a free account](https://app.hazelchat.co/signup?utm_source=contao&utm_medium=extension&utm_campaign=readme-signup)).
2. Install the bundle: search for **Hazelchat** in the Contao Manager and install it, or run `composer require hazelchat/contao-hazelchat` followed by `vendor/bin/contao-console contao:migrate`.
3. In the Contao back end, open **Site structure**, edit your website root and paste the code under **Hazelchat > Installation code**. Save.
4. Back in Hazelchat, click **Publish and switch on**. Hazelchat finds the chat on your live page and verifies the website.

## What it does

- Every page below a website root with a code gets one tag before `</body>`:
  `<script id="hazelchat-contao" data-agent="<key>" src="https://app.hazelchat.co/widget.js" defer></script>`
- **Installation code** takes the pasted code or just its 64-character key, and stores only the key. Anything else is refused with a message when you save.
- One root per language on the same domain: paste the code once. Roots without a code use it; a code saved in another root wins there. Each other domain uses its own code.
- **Contao Cookiebar** (Oveleon): add a cookie of the type **Hazelchat** to a group of your banner. The chat then starts only after the visitor agrees there, with no script to paste.
- **Wait for my cookie banner** (off), for any other banner, adds `data-require-consent="true"`: the chat starts only once your banner allows it. See [Cookie banners](https://hazelchat.co/integrations/contao/#consent).
- In both cases only the chat waits: browsers still download `widget.js` from `app.hazelchat.co` straight away, so the visitor's IP address reaches Hazelchat before they agree. This is by design. If your site must prevent that too, use a consent tool that can block the script itself.
- The script tag carries the id `hazelchat-contao`. When Hazelchat checks your website, it reads this id to record that the chat was installed with this bundle, and it counts the bundle's installations that way.
- If the website root's Content-Security-Policy is on, the bundle allows `https://app.hazelchat.co` in `script-src`, `frame-src` and `connect-src`.
- Nothing in the back end, no new tables (two fields on the website root), no requests from Contao to Hazelchat, no telemetry.

## Remove

Empty the installation code and save, or remove the bundle in the Contao Manager. The chat no longer appears on newly rendered pages.

## Deutsch

Hazelchat fügt Ihren KI-Chat-Agenten mit einem einzigen Installationscode in Ihre Contao-Website ein (Contao 5.3, 5.7 und 6.0). Installieren Sie das Bundle im Contao Manager (Suche: **Hazelchat**) oder mit `composer require hazelchat/contao-hazelchat`, öffnen Sie unter **Seitenstruktur** Ihren Startpunkt der Webseite und fügen Sie unter **Hazelchat > Installationscode** den Code aus Hazelchat ein. Bei mehreren Sprachen auf derselben Domain genügt ein Startpunkt. Mit der Contao Cookiebar fügen Sie Ihrem Banner ein Cookie vom Typ **Hazelchat** hinzu; der Chat startet dann erst nach der Zustimmung. Die Browser Ihrer Besucher laden den Chat von `app.hazelchat.co`; deren Fragen und Anfragedaten verarbeitet Hazelchat. Mit der Cookiebar und mit dem Schalter für andere Banner wartet nur der Chat: Der Browser lädt `widget.js` sofort, und die IP-Adresse des Besuchers erreicht Hazelchat schon vor der Einwilligung. Über die ID `hazelchat-contao` im Skript-Tag zählt Hazelchat die Installationen dieses Bundles. Sie brauchen ein Hazelchat-Konto; der Free-Tarif gilt für eine Website und bleibt kostenlos.

## Licence

LGPL-3.0-or-later. Contao is a registered trademark of the Contao Association. Hazelchat is not affiliated with or endorsed by it.
