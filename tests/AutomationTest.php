<?php
use PHPUnit\Framework\TestCase;
use NeuraPHP\Modules\Automations;

class AutomationTest extends TestCase
{
    public function testRegisterAndListWorkflows()
    {
        $auto = new Automations();
        $auto->registerWorkflow('wf', ['steps' => ['a']]);
        $workflows = $auto->listWorkflows();
        $this->assertArrayHasKey('wf', $workflows);
    }

    public function testTriggerWorkflow()
    {
        $auto = new Automations();
        $auto->registerWorkflow('wf', ['steps' => ['a']]);
        $result = $auto->trigger('wf', ['foo' => 'bar']);
        $this->assertEquals('triggered', $result['status']);
    }

    public function testWebhook()
    {
        $auto = new Automations();
        $result = $auto->webhook('event', ['x' => 1]);
        $this->assertEquals('received', $result['status']);
    }

    public function testAddJob()
    {
        $auto = new Automations();
        $result = $auto->addJob('job', ['y' => 2]);
        $this->assertEquals('queued', $result['status']);
    }
}
