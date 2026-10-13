<?php

namespace Tests;

use App\Models\Advertisement;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Talep içi reklam belleği testler arasında sızmasın.
        Advertisement::flushMemo();
    }
}
