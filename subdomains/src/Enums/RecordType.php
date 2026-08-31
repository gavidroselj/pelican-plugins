<?php

namespace Boy132\Subdomains\Enums;

use App\Models\Server;
use Filament\Support\Contracts\HasLabel;

enum RecordType: string implements HasLabel
{
    case A = 'A';
    case AAAA = 'AAAA';
    case CNAME = 'CNAME';
    case SRV = 'SRV';

    public function getLabel(): string
    {
        return $this->name;
    }

    /**
     * @return array<string>
     */
    public static function availableRecordTypes(Server $server): array
    {
        $types = [];

        if ($server->allocation && !in_array($server->allocation->ip, ['0.0.0.0', '::'])) {
            if (is_ipv6($server->allocation->ip)) {
                $types[self::AAAA->name] = self::AAAA->value;
            } else {
                $types[self::A->name] = self::A->value;
            }
        }

        // @phpstan-ignore property.notFound
        if ($server->node->subdomain_target) {
            $types[self::CNAME->name] = self::CNAME->value;
            $types[self::SRV->name] = self::SRV->value;
        }

        return $types;
    }
}
