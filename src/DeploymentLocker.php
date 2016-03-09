<?php
namespace In2code\DeploymentLocker;

use TYPO3\Surf\Domain\Model\Deployment;

/**
 * Class DeploymentLocker
 */
class DeploymentLocker
{
    /**
     * @var Deployment
     */
    protected $deployment = null;

    /**
     * Indicates if the LockFile should be removed after deployment
     *
     * @var bool
     */
    protected $locksCurrent = false;

    /**
     * DeploymentLocker constructor.
     *
     * @param Deployment $deployment
     */
    protected function __construct(Deployment $deployment)
    {
        $this->deployment = $deployment;
    }

    /**
     * @param Deployment $deployment
     * @throws \Exception
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function lockDeployment(Deployment $deployment)
    {
        global $argv;
        if (!empty($argv[1]) && $argv[1] === 'deploy') {
            $GLOBALS['deploymentLocker'] = new self($deployment);
            $GLOBALS['deploymentLocker']->checkAndLockDeployment();
        }
    }

    /**
     * @throws \Exception
     */
    public function checkAndLockDeployment()
    {
        if ($this->lockFileExists()) {
            throw new DeploymentLockedException($this->deployment);
        }
        $this->createLockFile();
    }

    /**
     * Deletes the LockFile if this Instance
     * of Locker was created for a valid deployment
     */
    public function __destruct()
    {
        if ($this->locksCurrent) {
            unlink($this->getLockFilePath() . $this->getLockFileName());
            echo 'LOCK FILE REMOVED' . PHP_EOL;
        }
    }

    /**
     * @return bool
     */
    protected function lockFileExists()
    {
        return is_file($this->getLockFileIdentifier());
    }

    /**
     * @return void
     */
    protected function createLockFile()
    {
        $this->locksCurrent = true;

        $lockFilePath = $this->getLockFilePath();

        // ensure the lock directory exists
        if (!is_dir($lockFilePath)) {
            mkdir($lockFilePath, 0775, true);
        }

        // gather all information for the lock file
        $lockInformation = array(
            'Deployment' => serialize($this->deployment),
            'Begin' => time(),
        );

        // create the lock with some basic information
        file_put_contents(
            $lockFilePath . $this->getLockFileName(),
            json_encode($lockInformation)
        );
        echo 'LOCK FILE CREATED' . PHP_EOL;
    }

    /**
     * @return string
     */
    protected function getLockFileIdentifier()
    {
        return $this->getLockFilePath() . $this->getLockFileName();
    }

    /**
     * @return string
     */
    protected function getLockFilePath()
    {
        return sys_get_temp_dir() . '/.surf/Locks/Deployment/';
    }

    /**
     * @return string
     */
    protected function getLockFileName()
    {
        return filter_var(
            $this->deployment->getName(),
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW
        );
    }
}


