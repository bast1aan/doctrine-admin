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
		
		/**
		 * @param array|string[] $formData
		 * @todo excerpt. needs to be tested and improved
		 */
		public function populate(array $formData) {
			$da = $this->entity->getDoctrineAdmin();
			foreach($this->entity->getFieldNames() as $fieldName) {
				$property = $this->entity->getColumn($fieldName);
				if (isset($formData[$fieldName])) {
					$property->setValueFromString($formData[$fieldName]);
				} else {
					$property->setValue(null);
				}
				$this->entity->setColumn($property);
			}
			
			foreach($this->entity->getAssociationNames() as $associationName) {
				$property = $this->entity->getColumn($fieldName);
				if ($property instanceof DoctrineAdmin\CollectionAssociationProperty) {
					$property->clear();
					
					if (isset($formData[$associationName])) {
						foreach((array) $formData[$associationName] as $id) {
							$property->add($da->find($property->getType(), $id));
						}
					}
				} elseif ($property instanceof DoctrineAdmin\AssociationProperty) {
					if (isset($formData[$associationName])) {
						$property->setValue($da->find($property->getType(), $id));
					} else {
						$property->setValue(null);
					}
				}
			}
		}
	}
}