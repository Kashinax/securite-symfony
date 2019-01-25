<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *    fields= {"email"},
 *    message= "L'email que vous avez indiqué est déjà utilisé !"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min = 8,
     *     minMessage = "Votre mot de passe doit faire minimum 8 caractères"
     * )
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Vous n'avez pas tapé le même mot de passe")
     */

    public $confirm_password;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    /**
     * @var array
     * @ORM\Column(type="array")
     */
    private $roles;

    public function __construct()
	{
		$this->isActive = true;
		$this->roles = ['ROLE_USER'];
	}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /*
	 * Get isActive
	 */
	public function getIsActive()
	{
		return $this->isActive;
	}
	/*
	 * Set isActive
	 */
	public function setIsActive($isActive)
	{
		$this->isActive = $isActive;
		return $this;
	}

    public function eraseCredentials() {}

    public function getSalt() {}

    // modifier la méthode getRoles
	public function getRoles()
	{
		return $this->roles;
	}
	public function setRoles(array $roles)
	{
		if (!in_array('ROLE_USER', $roles))
		{
			$roles[] = 'ROLE_USER';
		}
		foreach ($roles as $role)
		{
			if(substr($role, 0, 5) !== 'ROLE_') {
				throw new InvalidArgumentException("Chaque rôle doit commencer par 'ROLE_'");
			}
		}
		$this->roles = $roles;
		return $this;
	}

    /**
       * @var string le token qui servira lors de l'oubli de mot de passe
       * @ORM\Column(type="string", length=255, nullable=true)
       */
      protected $resetToken;

      /**
       * @return string
       */
      public function getResetToken(): string
      {
          return $this->resetToken;
      }

      /**
       * @param string $resetToken
       */
      public function setResetToken(?string $resetToken): void
      {
          $this->resetToken = $resetToken;
      }
}
