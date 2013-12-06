<?php

namespace Bast1aan\DoctrineAdmin {
	
	use Bast1aan\DoctrineAdmin\View\Form;
	
	interface EntityView {
		
		function setEntity(Entity $entity);
		
		/**
		 * @return Entity
		 */
		function getEntity();
		
		function toString();
		
		/**
		 * @return Form
		 */
		function getForm();
	}
}