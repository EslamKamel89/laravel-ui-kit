<?php

if (! function_exists('pr')) {
    /**
     * Log a value and return it unchanged.
     *
     * @template T
     *
     * @param  T  $value
     * @return T
     */
    function pr(mixed $value, ?string $title = null): mixed
    {
        if ($title !== null && $title !== '') {
            info("<< {$title} >>");
        }

        info('Debug value', [
            'data' => $value,
        ]);

        return $value;
    }
}
