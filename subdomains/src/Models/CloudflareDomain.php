<?php

namespace Boy132\Subdomains\Models;

use Boy132\Subdomains\Enums\RecordType;
use Exception;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

/**
 * @property int $id
 * @property string $name
 * @property ?string $prefix
 * @property ?string $cloudflare_id
 * @property Collection<RecordType> $allowed_record_types
 */
class CloudflareDomain extends Model
{
    protected $fillable = [
        'name',
        'prefix',
        'cloudflare_id',
        'allowed_record_types',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::created(function (self $model) {
            $model->fetchCloudflareId();
        });

        static::saving(function (self $model): void {
            $model->allowed_record_types = $model->allowed_record_types->sort();
        });
    }

    protected function casts(): array
    {
        return [
            'allowed_record_types' => AsEnumCollection::of(RecordType::class),
        ];
    }

    public function subdomains(): HasMany
    {
        return $this->hasMany(Subdomain::class, 'domain_id');
    }

    public function nameWithPrefix(): string
    {
        return is_null($this->prefix) ? $this->name : "$this->prefix.$this->name";
    }

    public function prependPrefix(string $subdomain): string
    {
        return is_null($this->prefix) ? $subdomain : "$subdomain.$this->prefix";
    }

    /** @throws Exception */
    public function fetchCloudflareId(): void
    {
        // @phpstan-ignore staticMethod.notFound
        $response = Http::cloudflare()->get('zones', [
            'name' => $this->name,
        ])->json();

        if ($response['success']) {
            $zones = $response['result'];

            if (count($zones) > 0) {
                $this->update([
                    'cloudflare_id' => $zones[0]['id'],
                ]);
            } else {
                throw new Exception("No zone with name $this->name found.");
            }
        } else {
            if ($response['errors'] && count($response['errors']) > 0) {
                throw new Exception($response['errors'][0]['message']);
            }
        }
    }
}
