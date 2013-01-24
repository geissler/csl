<?php
/**
 * Tests if a value from a *.csl file is a boolean.
 *
 * @param string $value
 * @return bool
 */
function isBoolean($value)
{
    if ((string) $value == 'true'
        || (string) $value == '1'
        || $value === true) {
        return true;
    }

    return false;
}
