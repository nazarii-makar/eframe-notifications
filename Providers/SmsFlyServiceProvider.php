<?php

namespace EFrame\Notifications\Providers;

use RuntimeException;
use Illuminate\Support\ServiceProvider;
use EFrame\Notifications\Clients\SmsFlyClient;
use Illuminate\Contracts\Config\Repository as Config;

class SmsFlyServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SmsFlyClient::class, function ($app) {
            return $this->createSmsFlyClient($app['config']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [SmsFlyClient::class];
    }

    /**
     * @param Config $config
     *
     * @return SmsFlyClient
     */
    protected function createSmsFlyClient(Config $config)
    {
        if (! $this->hasSmsFlyConfigSection()) {
            $this->raiseRunTimeException('Missing smsfly configuration section.');
        }

        if ($this->smsFlyConfigHasNo('login') || $this->smsFlyConfigHasNo('password')) {
            $this->raiseRunTimeException('Missing smsfly credentials');
        }

        $client = new SmsFlyClient(
            $config->get('notification.channels.smsfly.login'),
            $config->get('notification.channels.smsfly.password'),
            $config->get('notification.channels.smsfly.alfaname')
        );

        return $client;
    }

    /**
     * Checks if has global SmsFly configuration section.
     *
     * @return bool
     */
    protected function hasSmsFlyConfigSection()
    {
        return $this->app->make(
            Config::class
        )->has('notification.channels.smsfly');
    }

    /**
     * Checks if SmsFly config does not
     * have a value for the given key.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function smsFlyConfigHasNo($key)
    {
        return ! $this->smsFlyConfigHas($key);
    }

    /**
     * Checks if SmsFly config has value for the
     * given key.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function smsFlyConfigHas($key)
    {
        /** @var Config $config */
        $config = $this->app->make(Config::class);

        if (! $config->has('notification.channels.smsfly')) {
            return false;
        }

        return
            $config->has('notification.channels.smsfly.'.$key) &&
            ! is_null($config->get('notification.channels.smsfly.'.$key)) &&
            ! empty($config->get('notification.channels.smsfly.'.$key));
    }

    /**
     * Raises Runtime exception.
     *
     * @param string $message
     *
     * @throws RuntimeException
     */
    protected function raiseRunTimeException($message)
    {
        throw new RuntimeException($message);
    }
}