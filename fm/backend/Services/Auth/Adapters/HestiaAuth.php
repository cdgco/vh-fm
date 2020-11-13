<?php

/*
 * This file is part of the FileGator package.
 *
 * (c) Milos Stojanovic <alcalbg@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 */

namespace Filegator\Services\Auth\Adapters;

use Filegator\Services\Auth\AuthInterface;
use Filegator\Services\Auth\User;
use Filegator\Services\Auth\UsersCollection;
use Filegator\Services\Service;

/**
 * @codeCoverageIgnore
 */
class HestiaAuth implements Service, AuthInterface
{

    protected $permissions = [];

    protected $private_repos = false;

    protected $hestia_user = '';

    public function init(array $config = [])
    {
        if (isset($_SESSION['username'])) {
            $v_user = base64_decode($_SESSION['username']);
        }
        if (isset($_SESSION['proxied']) && base64_decode($_SESSION['proxied']) != 'admin' && $v_user === 'admin') {
            $v_user = base64_decode($_SESSION['proxied']);
        }
        $this->hestia_user = $v_user;
        $this->permissions = isset($config['permissions']) ? (array)$config['permissions'] : [];
        $this->private_repos = isset($config['private_repos']) ? (bool)$config['private_repos'] : false;
    }

    public function user(): ?User
    {

        return $this->transformUser();
    }

    public function transformUser(): User
    {
	if (isset($_SESSION['username'])) {
            $v_user = base64_decode($_SESSION['username']);
        }
        if (isset($_SESSION['proxied']) && base64_decode($_SESSION['proxied']) != 'admin' && $v_user === 'admin') {
            $v_user = base64_decode($_SESSION['proxied']);
        }

        $user = new User();
        $user->setUsername($v_user);
        $user->setName($v_user);
        $user->setRole('user');
        $user->setPermissions($this->permissions);
        $user->setHomedir('/');
        return $user;
    }

    public function authenticate($username, $password): bool
    {
        # Auth is handled by Hestia
        return false;
    }

    public function forget()
    {
        // Logout return to Hestia
        return $this->getGuest();
    }

    public function store(User $user)
    {
        return null; // not used
    }

    public function update($username, User $user, $password = ''): User
    {
        // Password change is handled by Hestia
        return $this->user();
    }

    public function add(User $user, $password): User
    {
        return new User(); // not used
    }

    public function delete(User $user)
    {
        return true; // not used
    }

    public function find($username): ?User
    {
        return null; // not used
    }

    public function allUsers(): UsersCollection
    {
        return new UsersCollection(); // not used
    }

    public function getGuest(): User
    {
        $guest = new User();

        $guest->setUsername('guest');
        $guest->setName('Guest');
        $guest->setRole('guest');
        $guest->setHomedir('/');
        $guest->setPermissions([]);

        return $guest;
    }

}

