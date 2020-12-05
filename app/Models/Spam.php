<?php


namespace App\Models;

use Exception;

/**
 * Class Spam
 *
 * Responsible for knowing what to classify as spam.
 *
 * @package App\Models
 */
class Spam
{
    /**
     * @param $body
     * @return false
     * @throws Exception
     */
    public function detect($body): bool
    {
        // detect invalid keywords
        $this->detectInvalidKeywords($body);

        return false; // no spam detected
    }

    /**
     * @param $body
     * @throws Exception
     */
    private function detectInvalidKeywords($body)
    {
        $invalidKeywords = [
            'php is dead'
        ];

        foreach ($invalidKeywords as $keyword) {
            if (stripos($body, $keyword) !== false) {
                throw new Exception('Your reply contains spam.');
            }
        }
    }
}
