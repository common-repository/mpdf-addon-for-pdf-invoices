<?php

namespace Psr\Log;

if ( !interface_exists( 'Psr\Log\LoggerAwareInterface' ) ) {
    /**
     * Describes a logger-aware instance.
     */
    interface LoggerAwareInterface
    {
        /**
         * Sets a logger instance on the object.
         *
         * @param LoggerInterface $logger
         *
         * @return void
         */
        public function setLogger(LoggerInterface $logger);
    }
}
