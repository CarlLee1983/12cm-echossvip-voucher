<?php

namespace CHYP\Partner\Echooss\Voucher;

use CHYP\Partner\Echooss\Voucher\Type\Response;

class RewardsCard
{
    /**
     * Request path prefix.
     *
     * @var string
     */
    protected string $apiPrefix = '/api/pos';

    /**
     * Maps action names to their specific API endpoint paths.
     *
     * @var array<string, string>
     */
    protected array $requestPath = [
        'accumulatePoint' => '/mps-card-send-point',
        'depletePoint'    => '/mps-card-deduct-point',
    ];

    /**
     * RewardsCard constructor.
     *
     * @param \CHYP\Partner\Echooss\Voucher\Core $core The Core instance for API communication.
     */
    public function __construct(Core $core)
    {
        $this->core = $core;
    }

    /**
     * Perform a rewards card API request.
     *
     * This method centralizes API requests for rewards card actions. It uses the `requestPath`
     * map to find the correct API endpoint for the given action.
     *
     * The response handling is generalized:
     * - For 'depletePoint', the entire JSON decoded response is used.
     * - For other actions (e.g., 'accumulatePoint'), it attempts to use the 'data' key from
     *   the JSON decoded response, falling back to the entire response if 'data' is not present.
     *
     * @param string $action The API action to perform (e.g., 'accumulatePoint', 'depletePoint').
     * @param array  $data   The data payload for the request.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response The API response.
     * @throws \CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException If the action is not defined in `requestPath`.
     */
    public function do(string $action, array $data): Response
    {
        if (!array_key_exists($action, $this->requestPath)) {
            throw new \CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException(
                'Request action [' . $action . '] not exists in RewardsCard.'
            );
        }

        $apiPath = $this->apiPrefix . $this->requestPath[$action];

        $response = $this->core->request(
            'POST',
            $apiPath,
            ['data' => $data]
        );

        $responseData = json_decode($response->getBody()->getContents(), true);

        if ($action === 'depletePoint') {
            return new Response($action, $responseData ?? []);
        }
        
        return new Response($action, $responseData['data'] ?? $responseData ?? []);
    }
}
