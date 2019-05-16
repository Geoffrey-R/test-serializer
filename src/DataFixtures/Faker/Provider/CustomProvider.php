<?php

namespace App\DataFixtures\Faker\Provider;

use \Faker\Provider\Base as BaseProvider;

class CustomProvider extends BaseProvider
{
    public function emailFromName($firstname, $lastname)
    {
        $emailFirstname = $this->sanitizeNameForEmail($firstname);
        $emailLastname = $this->sanitizeNameForEmail($lastname);
        $domain = $this->generator->freeEmailDomain;

        return "{$emailFirstname}.{$emailLastname}@{$domain}";
    }

    private function sanitizeNameForEmail($name)
    {
        $sanitized = strtolower($name);
        $sanitized = str_replace('\'', '', $sanitized);
        $sanitized = filter_var($sanitized, FILTER_SANITIZE_EMAIL);

        return $sanitized;
    }
}