<?php
/**
 * @brief 升级更新控制器
 */
class Update extends IController
{
	/**
	 * @brief iwebshop15011000 版本升级更新
	 */
	public function iwebshop15030300()
	{
		$sql = array(
			"ALTER TABLE `{pre}seller` ADD `sort` smallint(5) NOT NULL default '99' COMMENT '排序';",
			"ALTER TABLE `{pre}goods` ADD `is_share` tinyint(1) NOT NULL default '0' COMMENT '共享商品 0不共享 1共享';",
		);

		foreach($sql as $key => $val)
		{
			$val = str_replace('{pre}',IWeb::$app->config['DB']['tablePre'],$val);
			IDBFactory::getDB()->query($val);
		}

		die('升级成功！');
	}
}