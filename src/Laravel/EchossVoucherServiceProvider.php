<?php

namespace CHYP\Partner\Echooss\Voucher\Laravel;

use CHYP\Partner\Echooss\Voucher\Application\ApiContext;
use CHYP\Partner\Echooss\Voucher\Application\Assembler\RequestAssembler;
use CHYP\Partner\Echooss\Voucher\Application\Factory\RewardsCardUseCaseFactory;
use CHYP\Partner\Echooss\Voucher\Application\Factory\ResponseFactory;
use CHYP\Partner\Echooss\Voucher\Application\Factory\VoucherUseCaseFactory;
use CHYP\Partner\Echooss\Voucher\Application\Gateway\RewardsCardGatewayInterface;
use CHYP\Partner\Echooss\Voucher\Application\Gateway\VoucherGatewayInterface;
use CHYP\Partner\Echooss\Voucher\Application\Hydrator\ResponseHydrator;
use CHYP\Partner\Echooss\Voucher\Application\Service\RewardsCardService;
use CHYP\Partner\Echooss\Voucher\Application\Service\VoucherService;
use CHYP\Partner\Echooss\Voucher\Core;
use CHYP\Partner\Echooss\Voucher\Infrastructure\Http\RewardsCardGateway;
use CHYP\Partner\Echooss\Voucher\Infrastructure\Http\VoucherGateway;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * Laravel Service Provider for Echoss VIP Voucher SDK.
 *
 * 此 Service Provider 會將 Echoss Voucher SDK 註冊到 Laravel 的 IoC 容器中，
 * 支援配置檔案發佈、Facade 自動發現，並實作延遲載入以優化效能。
 */
class EchossVoucherServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * 註冊服務到容器中。
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom($this->configPath(), 'echoss-voucher');

        $this->registerApiContext();
        $this->registerHttpClient();
        $this->registerGateways();
        $this->registerServices();
        $this->registerUseCaseFactories();
        $this->registerCore();
    }

    /**
     * 啟動服務。
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->configPath() => config_path('echoss-voucher.php'),
            ], 'echoss-voucher-config');
        }
    }

    /**
     * 取得此 Provider 提供的服務列表（延遲載入用）。
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [
            Core::class,
            'echoss.voucher',
            ApiContext::class,
            VoucherGatewayInterface::class,
            RewardsCardGatewayInterface::class,
            VoucherService::class,
            RewardsCardService::class,
            VoucherUseCaseFactory::class,
            RewardsCardUseCaseFactory::class,
        ];
    }

    /**
     * 取得配置檔案路徑。
     *
     * @return string
     */
    protected function configPath(): string
    {
        return dirname(__DIR__, 2) . '/config/echoss-voucher.php';
    }

    /**
     * 註冊 API Context 單例。
     *
     * @return void
     */
    protected function registerApiContext(): void
    {
        $this->app->singleton(ApiContext::class, function ($app) {
            $config = $app['config']['echoss-voucher'];

            $context = new ApiContext(
                $config['sandbox'] ?? false,
                $config['hosts']['voucher_production'] ?? 'https://service.12cm.com.tw',
                $config['hosts']['voucher_sandbox'] ?? 'https://testservice.12cm.com.tw',
                $config['hosts']['rewards_card'] ?? 'https://stagevip-api.12cm.com.tw',
                $config['timeout'] ?? 10.0
            );

            if (!empty($config['token'])) {
                $context->setToken($config['token']);
            }

            return $context;
        });
    }

    /**
     * 註冊 HTTP Client 單例。
     *
     * @return void
     */
    protected function registerHttpClient(): void
    {
        $this->app->singleton(ClientInterface::class, function ($app) {
            $context = $app->make(ApiContext::class);

            return new Client([
                'timeout' => $context->timeout(),
            ]);
        });
    }

    /**
     * 註冊 Gateway 實作。
     *
     * @return void
     */
    protected function registerGateways(): void
    {
        $this->app->singleton(VoucherGatewayInterface::class, function ($app) {
            return new VoucherGateway(
                $app->make(ClientInterface::class),
                $app->make(ApiContext::class)
            );
        });

        $this->app->singleton(RewardsCardGatewayInterface::class, function ($app) {
            return new RewardsCardGateway(
                $app->make(ClientInterface::class),
                $app->make(ApiContext::class)
            );
        });
    }

    /**
     * 註冊應用服務。
     *
     * @return void
     */
    protected function registerServices(): void
    {
        $this->app->singleton(ResponseFactory::class, function () {
            return new ResponseFactory();
        });

        $this->app->singleton(ResponseHydrator::class, function ($app) {
            return new ResponseHydrator($app->make(ResponseFactory::class));
        });

        $this->app->singleton(RequestAssembler::class, function () {
            return new RequestAssembler();
        });

        $this->app->singleton(VoucherService::class, function ($app) {
            return new VoucherService(
                $app->make(VoucherGatewayInterface::class),
                $app->make(RequestAssembler::class),
                $app->make(ResponseHydrator::class)
            );
        });

        $this->app->singleton(RewardsCardService::class, function ($app) {
            return new RewardsCardService(
                $app->make(RewardsCardGatewayInterface::class),
                $app->make(RequestAssembler::class),
                $app->make(ResponseHydrator::class)
            );
        });
    }

    /**
     * 註冊 UseCase 工廠。
     *
     * @return void
     */
    protected function registerUseCaseFactories(): void
    {
        $this->app->singleton(VoucherUseCaseFactory::class, function () {
            return new VoucherUseCaseFactory();
        });

        $this->app->singleton(RewardsCardUseCaseFactory::class, function () {
            return new RewardsCardUseCaseFactory();
        });
    }

    /**
     * 註冊 Core Facade 主入口。
     *
     * @return void
     */
    protected function registerCore(): void
    {
        $this->app->singleton(Core::class, function ($app) {
            return new Core(
                $app['config']['echoss-voucher.sandbox'] ?? false,
                $app->make(ApiContext::class),
                $app->make(ClientInterface::class),
                $app->make(VoucherService::class),
                $app->make(RewardsCardService::class),
                $app->make(VoucherUseCaseFactory::class),
                $app->make(RewardsCardUseCaseFactory::class)
            );
        });

        // 註冊別名，方便使用
        $this->app->alias(Core::class, 'echoss.voucher');
    }
}

