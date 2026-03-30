<?php

namespace App\Support;

class CompanyNameNormalizer
{
    /**
     * Replace full Polish legal-form names with standard abbreviations.
     * Order matters – longer/more-specific patterns come first.
     */
    public static function abbreviate(string $name): string
    {
        $replacements = [
            '/prosta\s+spółka\s+akcyjna/iu'                     => 'P.S.A.',
            '/spółka\s+z\s+ograniczoną\s+odpowiedzialnością/iu' => 'sp. z o.o.',
            '/spółka\s+komandytowo-akcyjna/iu'                  => 'S.K.A.',
            '/spółka\s+komandytowa/iu'                          => 'sp. k.',
            '/spółka\s+akcyjna/iu'                              => 'S.A.',
            '/spółka\s+cywilna/iu'                              => 's.c.',
            '/spółka\s+jawna/iu'                                => 's.j.',
            '/spółka\s+partnerska/iu'                           => 'sp. p.',
            // Malformed / space-separated already-abbreviated forms
            '/sp\.\s*z\s+o\.\s*o\./iu'                         => 'sp. z o.o.',
            '/\bsp\s+z\s+o\s+o\b/iu'                           => 'sp. z o.o.',
        ];

        foreach ($replacements as $pattern => $replacement) {
            $name = (string) preg_replace($pattern, $replacement, $name);
        }

        return trim($name);
    }
}
