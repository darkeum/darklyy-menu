<?php

/*
* @name        DARKLYY-MENU
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Darkeum\Menu\Html;

class Tag
{
    public function __construct(
        public string $tagName,
        protected Attributes | null $attributes = null,
    ) {
        $this->attributes ??= new Attributes();
    }

    public static function make(string $tagName, Attributes $attributes = null): self
    {
        return new self($tagName, $attributes);
    }

    public function withContents($contents): string
    {
        if (is_array($contents)) {
            $contents = implode('', $contents);
        }

        return $this->open().$contents.$this->close();
    }

    public function open(): string
    {
        if ($this->attributes->isEmpty()) {
            return "<{$this->tagName}>";
        }

        return "<{$this->tagName} {$this->attributes}>";
    }

    public function close(): string
    {
        return "</{$this->tagName}>";
    }
}
