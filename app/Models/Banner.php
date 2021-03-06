<?php
/**
 * Copyright (c) 2018 Adshares sp. z o.o.
 *
 * This file is part of AdServer
 *
 * AdServer is free software: you can redistribute and/or modify it
 * under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * AdServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AdServer. If not, see <https://www.gnu.org/licenses/>
 */

namespace Adshares\Adserver\Models;

use Adshares\Adserver\Events\CreativeSha1;
use Adshares\Adserver\Events\GenerateUUID;
use Adshares\Adserver\Models\Traits\AutomateMutators;
use Adshares\Adserver\Models\Traits\BinHex;
use Adshares\Common\Domain\ValueObject\SecureUrl;
use Adshares\Supply\Domain\ValueObject\Size;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use function hex2bin;
use function in_array;

/**
 * @property Campaign campaign
 * @mixin Builder
 */
class Banner extends Model
{
    public const IMAGE_TYPE = 0;

    public const HTML_TYPE = 1;

    public const STATUS_DRAFT = 0;

    public const STATUS_INACTIVE = 1;

    public const STATUS_ACTIVE = 2;

    public const STATUSES = [self::STATUS_DRAFT, self::STATUS_INACTIVE, self::STATUS_ACTIVE];

    use AutomateMutators;
    use BinHex;
    use SoftDeletes;

    protected $dates = [
        'deleted_at',
    ];

    protected $dispatchesEvents = [
        'creating' => GenerateUUID::class,
        'saving' => CreativeSha1::class,
    ];

    protected $fillable = [
        'uuid',
        'campaign_id',
        'creative_contents',
        'creative_type',
        'creative_sha1',
        'creative_width',
        'creative_height',
        'name',
        'status',
    ];

    protected $hidden = [
        'creative_contents',
        'campaign_id',
        'deleted_at',
    ];

    protected $traitAutomate = [
        'uuid' => 'BinHex',
        'creative_sha1' => 'BinHex',
    ];

    protected $touches = ['campaign'];

    public static function isStatusAllowed(int $status): bool
    {
        return in_array($status, self::STATUSES);
    }

    public static function type($type)
    {
        if ($type === self::IMAGE_TYPE) {
            return 'image';
        }

        return 'html';
    }

    public static function size($size)
    {
        if (!isset(Size::SUPPORTED_SIZES[$size])) {
            throw new \RuntimeException(sprintf('Wrong image size.'));
        }

        return Size::SUPPORTED_SIZES[$size];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    protected function toArrayExtras($array)
    {
        $array['url'] = (new SecureUrl(route('banner-preview', ['id' => $this->uuid])))->toString();

        return $array;
    }

    public static function fetchBanner(string $bannerUuid): ?self
    {
        return self::where('uuid', hex2bin($bannerUuid))->first();
    }
}
