<?php

namespace App\Actions;

use App\Core\Http\Request;
use Random\RandomException;
use RuntimeException;

class HandleCsrfTokens
{

    /**
     * Generate a random CSRF token
     *
     * @return string
     * @throws RandomException
     */
    public function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Validate the CSRF token
     *
     * @param string $token
     * @return true
     * @throws RuntimeException
     */
    public function validateToken(string $token): true
    {
        if (!hash_equals($token, session()->get(('_token')))) {
            // TODO: return false here, so we can show a user-friendly error message
            throw new RuntimeException("CSRF token mismatch");
        }
        return true;
    }

}