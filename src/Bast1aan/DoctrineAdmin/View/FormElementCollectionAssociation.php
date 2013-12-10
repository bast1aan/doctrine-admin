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
	use Bast1aan\DoctrineAdmin\CollectionAssociationProperty;
	use Bast1aan\DoctrineAdmin\Entity;
	class FormElementCollectionAssociation extends FormElementAssociation {
		
		/**
		 * @var array
		 */
		private $remoteEntitiesById = array();
		
		public function __construct(CollectionAssociationProperty $property, Form $form) {
			$this->form = $form;
			$this->property = $property;
			
			// build array of remote entities mapped by string id
			foreach($this->property as $remoteEntity) {
				if ($remoteEntity instanceof Entity) {
					$id = $remoteEntity->getIdAsStr();
					$values[] = $id;
					$this->remoteEntitiesById[$id] = $remoteEntity; 
				}
			}
		}
		
		/**
		 * @return string
		 */
		protected function getTemplate() {
			return __DIR__ . '/form_element_collection_association.phtml';
		}
		
		/**
		 * Return list of IDs
		 * 
		 * @return string[]
		 */
		public function getValue() {
			return array_keys($this->remoteEntitiesById);
		}
		
		/**
		 * Set list of IDs for this association. Resets old list.
		 * 
		 * @param string[]
		 */
		public function setValue($value) {
			$this->property->clear();
			$this->remoteEntitiesById = array();
			$da = $this->property->getDoctrineAdmin();
			foreach((array)$value as $id) {
				$remoteEntity = $da->find($this->property->getEntityName(), $value);
				$this->property->add($remoteEntity);
				$this->remoteEntitiesById[$id] = $remoteEntity;
			}
		}
		
		/**
		 * Render entity given by id.
		 * 
		 * @param string $id
		 * @return string
		 */
		protected function renderEntity($id) {
			return isset($this->remoteEntitiesById[$id]) ? (string) $this->remoteEntitiesById[$id] : '';
		}
		
		/**
		 * @return string
		 */
		protected function getFieldType() {
			return 'collection_association';
		}		
	}
}