<?php

namespace OneAccount\OneAccountAgeVerification\Observer;

use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;

class ConfigObserver implements ObserverInterface
{
    const CLIENT_SECRET_PATH_URL = 'oneaccount/general/clientSecret';
    const CLIENT_ID_PATH_URL = 'oneaccount/general/clientID';
    const CLIENT_LOGO_PATH_URL = 'oneaccount/general/logo';
    const CLIENT_AVLEVEL_PATH_URL = 'oneaccount/general/avLevel';
    const MODULE_ENABLE_PATH_URL = 'oneaccount/general/enable';

    const ONEACCOUNT_VALIDATION_URL = 'https://api.1account.net/oauth/publisher/client-auth';

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * @var RedirectFactory
     */
    protected $redirectFactory;

    /**
     * @param EncryptorInterface $encryptor
     * @param RequestInterface $request
     * @param Logger $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param WriterInterface $configWriter
     * @param MessageManagerInterface $messageManager
     * @param RedirectFactory $redirectFactory
     */
    public function __construct(
        EncryptorInterface $encryptor,
        RequestInterface $request,
        Logger $logger,
        ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter,
        MessageManagerInterface $messageManager,
        RedirectFactory $redirectFactory
    ) {
        $this->encryptor = $encryptor;
        $this->request = $request;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->messageManager = $messageManager;
        $this->redirectFactory = $redirectFactory;
    }

    /**
     * @param EventObserver $observer
     * @throws LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        $oneaccountParams = $this->request->getParam('groups');

        if ($oneaccountParams['general']['fields']['enable']['value'] === '1') {
            $clientId = $oneaccountParams['general']['fields']['clientID']['value'];
            $clientSecretEnc = $this->scopeConfig->getValue(self::CLIENT_SECRET_PATH_URL);
            $hash = base64_encode($this->encryptor->decrypt($clientSecretEnc));

            $validateData = curl_init(self::ONEACCOUNT_VALIDATION_URL);
            $customerData = [
                'hash' => $hash,
                'id' => $clientId
            ];
            curl_setopt($validateData, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($validateData, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($validateData, CURLOPT_POSTFIELDS, json_encode($customerData));
            curl_setopt($validateData, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

            $response = json_decode(curl_exec($validateData), true);

            if (array_key_exists('errors', $response)) {
                $this->configWriter->save(self::CLIENT_SECRET_PATH_URL, null);
                $this->configWriter->save(self::CLIENT_ID_PATH_URL, null);
                $this->configWriter->save(self::CLIENT_AVLEVEL_PATH_URL, null);
                $this->configWriter->save(self::MODULE_ENABLE_PATH_URL, 0);

                $message = __("Fields 'clientID' and 'clientSecret' are invalid");
                throw new LocalizedException(__($message));
            } else {
                $this->configWriter->save(self::CLIENT_LOGO_PATH_URL, $response['logo']);
                $this->configWriter->save(self::CLIENT_AVLEVEL_PATH_URL, $response['avLevel']);
            }
        } else {
            $this->configWriter->save(self::CLIENT_SECRET_PATH_URL, null);
            $this->configWriter->save(self::CLIENT_ID_PATH_URL, null);
            $this->configWriter->save(self::CLIENT_AVLEVEL_PATH_URL, null);
        }

        return $this;
    }
}
