<?php

namespace MemcacheWrapper\Wrapper;

/**
 * Wrapper to plug FSP info into PHP - directory part
 */
abstract class AOperations
{
    /**
     * @param string $path
     * @return string
     */
    protected function parsePath(string $path): string
    {
        $into = parse_url($path, PHP_URL_PATH);
        return $into;
    }
}
