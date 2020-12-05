<?php

namespace Tests\Unit;

use App\Models\Spam;
use Tests\TestCase;

class SpamTest extends TestCase
{
    function test_it_detects_spam()
    {
        $this->withoutExceptionHandling();
        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply.'));
    }
}
