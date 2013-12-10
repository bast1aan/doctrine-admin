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
	use Bast1aan\DoctrineAdmin;
	class FormElementAssociation implements FormElement {
		
		/**
		 *
		 * @var Form
		 */
		protected $form;
		
		/**
		 *
		 * @var DoctrineAdmin\AssociationProperty
		 */
		protected $property;
		
		public function __construct(DoctrineAdmin\AssociationProperty $property, Form $form) {
			$this->form = $form;
			$this->property = $property;
		}
		
		public function __toString() {
			return $this->render();
		}
		
		/**
		 * @return Form
		 */
		protected function getForm() {
			return $this->form;
		}
		
		/**
		 * 
		 * @return DoctrineAdmin\AssociationProperty
		 */
		public function getProperty() {
			return $this->property;
		}
		
		public function setProperty(DoctrineAdmin\Property $property) {
			$this->property = $property;
		}
		
		/**
		 * @return string
		 */
		protected function getTemplate() {
			return __DIR__ . '/form_element_association.phtml';
		}
		
		/**
		 * @return string
		 */
		public function render() {
			ob_start();
			require $this->getTemplate();
			return ob_get_clean();
		}
		
		/**
		 * 
		 * @return string
		 */
		protected function renderEntity() {
			return (string) $this->property->getValue();
		}
		
		/**
		 * @param string
		 */
		public function getValue() {
			$remoteEntity = $this->property->getValue();
			if ($remoteEntity instanceof DoctrineAdmin\Entity) {
				return $remoteEntity->getIdAsStr();
			}
		}
		
		/**
		 * @param string $value
		 */
		public function setValue($value) {
			$da = $this->property->getDoctrineAdmin();
			$remoteEntity = $da->find($this->property->getEntityName(), $value);
			$this->property->setValue($remoteEntity);
		}
		
		/**
		 * @return string
		 */
		public function getFieldName() {
			return $this->property->getName();
		}
		
		/**
		 * @return string
		 */
		protected function getFieldType() {
			return 'association';
		}
	
	}
}