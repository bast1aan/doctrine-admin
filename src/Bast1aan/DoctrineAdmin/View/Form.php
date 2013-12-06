<?php

namespace Bast1aan\DoctrineAdmin\View {
	use Bast1aan\DoctrineAdmin;
	class Form {
		
		/**
		 * @var DoctrineAdmin\Entity
		 */
		private $entity;
		
		public function __construct(DoctrineAdmin\Entity $entity) {
			$this->entity = $entity;
		}
		
		protected function getTemplate() {
			return __DIR__ . '/form.phtml';
		}
		
		/**
		 * @return string
		 */
		public function render() {
			ob_start();
			require $this->getTemplate();
			return ob_get_clean();
		}
		
		public function __toString() {
			return $this->render();
		}
		
		/**
		 * 
		 * @return DoctrineAdmin\Entity
		 */
		public function getEntity() {
			return $this->entity;
		}
	}
}