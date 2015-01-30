<?php 
class Dependency_Default extends DependencyAbstract
{
	protected function setup()
	{
		parent::setup();

		if(!$this->registry->offsetExists('sql'))
		{
			$sql = new Sql(new Sql_Driver_Mysqli(), $this->config['sql_host'], $this->config['sql_user'], $this->config['sql_pw'], $this->config['sql_db'], $this->config['sql_port']);

			$this->registry->offsetSet('sql', $sql);
		}
	}
}
