<?php

declare(strict_types=1);

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class AuthFeatureTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testLoginPageLoads(): void
    {
        $result = $this->get('/login');

        $result->assertStatus(200);
        $result->assertSee('Login');
    }

    public function testRegisterPageLoads(): void
    {
        $result = $this->get('/register');

        $result->assertStatus(200);
        $result->assertSee('Register');
    }
}
