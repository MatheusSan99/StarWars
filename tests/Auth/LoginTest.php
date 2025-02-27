<?php

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use StarWars\UseCases\Account\CreateNewAccountCase;
use StarWars\UseCases\Auth\AccountLoginCase;
use StarWars\Service\Auth\AuthService;

class LoginTest extends TestCase
{
    private ContainerInterface $container;
    private CreateNewAccountCase $CreateNewAccountCase;
    private AccountLoginCase $LoginAccountCase;
    private AuthService $AuthService;

    protected function setUp(): void
    {
        $this->container = createTestContainer();
        $this->CreateNewAccountCase = $this->container->get(CreateNewAccountCase::class);
        $this->LoginAccountCase = $this->container->get(AccountLoginCase::class);
        $this->AuthService = $this->container->get(AuthService::class);
    }

    public function testLoginUserWithJWT()
    {
        $NewUser = $this->CreateNewAccountCase->execute('userNormal', 'user@gmail.com', '123456');
    
        $this->assertEquals('userNormal', $NewUser->getName());
        $this->assertEquals('user@gmail.com', $NewUser->getEmail());
    
        $AccountModel = $this->LoginAccountCase->execute($NewUser->getEmail(), $NewUser->getPassword());
        $this->assertNotEmpty($AccountModel);
    
        $AccountModel = $this->AuthService->generateToken($AccountModel);
        $decodedPayload = $this->AuthService->validateToken($AccountModel);
    
        $this->assertNotNull($decodedPayload);
        $this->assertEquals($NewUser->getEmail(), $decodedPayload->email);
        $this->assertEquals($NewUser->getId(), $decodedPayload->user_id);
    }
    
}
