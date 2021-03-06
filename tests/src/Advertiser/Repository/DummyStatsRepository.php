<?php
/**
 * Copyright (c) 2018-2019 Adshares sp. z o.o.
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

namespace Adshares\Tests\Advertiser\Repository;

use Adshares\Adserver\Models\Campaign;
use Adshares\Adserver\Models\User;
use Adshares\Advertiser\Dto\Result\ChartResult;
use Adshares\Advertiser\Dto\Result\Stats\Calculation;
use Adshares\Advertiser\Dto\Result\Stats\DataCollection;
use Adshares\Advertiser\Dto\Result\Stats\DataEntry;
use Adshares\Advertiser\Dto\Result\Stats\Total;
use Adshares\Advertiser\Repository\StatsRepository;
use DateTime;

class DummyStatsRepository implements StatsRepository
{
    const USER_EMAIL = 'postman@dev.dev';

    public function fetchView(
        string $advertiserId,
        string $resolution,
        DateTime $dateStart,
        DateTime $dateEnd,
        ?string $campaignId = null
    ): ChartResult {
        $data = [
            ['2019-01-01T15:00:00+00:00', 1, 1],
            ['2019-01-01T16:00:00+00:00', 2, 2],
            ['2019-01-01T17:00:00+00:00', 3, 3],
            ['2019-01-01T18:00:00+00:00', 4, 4],
        ];

        if ($campaignId) {
            $data = $this->setDataForCampaign($data);
        }

        return new ChartResult($data);
    }

    public function fetchViewAll(
        string $advertiserId,
        string $resolution,
        DateTime $dateStart,
        DateTime $dateEnd,
        ?string $campaignId = null
    ): ChartResult {
        // TODO: Implement fetchViewAll() method.
    }

    public function fetchViewInvalidRate(
        string $advertiserId,
        string $resolution,
        DateTime $dateStart,
        DateTime $dateEnd,
        ?string $campaignId = null
    ): ChartResult {
        // TODO: Implement fetchViewInvalidRate() method.
    }

    public function fetchViewUnique(
        string $advertiserId,
        string $resolution,
        DateTime $dateStart,
        DateTime $dateEnd,
        ?string $campaignId = null
    ): ChartResult {
        // TODO: Implement fetchViewUnique() method.
    }

    public function fetchClick(
        string $advertiserId,
        string $resolution,
        DateTime $dateStart,
        DateTime $dateEnd,
        ?string $campaignId = null
    ): ChartResult {
        $data = [
            ['2019-01-01T15:00:00+00:00', 11, 11],
            ['2019-01-01T16:00:00+00:00', 21, 21],
            ['2019-01-01T17:00:00+00:00', 31, 31],
            ['2019-01-01T18:00:00+00:00', 41, 41],
        ];

        return new ChartResult($data);
    }

    public function fetchClickAll(
        string $advertiserId,
        string $resolution,
        DateTime $dateStart,
        DateTime $dateEnd,
        ?string $campaignId = null
    ): ChartResult {
        // TODO: Implement fetchClickAll() method.
    }

    public function fetchClickInvalidRate(
        string $advertiserId,
        string $resolution,
        DateTime $dateStart,
        DateTime $dateEnd,
        ?string $campaignId = null
    ): ChartResult {
        // TODO: Implement fetchClickInvalidRate() method.
    }

    public function fetchCpc(
        string $advertiserId,
        string $resolution,
        DateTime $dateStart,
        DateTime $dateEnd,
        ?string $campaignId = null
    ): ChartResult {
        $data = [
            ['2019-01-01T15:00:00+00:00', 12, 12],
            ['2019-01-01T16:00:00+00:00', 22, 22],
            ['2019-01-01T17:00:00+00:00', 32, 32],
            ['2019-01-01T18:00:00+00:00', 42, 42],
        ];

        return new ChartResult($data);
    }

    public function fetchCpm(
        string $advertiserId,
        string $resolution,
        DateTime $dateStart,
        DateTime $dateEnd,
        ?string $campaignId = null
    ): ChartResult {
        $data = [
            ['2019-01-01T15:00:00+00:00', 13, 13],
            ['2019-01-01T16:00:00+00:00', 23, 23],
            ['2019-01-01T17:00:00+00:00', 33, 33],
            ['2019-01-01T18:00:00+00:00', 43, 43],
        ];

        return new ChartResult($data);
    }

    public function fetchSum(
        string $advertiserId,
        string $resolution,
        DateTime $dateStart,
        DateTime $dateEnd,
        ?string $campaignId = null
    ): ChartResult {
        $data = [
            ['2019-01-01T15:00:00+00:00', 14, 14],
            ['2019-01-01T16:00:00+00:00', 24, 24],
            ['2019-01-01T17:00:00+00:00', 34, 34],
            ['2019-01-01T18:00:00+00:00', 44, 44],
        ];

        return new ChartResult($data);
    }

    public function fetchCtr(
        string $advertiserId,
        string $resolution,
        DateTime $dateStart,
        DateTime $dateEnd,
        ?string $campaignId = null
    ): ChartResult {
        $data = [
            ['2019-01-01T15:00:00+00:00', 15, 15],
            ['2019-01-01T16:00:00+00:00', 25, 25],
            ['2019-01-01T17:00:00+00:00', 35, 35],
            ['2019-01-01T18:00:00+00:00', 45, 45],
        ];

        return new ChartResult($data);
    }

    public function fetchStats(
        string $advertiserId,
        DateTime $dateStart,
        DateTime $dateEnd,
        ?string $campaignId = null
    ): DataCollection {
        $user = User::fetchByEmail(self::USER_EMAIL);

        if ($campaignId) {
            $campaign = Campaign::fetchByUuid($campaignId);
            $banners = $campaign->banners;

            $bannerId1 = $banners[0]->uuid;
            $bannerId2 = $banners[1]->uuid;
            $bannerId3 = $banners[2]->uuid;
            $bannerId4 = $banners[3]->uuid;
        }

        $campaigns = $user->campaigns;

        $campaignUuid = $campaigns[0]->uuid;

        $data = [
            new DataEntry(new Calculation(1, 1, 1, 1, 1, 1), $campaignUuid, $bannerId1 ?? null),
            new DataEntry(new Calculation(2, 2, 2, 2, 2, 2), $campaignUuid, $bannerId2 ?? null),
            new DataEntry(new Calculation(3, 3, 3, 3, 3, 3), $campaignUuid, $bannerId3 ?? null),
            new DataEntry(new Calculation(4, 4, 4, 4, 4, 4), $campaignUuid, $bannerId4 ?? null),
        ];

        return new DataCollection($data);
    }

    public function fetchStatsTotal(
        string $advertiserId,
        DateTime $dateStart,
        DateTime $dateEnd,
        ?string $campaignId = null
    ): Total {
        $calculation = new Calculation(1, 1, 1, 1, 1, 1);

        return new Total($calculation);
    }

    private function setDataForCampaign(array $data): array
    {
        foreach ($data as &$entry) {
            foreach ($entry as &$value) {
                $value = 100 + $value;
            }
        }

        return $data;
    }

    public function fetchStatsToReport(
        string $advertiserId,
        DateTime $dateStart,
        DateTime $dateEnd,
        ?string $campaignId = null
    ): DataCollection {
        // TODO: Implement fetchStatsToReport() method.
    }
}
