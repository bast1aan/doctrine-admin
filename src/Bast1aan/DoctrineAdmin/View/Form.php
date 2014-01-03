<?php
/*
 * Doctrine Admin
 * Copyright (C) 2013 Bastiaan Welmers, bastiaan@welmers.net
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace Bast1aan\DoctrineAdmin\View {
	use Bast1aan\DoctrineAdmin\AssociationProperty;

	use Bast1aan\DoctrineAdmin\CollectionAssociationProperty;
	use Bast1aan\DoctrineAdmin\Entity;
	use Bast1aan\DoctrineAdmin\ScalarProperty;
	use Bast1aan\DoctrineAdmin;
	
	class Form implements HasView {

		const FIELD_NAME_IS_NULL = 'is_null';

		/**
		 * @var FormElement[]
		 */
		private $elements;

		/**
		 * @var Entity
		 */
		private $entity;

		/**
		 * @var View
		 */
		private $view;
		
		public function __construct(Entity $entity = null, View $view = null) {
			$this->entity = $entity;
			$this->view = $view;
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
		 * @return Entity
		 */
		public function getEntity() {
			return $this->entity;
		}

		/**
		 * @param Entity $entity
		 */
		public function setEntity($entity) {
			$this->entity = $entity;
		}

		/**
		 * @param View $view
		 */
		public function setView($view) {
			$this->view = $view;
		}

		/**
		 * @return View
		 */
		public function getView() {
			return $this->view;
		}
		
		/**
		 * Populate form and entities connected to it with data send from a browser.
		 * 
		 * @param array|string[] $formData
		 * @todo excerpt. needs to be tested and improved
		 */
		public function populate(array $formData) {
			
			foreach($this->getElements() as $element) {
				$property = $element->getProperty();
				$fieldName = $property->getName();
				$isNull = array();
				if (isset($formData[self::FIELD_NAME_IS_NULL]) && is_array($formData[self::FIELD_NAME_IS_NULL])) {
					$isNull = $formData[self::FIELD_NAME_IS_NULL];
				}
				if ($property instanceof ScalarProperty) {
					if (isset($formData[$fieldName]) && empty($isNull[$fieldName])) {
						$this->view->formatStringToScalarProperty($formData[$fieldName], $property);
					} elseif (!$property->isAutoId()) {
						$property->setValue(null);
					}
				} elseif ($property instanceof CollectionAssociationProperty) {
					if (is_array($formData[$fieldName])) {
						$entityIdsBefore = array();
						foreach($property as $entity) {
							$entityIdBefore = $this->view->renderEntityId($entity);
							if (!in_array($entityIdBefore, $formData[$fieldName])) {
								// entity ID is no longer in the form, so it is removed
								$property->remove($entity);
							} else {
								$entityIdsBefore[] = $entityIdBefore;
							}
						}
						foreach($formData[$fieldName] as $id) {
							if (empty($id))
								continue;
							if (!in_array($id, $entityIdsBefore)) {
								// an entity has been added
								$entity = $this->view->getEntityById($property->getEntityName(), $id);
								if ($entity instanceof Entity) {
									$property->add($entity);
								}
							}
						}
					} else {
						$property->clear();
					}
				} elseif ($property instanceof AssociationProperty) {
					$entity = null;
					if (!empty($formData[$fieldName])) {
						$entity = $this->view->getEntityById($property->getEntityName(), $formData[$fieldName]);
					}
					if ($entity instanceof Entity && empty($isNull[$fieldName])) {
						$property->setValue($entity);
					} else {
						$property->setValue(null);
					}
				}
			}
		}
		
		public function save() {
			foreach($this->getElements() as $element) {
				$this->entity->setColumn($element->getProperty());
			}
			$this->entity->save();
			
		}
		
		/**
		 * @return FormElement
		 * @param string $elementName
		 */
		public function getElement($elementName) {
			$property = $this->entity->getColumn($elementName);
			if ($property instanceof DoctrineAdmin\Property) {
				return $this->view->getFormElement($property, $this);
			}
		}

		
		/**
		 * @return FormElement[]
		 */
		public function getElements() {
			if ($this->elements == null) {
				$this->elements = array();
				foreach(array_merge($this->entity->getFieldNames(), $this->entity->getAssociationNames()) as $name) {
					$this->elements[] = $this->view->getFormElement($this->entity->getColumn($name), $this);
				}
			}
			return $this->elements;
		}
	}
}