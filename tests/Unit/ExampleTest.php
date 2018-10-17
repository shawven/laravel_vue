<?php

namespace Tests\Unit;

use App\Http\Models\User\User;
use App\Notifications\WithdrawNotification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        print_r(unserialize('a:3:{s:3:"sid";s:14:"6049,7051,7052";s:2:"id";s:20:"100443,100442,100444";s:3:"bet";s:47:"nspf-3#2.23|nspf-0#2.88,nspf-0#1.44,nspf-3#1.72";}'));

        $this->assertTrue(true);
    }
}
