<?php
namespace SCL\HTML;

use SCL\HTML\Exception\MismatchedTagException;
use SCL\HTML\Exception\TagStackException;

class HTMLBuilder
{
    const INDENT = 3;

    /** @var callable (function (string $str) : string) */
    private $stringCleaner;

    /** @var string[] */
    protected $stack = [];

    /** @var int */
    protected $level;

    /** @var string[] */
    protected $result = [];

    public function getText()
    {
        return implode("\n", $this->result);
    }

    public function clear()
    {
        $this->stack  = [];
        $this->result = [];
        $this->level  = 0;
    }

    public function popTag($expectedTagName = null)
    {
        $tagName = array_pop($this->stack);
        if (!$tagName) {
            throw TagStackException::popOnEmpty($expectedTagName);
        }
        if ($expectedTagName && $expectedTagName != $tagName) {
            throw MismatchedTagException::wrongTag($expectedTagName, $tagName);
        }
        $this->outdent();
        $this->appendStr($this->endTag($tagName));
    }

    public function pushTag($name, array $params, $extra = null)
    {
        $this->appendStr($this->startTag($name, $params, $extra));
        $this->indent();
        array_push($this->stack, $name);
    }

    /**
     * @param string $name
     * @param array $params
     * @param string $extra
     */
    public function appendTag($name, array $params, $extra = null)
    {
        $this->appendStr($this->startTag($name, $params, $extra));
    }

    public function outdent()
    {
        $this->level = max(0, $this->level - 1);
    }

    /**
     * @param $str
     *
     * @return mixed
     */
    public function append($str)
    {
        $this->result[] = str_repeat(' ', self::INDENT * $this->level) . $this->cleanup($str);
    }

    private function appendStr($str)
    {
        $this->result[] = str_repeat(' ', self::INDENT * $this->level) . $str;
    }

    public function endTag($name)
    {
        return vsprintf('</%s>', [$name]);
    }

    public function startTag($name, array $params, $extra = null)
    {
        $realParams = [];

        foreach ($params as $key => $value) {
            $realParams[] = vsprintf('%s="%s"', [$key, $value]);
        }

        return vsprintf(
            count($realParams) > 0 ? '<%s %s%s>' : '<%s%s%s>',
            [
                $name,
                implode(' ', $realParams),
                $extra ? (' ' . $extra) : ''
            ]
        );
    }

    public function indent()
    {
        $this->level++;
    }

    public function inlineTag($tagName, array $params, $text, $extra = null)
    {
        $this->appendStr(
            vsprintf(
                '%s%s%s',
                [
                    $this->startTag($tagName, $params, $extra),
                    $this->cleanup($text),
                    $this->endTag($tagName)
                ]
            )
        );
    }

    /**
     * @param $title
     */
    public function panelStart($title)
    {
    }

    public function panelEnd()
    {
    }

    public function setStringCleaner(callable $cleaner)
    {
        $this->stringCleaner = $cleaner;
    }

    private function cleanup($value)
    {
        if (!$this->stringCleaner) {
            return $value;
        }
        $cleaner = $this->stringCleaner;
        return $cleaner($value);
    }
}
