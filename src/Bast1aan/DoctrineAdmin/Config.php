<?php

namespace Bast1aan\DoctrineAdmin {
	interface Config {
		/**
		 * Return DoctrineAdmin Entity implementation per entity given by name.
		 * @param object $name
		 * @return Entity
		 */
		function getDoctrineAdminEntityByNativeEntity($entity);
	}
}