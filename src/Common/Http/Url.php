<?php

namespace OAuth\Common\Http;

use League\Url\Components\AbstractSegment;
use League\Url\Url as BaseUrl;
use OAuth\Common\Exception\Exception;

class Url extends BaseUrl
{

    /**
     * @param string $uri URI to be parsed
     */
    public function __construct($uri)
    {
        // Hack, prevent infinite recursion
        if (!$uri instanceof AbstractSegment and 1 == func_num_args()) {
            $uri = static::createFromUrl($uri);

            parent::__construct(
                $uri->getScheme(),
                $uri->getUser(),
                $uri->getPass(),
                $uri->getHost(),
                $uri->getPort(),
                $uri->getPath(),
                $uri->getQuery(),
                $uri->getFragment()
            );
        } // Recursion here
        else {
            call_user_func_array('parent::__construct', func_get_args());
        }
    }

    public function __clone()
    {
        foreach (['scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment'] as $prop) {
            if ($this->$prop and is_object($this->$prop)) {
                $this->$prop = clone $this->$prop;
            }
        }
    }

    public static function replacePlaceholders($uri, array $placeholders, array $placeholdersEmptyReplaces = [])
    {
        // Quick checks

        if (!$placeholders and !$placeholdersEmptyReplaces) {
            return $uri;
        } elseif (false === strpos($uri, '{')) {
            return $uri;
        }

        // Replace placeholders with values
        foreach ($placeholders as $placeholder => $value) {
            // Validate placeholder
            if (!(is_string($value) or is_numeric($value) or false === $value)) {
                throw new Exception(
                    'Placeholder value must be a string or numeric! Actual value "' .
                    $value .
                    '"'
                );
            }

            // If placeholder value is empty skip current loop pass
            if (!($value or 0 === $value or '0' === $value)) {
                if (empty($placeholdersEmptyReplaces[ $placeholder ])) {
                    $placeholdersEmptyReplaces[ $placeholder ] = '{}';
                }
                continue;
            }

            // Update uri
            if (false !== strpos($uri, '{' . $placeholder . '}')) {
                // Replace with value
                if ($value or 0 === $value or '0' === $value) {
                    $uri = str_replace('{' . $placeholder . '}', $value, $uri);
                }
            }
        }

        // Replace empty placeholders or placeholders with empty values
        foreach ($placeholdersEmptyReplaces as $placeholder => $emptyValue) {
            if (!(is_string($emptyValue) or is_numeric($emptyValue))
            ) {
                throw new Exception(
                    'Placeholder replacer value must be a string or numeric! Actual value: "' .
                    $emptyValue .
                    '"'
                );
            } elseif (false === strpos($emptyValue, '{}')) {
                throw new Exception(
                    'Placeholder replacer must contain "{}"! Actual value: "' .
                    $emptyValue .
                    '"'
                );
            }

            // Replace with empty value
            if (false !== strpos($uri, '{' . $placeholder . '}')) {
                $uri = str_replace(
                    str_replace('{}', '{' . $placeholder . '}', $emptyValue),
                    "",
                    $uri
                );
            }
        }


        return $uri;
    }
}
