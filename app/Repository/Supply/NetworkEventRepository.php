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

declare(strict_types = 1);

namespace Adshares\Adserver\Repository\Supply;

use Adshares\Adserver\Models\NetworkEventLog;
use Adshares\Supply\Domain\Repository\EventRepository;
use DateTime;

class NetworkEventRepository implements EventRepository
{
    public function fetchUnpaidEventsCreatedFromDate(
        DateTime $dateTime,
        int $limit = self::PACKAGE_SIZE,
        int $offset = 0
    ): array {
        $events = NetworkEventLog::where('created_at', '>=', $dateTime)
            ->whereNull('event_value')
            ->take($limit)
            ->skip($offset)
            ->get();

        return $events->toArray();
    }

    public function fetchPaidEventsUpdatedFromDate(
        DateTime $dateTime,
        int $limit = self::PACKAGE_SIZE,
        int $offset = 0
    ): array {
        $events = NetworkEventLog::where('updated_at', '>=', $dateTime)
            ->whereNotNull('event_value')
            ->take($limit)
            ->skip($offset)
            ->get();

        return $events->toArray();
    }
}
