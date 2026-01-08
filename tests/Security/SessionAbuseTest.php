<?php
use PHPUnit\Framework\TestCase;

class SessionAbuseTest extends TestCase
{
    public function testSessionIdRegeneration()
    {
        session_start();
        $oldId = session_id();
        session_regenerate_id();
        $newId = session_id();
        $this->assertNotEquals($oldId, $newId);
    }
}
