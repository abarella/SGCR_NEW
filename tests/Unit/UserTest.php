<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function test_user_model_fillable_attributes(): void
    {
        $user = new \App\Models\User();
        $this->assertEquals([
            'name',
            'email',
            'password',
        ], $user->getFillable());
    }
}
