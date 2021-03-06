<?php

namespace Elgentos\Imgix\Model;

class Image
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        Config $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    public function getDefaultUrl(string $currentUrl): string
    {
        return $this->getServiceUrl($currentUrl, $this->config->getConfigValue(Config::XPATH_FIELD_LARGE));
    }

    public function getSmallUrl(string $currentUrl): string
    {
        return $this->getServiceUrl($currentUrl, $this->config->getConfigValue(Config::XPATH_FIELD_SMALL));
    }

    public function getServiceUrl(string $currentUrl, string $params): string
    {
        return str_replace($this->storeManager->getStore()->getBaseUrl(),
            $this->config->getConfigValue(Config::XPATH_FIELD_SERVICE_URL),
            $currentUrl
        ) . '?' . $params;
    }

    public function isServiceEnabled(): bool {
        return $this->config->isEnabled();
    }

    public function getSignedUrl($url, $params = [])
    {
        $host = parse_url($this->config->getConfigValue(Config::XPATH_FIELD_SERVICE_URL), PHP_URL_HOST);
        if (!$host) {
            return false;
        }

        $builder = new \Imgix\UrlBuilder($host);
        $builder->setSignKey($this->config->getConfigValue(Config::XPATH_FIELD_SIGN_KEY));
        return $builder->createURL($url, $params);
    }
}
