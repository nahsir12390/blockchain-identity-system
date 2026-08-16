<?php

namespace App\Services;

class IdentityHasher
{
    public function hash(string $value): string
    {
        return hash_hmac('sha256', $this->normalize($value), $this->key());
    }

    private function normalize(string $value): string
    {
        return mb_strtolower(trim($value));
    }

    private function key(): string
    {
        $key = (string) config('app.key');

        if (str_starts_with($key, 'base64:')) {
            return base64_decode(substr($key, 7), true) ?: $key;
        }

        return $key;
    }
}
