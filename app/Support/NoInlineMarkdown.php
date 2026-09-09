<?php

namespace App\Support;

use Illuminate\Mail\Markdown;

/**
 * The live server has no ext-dom loaded, which
 * tijsverkoyen/css-to-inline-styles (Laravel's default CSS inliner for
 * markdown mail) requires via DOMDocument — so every markdown Mailable was
 * failing to render at all ("Class DOMDocument not found"). This swaps in a
 * plain pass-through "inliner" that prepends the theme CSS as a <style>
 * block instead of inlining it per-element. Every modern mail client (Gmail,
 * Apple Mail, Outlook.com, mobile clients) renders a <head><style> block
 * fine — only very old Outlook desktop versions don't — and a working email
 * beats none at all. See AppServiceProvider for where this is bound in
 * place of the stock Markdown class.
 */
class NoInlineMarkdown extends Markdown
{
    public function render($view, array $data = [], $inliner = null)
    {
        return parent::render($view, $data, $inliner ?: new class
        {
            public function convert(string $html, string $css): string
            {
                return "<style>{$css}</style>{$html}";
            }
        });
    }
}
