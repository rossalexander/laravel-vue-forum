<?php


namespace App\Inspections;

use Exception;

/**
 * Class Spam
 *
 * Responsible for knowing what to classify as spam.
 * @package App\Inspections
 */
class Spam
{
    protected $inspections = [
        InvalidKeywords::class,
        KeyHeldDown::class,
    ];

    /**
     * @param $body
     * @return false
     * @throws Exception
     */
    public function detect($body): bool
    {
        foreach ($this->inspections as $inspection) {
            app($inspection)->detect($body);
        }

        return false; // no spam detected
    }
}
