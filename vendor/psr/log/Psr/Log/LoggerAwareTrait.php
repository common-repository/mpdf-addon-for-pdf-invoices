<?php

namespace Psr\Log;

if( !trait_exists( 'Psr\Log\LoggerAwareTrait' ) ) {
    /**
     * Basic Implementation of LoggerAwareInterface.
     */
    trait LoggerAwareTrait
    {
        /**
         * The logger instance.
         *
         * @var LoggerInterface
         */
        protected $logger;

        /**
         * Sets a logger.
         *
         * @param LoggerInterface $logger
         */
        public function setLogger(LoggerInterface $logger)
        {
            $this->logger = $logger;
        }
    }

}
