<?php

namespace CHYP\Partner\Echooss\Voucher\Application\Hydrator;

use CHYP\Partner\Echooss\Voucher\Application\Factory\ResponseFactory;
use CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface;
use CHYP\Partner\Echooss\Voucher\Utils;

class ResponseHydrator
{
    protected ResponseFactory $factory;

    /**
     * @param ResponseFactory $factory Response factory.
     */
    public function __construct(ResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Hydrate response DTO based on type mapping.
     *
     * @param string $type    Response type key.
     * @param array  $payload Raw payload data.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface
     */
    public function hydrate(string $type, array $payload): ResponseInterface
    {
        $response = $this->factory->create($type);

        foreach ($payload as $key => $value) {
            $property = Utils::camelCase((string) $key);

            $response->$property = $value;
        }

        return $response;
    }
}
