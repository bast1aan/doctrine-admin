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
		
		/**
		 * @var FormElement[]
		 */
		private $elements;
		
		/**
		 * @var View
		 */
		private $view;
		
		public function __construct(View $view) {
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
		 * @deprecated
		 */
		public function getEntity() {
			return $this->view->getEntity();
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
				if (is_array($formData['is_null'])) {
					$isNull = $formData['is_null'];
				}
				if ($property instanceof ScalarProperty) {
					if (isset($formData[$fieldName]) && empty($isNull[$fieldName])) {
						$this->view->formatStringToScalarProperty($formData[$fieldName], $property);
					} else {
						$property->setValue(null);
					}
				} elseif ($property instanceof CollectionAssociationProperty) {
					$property->clear();
					if (is_array($formData[$fieldName])) {
						foreach($formData[$fieldName] as $id) {
							if (empty($id))
								continue;
							$entity = $this->view->getEntityById($property->getEntityName(), $id);
							if ($entity instanceof Entity) {
								$property->add($entity);
							}
						}
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
			$entity = $this->view->getEntity();
			foreach($this->getElements() as $element) {
				$entity->setColumn($element->getProperty());
			}
			$entity->save();
			
		}
		
		/**
		 * @return FormElement
		 * @param string $elementName
		 */
		public function getElement($elementName) {
			$property = $this->view->getEntity()->getColumn($elementName);
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
				$entity = $this->view->getEntity();
				foreach(array_merge($entity->getFieldNames(), $entity->getAssociationNames()) as $name) {
					$this->elements[] = $this->view->getFormElement($entity->getColumn($name), $this);
				}
			}
			return $this->elements;
		}
	}
}