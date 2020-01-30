<?php

namespace Hr\ApiBundle\Service;

use App\Kernel;
use Behat\Gherkin\Node\PyStringNode;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

abstract class BehatAbstractTest
{
    /** @var Application */
    protected $application;
    
    /** @var LoggerInterface */
    protected $logger;
    
    /** @var GenericFunctionHelper */
    protected $genericFunctionHelper;
    
    /** @var array test context */
    protected $context = [];
    
    /** @var array An http request payload */
    protected $payload;
    
    /** @var string */
    protected $testBaseUrl;
    
    const USER_ADMIN_EMAIL    = 'admin@admin.com';
    const USER_ADMIN_PASSWORD = 'adminpwd';
    
    /**
     * @param Kernel $kernel
     * @param LoggerInterface $logger
     */
    public function __construct(Kernel $kernel, LoggerInterface $logger, GenericFunctionHelper $genericFunctionHelper)
    {
        $this->application           = new Application($kernel);
        $this->testBaseUrl           = getenv('TEST_BASE_URL');
        $this->genericFunctionHelper = $genericFunctionHelper;
    }
    
    /**
     * @param string $commandName
     * @param array $commandParameters
     */
    public function executeInternalCommand(string $commandName, array $commandParameters = []): void
    {
        $command                      = $this->application->find($commandName);
        $commandTester                = new CommandTester($command);
        $commandParameters['command'] = $command->getName();
        $commandTester->execute($commandParameters);
    }
    
    /**
     * @return array
     */
    public function getAdminToken(): array
    {
        return $this->getUserToken([
            'email'    => $this::USER_ADMIN_EMAIL,
            'password' => $this::USER_ADMIN_PASSWORD,
        ]);
    }
    
    /**
     * @param array $payload = ['email'=>'','password'=>'']
     * @return array
     */
    public function getUserToken(array $payload): array
    {
        $guzzleClient    = new GuzzleClient();
        $result          = $guzzleClient->request(
            "POST",
            $this->testBaseUrl . '/token',
            ['json' => $payload]
        );
        $decodedResponse = json_decode((string)$result->getBody(), true);
        
        return $decodedResponse['apiKey'];
    }


//    ===============================================================================
    
    /**
     * @Given The payload
     */
    public function thePayload(PyStringNode $string)
    {
        $this->payload = json_decode($string->getRaw(), true);
    }
    
    /**
     * @When I request :arg1 on url :arg2
     */
    public function iRequestOnUrl($arg1, $arg2)
    {
        $method = $arg1;
        $url    = $this->testBaseUrl . $arg2;
        
        $guzzleClient = new GuzzleClient([
            'headers' => [
                'Content-Type' => 'application/json',
                'apiKey'       => $this->context['token']['apiKey'],
            ],
        ]);
        $result       = $guzzleClient->request(
            $method,
            $url,
            ['json' => $this->payload]
        );
        
        $receivedPayload                     = json_decode((string)($result->getBody()), true);
        $this->context['receivedPayload']    = $receivedPayload;
        $this->context['receivedStatusCode'] = $result->getStatusCode();
    }
    
    /**
     * @Then the response status code should be :arg1
     */
    public function theResponseStatusCodeShouldBe($arg1)
    {
        $expectedStatusCode = $arg1;
        $receivedStatusCode = $this->context['receivedStatusCode'];
        if ($receivedStatusCode != $expectedStatusCode) {
            throw new \Exception("invalid status code : $expectedStatusCode != $receivedStatusCode");
        }
    }
    
    /**
     * @Then the response payload should contain
     */
    public function theResponsePayloadShouldContain(PyStringNode $string)
    {
        $responsePayload = json_decode($string->getRaw(), true);
        $missingValues = $this->genericFunctionHelper->getRecursiveDifference($this->context['receivedPayload'], $responsePayload);
        
        var_dump($missingValues);
    
        $missingValues = $this->genericFunctionHelper->getRecursiveDifference($responsePayload, $this->context['receivedPayload']);
        var_dump($missingValues);
    
        exit;
    }
    
    
}
