=== WordPress Codex Bridge ===
Contributors: spiritualcodex
Tags: api, webhook, shortcode, logging
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Connects WordPress with an external Codex API: provides a secure webhook receiver, a shortcode ([codex_soul_decoder]) that calls the Codex API, an admin settings screen for configuring the API URL and key, and logs to wp-content/uploads/codex-logs.

== Description ==
The Codex Bridge plugin makes it simple to integrate an external "Codex" service with WordPress. It provides:

* A REST webhook endpoint at /wp-json/codex/v1/webhook
* A shortcode [codex_soul_decoder] that displays a small form for sending text to the Codex API and rendering results
* Admin settings page (Settings → Codex Bridge) where you can safely configure the API URL and API key
* Logging to wp-content/uploads/codex-logs/

== Installation ==
1. Copy the `wordpress-codex-bridge` folder into your site's `wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to Settings → Codex Bridge and provide your Codex API URL and optional API Key.

== Usage ==
Shortcode:

[codex_soul_decoder]

This will render a small text box and a Decode button. Submitted text is sent to the configured Codex API and results are displayed inline.

REST Webhook:

The plugin exposes a webhook at the REST route: `/wp-json/codex/v1/webhook` (method: POST).

If you provide an API Key in the settings, incoming webhook requests must include the header `X-Codex-Api-Key` with the same key.

== Debugging and Logs ==
Logs are written to `wp-content/uploads/codex-logs/` and are viewable inside the plugin's settings screen.

== Frequently Asked Questions ==
Q: Will this delete my data when deactivated?
A: No — the plugin leaves settings in place so you can reactivate without reconfiguration.

== Changelog ==
= 0.1.0 =
* Initial release
