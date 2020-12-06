<?php


namespace App\Inspections;


use Exception;

/**
 * Class InvalidKeywords
 * @package App\Inspections
 */
class InvalidKeywords
{
    /**
     * @var string[]
     */
    protected $keywords = [
        'php is dead'
    ];

    /**
     * @param $body
     * @throws Exception
     */
    public function detect($body)
    {
        foreach ($this->keywords as $keyword) {
            if (stripos($body, $keyword) !== false) {
                throw new Exception('Your reply contains spam.');
            }
        }
    }
}
