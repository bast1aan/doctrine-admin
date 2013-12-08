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
	class FormElementCollectionAssociation extends FormElementAssociation {
		
		public function __construct(CollectionAssociationProperty $property, Form $form) {
			$this->form = $form;
			$this->property = $property;
		}
		
		/**
		 * @return string
		 */
		protected function getTemplate() {
			return __DIR__ . '/form_element_collection_association.phtml';
		}
	}
}