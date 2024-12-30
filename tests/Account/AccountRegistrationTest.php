<?php

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use StarWars\Controller\UseCases\Account\CreateNewAccountCase;
use StarWars\Repository\Account\AccountRepository;

class AccountRegistrationTest extends TestCase
{
    private ContainerInterface $container;

    protected function setUp(): void
    {
        $this->container = createTestContainer();
    }

    public function testCreateNewUser()
    {
        $CreateNewAccountCase = new CreateNewAccountCase($this->container->get(AccountRepository::class));

        $NewUser = $CreateNewAccountCase->execute('userNormal', 'user@gmail.com', '123456');

        $this->assertEquals('userNormal', $NewUser->getName());
        $this->assertEquals('user@gmail.com', $NewUser->getEmail());
        $this->assertEquals('user', $NewUser->getRole());
        $this->assertFalse($NewUser->isAdmin());
    }

    public function testCreateNewAdmin()
    {
        $CreateNewAccountCase = new CreateNewAccountCase($this->container->get(AccountRepository::class));

        $NewUser = $CreateNewAccountCase->execute('userAdmin', 'admin@gmail.com', '123456', 'admin');
        
        $this->assertEquals('userAdmin', $NewUser->getName());
        $this->assertEquals('admin@gmail.com', $NewUser->getEmail());
        $this->assertEquals('admin', $NewUser->getRole());
        $this->assertTrue($NewUser->isAdmin());
    }
}
