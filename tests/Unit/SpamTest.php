<?php

namespace Tests\Unit;


use App\Inspections\Spam;
use Tests\TestCase;

class SpamTest extends TestCase
{
    function test_it_detects_invalid_keywords()
    {
        $this->withoutExceptionHandling();

        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply.'));

        $this->expectException('Exception');
        $this->assertTrue($spam->detect('php is dead'));
    }

    function test_it_detects_key_held_down()
    {
        $spam = new Spam();

        $this->expectException('Exception');
        $spam->detect('Hello world aaaaaaaa');
    }
}
