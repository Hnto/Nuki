<?php
/**
 * Created by PhpStorm.
 * User: herant
 * Date: 20-03-18
 * Time: 20:32
 */

namespace Nuki\Skeletons\Providers;


use Nuki\Application\Application;

abstract class Extender
{

    /**
     * Contains the default
     * method name of the execute
     */
    const EXECUTE_METHOD = 'execute';

    /**
     * Contains the application
     *
     * @var Application
     */
    protected $app;

    /**
     * Extender constructor.
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->app = $application;
    }
}
