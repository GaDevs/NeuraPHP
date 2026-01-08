<?php
use PHPUnit\Framework\TestCase;

class RestApiTest extends TestCase
{
    public function testRestEndpoint()
    {
        $url = 'http://localhost/NeuraPHP/neuraphp/rest/api.php';
        $data = ['prompt' => 'Hello world'];
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => json_encode($data)
            ]
        ];
        $context = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);
        $this->assertNotFalse($result);
        $json = json_decode($result, true);
        $this->assertIsArray($json);
    }
}
