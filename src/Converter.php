<?php

namespace Watermelon\BitcoinConverter;

use GuzzleHttp\Client;
use Watermelon\BitcoinConverter\CurrencyCodeChecker;
use Watermelon\BitcoinConverter\Provider\CoinbaseProvider;
use Watermelon\BitcoinConverter\Provider\ProviderInterface;
use Watermelon\BitcoinConverter\Exception\InvalidArgumentException;

class Converter
{
    /**
     * Provider instance.
     *
     * @var Watermelon\BitcoinConverter\Provider\ProviderInterface
     */
    protected $provider;

    /**
     * Create Converter instance.
     *
     * @param ProviderInterface $provider
     */
    public function __construct(ProviderInterface $provider = null)
    {
        if (empty($provider)) {
            $provider = new CoinbaseProvider;
        }

        $this->provider = $provider;
    }

    /**
     * Convert Bitcoin amount to a specific currency.
     *
     * @param  string $currencyCode
     * @param  float  $btcAmount
     * @return float
     */
    public function toCurrency($currencyCode, $btcAmount)
    {
        $rate = $this->getRate($currencyCode);

        $value = $this->computeCurrencyValue($btcAmount, $rate);

        return $this->formatToCurrency($currencyCode, $value);
    }

    /**
     * Get rate of currency.
     *
     * @param  string $currencyCode
     * @return float
     */
    protected function getRate($currencyCode)
    {
        return $this->provider->getRate($currencyCode);
    }

    /**
     * Compute currency value.
     *
     * @param  float $btcAmount
     * @param  float $rate
     * @return float
     * @throws Watermelon\BitcoinConverter\Exception\InvalidArgumentException
     */
    protected function computeCurrencyValue($btcAmount, $rate)
    {
        if (! is_numeric($btcAmount)) {
            throw new InvalidArgumentException("Argument \$btcAmount should be numeric, '{$btcAmount}' given.");
        }

        return $btcAmount * $rate;
    }

    /**
     * Format value based on currency.
     *
     * @param  string $currencyCode
     * @param  float  $value
     * @return float
     */
    protected function formatToCurrency($currencyCode, $value)
    {
        return $this->format_to_currency($currencyCode, $value);
    }

    public function format_to_currency($currencyCode, $value)
    {
        if ($this->is_crypto_currency($currencyCode)) {
            return round($value, 8, PHP_ROUND_HALF_UP);
        }

        if ($this->is_fiat_currency($currencyCode)) {
            return round($value, 2, PHP_ROUND_HALF_UP);
        }

        throw new InvalidArgumentException("Argument \$currencyCode not valid currency code, '{$currencyCode}' given.");
    }
      /**
     * Check if cryptocurrency.
     *
     * @param  string  $currencyCode
     * @return boolean
     */
    public function is_crypto_currency($currencyCode)
    {
        return (new CurrencyCodeChecker)->isCryptoCurrency($currencyCode);
    }

     /**
     * Check if fiat currency.
     *
     * @param  string  $currencyCode
     * @return boolean
     */
    public function is_fiat_currency($currencyCode)
    {
        return (new CurrencyCodeChecker)->isFiatCurrency($currencyCode);
    }

    /**
     * Convert currency amount to Bitcoin.
     *
     * @param  float  $amount
     * @param  string $currency
     * @return float
     */
    public function toBtc($amount, $currencyCode)
    {
        $rate = $this->getRate($currencyCode);

        $value = $this->computeBtcValue($amount, $rate);

        return $this->formatToCurrency('BTC', $value);
    }

    /**
     * Compute Bitcoin value.
     *
     * @param  float $amount
     * @param  float $rate
     * @return float
     * @throws Watermelon\BitcoinConverter\Exception\InvalidArgumentException
     */
    protected function computeBtcValue($amount, $rate)
    {
        if (! is_numeric($amount)) {
            throw new InvalidArgumentException("Argument \$amount should be numeric, '{$amount}' given.");
        }

        return $amount / $rate;
    }
}
