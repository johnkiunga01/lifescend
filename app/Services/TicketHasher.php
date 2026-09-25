<?php

namespace App\Services;

use Hashids\Hashids;

class TicketHasher
{
    private Hashids $hashids;

    public function __construct()
    {
        $this->hashids = new Hashids('lifescend_tickets', 8);
    }

    public function encode(int $id): string
    {
        return $this->hashids->encode($id);
    }

    public function decode(string $hash): ?int
    {
        $decoded = $this->hashids->decode($hash);
        return $decoded[0] ?? null;
    }
}
