<?php

namespace Core\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LogHeader
 *
 * @ORM\Table(name="log_header", indexes={@ORM\Index(name="log_message_to_header", columns={"log_message_id"})})
 * @ORM\Entity
 */
class LogHeader
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="var_key", type="string", length=255, nullable=true)
     */
    private $varKey;

    /**
     * @var string
     *
     * @ORM\Column(name="var_value", type="string", length=255, nullable=true)
     */
    private $varValue;

    /**
     * @var LogMessage
     *
     * @ORM\ManyToOne(targetEntity="LogMessage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="log_message_id", referencedColumnName="id")
     * })
     */
    private $logMessage;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set varKey
     *
     * @param string $varKey
     *
     * @return LogHeader
     */
    public function setVarKey($varKey)
    {
        $this->varKey = $varKey;

        return $this;
    }

    /**
     * Get varKey
     *
     * @return string
     */
    public function getVarKey()
    {
        return $this->varKey;
    }

    /**
     * Set varValue
     *
     * @param string $varValue
     *
     * @return LogHeader
     */
    public function setVarValue($varValue)
    {
        $this->varValue = $varValue;

        return $this;
    }

    /**
     * Get varValue
     *
     * @return string
     */
    public function getVarValue()
    {
        return $this->varValue;
    }

    /**
     * Set logMessage
     *
     * @param LogMessage $logMessage
     *
     * @return LogHeader
     */
    public function setLogMessage(LogMessage $logMessage = null)
    {
        $this->logMessage = $logMessage;

        return $this;
    }

    /**
     * Get logMessage
     *
     * @return LogMessage
     */
    public function getLogMessage()
    {
        return $this->logMessage;
    }
}
