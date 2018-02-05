<?php
namespace VoolaTech\Authentication\Observer;

class ControllerAuthenticationAction implements \Magento\Framework\Event\ObserverInterface
{
    const CONFIG_PATCH_FRONTEND_PROTECTION_IS_ACTIVE = 'frontend_protection/authentication/is_active';
    const CONFIG_PATCH_FRONTEND_PROTECTION_USER = 'frontend_protection/authentication/user';
    const CONFIG_PATCH_FRONTEND_PROTECTION_PASSWORD = 'frontend_protection/authentication/password';
    private $scopeConfig;
    private $httpAuthentication;
    private $actionFlag;
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\HTTP\Authentication $httpAuthentication,
        \Magento\Framework\App\ActionFlag $actionFlag
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->httpAuthentication = $httpAuthentication;
        $this->actionFlag = $actionFlag;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $isActive = $this->scopeConfig->getValue(self::CONFIG_PATCH_FRONTEND_PROTECTION_IS_ACTIVE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($isActive) {
            list($user, $password) = $this->httpAuthentication->getCredentials();
            $validUser = $this->scopeConfig->getValue(self::CONFIG_PATCH_FRONTEND_PROTECTION_USER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $validPassword = $this->scopeConfig->getValue(self::CONFIG_PATCH_FRONTEND_PROTECTION_PASSWORD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($user !== $validUser || $password !== $validPassword) {
                $this->httpAuthentication->setAuthenticationFailed(__('Authentication'));
                $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
            }
        }
    }
}