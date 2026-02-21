<?php

declare(strict_types=1);

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class RoomApiFeatureTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testRoomsApiRequiresAuthentication(): void
    {
        $result = $this->get('/api/rooms/1');

        $result->assertStatus(302);
        $result->assertRedirectTo('/login');
    }
}
