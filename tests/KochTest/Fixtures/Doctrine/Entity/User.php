<?php
namespace KochTest\Fixtures\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class User
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(length=40)
     */
    private $username;

    /**
     * @ORM\Column(length=32)
     */
    private $password;

    /**
     * @ORM\Column(length=255)
     */
    private $email;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setPassword($password)
    {
        $this->password = md5($password);
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }
}
