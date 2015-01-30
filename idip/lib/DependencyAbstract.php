<?php 
abstract class DependencyAbstract
{
	protected $base;
	protected $registry;
	protected $config;

	public function __construct(DependencyAbstract $dependency = null)
	{
		$this->base     = Base::getInstance();
		$this->registry = Registry::getInstance();
		$this->config   = $this->base->getConfig();

		// if we have a parent dependency
		if($dependency !== null)
		{
			$dependency->setup();
		}

		$this->setup();
	}

	public function getArgs()
	{
		return $this->registry;
	}

	protected function setup()
	{
		if(!$this->registry->offsetExists('base'))
		{
			$this->registry->offsetSet('base', $this->base);
		}

		if(!$this->registry->offsetExists('config'))
		{
			$this->registry->offsetSet('config', $this->base->getConfig());
		}

		if(!$this->registry->offsetExists('loader'))
		{
			$this->registry->offsetSet('loader', $this->base->getLoader());
		}
	}
}