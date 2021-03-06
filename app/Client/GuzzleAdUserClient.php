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

namespace Adshares\Adserver\Client;

use Adshares\Adserver\Http\Utils;
use Adshares\Common\Application\Dto\Taxonomy;
use Adshares\Common\Application\Factory\TaxonomyFactory;
use Adshares\Common\Application\Service\AdUser;
use Adshares\Common\Exception\RuntimeException;
use Adshares\Supply\Application\Dto\ImpressionContext;
use Adshares\Supply\Application\Dto\UserContext;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use function config;
use function GuzzleHttp\json_decode;
use function sprintf;

final class GuzzleAdUserClient implements AdUser
{
    /** @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function fetchTargetingOptions(): Taxonomy
    {
        $path = '/api/v0/taxonomy';
        try {
            $response = $this->client->get($path);
            $taxonomy = json_decode((string)$response->getBody(), true);

            return TaxonomyFactory::fromArray($taxonomy);
        } catch (RequestException $exception) {
            throw new RuntimeException(sprintf(
                '{"url": "%s", "path": "%s",  "message": "%s"}',
                (string)$this->client->getConfig('base_uri'),
                $path,
                $exception->getMessage()
            ));
        }
    }

    public function getUserContext(ImpressionContext $partialContext): UserContext
    {
        $path = sprintf(
            '/api/v1/data/%s/%s',
            config('app.adserver_id'),
            $partialContext->trackingId()
        );

        try {
            $response = $this->client->post(
                $path,
                ['form_params' => $partialContext->adUserRequestBody()]
            );

//            Log::debug(sprintf(
//                '%s {"url":"%s","path":"%s","request":%s,"response":%s}',
//                __METHOD__,
//                (string)$this->client->getConfig('base_uri'),
//                $path,
//                json_encode($partialContext->adUserRequestBody()),
//                (string)$response->getBody()
//            ));

            $body = json_decode((string)$response->getBody(), true);

            return UserContext::fromAdUserArray($body);
        } catch (GuzzleException $exception) {
            return new UserContext(
                $partialContext->keywords(),
                AdUser::HUMAN_SCORE_ON_CONNECTION_ERROR,
                Utils::hexUuidFromBase64UrlWithChecksum($partialContext->trackingId())
            );
        }
    }
}
