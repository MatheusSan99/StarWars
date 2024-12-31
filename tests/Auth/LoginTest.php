<?php

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use StarWars\UseCases\Account\CreateNewAccountCase;
use StarWars\UseCases\Auth\AccountLoginCase;
use StarWars\Repository\Account\AccountRepository;
use StarWars\Service\Auth\AuthService;

class LoginTest extends TestCase
{
    private ContainerInterface $container;

    protected function setUp(): void
    {
        $this->container = createTestContainer();
    }

    public function testLoginUserWithJWT()
    {
        $CreateNewAccountCase = new CreateNewAccountCase($this->container->get(AccountRepository::class));
        $NewUser = $CreateNewAccountCase->execute('userNormal', 'user@gmail.com', '123456');
    
        $this->assertEquals('userNormal', $NewUser->getName());
        $this->assertEquals('user@gmail.com', $NewUser->getEmail());
    
        $LoginAccountCase = new AccountLoginCase($this->container->get(AccountRepository::class));
        $token = $LoginAccountCase->execute($NewUser->getEmail(), $NewUser->getPassword());
    
        $this->assertNotEmpty($token);
    
        $AuthService = new AuthService();
        $decodedPayload = $AuthService->validateToken($token);
    
        $this->assertNotNull($decodedPayload);
        $this->assertEquals($NewUser->getEmail(), $decodedPayload->email);
        $this->assertEquals($NewUser->getId(), $decodedPayload->user_id);
    }
    
}
