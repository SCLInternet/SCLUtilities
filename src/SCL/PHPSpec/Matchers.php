<?php
namespace SCL\PHPSpec;

use RobTeifi\Differencer\FormattingDifferencer;

class Matchers
{
    public static function returnLinesMatcher()
    {
        return function ($value, array $expected) {
            $result = FormattingDifferencer::tryCompare(
                $expected,
                explode("\n", $value),
                0.001
            );
            if ($result->getMatched()) {
                return true;
            }
            FormattingDifferencer::explain($result);

            return false;
        };
    }

    public static function containsMatcher()
    {
        return function ($value, array $expected) {
            $lastPos = 0;
            foreach ($expected as $idx => $expect) {
                $expect = trim($expect);
                $pos    = strpos($value, $expect, $lastPos);
                if ($pos === false) {
                    echo "'$expect' [$idx]not found in\n";
                    echo "$value\n";

                    return false;
                }
                $lastPos = $pos + strlen($expect);
            }

            return true;
        };
    }

    public static function containsWithoutBreaksMatcher()
    {
        return function ($value, $expected) {
            $value  = preg_replace('/\n\s*/', '', $value);
            $result = FormattingDifferencer::tryCompare(
                $expected,
                $value,
                0.001
            );
            if ($result->getMatched()) {
                return true;
            }
            echo "\n\n";
            FormattingDifferencer::explain($result);

            return false;
        };
    }

    public static function returnArray()
    {
        return function (array $value, array $expected) {
            $result = FormattingDifferencer::tryCompare(
                $expected,
                $value,
                0.001
            );
            if ($result->getMatched()) {
                return true;
            }
            FormattingDifferencer::explain($result);

            return false;
        };
    }

    public static function haveSet()
    {
        return function ($subject, $fieldName) {
            return isset($subject->$fieldName);
        };
    }

    public static function returnPeriodCollection()
    {
        return function ($subject, $value) {
            $result = FormattingDifferencer::tryCompare($subject, $value, 0.001);

            if ($result->getMatched()) {
                return true;
            }
            FormattingDifferencer::explain($result);

            return false;
        };
    }
}
