<?php

namespace Oforge\Engine\Auth\Services;

use Exception;
use Oforge\Engine\Auth\Exceptions\InvalidPasswordFormatException;
use Oforge\Engine\Auth\Exceptions\PasswordGeneratorException;
use Oforge\Engine\Core\Services\ConfigService;

/**
 * Class PasswordService
 *
 * @package Oforge\Engine\Auth\Services
 */
class PasswordService {

    /**
     * @param string $password
     *
     * @return bool|string
     */
    public function hash(string $password) {
        return password_hash($this->getSalted($password), PASSWORD_BCRYPT);
    }

    /**
     * @param string $password
     *
     * @return PasswordService
     * @throws InvalidPasswordFormatException
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function validateFormat(string $password) : PasswordService {
        /** @var ConfigService $configService */
        /** @noinspection PhpUnhandledExceptionInspection */
        $configService = Oforge()->Services()->get('config');

        if (empty(trim($password))) {
            throw new InvalidPasswordFormatException('The password cannot be empty or only contain spaces.');
            // TODO I18N?
            // throw new InvalidPasswordFormatException(I18N::translate('auth_invalid_password_format_empty', [
            //     'en' => 'The password cannot be empty or only contain spaces.',
            //     'de' => 'Das Passwort darf nicht leer sein oder nur aus Leerzeichen bestehen.',
            // ]));
        }
        $passwordMinLength = (int) $configService->get('auth_password_min_length');
        if (strlen($password) < $passwordMinLength) {
            throw new InvalidPasswordFormatException(sprintf(#
                'The password must be at least %s characters long.',#
                $passwordMinLength#
            ));
            // TODO I18N?
            // throw new InvalidPasswordFormatException(sprintf(#
            //     I18N::translate('auth_invalid_password_format_length', [
            //         'en' => 'The password must be at least %s characters long.',#
            //         'de' => 'Das Password muss mindestents %s Zeichen lang sein.',#
            //     ]),#
            //     $passwordMinLength#
            // ));
        }

        return $this;
    }

    /**
     * @param string $password
     * @param string $passwordHash
     *
     * @return bool
     */
    public function validate(string $password, string $passwordHash) : bool {
        return password_verify($this->getSalted($password), $passwordHash);
    }

    /**
     * Create a password
     *
     * @param int $length
     *
     * @return string
     * @throws PasswordGeneratorException
     */
    public function generatePassword($length = 12) : string {
        $length     = max(1, $length);
        $characters = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789-=~@#$%^&*()_+,./<>?;:[]{}';
        $password   = '';
        $lastIndex  = strlen($characters) - 1;

        for ($i = 0; $i < $length; $i++) {
            try {
                $password .= $characters[random_int(0, $lastIndex)];
            } catch (Exception $exception) {
                throw new PasswordGeneratorException($exception);
            }
        }

        return $password;
    }

    /**
     * @param string $password
     *
     * @return string
     */
    protected function getSalted(string $password) : string {
        $salt = Oforge()->Settings()->get('salts.password', '');

        return $salt . $password;
    }

}
