<?php
/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

/**
 * Class GoogleGeoLocationService
 * https://developers.google.com/maps/documentation/geocoding/
 * Users of the free API:
 * 2,500 requests per 24 hour period.
 */
final class GoogleGeoCodingService implements IGeoCodingService
{

    const ApiHost    = 'https://maps.googleapis.com';
    const ApiBaseUrl = '/maps/api/geocode/json';

    /**
     * @var string
     */
    private $api_key;
    /**
     * @var string
     */
    private $client_id;
    /**
     * @var string
     */
    private $private_key;
    /**
     * @var IGeoCodingQueryRepository
     */
    private $repository;

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @var IUtilFactory
     */
    private $factory;

    public function __construct(IGeoCodingQueryRepository $repository,
                                IUtilFactory $factory,
                                ITransactionManager $tx_manager,
                                $api_key = null,
                                $client_id = null,
                                $private_key = null)
    {

        if (defined('GOOGLE_GEO_CODING_API_KEY')) {
            $api_key = GOOGLE_GEO_CODING_API_KEY;
        } else if (defined('GOOGLE_GEO_CODING_CLIENT_ID') && defined('GOOGLE_GEO_CODING_PRIVATE_KEY')) {
            $client_id = GOOGLE_GEO_CODING_CLIENT_ID;
            $private_key = GOOGLE_GEO_CODING_PRIVATE_KEY;
        }

        $this->api_key = $api_key;
        $this->client_id = $client_id;
        $this->private_key = $private_key;
        $this->repository = $repository;
        $this->tx_manager = $tx_manager;
        $this->factory = $factory;
    }

    // Encode a string to URL-safe base64
    private function encodeBase64UrlSafe($value)
    {
        return str_replace(array('+', '/'), array('-', '_'), base64_encode($value));
    }

    // Decode a string from URL-safe base64
    private function decodeBase64UrlSafe($value)
    {
        return base64_decode(str_replace(array('-', '_'), array('+', '/'), $value));
    }


    private function shouldSignUrl()
    {
        return !empty($this->client_id) && !empty($this->private_key);
    }

    private function shouldUseKey()
    {
        return !empty($this->api_key);
    }

    /**
     * @param string $url
     * @return string
     */
    private function calculateSignature($url)
    {
        $url .= "&client={$this->client_id}";
        // Decode the private key into its binary format
        $decodedKey = $this->decodeBase64UrlSafe($this->private_key);
        // Create a signature using the private key and the URL-encoded
        // string using HMAC SHA1. This signature will be binary.
        $signature = hash_hmac("sha1", $url, $decodedKey, true);
        return $this->encodeBase64UrlSafe($signature);
    }

    /**
     * return GPS coordinates array($lat,$lng)
     * @param string $city
     * @param string $country
     * @param string|null $state
     * @param string|null $address
     * @param string|null $zip_code
     * @throws EntityValidationException
     * @return array
     */
    private function doGeoQuery($city, $country, $state = null, $address = null, $zip_code = null)
    {

        return $this->tx_manager->transaction(function () use ($city, $country, $state, $address, $zip_code) {

            $query          = [];
            $formatted_city = urlencode($city);
            $components     = "locality:{$formatted_city}|country:{$country}";

            if (!empty($state)) {
                $formatted_state = urlencode($state);
                $components .= "|administrative_area:{$formatted_state}";
            }
            if (!empty($address)) {
                $formatted_address = urlencode($address);
                $components .= "|address:{$formatted_address}";
            }
            $query['components'] = $components;

            if (!empty($zip_code)) {
                $query['postal_code'] = urlencode($zip_code);
            }

            $res = $this->repository->getByGeoQuery(http_build_query($query));
            if ($res) return [$res->getLat(), $res->getLng()];

            $url = self::ApiBaseUrl . '?' . http_build_query($query);
            if ($this->shouldUseKey()) {
                $query['key'] = $this->api_key;
            } else if ($this->shouldSignUrl()) {
                $query['client']    = $this->client_id;
                $query['signature'] = $this->calculateSignature($url);
            }
            $response = null;

            try {
                $client = new \GuzzleHttp\Client();
                $response = json_decode($client->get(self::ApiHost . self::ApiBaseUrl, [
                    'query' => $query
                ])->getBody());
                $client = null;
            } catch (Exception $ex) {
                SS_Log::log($ex, SS_Log::ERR);
                throw new EntityValidationException
                (
                    [
                        ['message' => 'server error']
                    ]
                );
            }

            if (is_null($response)) {
                if (!empty($address))
                    throw new EntityValidationException
                    (
                        [
                            [
                                'message' => sprintf
                                (
                                    'Address %s (%s) does not exist on City %s', $address, $zip_code, $city
                                )
                            ]
                        ]
                    );
                throw new EntityValidationException
                (
                    [
                        ['message' => sprintf('City %s does not exist on Country %s', $city, $country)]
                    ]
                );
            }

            if ($response->status != 'OK') {
                if (!empty($address))
                    throw new EntityValidationException(
                        [
                            [
                                'message' => sprintf
                                (
                                    'Address %s (%s) does not exist on City %s - (STATUS: %s)',
                                    $address,
                                    $zip_code,
                                    $city, $response->status
                                )
                            ]
                        ]
                    );
                throw new EntityValidationException
                (
                    [
                        [
                            'message' => sprintf
                            (
                                'City %s does not exist on Country %s (STATUS: %s)',
                                $city,
                                $country,
                                $response->status
                            )
                        ]
                    ]
                );
            }

            $geo_query = $this->factory->buildGeoCodingQuery
            (
                http_build_query($query),
                $response->results[0]->geometry->location->lat,
                $response->results[0]->geometry->location->lng
            );

            $this->repository->add($geo_query);
            return [$response->results[0]->geometry->location->lat, $response->results[0]->geometry->location->lng];
        });
    }

    /**
     * given a city name and an ISO 3166-1 country code
     * return GPS coordinates array($lat,$lng)
     * @param string $city
     * @param string $country
     * @param string|null $state
     * @throws EntityValidationException
     * @return array
     */
    public function getCityCoordinates($city, $country, $state = null)
    {
        return $this->doGeoQuery($city, $country, $state);
    }

    /**
     * given an address info
     * return GPS coordinates array($lat,$lng)
     * @param AddressInfo $address_info
     * @throws EntityValidationException
     * @return array
     */
    public function getAddressCoordinates(AddressInfo $address_info)
    {
        list($address1, $address2) = $address_info->getAddress();
        $address = $address1 . ' ' . $address2;
        $city = $address_info->getCity();
        $state = $address_info->getState();
        if (!empty($city)) {
            $address .= ", {$city}";
        }
        if (!empty($state)) {
            $address .= ", {$state}";
        }
        $zip_code = $address_info->getZipCode();
        $country = $address_info->getCountry();
        return $this->doGeoQuery($city, $country, $state, $address, $zip_code);
    }
}