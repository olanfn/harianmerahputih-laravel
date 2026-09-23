<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class SafeRichText
{
    private const ALLOWED_TAGS = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'h2', 'h3', 'h4', 'ul', 'ol', 'li', 'blockquote', 'a', 'span', 'font'];

    public function clean(string $html): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8" ?><div id="safe-rich-text">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('safe-rich-text');
        if (! $root) {
            return '';
        }

        $this->sanitizeChildren($root);

        $clean = '';
        foreach ($root->childNodes as $child) {
            $clean .= $document->saveHTML($child);
        }

        return trim($clean);
    }

    private function sanitizeChildren(DOMNode $parent): void
    {
        foreach (iterator_to_array($parent->childNodes) as $node) {
            if ($node instanceof DOMElement) {
                $tag = strtolower($node->tagName);

                if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                    while ($node->firstChild) {
                        $parent->insertBefore($node->firstChild, $node);
                    }
                    $parent->removeChild($node);
                    continue;
                }

                $this->sanitizeAttributes($node, $tag);
                $this->sanitizeChildren($node);
            }
        }
    }

    private function sanitizeAttributes(DOMElement $element, string $tag): void
    {
        foreach (iterator_to_array($element->attributes) as $attribute) {
            $name = strtolower($attribute->name);
            $allowed = ($tag === 'a' && in_array($name, ['href', 'target', 'rel'], true))
                || ($tag === 'font' && $name === 'size')
                || ($tag === 'span' && $name === 'style');

            if (! $allowed) {
                $element->removeAttribute($attribute->name);
            }
        }

        if ($tag === 'a') {
            $href = trim($element->getAttribute('href'));
            if ($href !== '' && ! preg_match('/^(https?:\/\/|mailto:|\/|#)/i', $href)) {
                $element->removeAttribute('href');
            }
            if ($element->getAttribute('target') === '_blank') {
                $element->setAttribute('rel', 'noopener noreferrer');
            } else {
                $element->removeAttribute('target');
                $element->removeAttribute('rel');
            }
        }

        if ($tag === 'font') {
            $size = (int) $element->getAttribute('size');
            $element->setAttribute('size', (string) max(1, min(7, $size ?: 3)));
        }

        if ($tag === 'span' && $element->hasAttribute('style')) {
            $safeStyles = [];
            foreach (explode(';', $element->getAttribute('style')) as $declaration) {
                [$property, $value] = array_pad(explode(':', $declaration, 2), 2, '');
                $property = strtolower(trim($property));
                $value = strtolower(trim($value));
                if ($property === 'font-size' && preg_match('/^(10|12|14|16|18|20|24|28|32|36|40|48)px$/', $value)) $safeStyles[] = 'font-size: '.$value;
                if ($property === 'text-align' && in_array($value, ['left', 'center', 'right', 'justify'], true)) $safeStyles[] = 'text-align: '.$value;
            }
            $safeStyles ? $element->setAttribute('style', implode('; ', $safeStyles)) : $element->removeAttribute('style');
        }
    }
}
