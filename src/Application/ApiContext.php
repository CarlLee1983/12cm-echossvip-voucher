<?php

namespace CHYP\Partner\Echooss\Voucher\Application;

class ApiContext
{
    protected string $voucherProdHost;
    protected string $voucherSandboxHost;
    protected string $rewardsCardHost;
    protected bool $useSandbox;
    protected string $token = '';
    protected float $timeout;

    /**
     * @param boolean $useSandbox         Whether to target sandbox host.
     * @param string  $voucherProdHost    Production voucher base URI.
     * @param string  $voucherSandboxHost Sandbox voucher base URI.
     * @param string  $rewardsCardHost    Rewards card base URI.
     * @param float   $timeout            HTTP timeout seconds.
     */
    public function __construct(
        bool $useSandbox = false,
        string $voucherProdHost = 'https://service.12cm.com.tw',
        string $voucherSandboxHost = 'https://testservice.12cm.com.tw',
        string $rewardsCardHost = 'https://stagevip-api.12cm.com.tw',
        float $timeout = 10.0
    ) {
        $this->useSandbox = $useSandbox;
        $this->voucherProdHost = $voucherProdHost;
        $this->voucherSandboxHost = $voucherSandboxHost;
        $this->rewardsCardHost = $rewardsCardHost;
        $this->timeout = $timeout;
    }

    /**
     * Determine whether sandbox endpoint is used.
     *
     * @return boolean
     */
    public function useSandbox(): bool
    {
        return $this->useSandbox;
    }

    /**
     * Toggle sandbox mode.
     *
     * @param boolean $useSandbox Sandbox flag.
     *
     * @return self
     */
    public function setSandbox(bool $useSandbox): self
    {
        $this->useSandbox = $useSandbox;

        return $this;
    }

    /**
     * Assign API token.
     *
     * @param string $token Bearer token string.
     *
     * @return self
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Retrieve current API token.
     *
     * @return string
     */
    public function token(): string
    {
        return $this->token;
    }

    /**
     * Resolve voucher host based on environment.
     *
     * @return string
     */
    public function voucherBaseUri(): string
    {
        return $this->useSandbox ? $this->voucherSandboxHost : $this->voucherProdHost;
    }

    /**
     * Get rewards card base URI.
     *
     * @return string
     */
    public function rewardsCardBaseUri(): string
    {
        return $this->rewardsCardHost;
    }

    /**
     * Get HTTP timeout seconds.
     *
     * @return float
     */
    public function timeout(): float
    {
        return $this->timeout;
    }

    /**
     * Override HTTP timeout seconds.
     *
     * @param float $timeout Timeout seconds.
     *
     * @return self
     */
    public function setTimeout(float $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }
}
