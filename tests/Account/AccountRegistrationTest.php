<?php

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use StarWars\Exceptions\Auth\RegisterException;
use StarWars\UseCases\Account\CreateNewAccountCase;
use StarWars\Repository\Account\AccountRepository;

class AccountRegistrationTest extends TestCase
{
    private ContainerInterface $container;
    private CreateNewAccountCase $CreateNewAccountCase;

    protected function setUp(): void
    {
        $this->container = createTestContainer();
        $this->CreateNewAccountCase = new CreateNewAccountCase($this->container->get(AccountRepository::class));
    }

    public function testCreateNewUser()
    {
        $NewUser = $this->CreateNewAccountCase->execute('userNormal', 'user@gmail.com', '123456');

        $this->assertEquals('userNormal', $NewUser->getName());
        $this->assertEquals('user@gmail.com', $NewUser->getEmail());
        $this->assertEquals('user', $NewUser->getRole());
        $this->assertFalse($NewUser->isAdmin());
    }

    public function testCreateNewAdmin()
    {
        $NewUser = $this->CreateNewAccountCase->execute('userAdmin', 'admin@gmail.com', '123456', 'admin');
        $this->assertEquals('userAdmin', $NewUser->getName());
        $this->assertEquals('admin@gmail.com', $NewUser->getEmail());
        $this->assertEquals('admin', $NewUser->getRole());
        $this->assertTrue($NewUser->isAdmin());
    }

    public function testCreateNewUserWithInvalidEmail()
    {
        $this->expectException(RegisterException::class);
        $this->expectExceptionMessage(RegisterException::INVALID_EMAIL);

        $this->CreateNewAccountCase->execute('userNormal', 'usergmail.com', '123456');
    }

    public function testCreateNewUserWithShortName()
    {
        $this->expectException(RegisterException::class);
        $this->expectExceptionMessage(RegisterException::NAME_TOO_SHORT);

        $this->CreateNewAccountCase->execute('us', 'user@gmail.com', '123456');
    }
}
